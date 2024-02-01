<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Republic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepublicController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $republics = Republic::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);
        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedRepublics = $republics->map(function ($republic) {
                return [
                    'id' => $republic->id,
                    'responsible_office' => $republic->responsible_office,
                    'issuance' => [
                        'id' => $republic->issuance->id,
                        'date' => $republic->issuance->date,
                        'title' => $republic->issuance->title,
                        'reference_no' => $republic->issuance->reference_no,
                        'keyword' => $republic->issuance->keyword,
                        'url_link' => $republic->issuance->url_link,
                    ],
                ];
            });

            return response()->json(['$republics' => $formattedRepublics]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('republic.index', compact('republics', 'search'));
        }

        // return view('republic.index', compact('republics', 'search'));
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
            'type' => 'Republic Acts', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $republic = Republic::create([
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        $log_entry = Auth::user()->name . " created a Republic Act  " . $republic->title . " with the id# " . $republic->id;
        event(new UserLog($log_entry));

        return redirect('/republic_acts')->with('success', 'Republic Act successfully created');
    }

    public function edit(Republic $republic){
        $republic->load([ 'issuance'])->get();
        return view('republic.edit', compact('republic'));
    }

    public function update(Request $request, Republic $republic) {
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
        $issuance = $republic->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $republic->update([
            'responsible_office' => $data['responsible_office']
        ]);

        $log_entry = Auth::user()->name . " updated a Republic Act  " . $republic->title . " with the id# " . $republic->id;
        event(new UserLog($log_entry));

        return redirect('/republic_acts')->with('success', 'Republic Act successfully updated');
    }


    public function destroy(Republic $republic){
        $republic->issuance->delete();

        // Now, delete the republic
        $republic->delete();

        $log_entry = Auth::user()->name . " deleted a Republic Act  " . $republic->title . " with the id# " . $republic->id;
        event(new UserLog($log_entry));

        return redirect('/republic_acts')->with('success','Republic Act deleted successfully.');
    }
}
