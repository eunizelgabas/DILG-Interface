<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Draft;
use App\Models\Issuances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DraftController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $drafts = Draft::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedDrafts = $drafts->map(function ($draft) {
                return [
                    'id' => $draft->id,
                    'responsible_office' => $draft->responsible_office ?? 'N/A',
                    'issuance' => [
                        'id' => $draft->issuance->id,
                        'date' => $draft->issuance->date,
                        'title' => $draft->issuance->title,
                        'reference_no' => $draft->issuance->reference_no,
                        'keyword' => $draft->issuance->keyword,
                        'url_link' => $draft->issuance->url_link,
                        'type' => $draft->issuance->type
                    ],
                ];
            });

            return response()->json(['drafts' => $formattedDrafts]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('draft.index', compact('drafts', 'search'));
        }

        // return view('draft.index', compact('drafts', 'search'));
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
            'type' => 'Draft Issuances', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $draft = Draft::create([
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        $log_entry = Auth::user()->name . " created a Draft Issuances  " . $draft->title . " with the id# " . $draft->id;
        event(new UserLog($log_entry));

        return redirect('/draft_issuances')->with('success', 'Draft Issuance successfully created');
    }

    public function edit(Draft $draft){
        $draft->load([ 'issuance'])->get();
        return view('draft.edit', compact('draft'));
    }

    public function update(Request $request, Draft $draft) {
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
        $issuance = $draft->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $draft->update([
            'responsible_office' => $data['responsible_office']
        ]);


        $log_entry = Auth::user()->name . " udpated a Draft Issuances  " . $draft->title . " with the id# " . $draft->id;
        event(new UserLog($log_entry));

        return redirect('/draft_issuances')->with('success', 'Draft Issuance successfully updated');
    }


    public function destroy(Draft $draft){
        $draft->issuance->delete();

        // Now, delete the presi$draft
        $draft->delete();

        $log_entry = Auth::user()->name . " deleted a Draft Issuances  " . $draft->title . " with the id# " . $draft->id;
        event(new UserLog($log_entry));

        return redirect('/draft_issuances')->with('success','Draft Issuance deleted successfully.');
    }
}

