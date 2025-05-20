<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance App - Mobile</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .mobile-header { background: #2563eb; color: #fff; padding: 1.5rem 1rem; text-align: center; }
        .mobile-section { margin: 1.5rem 1rem; background: #fff; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.5rem; }
        .mobile-btn { display: block; width: 100%; background: #2563eb; color: #fff; border: none; border-radius: 0.5rem; padding: 1rem; font-size: 1.1rem; margin-top: 1.5rem; text-align: center; text-decoration: none; }
    </style>
</head>
<body>
    <div class="mobile-header">
        <h1>Maintenance App</h1>
        <p style="font-size:1.1rem;">Easy maintenance management on the go</p>
    </div>
    <div class="mobile-section">
        <h2 style="font-size:1.3rem; font-weight:600;">Welcome!</h2>
        <p style="margin-top:0.5rem;">Submit requests, track progress, and stay updated—all from your phone.</p>
        <a href="{{ route('register') }}" class="mobile-btn">Get Started</a>
    </div>
    <div class="mobile-section">
        <h3 style="font-size:1.1rem; font-weight:500;">Features</h3>
        <ul style="margin-top:0.5rem; padding-left:1.2rem;">
            <li>• Submit maintenance requests</li>
            <li>• Track request status</li>
            <li>• Receive notifications</li>
            <li>• Contact property manager</li>
        </ul>
    </div>
</body>
</html> 