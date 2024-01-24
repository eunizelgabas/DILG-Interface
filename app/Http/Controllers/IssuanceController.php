<?php

namespace App\Http\Controllers;

use App\Models\Issuances;
use App\Models\Latest;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{
    public function index(){
        $latests = Latest::with('issuance')->orderBy('created_at', 'desc')->get();
        return view('latest.index',compact('latests')
    );
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|date',
            'url_link' => 'required|string',
            'category' => 'required|string',
            'outcome' => 'required',
            'keyword.*' => 'required|string',
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Create Issuances record
        $issuance = Issuances::create([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
            'type' => 'Latest Issuance', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $latest = Latest::create([
            'category' => $data['category'],
            'outcome' => $data['outcome'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        return redirect('/latest_issuances')->with('success', 'Latest Issuance successfully created');
    }

}

