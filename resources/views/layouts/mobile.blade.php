<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - MaintainXtra</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
        .page-title {
            background: #55d748;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .page-url {
            background: #f8f9fa;
            color: #0000ff;
            text-align: center;
            padding: 5px;
            margin: 0;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #fff;
            border-bottom: 1px solid #ddd;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #000;
        }
        .logo span {
            color: #0000ff;
        }
        .user-type {
            color: #333;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .user-type i {
            margin-right: 5px;
        }
        .nav-icons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .nav-icon {
            padding: 10px;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100px;
        }
        .nav-icon:last-child {
            border-right: none;
        }
        .nav-icon i {
            font-size: 24px;
            color: #4CAF50;
            margin-bottom: 5px;
        }
        .nav-icon .count {
            font-weight: bold;
            font-size: 20px;
        }
        .nav-icon .add {
            margin-top: 5px;
            font-size: 24px;
            color: #666;
        }
        .section-title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }
        .search-bar {
            margin: 10px 15px;
        }
        .search-bar input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: #f8f9fa;
            text-align: left;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
        .data-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
        .priority-low {
            background-color: #ffff00;
            padding: 5px;
            text-align: center;
        }
        .priority-high {
            background-color: #ff0000;
            color: white;
            padding: 5px;
            text-align: center;
        }
        .view-icon {
            text-align: center;
        }
        .view-icon i {
            font-size: 20px;
        }
        .action-buttons {
            display: flex;
            margin: 15px 0;
        }
        .action-buttons .btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .btn-decline {
            background: white;
        }
        .btn-approve {
            background: #4CAF50;
            color: white;
        }
        .btn-start {
            background: #4CAF50;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            display: block;
            width: 100%;
            border: none;
        }
        .btn-finish {
            background: black;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            display: block;
            width: 100%;
            border: none;
        }
        .btn-complete {
            background: #555;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            display: block;
            width: 100%;
            border: none;
            margin: 15px 0;
        }
        .property-details {
            padding: 15px;
            background: white;
            margin: 15px 0;
            border: 1px solid #ddd;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-actions {
            display: flex;
            margin-top: 20px;
        }
        .form-actions .btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .btn-cancel {
            background: white;
        }
        .btn-save {
            background: white;
        }
        .btn-delete {
            background: #ff0000;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            display: block;
            width: 100%;
            border: none;
            margin-top: 20px;
        }
        .status-tag {
            display: inline-block;
            padding: 5px 10px;
            background: #eee;
            border: 1px solid #ddd;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .image-preview {
            width: 100%;
            height: 150px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .file-input {
            margin: 10px 0;
        }
        .section-subtitle {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
        .property-card {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        .action-menu {
            position: fixed;
            bottom: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .action-menu div {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    @if(isset($pageTitle))
    <div class="page-title">{{ $pageTitle }}</div>
    @endif
    
    @if(isset($pageUrl))
    <div class="page-url">{{ $pageUrl }}</div>
    @endif
    
    <div class="header">
        <div class="logo">
            <a href="{{ route('mobile.welcome') }}" style="text-decoration: none; color: inherit;">
                <span>M</span>aintain<span>X</span>tra
            </a>
        </div>
        <div class="user-type">
            @if(Auth::check() && Auth::user()->isManager())
                Manager <i class="fas fa-chevron-right"></i>
            @elseif(Auth::check() && Auth::user()->isTechnician())
                Technician <i class="fas fa-chevron-right"></i>
            @endif
        </div>
    </div>
    
    @yield('nav-icons')
    
    <div class="content">
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html> 