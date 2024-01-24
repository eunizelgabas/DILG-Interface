<?php

namespace App\Http\Controllers;

use App\Models\Issuances;
use Illuminate\Http\Request;

class JointController extends Controller
{
    public function index(){
        return view('joint.index');
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|string',
            'url_link' => 'required|string',
            'keyword' => 'required',
        ]);

        // Create Issuances record
        $issuance = Issuances::create([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $data['keyword'],
            'type' => 'Joint Circulars', // Automatically set the type
        ]);



        // dd($request->all());
        return redirect('/latest_issuance')->with('success', 'Latest Issuance successfully created');
    }
}
