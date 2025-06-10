<x-mail::message>
# Xin chào, {{ $userName }}!

Bạn nhận được email này vì bạn (hoặc ai đó) đã yêu cầu đặt lại mật khẩu cho tài khoản của bạn.

Mã OTP của bạn là: **{{ $otp }}**

Mã này sẽ hết hạn trong 5 phút.

Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.

Trân trọng,
{{ config('app.name') }}
</x-mail::message>
