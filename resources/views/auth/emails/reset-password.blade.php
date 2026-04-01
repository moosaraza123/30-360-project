<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; background-color: #f1f5f9; padding: 40px 16px; }
        .container { max-width: 580px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); border-radius: 12px 12px 0 0; padding: 36px 40px 32px; text-align: center; }
        .logo-mark { display: inline-block; width: 52px; height: 52px; background: linear-gradient(135deg, #c9a227, #a07d18); border-radius: 12px; margin-bottom: 16px; line-height: 52px; font-size: 24px; font-weight: 900; color: #0f172a; letter-spacing: -1px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; margin: 0; }
        .header p { color: #94a3b8; font-size: 13px; margin-top: 4px; }
        .body { background: #ffffff; padding: 40px; }
        .greeting { font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 16px; }
        .text { font-size: 15px; color: #475569; line-height: 1.65; margin-bottom: 20px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #c9a227, #a07d18); color: #0f172a !important; text-decoration: none; font-size: 15px; font-weight: 700; padding: 14px 36px; border-radius: 8px; letter-spacing: 0.2px; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0; }
        .fallback { font-size: 13px; color: #94a3b8; line-height: 1.6; }
        .fallback a { color: #c9a227; word-break: break-all; }
        .expire-note { background: #fef9ee; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #92400e; margin-bottom: 24px; }
        .footer { background: #f8fafc; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; padding: 24px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .footer a { color: #c9a227; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">

            <!-- Header -->
            <div class="header">
                <div class="logo-mark">30</div>
                <h1>{{ $appName }}</h1>
                <p>Bond &amp; Derivative Day Count Calculator</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="greeting">Hello, {{ $userName }}</p>

                <p class="text">
                    We received a request to reset the password for your account associated with this email address.
                    Click the button below to choose a new password.
                </p>

                <div class="expire-note">
                    ⏱ This link will expire in <strong>{{ $expireMinutes }} minutes</strong>. If you didn't request a password reset, you can safely ignore this email.
                </div>

                <div class="btn-wrap">
                    <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
                </div>

                <hr class="divider">

                <p class="fallback">
                    If the button above doesn't work, copy and paste the link below into your browser:
                </p>
                <p class="fallback" style="margin-top: 8px;">
                    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    You're receiving this email because a password reset was requested for your <strong>{{ $appName }}</strong> account.<br>
                    If you did not make this request, no action is required — your password will remain unchanged.
                </p>
                <p style="margin-top: 12px;">
                    &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                </p>
            </div>

        </div>
    </div>
</body>
</html>
