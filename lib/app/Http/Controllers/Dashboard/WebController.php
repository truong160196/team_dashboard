<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebController extends Controller
{

    public function dashboard(Request $request)
    {
        return view('dashboard.index');
    }

    public function server(Request $request)
    {
        return view('server.index');
    }
}
