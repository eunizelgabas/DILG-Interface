<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Presidential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresidentialController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $presidentialsQuery = Presidential::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('id', 'desc');


        if ($request->expectsJson()) {
            $presidentials = $presidentialsQuery->get(); // Get all data for JSON API requests
        } else {
            $presidentials = $presidentialsQuery->paginate(5); // Paginate for web requests
        }

        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedPresidentials = $presidentials->map(function ($presidential) {
                return [
                    'id' => $presidential->id,
                    'responsible_office' => $presidential->responsible_office ?? 'N/A',
                    'issuance' => [
                        'id' => $presidential->issuance->id,
                        'date' => $presidential->issuance->date ?? 'N/A',
                        'title' => $presidential->issuance->title,
                        'reference_no' => $presidential->issuance->reference_no ?? 'N/A',
                        'keyword' => $presidential->issuance->keyword,
                        'url_link' => $presidential->issuance->url_link ?? 'N/A',
                        'type' => $presidential->issuance->type
                    ],
                ];
            });

            return response()->json(['presidentials' => $formattedPresidentials]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('presidential.index', compact('presidentials', 'search'));
        }

        // return view('presidential.index', compact('presidentials', 'search'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'nullable|string',
            'date' => 'nullable|date',
            'url_link' => 'nullable|string',
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
        $log_entry = Auth::user()->name . " created a Presidential Directive  " . $presidential->title . " with the id# " . $presidential->id;
        event(new UserLog($log_entry));

        return redirect('/presidential_directives')->with('success', 'Presidential Directives successfully created');
    }

    public function edit(Presidential $presidential){
        $presidential->load([ 'issuance'])->get();
        return view('presidential.edit', compact('presidential'));
    }

    public function update(Request $request, Presidential $presidential) {
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'nullable|string',
            'date' => 'nullable|date',
            'url_link' => 'nullable|string',
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

        $log_entry = Auth::user()->name . " udpated a Presidential Directive  " . $presidential->title . " with the id# " . $presidential->id;
        event(new UserLog($log_entry));


        return redirect('/presidential_directives')->with('success', 'Presidential Directives successfully updated');
    }


    public function destroy(Presidential $presidential){
        $presidential->issuance->delete();

        // Now, delete the $presidential

        $log_entry = Auth::user()->name . " deleted a Presidential Directive  " . $presidential->title . " with the id# " . $presidential->id;
        event(new UserLog($log_entry));

        $presidential->delete();


        return redirect('/presidential_directives')->with('success','Presidential Directives deleted successfully.');
    }
}
