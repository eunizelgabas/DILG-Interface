<?php

namespace App\Http\Controllers;

use App\Models\Issuances;
use App\Models\Latest;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $latests = Latest::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
         })->with('issuance')->orderBy('created_at', 'desc')->get();

    return view('latest.index',compact('latests' ,'search'));
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

