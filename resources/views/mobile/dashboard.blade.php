@extends('mobile.layout')

@section('title', 'Dash – Manager')

@section('header-actions')
<a href="#" class="text-sm font-medium">Manager &gt;</a>
@endsection

@section('content')
<div class="text-center mb-2">
    <div class="text-xs bg-green-300 text-black px-2 py-1 rounded inline-block mb-1">Dash – Manager</div>
    <div class="text-xs mb-2">maintainxtra.com/m/dash</div>
</div>
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <div class="font-extrabold text-xl"><span class="text-blue-700">Maintain</span><span class="text-black">Xtra</span></div>
            <a href="#" class="text-xs">Manager &gt;</a>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center mb-4">
            <div>
                <div><i class="fas fa-home text-3xl text-green-600"></i></div>
                <div class="text-lg font-bold">{{ $properties->count() }}</div>
                <div class="text-xs">+</div>
            </div>
            <div>
                <div><i class="fas fa-user-cog text-3xl text-gray-700"></i></div>
                <div class="text-lg font-bold">{{ $technicians->count() }}</div>
                <div class="text-xs">+</div>
            </div>
            <div>
                <div><i class="fas fa-file-alt text-3xl text-gray-700"></i></div>
                <div class="text-lg font-bold">{{ $pendingRequests->count() }}</div>
                <div class="text-xs">+</div>
            </div>
        </div>
        <div class="text-center font-bold text-lg mb-2">Pending Requests</div>
        @if($pendingRequests->count())
            <table class="w-full text-xs border rounded overflow-hidden">
                <thead>
                    <tr class="border-b bg-gray-100">
                        <th class="p-1">Property</th>
                        <th class="p-1">Priority &gt;</th>
                        <th class="p-1">Date &gt;</th>
                        <th class="p-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $req)
                    <tr class="border-b">
                        <td class="p-1 align-top">
                            <span class="font-semibold">{{ $req->property->name }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $req->property->address }}</span>
                        </td>
                        <td class="p-1 align-top {{ $req->priority == 'high' ? 'bg-red-500 text-white' : ($req->priority == 'low' ? 'bg-yellow-200' : 'bg-yellow-100') }}">
                            {{ ucfirst($req->priority) }}
                        </td>
                        <td class="p-1 align-top">{{ \Carbon\Carbon::parse($req->created_at)->format('d M, Y') }}</td>
                        <td class="p-1 align-top"><a href="#"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center text-sm py-4">You have no new Pending Requests</div>
        @endif
    </div>
</div>
@endsection 