@extends('mobile.layout')

@section('title', 'Dash – Technician')

@section('header-actions')
<a href="#" class="text-sm font-medium">Technician &gt;</a>
@endsection

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full" x-data="{ activeTab: 'assigned', search: '' }">
        <div class="grid grid-cols-3 gap-2 mb-4">
            <button :class="activeTab === 'assigned' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-2 font-bold focus:outline-none" @click="activeTab = 'assigned'">
                <div class="text-xs">Assigned</div>
                <div class="text-2xl">{{ $assignedCount }}</div>
            </button>
            <button :class="activeTab === 'accepted' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-2 font-bold focus:outline-none" @click="activeTab = 'accepted'">
                <div class="text-xs">Accepted</div>
                <div class="text-2xl">{{ $acceptedCount }}</div>
            </button>
            <button :class="activeTab === 'completed' ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-700'" class="text-center rounded py-2 font-bold focus:outline-none" @click="activeTab = 'completed'">
                <div class="text-xs">Completed</div>
                <div class="text-2xl">{{ $completedCount }}</div>
            </button>
        </div>
        <div class="text-center font-bold text-lg mb-2" x-text="activeTab.charAt(0).toUpperCase() + activeTab.slice(1)"></div>
        <div class="mb-2">
            <input type="text" placeholder="Search" class="w-full border rounded p-2 text-xs" x-model="search">
        </div>
        <div class="overflow-x-auto">
            <template x-if="activeTab === 'assigned'">
                <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 border-r border-gray-400">Property</th>
                            <th class="p-1 border-r border-gray-400">Priority &gt;</th>
                            <th class="p-1 border-r border-gray-400">Date &gt;</th>
                            <th class="p-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedRequests as $req)
                        <tr class="border-b border-gray-400" x-show="!search || '{{ strtolower($req->property->name ?? '') }}'.includes(search.toLowerCase()) || '{{ strtolower($req->property->address ?? '') }}'.includes(search.toLowerCase())">
                            <td class="p-1 align-top border-r border-gray-400">
                                <div class="font-semibold">{{ $req->property->name ?? '' }}</div>
                                <div class="text-xs text-blue-700 underline">{{ $req->property->address ?? '' }}</div>
                            </td>
                            <td class="p-1 align-top border-r border-gray-400 {{ strtolower($req->priority) == 'high' ? 'bg-red-500 text-white' : (strtolower($req->priority) == 'low' ? 'bg-yellow-200' : (strtolower($req->priority) == 'medium' ? 'bg-yellow-100' : '')) }}">
                                {{ ucfirst($req->priority) }}
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">
                                {{ $req->created_at ? date('d M, Y H:i', strtotime($req->created_at)) : '-' }}
                            </td>
                            <td class="p-1 align-top">
                                <a href="{{ url('/t/r/'.$req->id) }}" class="inline-block"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </template>
            <template x-if="activeTab === 'accepted'">
                <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 border-r border-gray-400">Property</th>
                            <th class="p-1 border-r border-gray-400">Priority &gt;</th>
                            <th class="p-1 border-r border-gray-400">Date &gt;</th>
                            <th class="p-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acceptedRequests as $req)
                        <tr class="border-b border-gray-400" x-show="!search || '{{ strtolower($req->property->name ?? '') }}'.includes(search.toLowerCase()) || '{{ strtolower($req->property->address ?? '') }}'.includes(search.toLowerCase())">
                            <td class="p-1 align-top border-r border-gray-400">
                                <div class="font-semibold">{{ $req->property->name ?? '' }}</div>
                                <div class="text-xs text-blue-700 underline">{{ $req->property->address ?? '' }}</div>
                            </td>
                            <td class="p-1 align-top border-r border-gray-400 {{ strtolower($req->priority) == 'high' ? 'bg-red-500 text-white' : (strtolower($req->priority) == 'low' ? 'bg-yellow-200' : (strtolower($req->priority) == 'medium' ? 'bg-yellow-100' : '')) }}">
                                {{ ucfirst($req->priority) }}
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">
                                {{ $req->created_at ? date('d M, Y H:i', strtotime($req->created_at)) : '-' }}
                            </td>
                            <td class="p-1 align-top">
                                <a href="{{ url('/t/r/'.$req->id) }}" class="inline-block"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </template>
            <template x-if="activeTab === 'completed'">
                <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                    <thead>
                        <tr class="bg-gray-100 border-b border-gray-400">
                            <th class="p-1 border-r border-gray-400">Property</th>
                            <th class="p-1 border-r border-gray-400">Priority &gt;</th>
                            <th class="p-1 border-r border-gray-400">Date &gt;</th>
                            <th class="p-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedRequests as $req)
                        <tr class="border-b border-gray-400" x-show="!search || '{{ strtolower($req->property->name ?? '') }}'.includes(search.toLowerCase()) || '{{ strtolower($req->property->address ?? '') }}'.includes(search.toLowerCase())">
                            <td class="p-1 align-top border-r border-gray-400">
                                <div class="font-semibold">{{ $req->property->name ?? '' }}</div>
                                <div class="text-xs text-blue-700 underline">{{ $req->property->address ?? '' }}</div>
                            </td>
                            <td class="p-1 align-top border-r border-gray-400 {{ strtolower($req->priority) == 'high' ? 'bg-red-500 text-white' : (strtolower($req->priority) == 'low' ? 'bg-yellow-200' : (strtolower($req->priority) == 'medium' ? 'bg-yellow-100' : '')) }}">
                                {{ ucfirst($req->priority) }}
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">
                                {{ $req->created_at ? date('d M, Y H:i', strtotime($req->created_at)) : '-' }}
                            </td>
                            <td class="p-1 align-top">
                                <a href="{{ url('/t/r/'.$req->id) }}" class="inline-block"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </template>
        </div>
    </div>
</div>
@endsection 