<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class FacebookAuthController extends Controller
{
    //
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

     // Xử lý callback từ Facebook
     public function handleFacebookCallback()
     {
         try {
             $facebookUser = Socialite::driver('facebook')->user();
 
             // Tìm hoặc tạo user mới
             $user = Customer::updateOrCreate([
                 'email' => $facebookUser->getEmail(),
             ], [
                 'name' => $facebookUser->getName(),
                 'platform_id' => $facebookUser->getId(),
                 'avatar' => $facebookUser->getAvatar(),
             ]);
 
             // Đăng nhập user
             Auth::guard('customer')->login($user);
 
             return redirect()->intended('/');
         } catch (\Exception $e) {
             return redirect()->route('user.login')->with('error', 'Đăng nhập thất bại, vui lòng thử lại.');
         }
     }
}
