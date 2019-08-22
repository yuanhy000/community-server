<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 18:03
 */

namespace App\Http\Service;


use App\Exceptions\OrderException;
use App\Exceptions\UserException;
use App\Http\model\OrderModel;
use App\Http\model\OrderProductModel;
use App\Http\Model\ProductModel;
use App\Http\model\UserAddressModel;
use Exception;

class Order
{
    //订单的商品列表，客户端传递过来的Product参数
    protected $orderProducts;
    //真实的商品信息（库存量等），全部
    protected $products;
    //用户id
    protected $uid;

    //进行下单
    public function place($uid, $orderProducts)
    {
        $this->orderProducts = $orderProducts;
        $this->products = $this->getProductsByOrder($orderProducts);
        $this->uid = $uid;
        //判断订单状态
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        //生成订单快照
        $orderSnap = $this->snapOrder($status);
        //开始创建订单
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    private function createOrder($snap)
    {
        //采用事物，避免数据不一致
        \DB::beginTransaction();
        try {
            //将数据存入order表中
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['productStatus']);
            $order->save();
            //将数据存入order_product表中
            $orderID = $order->id;
            $created_at = $order->created_at->format('Y-m-d H:i:s');
            foreach ($this->orderProducts as &$orderProduct) {
                $orderProduct['order_id'] = $orderID;
                OrderProductModel::create($orderProduct);
            }
            \DB::commit();
//            \DB::table('order_product')->insert($this->orderProducts);
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'created_at' => $created_at
            ];
        } catch (Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
    }

    //生成订单快照，订单的当前状态，不会受数据改变影响
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'productStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['productStatus'] = $status['productStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    //获得用户的地址信息
    private function getUserAddress()
    {
        $userAddress = UserAddressModel::where('user_id', '=', $this->uid)->get()->toArray();
        if (empty($userAddress)) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败了',
                'errorCode' => 60001,
            ]);
        }
        return $userAddress;
    }

    //外部调用判断库存量
    public function checkOrderStock($orderID)
    {
        $orderProducts = OrderProductModel::where('order_id', '=', $orderID)->get();
        $this->orderProducts = $orderProducts;
        $this->products = $this->getProductsByOrder($orderProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

    //对订单进行判断，获取订单信息
    private function getOrderStatus()
    {
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'productStatusArray' => []
        ];
        //订单中的多组商品遍历，判断
        foreach ($this->orderProducts as $orderProduct) {
            //获取商品信息，是否有库存，价格等信息
            $productStatus = $this->
            getProductStatus($orderProduct['product_id'], $orderProduct['count'], $this->products);
            if (!$productStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $productStatus['totalPrice'];
            $status['totalCount'] += $productStatus['counts'];
            array_push($status['productStatusArray'], $productStatus);
        }
        return $status;
    }

    //对商品信息进行判断，获取商品信息
    private function getProductStatus($orderProductID, $orderCount, $products)
    {
        $productIndex = -1;
        $productStatus = [
            'id' => null,
            'haveStock' => false,
            'counts' => 0,
            'price' => 0,
            'name' => '',
            'totalPrice' => 0,
            'main_img_url' => null
        ];
        //将目标商品与订单中真实的商品比较，得到目标商品在全部商品中的id号
        for ($i = 0; $i < count($products); $i++) {
            if ($orderProductID == $products[$i]['id']) {
                $productIndex = $i;
            }
        }
        if ($productIndex == -1) {
            throw new OrderException([
                'msg' => 'id为' . $orderProductID . '的商品不存在，创建订单失败'
            ]);
        } else {
            //获取商品实例，设置商品信息
            $product = $products[$productIndex];
            $productStatus['id'] = $product['id'];
            $productStatus['name'] = $product['name'];
            $productStatus['counts'] = $orderCount;
            $productStatus['price'] = $product['price'];
            $productStatus['main_img_url'] = $product['main_img_url'];
            $productStatus['totalPrice'] = $product['price'] * $orderCount;
        }
        if ($product['stock'] - $orderCount >= 0) {
            $productStatus['haveStock'] = true;
        }
        return $productStatus;
    }

    //根据订单信息查找订单中真实的商品信息
    private function getProductsByOrder($orderProducts)
    {
        $orderProductIDs = [];
        //记录订单中目标商品的id，统一查询，避免多次访问数据库
        foreach ($orderProducts as $item) {
            array_push($orderProductIDs, $item['product_id'] + 0);
        }
        $products = ProductModel::find($orderProductIDs)
            ->makeVisible(['id', 'price', 'stock', 'name', 'main_img_url'])->toArray();
        return $products;
    }

    //随机生成订单号
    public function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $orderNo = $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5) . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        return $orderNo;
    }
}