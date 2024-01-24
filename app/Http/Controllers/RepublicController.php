<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RepublicController extends Controller
{
    public function index(){
        return view('republic.index');
    }
}
