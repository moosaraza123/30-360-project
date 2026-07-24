<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email — {{ $appName }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                    {{-- Header band --}}
                    <tr>
                        <td style="background-color:#0f172a;border-radius:12px 12px 0 0;padding:28px 40px;text-align:center;">
                            <span style="font-size:26px;font-weight:800;color:#c9a227;">30</span><span style="font-size:26px;font-weight:300;color:rgba(255,255,255,0.35);">/</span><span style="font-size:26px;font-weight:700;color:#ffffff;">360</span>
                            <span style="font-size:15px;color:#ffffff;font-weight:500;">&nbsp;Calculator</span>
                            <div style="margin-top:6px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,0.45);">
                                Professional Day Count Conventions
                            </div>
                        </td>
                    </tr>

                    {{-- Body card --}}
                    <tr>
                        <td style="background-color:#ffffff;border-radius:0 0 12px 12px;padding:40px;">
                            <h1 style="margin:0 0 8px;color:#0f172a;font-size:22px;font-weight:700;">
                                Welcome, {{ $userName }}!
                            </h1>
                            <div style="width:44px;height:3px;background-color:#c9a227;border-radius:2px;margin:0 0 24px;"></div>

                            <p style="margin:0 0 12px;color:#334155;font-size:15px;line-height:1.65;">
                                Thanks for creating your {{ $appName }} account. Please confirm your
                                email address to unlock your dashboard and saved calculations.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:26px 0;">
                                        <a href="{{ $verificationUrl }}"
                                           style="display:inline-block;background-color:#c9a227;color:#0f172a;font-size:15px;font-weight:700;text-decoration:none;padding:14px 40px;border-radius:8px;">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 20px;color:#64748b;font-size:13px;line-height:1.6;">
                                This link expires in {{ $expireMinutes }} minutes. After verifying you can
                                save calculations, build your history, and export results.
                            </p>

                            <div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:24px;">
                                <div style="font-size:12px;font-weight:700;color:#334155;margin-bottom:6px;">
                                    Button not working? Copy this link into your browser:
                                </div>
                                <div style="font-size:12px;color:#64748b;word-break:break-all;">
                                    {{ $verificationUrl }}
                                </div>
                            </div>

                            <hr style="border:none;border-top:1px solid #e2e8f0;margin:0 0 20px;">

                            <p style="margin:0 0 6px;color:#94a3b8;font-size:12px;line-height:1.6;">
                                If you didn't create an account with {{ $appName }}, no further action is
                                required — you can safely ignore this email.
                            </p>
                            <p style="margin:0;color:#94a3b8;font-size:12px;">
                                <strong style="color:#64748b;">{{ $appName }}</strong> — Professional financial calculation tools.
                                This is an automated email; please do not reply.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
