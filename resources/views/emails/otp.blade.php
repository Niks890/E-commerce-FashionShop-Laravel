<x-mail::message>
{{-- Header --}}
<div style="text-align: center; margin-bottom: 30px;">
    <h1 style="color: #2563eb; margin: 0; font-size: 28px; font-weight: 700;">
        🔐 Đặt lại mật khẩu
    </h1>
</div>

{{-- Greeting --}}
<div style="margin-bottom: 25px;">
    <p style="font-size: 16px; color: #374151; margin: 0; line-height: 1.6;">
        Xin chào <strong style="color: #1f2937;">{{ $userName }}</strong>!
    </p>
</div>

{{-- Main content --}}
<div style="margin-bottom: 25px;">
    <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0; line-height: 1.6;">
        Bạn nhận được email này vì bạn (hoặc ai đó) đã yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
    </p>
</div>

{{-- OTP Code Box --}}
<div style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border: 2px solid #d1d5db; border-radius: 12px; padding: 25px; text-align: center; margin: 25px 0;">
    <p style="margin: 0 0 10px 0; font-size: 14px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
        MÃ OTP CỦA BẠN
    </p>
    <div style="background: #ffffff; border: 2px dashed #2563eb; border-radius: 8px; padding: 15px; margin: 10px 0;">
        <span style="font-size: 32px; font-weight: 800; color: #2563eb; letter-spacing: 4px; font-family: 'Courier New', monospace;">
            {{ $otp }}
        </span>
    </div>
    <p style="margin: 15px 0 0 0; font-size: 14px; color: #dc2626; font-weight: 600;">
        ⏰ Mã này sẽ hết hạn trong <strong>1 phút</strong>
    </p>
</div>

{{-- Security Warning --}}
<div style="background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #dc2626; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <div style="display: flex; align-items: flex-start;">
        <div style="margin-right: 12px; font-size: 20px;">⚠️</div>
        <div>
            <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #dc2626; font-weight: 700;">
                CẢNH BÁO BẢO MẬT
            </h3>
            <ul style="margin: 0; padding-left: 16px; color: #7f1d1d; font-size: 14px; line-height: 1.6;">
                <li style="margin-bottom: 5px;"><strong>KHÔNG</strong> chia sẻ mã OTP này với bất kỳ ai</li>
                <li style="margin-bottom: 5px;"><strong>KHÔNG</strong> gửi mã này qua tin nhắn, email hay mạng xã hội</li>
                <li style="margin-bottom: 5px;">Nhân viên {{ config('app.name') }} <strong>KHÔNG BAO GIỜ</strong> yêu cầu mã OTP qua điện thoại</li>
                <li>Nếu có nghi ngờ, vui lòng liên hệ bộ phận hỗ trợ ngay lập tức</li>
            </ul>
        </div>
    </div>
</div>

{{-- Instructions --}}
<div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #0369a1; font-weight: 600;">
        📋 Hướng dẫn sử dụng:
    </h3>
    <ol style="margin: 0; padding-left: 16px; color: #0c4a6e; font-size: 14px; line-height: 1.6;">
        <li style="margin-bottom: 5px;">Sao chép mã OTP phía trên</li>
        <li style="margin-bottom: 5px;">Quay lại trang đặt lại mật khẩu</li>
        <li style="margin-bottom: 5px;">Nhập mã OTP vào ô yêu cầu</li>
        <li>Tạo mật khẩu mới mạnh và bảo mật</li>
    </ol>
</div>

{{-- Footer note --}}
<div style="margin: 30px 0 20px 0; padding: 20px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
    <p style="font-size: 14px; color: #6b7280; margin: 0; line-height: 1.6; text-align: center;">
        Nếu bạn <strong>không yêu cầu</strong> đặt lại mật khẩu, vui lòng <strong>bỏ qua email này</strong> và
        <span style="color: #dc2626; font-weight: 600;">thay đổi mật khẩu ngay lập tức</span> nếu nghi ngờ tài khoản bị xâm phạm.
    </p>
</div>

{{-- Signature --}}
<div style="margin-top: 40px; text-align: center;">
    <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 500;">
        Trân trọng,<br>
        <strong style="color: #2563eb;">Đội ngũ {{ config('app.name') }}</strong>
    </p>
</div>

{{-- Footer --}}
<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
    <p style="font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.5;">
        Email này được gửi tự động, vui lòng không trả lời.<br>
        Nếu cần hỗ trợ, vui lòng liên hệ với chúng tôi qua website chính thức.
    </p>
</div>
</x-mail::message>
