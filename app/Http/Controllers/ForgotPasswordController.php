<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Để tạo OTP ngẫu nhiên
use Illuminate\Support\Facades\Mail; // Để gửi Email
use App\Mail\OtpMail; // Bạn cần tạo Mail class này
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Gửi mã OTP đến email/username của người dùng và lưu vào Redis.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
        // 1. Validate dữ liệu đầu vào (email hoặc username)
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string', // Có thể là email hoặc username
        ], [
            'identifier.required' => 'Vui lòng nhập Email hoặc tên đăng nhập.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $identifier = $request->input('identifier');

        // 2. Tìm người dùng dựa trên email hoặc username
        $user = Customer::where('email', $identifier)
            ->whereNull('platform_id') // Chỉ tài khoản đăng ký thông thường
            ->first();


        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng với thông tin bạn cung cấp.'], 404);
        }

        $redisKey = 'otp:' . $user->id;
        $existingOtp = Cache::store('redis')->get($redisKey);

        if ($existingOtp) {
            return response()->json([
                'message' => 'Mã OTP vẫn còn hiệu lực. Vui lòng kiểm tra email hoặc đợi 1 phút để gửi lại.'
            ], 429);
        }



        // 4. Tạo mã OTP
        $otp = random_int(100000, 999999); // OTP 6 chữ số

        // 5. Lưu OTP vào Redis với thời gian sống 1 phút
        $ttl = now()->addMinutes(1);
        Cache::store('redis')->put($redisKey, $otp, $ttl);


        // 5. Gửi mã OTP qua email (hoặc SMS)
        // Bạn cần tạo một Mailable class (App\Mail\OtpMail) để gửi email
        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));
        } catch (\Exception $e) {
            // Log lỗi nếu gửi email thất bại, nhưng vẫn trả về thành công cho người dùng
            // để tránh lộ thông tin email không tồn tại.
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            return response()->json(['message' => 'Đã gửi mã OTP, nhưng có lỗi xảy ra khi gửi email. Vui lòng kiểm tra hộp thư Spam hoặc thử lại sau.'], 500);
        }


        return response()->json(['message' => 'Mã OTP đã được gửi đến Email của bạn. Vui lòng kiểm tra.'], 200);
    }

    /**
     * Xác nhận OTP và đổi mật khẩu.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'identifier_hidden' => 'required|string', // email/username đã gửi OTP
            'otp_code' => 'required|numeric|digits:6',
            'new_password' => 'required|string|min:6|confirmed', // 'confirmed' sẽ tự động kiểm tra new_password_confirmation
        ], [
            'identifier_hidden.required' => 'Thông tin người dùng không hợp lệ.',
            'otp_code.required' => 'Vui lòng nhập mã OTP.',
            'otp_code.numeric' => 'Mã OTP phải là số.',
            'otp_code.digits' => 'Mã OTP phải có 6 chữ số.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất :min ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $identifier = $request->input('identifier_hidden');
        $otpCode = $request->input('otp_code');
        $newPassword = $request->input('new_password');

        // 2. Tìm người dùng
        $user = Customer::where('email', $identifier)
            ->whereNull('platform_id') // Chỉ tài khoản đăng ký thông thường
            ->first();



        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại.'], 404);
        }

        // 3. Lấy OTP từ Redis
        $redisKey = 'otp:' . $user->id; // Phải khớp với key khi lưu
        $storedOtp = Cache::store('redis')->get($redisKey);

        // 4. Kiểm tra OTP
        if (!$storedOtp || $storedOtp != $otpCode) {
            return response()->json(['message' => 'Mã OTP không hợp lệ hoặc đã hết hạn.'], 400);
        }

        // 5. Xóa OTP khỏi Redis sau khi xác nhận thành công (để tránh dùng lại)
        Cache::store('redis')->forget($redisKey);

        // 6. Cập nhật mật khẩu mới
        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json(['message' => 'Mật khẩu của bạn đã được đặt lại thành công!'], 200);
    }
}
