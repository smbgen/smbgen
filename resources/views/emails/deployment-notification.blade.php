@extends('layouts.email')

@section('content')
    <h2 style="margin-top: 0; color: #1e40af;">{{ config('app.name') }} Deployment Notification</h2>
    
    <p style="margin-bottom: 24px;">A new deployment has been completed for <strong>{{ $appName }}</strong>.</p>

    <!-- Deployment Info -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6;">
        <h3 style="margin-top: 0; color: #1e40af;">📋 Deployment Details</h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #666; font-weight: 600; width: 140px;">Environment:</td>
                <td style="padding: 8px 0; color: #333;">
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;
                        {{ $environment === 'production' ? 'background: #dc2626; color: white;' : 'background: #fbbf24; color: #000;' }}">
                        {{ strtoupper($environment) }}
                    </span>
                </td>
            </tr>
            @if($branch)
            <tr>
                <td style="padding: 8px 0; color: #666; font-weight: 600;">Branch:</td>
                <td style="padding: 8px 0; color: #333; font-family: 'Courier New', monospace;">{{ $branch }}</td>
            </tr>
            @endif
            @if($commit)
            <tr>
                <td style="padding: 8px 0; color: #666; font-weight: 600;">Commit:</td>
                <td style="padding: 8px 0; color: #333; font-family: 'Courier New', monospace;">{{ $commit }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 8px 0; color: #666; font-weight: 600;">Deployed At:</td>
                <td style="padding: 8px 0; color: #333;">
                    {{ $deploymentTime }}<br>
                    <span style="color: #6b7280; font-size: 13px;">{{ $deploymentTimezone }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666; font-weight: 600;">Application URL:</td>
                <td style="padding: 8px 0;">
                    <a href="{{ $appUrl }}" style="color: #3b82f6; text-decoration: none;">{{ $appUrl }}</a>
                </td>
            </tr>
        </table>
    </div>

    @if(count($commitHistory) > 0)
        <!-- Commit History -->
        <div style="margin: 24px 0;">
            <h3 style="color: #1e40af; margin-bottom: 16px;">📝 Recent Commits</h3>
            
            <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                @foreach($commitHistory as $index => $commit)
                    <div style="padding: 16px 20px; {{ $index > 0 ? 'border-top: 1px solid #e5e7eb;' : '' }} {{ $index % 2 === 1 ? 'background: #f9fafb;' : '' }}">
                        <div>
                            <div style="margin-bottom: 8px;">
                                <span style="display: inline-block; background: #3b82f6; color: white; padding: 4px 10px; border-radius: 6px; font-family: 'Courier New', monospace; font-size: 12px; font-weight: 600;">
                                    {{ $commit['hash'] }}
                                </span>
                            </div>
                            <div style="color: #111827; font-size: 15px; font-weight: 500; margin-bottom: 6px; line-height: 1.4;">
                                {{ $commit['message'] }}
                            </div>
                            <div style="color: #6b7280; font-size: 13px;">
                                <span style="font-weight: 600;">{{ $commit['author'] }}</span>
                                <span style="color: #9ca3af;">•</span>
                                <span>{{ $commit['time'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(!$gitAvailable)
        <div style="background: #e0f2fe; border: 1px solid #38bdf8; padding: 16px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0; color: #0c4a6e;">
                <strong>ℹ️ Note:</strong> Git is not available in this environment. Deployment completed successfully via Laravel Cloud.
            </p>
        </div>
    @endif

    <!-- Action Button -->
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $appUrl }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff; padding: 12px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">
            View Application
        </a>
    </div>

    <hr style="margin: 32px 0; border: none; border-top: 1px solid #e5e7eb;">

    <p style="color: #6b7280; font-size: 13px; margin: 0;">
        This is an automated deployment notification from {{ $appName }}. 
        If you have any concerns about this deployment, please check the application immediately.
    </p>
@endsection
