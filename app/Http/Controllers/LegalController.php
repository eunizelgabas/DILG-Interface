<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LegalController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $legals = Legal::when($search, function ($query) use ($search) {
            $query->where('category', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
         })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

         if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedLegals = $legals->map(function ($legal) {
                return [
                    'id' => $legal->id,
                    'category' => $legal->category,
                    'issuance' => [
                        'id' => $legal->issuance->id,
                        'date' => $legal->issuance->date,
                        'title' => $legal->issuance->title,
                        'reference_no' => $legal->issuance->reference_no,
                        'keyword' => $legal->issuance->keyword,
                        'url_link' => $legal->issuance->url_link,
                    ],
                ];
            });

            return response()->json(['legals' => $formattedLegals]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('legal.index',compact('legals' ,'search'));
        }
    // return view('legal.index',compact('legals' ,'search'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|date',
            'url_link' => 'required|string',
            'category' => 'required|string',
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
            'type' => 'Legal Opinions', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $legal = Legal::create([
            'category' => $data['category'],
            'issuance_id' => $issuance->id,
        ]);

        $log_entry = Auth::user()->name . " created a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));

        return redirect('/legal_opinions')->with('success', 'Legal Opinion successfully created');
    }

    public function edit(Legal $legal){
        $legal->load([ 'issuance'])->get();
        return view('legal.edit', compact('legal'));
    }

    public function update(Request $request, Legal $legal){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
            'date' => 'nullable|date',
            'url_link' => 'required|string',
            'keyword.*' => 'required|string',
            'category' => 'required|string'
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Update Issuances record
        $issuance = $legal->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $legal->update([
            'category' => $data['category'],

        ]);

        $log_entry = Auth::user()->name . " updated a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));

        return redirect('/legal_opinions')->with('success', 'Legal Opinion successfully updated');
    }

    public function destroy(Legal $legal){
        $legal->issuance->delete();

        // Now, delete the legal
        $legal->delete();

        $log_entry = Auth::user()->name . " deleted a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));
        return redirect('/legal_opinions')->with('Joint Circular deleted successfully.');
    }

}
