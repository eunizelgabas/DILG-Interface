<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresidentialController extends Controller
{
    public function index(){
        return view('presidential.index');
    }
}
