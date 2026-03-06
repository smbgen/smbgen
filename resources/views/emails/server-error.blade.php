<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #dc2626;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .error-box {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        .context-box {
            background: #f3f4f6;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .context-box h3 {
            margin-top: 0;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table td:first-child {
            font-weight: bold;
            width: 150px;
            color: #6b7280;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">🚨 500 Server Error Occurred</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">{{ config('app.name') }} - {{ config('app.env') }}</p>
    </div>

    <div class="content">
        <div class="context-box">
            <h3 style="color: #1f2937; margin-top: 0;">Error Details</h3>
            <table>
                <tr>
                    <td>Exception</td>
                    <td>{{ get_class($exception) }}</td>
                </tr>
                <tr>
                    <td>Message</td>
                    <td>{{ $exception->getMessage() }}</td>
                </tr>
                <tr>
                    <td>File</td>
                    <td>{{ $exception->getFile() }}</td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td>{{ $exception->getLine() }}</td>
                </tr>
                <tr>
                    <td>Time</td>
                    <td>{{ now()->format('Y-m-d H:i:s T') }}</td>
                </tr>
            </table>
        </div>

        @if(!empty($context))
            <div class="context-box">
                <h3 style="color: #1f2937; margin-top: 0;">Request Context</h3>
                <table>
                    @if(isset($context['url']))
                        <tr>
                            <td>URL</td>
                            <td>{{ $context['url'] }}</td>
                        </tr>
                    @endif
                    @if(isset($context['method']))
                        <tr>
                            <td>Method</td>
                            <td>{{ $context['method'] }}</td>
                        </tr>
                    @endif
                    @if(isset($context['user_id']))
                        <tr>
                            <td>User ID</td>
                            <td>{{ $context['user_id'] ?? 'Guest' }}</td>
                        </tr>
                    @endif
                    @if(isset($context['ip']))
                        <tr>
                            <td>IP Address</td>
                            <td>{{ $context['ip'] }}</td>
                        </tr>
                    @endif
                    @if(isset($context['user_agent']))
                        <tr>
                            <td>User Agent</td>
                            <td style="word-break: break-all;">{{ $context['user_agent'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        <div class="error-box">
            <strong>Stack Trace (first 5 lines):</strong><br><br>
            @foreach(array_slice(explode("\n", $exception->getTraceAsString()), 0, 5) as $line)
                {{ $line }}<br>
            @endforeach
            @if(count(explode("\n", $exception->getTraceAsString())) > 5)
                <br><em>... (see logs for full trace)</em>
            @endif
        </div>

        <div class="footer">
            <p>
                This is an automated error notification from {{ config('app.name') }}.<br>
                Check <code>storage/logs/laravel.log</code> for the complete stack trace.
            </p>
        </div>
    </div>
</body>
</html>
