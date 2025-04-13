@extends('layouts.app')

@section('title', 'Maintenance Requests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Maintenance Requests</h1>
        
        @if(auth()->user()->isPropertyManager() || auth()->user()->isAdmin())
            <a href="{{ route('maintenance.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-plus mr-2"></i>Create Request
            </a>
        @endif
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($requests->isEmpty())
            <div class="p-6 text-center">
                <p class="text-gray-500">No maintenance requests found.</p>
                
                @if(auth()->user()->isPropertyManager() || auth()->user()->isAdmin())
                    <a href="{{ route('maintenance.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Create Your First Request
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Property
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Priority
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned To
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->location }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->property->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($request->priority == 'low') bg-blue-100 text-blue-800
                                        @elseif($request->priority == 'medium') bg-yellow-100 text-yellow-800
                                        @elseif($request->priority == 'high') bg-red-100 text-red-800
                                        @endif">
                                        {{ strtoupper($request->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($request->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($request->status == 'accepted') bg-blue-100 text-blue-800
                                        @elseif($request->status == 'assigned') bg-purple-100 text-purple-800
                                        @elseif($request->status == 'started') bg-indigo-100 text-indigo-800
                                        @elseif($request->status == 'completed') bg-green-100 text-green-800
                                        @elseif($request->status == 'declined') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $request->assignedTechnician ? $request->assignedTechnician->name : 'Not Assigned' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('maintenance.show', $request) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        View
                                    </a>
                                    
                                    @if(auth()->user()->isPropertyManager() || auth()->user()->isAdmin())
                                        @if($request->status == 'pending')
                                            <a href="{{ route('maintenance.edit', $request) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Edit
                                            </a>
                                            
                                            <form action="{{ route('maintenance.destroy', $request) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this request?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 