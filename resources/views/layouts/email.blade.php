<!DOCTYPE html>
<html>
<head>
    @php
        $brandName = config('business.name')
            ?: config('business.company_name')
            ?: config('app.company_name', config('app.name', 'smbgen'));
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $brandName }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
            position: relative;
        }
        .email-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .email-body {
            padding: 45px 35px;
            background-color: #ffffff;
        }
        .email-footer {
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
            padding: 25px 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .email-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: 700;
            text-align: center;
            font-size: 16px;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transition: all 0.3s;
            -webkit-text-fill-color: #ffffff;
        }
        .btn:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        p {
            margin: 16px 0;
            color: #4b5563;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px !important;
            }
            .email-header {
                padding: 25px 20px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>{{ $brandName }}</h1>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p style="font-weight: 600; color: #1f2937; margin-bottom: 10px;">{{ $brandName }}</p>
            <p>&copy; {{ date('Y') }} All rights reserved.</p>
            <p style="margin-top: 15px;">
                <a href="{{ config('app.url') }}">Visit our website</a>
            </p>
        </div>
    </div>
</body>
</html>
