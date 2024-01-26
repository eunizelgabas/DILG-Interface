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
            $query->where('outcome', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
         })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

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

    public function edit(Latest $latest){
        $latest->load([ 'issuance'])->get();
        return view('latest.edit', compact('latest'));
    }

    public function update(Request $request, Latest $latest){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|date',
            'url_link' => 'required|string',
            'keyword.*' => 'required|string',
            'outcome' => 'required|string',
            'category' => 'required|string'
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Update Issuances record
        $issuance = $latest->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $latest->update([
            'category' => $data['category'],
            'outcome' => $data['outcome']
        ]);

        return redirect('/latest_issuances')->with('success', 'Latest Issuance successfully updated');
    }

    public function destroy(Latest $latest){
        $latest->issuance->delete();

        // Now, delete the latest
        $latest->delete();


        return redirect('/latest_issuances')->with('Joint Circular deleted successfully.');
    }

}

