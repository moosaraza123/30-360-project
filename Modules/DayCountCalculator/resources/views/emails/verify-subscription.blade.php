<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Day Count Calculator</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
        }
        h1 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #64748b;
        }
        .link-backup {
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            word-break: break-all;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Day Count Calculator</div>
        </div>

        <h1>Verify Your Email Address</h1>

        <p>Thank you for subscribing to Day Count Calculator updates!</p>

        <p>Please click the button below to verify your email address and activate your subscription:</p>

        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">
                Verify Email Address
            </a>
        </div>

        <p>Once verified, you'll receive:</p>
        <ul>
            <li>Updates about new calculators and features</li>
            <li>Educational content about day count conventions</li>
            <li>Tips for financial calculations</li>
            <li>Exclusive resources and guides</li>
        </ul>

        <div class="link-backup">
            <strong>If the button doesn't work, copy and paste this link into your browser:</strong><br>
            {{ $verificationUrl }}
        </div>

        <div class="footer">
            <p>
                If you didn't subscribe to our mailing list, you can safely ignore this email.
            </p>
            <p>
                <strong>Day Count Calculator</strong><br>
                Professional Financial Calculation Tools
            </p>
            <p style="font-size: 12px; color: #94a3b8;">
                This is an automated email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
