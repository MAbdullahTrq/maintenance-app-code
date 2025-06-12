@extends('mobile.layout')

@section('title', 'Technician Details')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-2 md:p-3 lg:p-4 w-full max-w-6xl mx-auto">
        <div class="mb-2 flex items-center">
            <a href="#" onclick="window.history.back(); return false;" class="mr-2 text-blue-700 hover:underline flex items-center"><i class="fas fa-arrow-left mr-1"></i> Back</a>
        </div>
        <div class="mb-4 md:mb-6 text-center">
            @if($technician->image)
                <img src="{{ asset('storage/' . $technician->image) }}" class="rounded-full w-20 h-20 md:w-28 md:h-28 lg:w-32 lg:h-32 object-cover mx-auto mb-2 md:mb-4" alt="Profile">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($technician->name) }}&background=eee&color=555&size=128" class="rounded-full w-20 h-20 md:w-28 md:h-28 lg:w-32 lg:h-32 object-cover mx-auto mb-2 md:mb-4" alt="Profile">
            @endif
            <span class="block font-bold text-lg md:text-xl lg:text-2xl bg-yellow-300 py-2 md:py-3 lg:py-4 rounded mt-2">{{ explode(' ', $technician->name)[0] }}</span>
        </div>
        <div class="mb-4 md:mb-6 p-3 md:p-4 lg:p-6 border rounded bg-gray-50">
            <div class="mb-3 md:mb-4">
                <div class="font-semibold text-sm md:text-base">Technician name</div>
                <div class="text-sm md:text-base lg:text-lg">{{ $technician->name }}</div>
            </div>
            <div class="mb-3 md:mb-4">
                <div class="font-semibold text-sm md:text-base">Email</div>
                <div class="text-sm md:text-base lg:text-lg">{{ $technician->email }}</div>
            </div>
            <div class="mb-2">
                <div class="font-semibold text-sm md:text-base">Phone</div>
                <div class="text-sm md:text-base lg:text-lg">{{ $technician->phone }}</div>
            </div>
        </div>
        <div class="text-lg md:text-xl lg:text-2xl font-bold text-center mb-2 md:mb-4">Maintenance requests</div>
        <div class="overflow-x-auto w-full">
            <table class="min-w-full text-xs md:text-sm lg:text-base border border-gray-400 border-collapse rounded overflow-hidden mb-4">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-left">Property</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date started</th>
                        <th class="p-2 md:p-3 lg:p-4 border-r border-gray-400 text-center">Date completed</th>
                        <th class="p-2 md:p-3 lg:p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maintenanceRequests as $req)
                    <tr class="border-b border-gray-400 hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('mobile.request.show', $req->id) }}'">
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400">
                            <div class="font-semibold">{{ $req->property->name ?? '' }}</div>
                            <div class="text-xs md:text-sm text-blue-700 underline">{{ $req->property->address ?? '' }}</div>
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            {{ $req->started_at ? date('d M, Y H:i', strtotime($req->started_at)) : '-' }}
                        </td>
                        <td class="p-2 md:p-3 lg:p-4 align-top border-r border-gray-400 text-center">
                            {{ $req->completed_at ? date('d M, Y H:i', strtotime($req->completed_at)) : '-' }}
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
        <div class="mt-4 md:mt-6 text-center">
            <a href="{{ url('m/at') }}" class="text-blue-700 underline text-sm md:text-base hover:text-blue-900">Back to list</a>
        </div>
    </div>
</div>
@endsection 