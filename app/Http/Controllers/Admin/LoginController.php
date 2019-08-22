<?php

namespace App\Http\Controllers\Admin;

use App\http\Model\AdminModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if ($request->user_name) {
            $user = AdminModel::checkAdmin($request->user_name, $request->user_pass);
            if (!$user) {
                return back()->with('msg', '用户名或密码错误！');
            }
            session(['user' => $user]);
            return redirect('admin');
        } else {
            session(['user' => null]);
            return view('admin.login.login');
        }
    }

    public function quit()
    {
        session(['user' => null]);
        return redirect('admin/login');
    }
}
