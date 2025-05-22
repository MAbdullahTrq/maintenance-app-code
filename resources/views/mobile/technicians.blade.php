@extends('mobile.layout')

@section('title', 'Technicians')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="font-bold text-lg mb-4">All Technicians</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 border-r border-gray-400">Name</th>
                        <th class="p-1 border-r border-gray-400">Email</th>
                        <th class="p-1">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($technicians as $tech)
                    <tr class="border-b border-gray-400">
                        <td class="p-1 align-top border-r border-gray-400">{{ $tech->name }}</td>
                        <td class="p-1 align-top border-r border-gray-400">{{ $tech->email }}</td>
                        <td class="p-1 align-top">{{ $tech->phone }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 