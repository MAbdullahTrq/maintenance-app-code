@extends('mobile.layout')

@section('title', 'Technician Details')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="mb-4 text-center">
            <span class="block font-bold text-xl bg-yellow-300 py-2 rounded">{{ explode(' ', $technician->name)[0] }}</span>
        </div>
        <div class="mb-4 p-4 border rounded bg-gray-50">
            <div class="mb-2 font-semibold">Property name</div>
            <div>{{ $technician->name }}</div>
            <div class="mt-2 font-semibold">Email</div>
            <div class="mb-2">{{ $technician->email }}</div>
            <div class="font-semibold">Phone</div>
            <div class="mb-2">{{ $technician->phone }}</div>
            <div class="my-2 flex justify-center">
                <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=facearea&w=400&h=200&q=80" class="rounded w-48 h-28 object-cover" alt="Technician Photo">
            </div>
        </div>
        <div class="text-lg font-bold text-center mb-2">Maintenance requests</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden mb-4">
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