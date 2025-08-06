<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .button {
            display: inline-block;
            background: #007bff !important;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
            border: none;
        }
        .button:hover {
            background: #0056b3 !important;
            color: white !important;
        }
        .button:visited {
            color: white !important;
        }
        .button:active {
            color: white !important;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 You're Invited!</h1>
        <p>Join the {{ config('app.name') }} team</p>
    </div>

    <div class="content">
        <h2>Hello {{ $invitation->name }},</h2>
        
        <p>You've been invited by <strong>{{ $invitation->invitedBy->name }}</strong> to join their team on {{ config('app.name') }}.</p>
        
        <p><strong>Role:</strong> {{ $invitation->role->name }}</p>
        <p><strong>Description:</strong> {{ $invitation->role->description }}</p>
        
        <p>As a team member, you'll be able to:</p>
        <ul>
            @if($invitation->role->slug === 'viewer')
                <li>View properties and maintenance requests</li>
                <li>Access reports and analytics</li>
            @elseif($invitation->role->slug === 'editor')
                <li>View and edit properties and maintenance requests</li>
                <li>Create and manage reports</li>
                <li>Access all team data</li>
            @else
                <li>View and manage properties</li>
                <li>Handle maintenance requests</li>
                <li>Access reports and team data</li>
            @endif
        </ul>
        
        <p>This invitation will expire in 7 days. Click the button below to accept and create your account:</p>
        
        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="button">Accept Invitation</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #007bff;">{{ $acceptUrl }}</p>
    </div>

    <div class="footer">
        <p>This invitation was sent from {{ config('app.name') }}.</p>
        <p>If you didn't expect this invitation, you can safely ignore this email.</p>
    </div>
</body>
</html> 