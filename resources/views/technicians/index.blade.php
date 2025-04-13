@extends('layouts.app')

@section('title', 'Manage Technicians')

@section('header', 'Manage Technicians')

@section('content')
<div class="container mx-auto px-4 pt-12">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Your Technicians</h2>
        <a href="{{ route('technicians.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Add New Technician
        </a>
    </div>

    @if(session('password_reset'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('password_reset') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Phone
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($technicians as $technician)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($technician->image)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $technician->image) }}" alt="{{ $technician->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $technician->name }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $technician->email }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $technician->phone }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold {{ $technician->is_active ? 'text-green-900' : 'text-red-900' }} leading-tight">
                                <span aria-hidden class="absolute inset-0 {{ $technician->is_active ? 'bg-green-200' : 'bg-red-200' }} opacity-50 rounded-full"></span>
                                <span class="relative">{{ $technician->is_active ? 'Active' : 'Inactive' }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex space-x-3">
                                <a href="{{ route('technicians.edit', $technician) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                
                                <form action="{{ route('technicians.toggle-active', $technician) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                        {{ $technician->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('technicians.reset-password', $technician) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-900">Reset Password</button>
                                </form>

                                <form action="{{ route('technicians.destroy', $technician) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this technician?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No technicians found. <a href="{{ route('technicians.create') }}" class="text-blue-600 hover:text-blue-900">Add your first technician</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 