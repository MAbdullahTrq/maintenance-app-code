@extends('mobile.layout')

@section('title', 'All Requests - Manager')

@section('header-actions')
<a href="{{ route('mobile.manager.dashboard') }}" class="text-sm font-medium">Back to Dashboard</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="text-center font-bold text-lg mb-2">All Requests</div>
        @if($allRequests->count())
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 border-r border-gray-400">Property</th>
                        <th class="p-1 border-r border-gray-400">Priority</th>
                        <th class="p-1 border-r border-gray-400">Date</th>
                        <th class="p-1 border-r border-gray-400">Status</th>
                        <th class="p-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allRequests as $req)
                    <tr class="border-b border-gray-400">
                        <td class="p-1 align-top border-r border-gray-400">
                            <span class="font-semibold">{{ $req->property->name }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $req->property->address }}</span>
                        </td>
                        <td class="p-1 align-top border-r border-gray-400 {{ $req->priority == 'high' ? 'bg-red-500 text-white' : ($req->priority == 'low' ? 'bg-yellow-200' : ($req->priority == 'medium' ? 'bg-yellow-100' : '')) }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-1 align-top border-r border-gray-400">{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</td>
                        <td class="p-1 align-top border-r border-gray-400">{{ ucfirst($req->status) }}</td>
                        <td class="p-1 align-top">
                            <a href="{{ route('mobile.request.show', $req->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-gray-500 py-4">No requests found.</div>
        @endif
    </div>
</div>
@endsection 