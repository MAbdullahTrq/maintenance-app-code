<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaintainXtra - Mobile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="MaintainXtra Logo" class="mx-auto h-24 w-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">MaintainXtra</h1>
            <p class="text-gray-600">Property Maintenance Management</p>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </a>
            
            <a href="{{ route('register') }}" class="block w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                <i class="fas fa-user-plus mr-2"></i> Register
            </a>
            
            <a href="{{ route('guest.request.create') }}" class="block w-full bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                <i class="fas fa-tools mr-2"></i> Submit Maintenance Request
            </a>
        </div>
        
        <div class="mt-8 text-sm text-gray-500">
            <p>Choose your preferred option above</p>
        </div>
    </div>
</body>
</html> 