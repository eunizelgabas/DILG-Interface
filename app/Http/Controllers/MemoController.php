<?php

namespace App\Http\Controllers;

use App\Models\Issuances;
use App\Models\Joint;
use App\Models\Memo;
use Illuminate\Http\Request;

class MemoController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $memos = Memo::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

        return view('memo.index', compact('memos', 'search'));
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
            'type' => 'Memo Circulars', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $memo = Memo::create([
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        return redirect('/memo_circulars')->with('success', 'Latest Issuance successfully created');
    }

    public function edit(Memo $memo){
        $memo->load([ 'issuance'])->get();
        return view('memo.edit', compact('memo'));
    }

    public function update(Request $request, Memo $memo) {
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
        $issuance = $memo->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $memo->update([
            'responsible_office' => $data['responsible_office']
        ]);

        return redirect('/memo_circulars')->with('success', 'Latest Issuance successfully updated');
    }


    public function destroy(Memo $memo){
        $memo->issuance->delete();

        // Now, delete the memo
        $memo->delete();


        return redirect('/memo_circulars')->with('Joint Circular deleted successfully.');
    }
}
