<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Joint;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedMemos = $memos->map(function ($memo) {
                return [
                    'id' => $memo->id,
                    'responsible_office' => $memo->responsible_office ?? 'N/A',
                    'issuance' => [
                        'id' => $memo->issuance->id,
                        'date' => $memo->issuance->date,
                        'title' => $memo->issuance->title,
                        'reference_no' => $memo->issuance->reference_no,
                        'keyword' => $memo->issuance->keyword,
                        'url_link' => $memo->issuance->url_link,
                    ],
                ];
            });

            return response()->json(['memos' => $formattedMemos]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('memo.index', compact('memos', 'search'));
        }

        // return view('memo.index', compact('memos', 'search'));
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
        $log_entry = Auth::user()->name . " created a Memo Circular  " . $memo->title . " with the id# " . $memo->id;
        event(new UserLog($log_entry));

        return redirect('/memo_circulars')->with('success', 'Memo Circular successfully created');
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

        $log_entry = Auth::user()->name . " updated a Memo Circular  " . $memo->title . " with the id# " . $memo->id;
        event(new UserLog($log_entry));

        return redirect('/memo_circulars')->with('success', 'Latest Issuance successfully updated');
    }


    public function destroy(Memo $memo){
        $memo->issuance->delete();

        // Now, delete the memo

        $memo->delete();
        $log_entry = Auth::user()->name . " deleted a Memo Circular with the id # " . $memo->id;
        event(new UserLog($log_entry));

        return redirect('/memo_circulars')->with('success','Memo Circular deleted successfully.');
    }
}
