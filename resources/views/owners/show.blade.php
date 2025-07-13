@extends('layouts.app')

@section('title', $owner->name)
@section('header', $owner->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb Navigation -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('owners.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Owners
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $owner->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ $owner->name }}</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('owners.edit', $owner) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        <i class="fas fa-edit mr-2"></i>Edit Owner
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Owner Details -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Owner Details</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Name</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $owner->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $owner->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Phone</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $owner->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Company</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $owner->company ?? 'Not provided' }}</p>
                        </div>
                        @if($owner->address)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Address</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $owner->address }}</p>
                        </div>
                        @endif
                        @if($owner->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Notes</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $owner->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistics</h2>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $owner->properties->count() }}</div>
                            <div class="text-sm text-gray-600">Properties Owned</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $owner->created_at->format('M Y') }}</div>
                            <div class="text-sm text-gray-600">Added to System</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Properties List -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Properties</h2>
                @if($properties->count() > 0)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($properties as $property)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $property->name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $property->address }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('properties.show', $property) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-6 py-4 border-t">
                            {{ $properties->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500">No properties assigned to this owner yet.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 