<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    /**
     * Display the terms and conditions page.
     */
    public function index()
    {
        return view('terms.index');
    }
}

