@extends('mobile.layout')

@section('title', 'Properties')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div class="font-bold text-lg mb-4">All Properties</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border border-gray-400 border-collapse rounded overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-400">
                        <th class="p-1 border-r border-gray-400">Name</th>
                        <th class="p-1 border-r border-gray-400">Address</th>
                        <th class="p-1">Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($properties as $property)
                    <tr class="border-b border-gray-400">
                        <td class="p-1 align-top border-r border-gray-400">{{ $property->name }}</td>
                        <td class="p-1 align-top border-r border-gray-400">{{ $property->address }}</td>
                        <td class="p-1 align-top">{{ $property->special_instructions }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 