<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TechnicianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $technicians = User::whereHas('role', function ($q) { $q->where('slug', 'technician'); })->where('invited_by', $user->id)->get();
        $propertiesCount = \App\Models\Property::where('manager_id', $user->id)->count();
        $requestsCount = \App\Models\MaintenanceRequest::whereIn('property_id', \App\Models\Property::where('manager_id', $user->id)->pluck('id'))->count();
        return view('mobile.technicians', [
            'technicians' => $technicians,
            'techniciansCount' => $technicians->count(),
            'propertiesCount' => $propertiesCount,
            'requestsCount' => $requestsCount,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
        ]);
        $technician = new User();
        $technician->name = $request->name;
        $technician->email = $request->email;
        $technician->phone = $request->phone;
        $technician->invited_by = $user->id;
        $technician->password = bcrypt('password'); // Default password, should be changed
        $technician->save();
        // Assign technician role
        $technician->role_id = \App\Models\Role::where('slug', 'technician')->first()->id;
        $technician->save();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
        ]);
        $technician = User::findOrFail($id);
        $technician->name = $request->name;
        $technician->email = $request->email;
        $technician->phone = $request->phone;
        $technician->save();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician updated successfully.');
    }

    public function destroy($id)
    {
        $technician = User::findOrFail($id);
        $technician->delete();
        return redirect()->route('mobile.technicians.index')->with('success', 'Technician deleted successfully.');
    }
}
