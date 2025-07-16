@extends('layouts.app')

@section('title', $property->name)
@section('header', $property->name)

@section('content')
<div class="container mx-auto px-4 pt-12">
    <!-- Breadcrumb Navigation -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('properties.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Properties
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $property->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Property Image (Left side on large screens, top on mobile) -->
                @if($property->image)
                    <div class="lg:col-span-1">
                        <div class="aspect-w-4 aspect-h-3 lg:aspect-w-1 lg:aspect-h-1">
                            <img src="{{ asset('storage/' . $property->image) }}" alt="{{ $property->name }}" class="w-full h-64 lg:h-full object-cover rounded-lg shadow-md">
                        </div>
                    </div>
                @endif

                <!-- Property Details and QR Code (Right side on large screens) -->
                <div class="@if($property->image) lg:col-span-2 @else lg:col-span-3 @endif">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        <!-- Property Details -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Property Details</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Name</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $property->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Address</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $property->address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Created</label>
                                    <p class="mt-1 text-lg text-gray-900">{{ $property->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                <a href="{{ route('properties.edit', $property) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                    <i class="fas fa-edit mr-2"></i> Edit Property
                                </a>
                                <form action="{{ route('properties.destroy', $property) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition">
                                        <i class="fas fa-trash-alt mr-2"></i> Delete Property
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">QR Code</h2>
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                @if($property->qr_code)
                                    <img src="{{ asset('storage/' . $property->qr_code) }}" alt="Property QR Code" class="mx-auto mb-4 max-w-[200px]">
                                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                                        <a href="{{ route('properties.qrcode', $property) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition">
                                            <i class="fas fa-download mr-2"></i> Download QR Code
                                        </a>
                                        <button type="button" 
                                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition copy-link-btn" 
                                            data-url="{{ $property->getRequestUrl() }}">
                                            <i class="fas fa-copy mr-2"></i> Copy Link
                                        </button>
                                    </div>
                                @else
                                    <p class="text-gray-500">QR code not generated yet.</p>
                                @endif
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                <p>Scan this QR code to access the maintenance request form for this property.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Maintenance Requests -->
    <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Maintenance Requests</h2>
            @if($property->maintenanceRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($property->maintenanceRequests()->latest()->take(5)->get() as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $request->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($request->status == 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Approved</span>
                                        @elseif($request->status == 'in_progress')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">In Progress</span>
                                        @elseif($request->status == 'completed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                        @elseif($request->status == 'declined')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Declined</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('maintenance.show', $request) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No maintenance requests found for this property.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.copy-link-btn');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    // Change button text temporarily
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        });
    });
</script>
@endpush 