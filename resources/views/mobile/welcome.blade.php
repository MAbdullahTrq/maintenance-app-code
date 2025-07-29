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
            <div class="font-extrabold text-4xl md:text-5xl lg:text-6xl mb-4">
                <span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Property Maintenance Management</h1>
            <p class="text-gray-600 mb-4">For <span class="text-blue-600 underline">small</span> to <span class="text-blue-600 underline">medium</span> property managers</p>
            <p class="text-xl font-bold text-black mb-4">A simple set of powerful tools</p>
            <ul class="text-left text-gray-700 mb-6 space-y-2">
                <li>• QR codes – submitting a request has never been easier</li>
                <li>• Assign tasks – to your preferred technicians</li>
                <li>• Real-time notifications – keep everyone in the loop</li>
                <li>• Create reports – Admin is a breeze</li>
                <li>• Add team members – <span class="underline">role based permissions</span></li>
            </ul>
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
        
        <div class="mt-8 text-center">
            <h2 class="text-2xl font-bold text-blue-600 mb-2">Built for vacation rental managers</h2>
            <p class="text-lg text-gray-700 mb-4">Everything you need to stay on top of property maintenance</p>
            <p class="text-gray-600">
                Whether you manage a few vacation homes or a growing portfolio of <span class="underline">long term rental properties</span>, 
                <span class="text-red-600 underline font-bold">MaintainXtra</span> helps you track repairs, assign tasks, create reports, 
                and keep things running smoothly—without the overwhelm.
            </p>
        </div>
        
        <div class="mt-8 text-sm text-gray-500">
            <p>Choose your preferred option above</p>
        </div>
    </div>
</body>
</html> 