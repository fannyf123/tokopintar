<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PanduanController extends Controller
{
    public function index(): View
    {
        return view('panduan.index');
    }
}
