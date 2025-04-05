<?php

namespace App\Http\Controllers;

use App\Events\NewIssuanceEvent;
use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Republic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepublicController extends Controller
{
    public function indexMobile(Request $request)
    {
        $search = $request->input('search');
        $selectedDate = $request->input('date', 'All');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $republicsQuery = Republic::query();

        if ($search) {
            $republicsQuery->where(function ($query) use ($search) {
                $query->where('date', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%');
            });

            $republics = $republicsQuery->orderBy('id', 'asc')->get();
        } else {
            if ($selectedDate !== 'All') {
                $republicsQuery->where('date', $selectedDate);
            }

            $republics = $republicsQuery->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);
        }

        $formattedRepublics = $republics->map(function ($republic) {
            return [
                "id" => $republic->id,
                "title" => $republic->title,
                "link" => $republic->link,
                "reference" => $republic->reference,
                "date" => $republic->date,
                "download_link" => $republic->download_link,
            ];
        });

        return response()->json([
            'status' => 'success',
            'republics' => $formattedRepublics,
            'pagination' => $search ? null : [
                'current_page' => $republics->currentPage(),
                'per_page' => $republics->perPage(),
                'total' => $republics->total(),
                'last_page' => $republics->lastPage(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
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

    public function edit(Republic $republic)
    {
        $republic->load(['issuance'])->get();
        return view('republic.edit', compact('republic'));
    }

    public function update(Request $request, Republic $republic)
    {
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


    public function destroy(Republic $republic)
    {

        $republic->issuance()->delete(); // Make sure to use the correct relationship method
        $republic->delete();

        $log_entry = Auth::user()->name . " deleted a Republic Act  " . $republic->title . " with the id# " . $republic->id;
        event(new UserLog($log_entry));

        return redirect('/republic_acts')->with('success', 'Republic Act deleted successfully.');
    }

}
