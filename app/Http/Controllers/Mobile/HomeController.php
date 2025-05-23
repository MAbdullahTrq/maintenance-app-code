<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // If you want to pass data, fetch it here (e.g., stats, features, etc.)
        return view('mobile.home');
    }
}
