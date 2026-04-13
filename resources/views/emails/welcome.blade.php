<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $companyName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #333;
            font-size: 20px;
            margin-top: 0;
        }
        .content p {
            margin: 16px 0;
            color: #555;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            margin: 24px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }
        .features {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 24px;
            margin: 24px 0;
        }
        .features h3 {
            color: #333;
            font-size: 18px;
            margin-top: 0;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 16px 0;
        }
        .feature-list li {
            padding: 8px 0;
            color: #555;
        }
        .feature-list li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 8px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #1e40af; margin: 0;">Welcome to {{ $companyName }}! 🎉</h1>
        </div>
        
        <div class="content">
            <h2 style="color: #1e40af; margin-top: 0;">Hello {{ $user->name }},</h2>
            
            <p>Thank you for creating your account! We're thrilled to have you join our platform.</p>
            
            <p>Your account has been successfully created and you're all set to get started.</p>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/dashboard" class="button">Go to Your Dashboard</a>
            </div>
            
            <div class="features">
                <h3 style="color: #1e40af; margin-top: 0;">What you can do now:</h3>
                <ul class="feature-list">
                    <li>Access your personalized dashboard</li>
                    <li>Manage your profile and preferences</li>
                    <li>Connect with our team</li>
                    <li>Explore all available features</li>
                </ul>
            </div>
            
            <p>If you have any questions or need assistance, our support team is here to help. Just reply to this email or reach out through your dashboard.</p>
            
            <p style="margin-top: 32px;">
                <strong>Welcome aboard!</strong><br>
                The {{ $companyName }} Team
            </p>
        </div>
        
        <div class="footer">
            <p>
                You're receiving this email because you created an account at {{ $companyName }}.<br>
                <a href="{{ config('app.url') }}">Visit our website</a>
            </p>
        </div>
    </div>
</body>
</html>
