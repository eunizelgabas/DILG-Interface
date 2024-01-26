<?php

namespace App\Http\Controllers;

use App\Models\Issuances;
use App\Models\Presidential;
use Illuminate\Http\Request;

class PresidentialController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $presidentials = Presidential::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

        return view('presidential.index', compact('presidentials', 'search'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|date',
            'url_link' => 'required|string',
            'keyword.*' => 'required|string',
            'responsible_office' => 'nullable|string'
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
            'type' => 'Presidential Directives', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $presidential = Presidential::create([
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        return redirect('/presidential_directives')->with('success', 'Presidential Directives successfully created');
    }

    public function edit(Presidential $presidential){
        $presidential->load([ 'issuance'])->get();
        return view('presidential.edit', compact('presidential'));
    }

    public function update(Request $request, Presidential $presidential) {
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|date',
            'url_link' => 'required|string',
            'keyword.*' => 'required|string',
            'responsible_office' => 'nullable|string'
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Update Issuances record
        $issuance = $presidential->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $presidential->update([
            'responsible_office' => $data['responsible_office']
        ]);

        return redirect('/presidential_directives')->with('success', 'Presidential Directives successfully updated');
    }


    public function destroy(Presidential $presidential){
        $presidential->issuance->delete();

        // Now, delete the presi$presidential
        $presidential->delete();


        return redirect('/presidential_directives')->with('Joint Circular deleted successfully.');
    }
}
