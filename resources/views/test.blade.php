<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test View</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
        <h1 class="text-2xl font-bold text-center mb-6">Test View</h1>
        
        <div class="mb-4">
            <p class="text-gray-700">This is a test view to ensure that views are working correctly.</p>
        </div>
        
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2">User Information:</h2>
            @auth
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Role ID:</strong> {{ auth()->user()->role_id }}</p>
                    <p><strong>Role:</strong> {{ auth()->user()->role ? auth()->user()->role->name : 'No role assigned' }}</p>
                    <p><strong>Is Admin:</strong> {{ auth()->user()->isAdmin() ? 'Yes' : 'No' }}</p>
                    <p><strong>Is Property Manager:</strong> {{ auth()->user()->isPropertyManager() ? 'Yes' : 'No' }}</p>
                    <p><strong>Is Technician:</strong> {{ auth()->user()->isTechnician() ? 'Yes' : 'No' }}</p>
                </div>
            @else
                <p class="text-red-500">Not logged in</p>
            @endauth
        </div>
        
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2">Links:</h2>
            <ul class="list-disc pl-5">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-500 hover:underline">Admin Dashboard</a></li>
                <li><a href="{{ route('direct.properties.create') }}" class="text-blue-500 hover:underline">Create Property (Direct)</a></li>
                <li><a href="{{ route('direct.users.create') }}" class="text-blue-500 hover:underline">Create User (Direct)</a></li>
                <li><a href="{{ url('/properties/create') }}" class="text-blue-500 hover:underline">Create Property (Normal)</a></li>
                <li><a href="{{ url('/users/create') }}" class="text-blue-500 hover:underline">Create User (Normal)</a></li>
            </ul>
        </div>
    </div>
</body>
</html> 