<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TechnicianController extends Controller
{
    public function create()
    {
        return view('technicians.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
        ]);

        // Generate a random password
        $password = Str::random(10);

        // Create the technician user
        $technician = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($password),
            'property_manager_id' => auth()->id(), // Associate with current property manager
        ]);

        // Assign technician role
        $technicianRole = Role::where('name', 'technician')->first();
        $technician->roles()->attach($technicianRole);

        // TODO: Send email to technician with their credentials

        return redirect()->route('manager.dashboard')
            ->with('success', 'Technician created successfully. Password: ' . $password);
    }
}
