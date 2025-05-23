@extends('mobile.layout')

@section('title', 'Technicians')

@section('content')
<div class="flex justify-center">
    <div class="bg-white rounded-xl shadow p-4 max-w-md w-full">
        <div x-data="{ showForm: false }">
            <div class="flex justify-between items-center mb-4">
                <div class="font-bold text-lg">All Technicians</div>
                <button @click="showForm = !showForm" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Add Technician</button>
            </div>
            <form x-show="showForm" method="POST" action="{{ route('mobile.technicians.store') }}" class="mb-4 bg-gray-50 p-3 rounded border" @submit="showForm = false">
                @csrf
                <input type="text" name="name" class="w-full border rounded p-2 mb-2" placeholder="Name" required>
                <input type="email" name="email" class="w-full border rounded p-2 mb-2" placeholder="Email" required>
                <input type="text" name="phone" class="w-full border rounded p-2 mb-2" placeholder="Phone" required>
                <div class="flex gap-2">
                    <button type="submit" class="w-1/2 bg-blue-700 text-white py-2 rounded">Add</button>
                    <button type="button" @click="showForm = false" class="w-1/2 bg-gray-300 text-black py-2 rounded">Cancel</button>
                </div>
            </form>
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
                            <td class="p-1 align-top border-r border-gray-400">
                                <div x-data="{ edit: false }">
                                    <span x-show="!edit">{{ $tech->name }}</span>
                                    <form x-show="edit" method="POST" action="{{ route('mobile.technicians.update', $tech->id) }}" class="flex flex-col gap-1">
                                        @csrf
                                        <input type="text" name="name" class="border rounded p-1 mb-1" value="{{ $tech->name }}" required>
                                        <input type="email" name="email" class="border rounded p-1 mb-1" value="{{ $tech->email }}" required>
                                        <input type="text" name="phone" class="border rounded p-1 mb-1" value="{{ $tech->phone }}" required>
                                        <div class="flex gap-1">
                                            <button type="submit" class="w-1/2 bg-blue-700 text-white py-1 rounded text-xs">Save</button>
                                            <button type="button" @click="edit = false" class="w-1/2 bg-gray-300 text-black py-1 rounded text-xs">Cancel</button>
                                        </div>
                                    </form>
                                    <div class="flex gap-1 mt-1" x-show="!edit">
                                        <button @click="edit = true" class="bg-yellow-400 text-black px-2 py-1 rounded text-xs">Edit</button>
                                        <form method="POST" action="{{ route('mobile.technicians.destroy', $tech->id) }}" onsubmit="return confirm('Are you sure you want to delete this technician?');">
                                            @csrf
                                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td class="p-1 align-top border-r border-gray-400">{{ $tech->email }}</td>
                            <td class="p-1 align-top">{{ $tech->phone }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 