@extends('mobile.layout')

@section('title', 'Technician Details')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 w-full max-w-4xl mx-auto">
        <div class="mb-4 text-center">
            @if($technician->image)
                <img src="{{ asset('storage/' . $technician->image) }}" class="rounded-full w-24 h-24 object-cover mx-auto mb-2" alt="Profile">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($technician->name) }}&background=eee&color=555&size=96" class="rounded-full w-24 h-24 object-cover mx-auto mb-2" alt="Profile">
            @endif
            <span class="block font-bold text-xl bg-yellow-300 py-2 rounded mt-2">{{ explode(' ', $technician->name)[0] }}</span>
        </div>
        <div class="mb-4 p-4 border rounded bg-gray-50">
            <div class="mb-2 font-semibold">Property name</div>
            <div>{{ $technician->name }}</div>
            <div class="mt-2 font-semibold">Email</div>
            <div class="mb-2">{{ $technician->email }}</div>
            <div class="font-semibold">Phone</div>
            <div class="mb-2">{{ $technician->phone }}</div>
        </div>
        <div class="text-lg font-bold text-center mb-2">Maintenance requests</div>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm border border-gray-400 border-collapse rounded overflow-hidden mb-4">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 border-r border-gray-400">Property</th>
                        <th class="p-1 border-r border-gray-400">Date started &gt;</th>
                        <th class="p-1 border-r border-gray-400">Date completed &gt;</th>
                        <th class="p-1"> </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenanceRequests as $req)
                    <tr class="border-b border-gray-400">
                        <td class="p-1 align-top border-r border-gray-400">
                            <div class="font-semibold">{{ $req->property->name ?? '' }}</div>
                            <div class="text-xs text-blue-700 underline">{{ $req->property->address ?? '' }}</div>
                        </td>
                        <td class="p-1 align-top border-r border-gray-400">{{ $req->started_at ? date('d M, Y H:i', strtotime($req->started_at)) : '-' }}</td>
                        <td class="p-1 align-top border-r border-gray-400">{{ $req->completed_at ? date('d M, Y H:i', strtotime($req->completed_at)) : '-' }}</td>
                        <td class="p-1 align-top">
                            <a href="{{ route('mobile.request.show', $req->id) }}" class="inline-block"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ url('m/at') }}" class="text-blue-700 underline">Back to list</a>
        </div>
    </div>
</div>
@endsection 