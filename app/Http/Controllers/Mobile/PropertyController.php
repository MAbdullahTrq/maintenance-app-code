<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $properties = Property::where('manager_id', $user->id)->get();
        return view('mobile.properties', ['properties' => $properties]);
    }
}
