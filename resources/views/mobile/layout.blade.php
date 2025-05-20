<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MaintainXtra Mobile')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Make sure Tailwind CSS is built into public/css/app.css -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white shadow p-4 flex items-center justify-between">
        <div class="font-extrabold text-xl"><span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span></div>
        <div>
            @yield('header-actions')
        </div>
    </header>
    <main class="p-2">
        @yield('content')
    </main>
</body>
</html> 