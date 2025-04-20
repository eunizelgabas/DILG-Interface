<?php

namespace App\Http\Controllers;

use App\Events\NewIssuanceEvent;
use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Presidential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PresidentialController extends Controller
{
    public function receivePresidentialDirectives(Request $request)
    {
        set_time_limit(0);
        Log::info('Incoming webhook data:', $request->all());

        try {
            $validatedData = $request->validate([
                'presidential_directives' => 'required|array',
                'presidential_directives.*.title' => 'nullable|string',
                'presidential_directives.*.link' => 'nullable|string',
                'presidential_directives.*.reference' => 'required|string',
                'presidential_directives.*.date' => 'nullable|string',
                'presidential_directives.*.download_link' => 'nullable|string|url',
            ]);

            foreach ($validatedData['presidential_directives'] as $directive) {
                Log::info('Processing presidential directives:', $directive);

                $presidentialDirective = Presidential::updateOrCreate(
                    ['reference' => $directive['reference']],
                    [
                        'title' => $directive['title'],
                        'link' => $directive['link'],
                        'reference' => $directive['reference'],
                        'date' => $directive['date'],
                        'type' => 'Presidential Directives',
                        'download_link' => $directive['download_link'],
                    ]
                );
            }

            return response()->json(['message' => 'Presidential directivess stored successfully'], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('An error occurred:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while processing'], 500);
        }
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedDate = $request->input('date', 'All');

        $presidentialsQuery = Presidential::query();

        if ($search) {
            $presidentialsQuery->where(function ($query) use ($search) {
                $query->where('date', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%');
            });
        }

        if ($selectedDate !== 'All') {
            $presidentialsQuery->where('date', $selectedDate);
        }

        $presidentials = $presidentialsQuery->orderBy('id', 'asc')->paginate(10);
        $dates = Presidential::whereNotNull('date')->pluck('date')->unique();

        return view('presidential.index', compact('presidentials', 'search', 'dates', 'selectedDate'));
    }

    public function indexMobile(Request $request)
    {
        $search = $request->input('search');
        $selectedDate = $request->input('date', 'All');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $presidentialsQuery = Presidential::query();

        if ($search) {
            $presidentialsQuery->where(function ($query) use ($search) {
                $query->where('date', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%');
            });

            $presidentials = $presidentialsQuery->orderBy('id', 'asc')->get();
        } else {
            if ($selectedDate !== 'All') {
                $presidentialsQuery->where('date', $selectedDate);
            }

            $presidentials = $presidentialsQuery->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);
        }

        $formattedPresidentials = $presidentials->map(function ($presidential) {
            return [
                "id" => $presidential->id,
                "title" => $presidential->title,
                "link" => $presidential->link,
                "reference" => $presidential->reference,
                "date" => $presidential->date,
                "download_link" => $presidential->download_link,
            ];
        });

        return response()->json([
            'status' => 'success',
            'presidentials' => $formattedPresidentials,
            'pagination' => $search ? null : [
                'current_page' => $presidentials->currentPage(),
                'per_page' => $presidentials->perPage(),
                'total' => $presidentials->total(),
                'last_page' => $presidentials->lastPage(),
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

    public function edit(Presidential $presidential)
    {
        $presidential->load(['issuance'])->get();
        return view('presidential.edit', compact('presidential'));
    }

    public function update(Request $request, Presidential $presidential)
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


    public function destroy(Presidential $presidential)
    {
        $presidential->issuance->delete();

        // Now, delete the $presidential

        $log_entry = Auth::user()->name . " deleted a Presidential Directive  " . $presidential->title . " with the id# " . $presidential->id;
        event(new UserLog($log_entry));

        $presidential->delete();


        return redirect('/presidential_directives')->with('success', 'Presidential Directives deleted successfully.');
    }
}
