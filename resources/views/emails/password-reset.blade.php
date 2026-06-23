<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f6f6f8;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f6f6f8;
            padding-bottom: 40px;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            border-collapse: separate;
            shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #135bec;
            padding: 40px;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }

        .content {
            padding: 40px;
            color: #334155;
            line-height: 1.6;
        }

        .button {
            background-color: #135bec;
            border-radius: 8px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            line-height: 50px;
            text-align: center;
            text-decoration: none;
            width: 220px;
            -webkit-text-size-adjust: none;
        }

        .otp-box {
            background-color: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }

        .otp-code {
            font-family: monospace;
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 2px;
        }

        .footer {
            padding: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Reset Your Password</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Hello <strong>{{ $name }}</strong>,</p>
                    <p>We received a request to reset the password for your account. You can reset your password by
                        clicking the button below:</p>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ $url }}" class="button">Reset Password</a>
                    </div>

                    <p>If you prefer to use a manual OTP for verification, please use the code below:</p>

                    <div class="otp-box">
                        <span class="otp-code">{{ $otp }}</span>
                    </div>

                    <p style="font-size: 14px; color: #64748b;">This reset link and code will expire in 60 minutes. If
                        you did not request a password reset, no further action is required.</p>

                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">

                    <p style="font-size: 12px; color: #94a3b8;">If you're having trouble clicking the "Reset Password"
                        button, copy and paste the URL below into your web browser:<br>
                        <a href="{{ $url }}" style="color: #135bec; word-break: break-all;">{{ $url }}</a>
                    </p>
                </td>
            </tr>
        </table>
        <div class="footer">
            &copy; {{ date('Y') }} {{ setting('site_title') ?: config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>