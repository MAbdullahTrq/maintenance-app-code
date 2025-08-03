@extends('layouts.app')

@section('title', 'Owners')
@section('header', 'Owners')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Owners</h1>
        <a href="{{ route('owners.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Add New Owner
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($owners->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Properties</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($owners as $owner)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $owner->displayName }}</div>
                                        <div class="text-sm text-gray-500">{{ $owner->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $owner->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $owner->company ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $owner->properties->count() }} properties</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('owners.show', $owner) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('owners.edit', $owner) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if($owner->properties->count() === 0)
                                        <form method="POST" action="{{ route('owners.destroy', $owner) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this owner?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 cursor-not-allowed" title="Cannot delete owner with properties: {{ $owner->properties->pluck('name')->implode(', ') }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">
                {{ $owners->links() }}
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                No owners found. Click "Add New Owner" to create one.
            </div>
        @endif
    </div>
</div>
@endsection 