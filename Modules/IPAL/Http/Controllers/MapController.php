<?php

namespace Modules\IPAL\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        return view('ipal::map.index');
    }
}
