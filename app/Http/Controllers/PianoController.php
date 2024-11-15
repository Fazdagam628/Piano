<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PianoController extends Controller
{
    //
    public function index(): View
    {
        return view("piano.index");
    }
}
