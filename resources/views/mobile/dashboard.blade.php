@extends('mobile.layout')

@section('title', 'Dash – Manager')

@section('header-actions')
<a href="#" class="text-sm font-medium">Manager &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <!-- Icon grid (add your icon grid here if not present) -->
        <div class="flex justify-center gap-8 mb-4">
            <div class="flex flex-col items-center">
                <i class="fas fa-home text-3xl text-green-600"></i>
                <span class="text-lg font-bold mt-1">{{ $propertiesCount ?? 0 }}</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-user-cog text-3xl text-gray-700"></i>
                <span class="text-lg font-bold mt-1">{{ $techniciansCount ?? 0 }}</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-file-alt text-3xl text-blue-700"></i>
                <span class="text-lg font-bold mt-1">{{ $requestsCount ?? 0 }}</span>
            </div>
        </div>
        <!-- Add buttons below the icon grid -->
        <div class="flex justify-center gap-8 mb-4">
            <a href="{{ route('mobile.properties.create') }}" class="flex flex-col items-center">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-2xl mb-1">+</span>
                <span class="text-xs">Add Property</span>
            </a>
            <a href="{{ route('mobile.technicians.create') }}" class="flex flex-col items-center">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-2xl mb-1">+</span>
                <span class="text-xs">Add Technician</span>
            </a>
            <a href="{{ route('mobile.requests.create') }}" class="flex flex-col items-center">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-2xl mb-1">+</span>
                <span class="text-xs">New Request</span>
            </a>
        </div>
        <!-- Pending Requests section follows -->
        <div class="flex justify-between items-center mb-2">
            <div class="text-center font-bold text-lg">Pending Requests</div>
            <a href="{{ route('mobile.manager.all-requests') }}" class="text-sm text-blue-600 hover:text-blue-800">
                View All Requests
            </a>
        </div>
        @if($pendingRequests->count())
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 border-r border-gray-400">Property</th>
                        <th class="p-1 border-r border-gray-400">Priority</th>
                        <th class="p-1 border-r border-gray-400">Date</th>
                        <th class="p-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $req)
                    <tr class="border-b border-gray-400">
                        <td class="p-1 align-top border-r border-gray-400">
                            <span class="font-semibold">{{ $req->property->name }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $req->property->address }}</span>
                        </td>
                        <td class="p-1 align-top border-r border-gray-400 {{ $req->priority == 'high' ? 'bg-red-500 text-white' : ($req->priority == 'low' ? 'bg-yellow-200' : ($req->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-1 align-top border-r border-gray-400">{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</td>
                        <td class="p-1 align-top">
                            <a href="{{ route('mobile.maintenance.show', $req->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-gray-500 py-4">No pending requests.</div>
        @endif
    </div>
</div>
@endsection 