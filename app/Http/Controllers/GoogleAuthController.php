<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class GoogleAuthController extends Controller
{
    //

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    // Xử lý callback sau khi đăng nhập Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Tìm user theo email hoặc tạo mới
            $user = Customer::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'platform_id' => $googleUser->getId(),
                'image' => $googleUser->getAvatar(),
            ]);

            // Đăng nhập user
            Auth::guard('customer')->login($user);

            // Chuyển hướng về trang trước đó hoặc trang chủ
            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        } catch (\Exception $e) {
            return redirect('/user/login')->with('error', 'Đăng nhập Google thất bại!');
        }
    }
}
