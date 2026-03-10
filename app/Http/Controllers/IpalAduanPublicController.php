<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IpalAduanPublicController extends Controller
{
    public function index()
    {
        return view('ipal.aduan-form');
    }
}
