@extends('mobile.layout')

@section('title', 'Dash – ' . (Auth::user()->isPropertyManager() ? 'Manager' : 'Team Assistant'))

@section('header-actions')
@if(Auth::user()->isPropertyManager())
<a href="#" class="text-sm font-medium">Manager &gt;</a>
@elseif(Auth::user()->hasTeamMemberRole())
<a href="#" class="text-sm font-medium">Team Assistant &gt;</a>
@endif
@endsection

@section('content')
@if(isset($hasActiveSubscription) && !$hasActiveSubscription)
    <div class="mb-6 mx-4">
        <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl shadow-lg p-6 border-2 border-orange-300">
            <div class="text-center">
                <div class="mb-4">
                    <div class="text-4xl mb-2">⚠️</div>
                    <h2 class="text-xl md:text-2xl font-bold mb-2">Subscription Required</h2>
                    <p class="text-lg md:text-xl font-medium mb-4">You need an active subscription to manage properties and maintenance requests.</p>
                </div>
                <div class="space-y-3">
                    <a href="/m/subscription/plans" class="block w-full bg-white text-orange-600 font-bold py-4 px-6 rounded-lg text-lg md:text-xl hover:bg-gray-100 transition duration-200 shadow-md">
                        🚀 Subscribe Now - Get Started!
                    </a>
                    <div class="text-sm opacity-90">
                        <p>✅ Unlimited Properties • ✅ Unlimited Requests • ✅ Full Management Access</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- Pending Requests card below -->
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-2">
            <div class="text-center font-bold text-lg md:text-xl lg:text-2xl">Pending Requests</div>
            <a href="{{ route('mobile.manager.all-requests') }}" class="text-sm md:text-base text-blue-600 hover:text-blue-800">
                View All Requests
            </a>
        </div>
        @if($pendingRequests->count())
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden table-fixed">
                <colgroup>
                    <col class="w-1/5">
                    <col class="w-2/5">
                    <col class="w-1/6">
                    <col class="w-1/6">
                    <col class="w-1/12">
                </colgroup>
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Request</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Priority</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date</th>
                        <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $req)
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('mobile.request.show', $req->id) }}'">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <span class="font-semibold">{{ Str::limit($req->property->name, 15) }}</span><br>
                            <span class="text-gray-500 text-xs md:text-sm">
                                <span class="block md:hidden lg:hidden">{{ Str::limit($req->property->address, 15) }}</span>
                                <span class="hidden md:block lg:block">{{ Str::limit($req->property->address, 30) }}</span>
                            </span>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <span class="font-bold text-black">{{ Str::limit($req->title, 25) }}</span><br>
                            <span class="text-gray-700 text-xs md:text-sm">{{ Str::limit($req->description, 25) }}</span>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center {{ $req->priority == 'high' ? 'bg-red-200' : ($req->priority == 'low' ? 'bg-blue-200' : ($req->priority == 'medium' ? 'bg-yellow-200' : '')) }}" style="{{ $req->priority == 'high' ? 'background-color: #fecaca;' : ($req->priority == 'low' ? 'background-color: #bfdbfe;' : ($req->priority == 'medium' ? 'background-color: #fde68a;' : '')) }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            <div>{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top text-center">
                            <a href="{{ route('mobile.request.show', $req->id) }}" class="text-blue-600 hover:text-blue-800 text-lg md:text-xl" onclick="event.stopPropagation();">
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