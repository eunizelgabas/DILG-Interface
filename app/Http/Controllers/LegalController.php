<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LegalController extends Controller
{
    public function receiveLegalOpinion(Request $request)
    {
        set_time_limit(0);
        Log::info('Incoming webhook data:', $request->all());

        try {
            $validatedData = $request->validate([
                'legal_opinions' => 'required|array',
                'legal_opinions.*.title' => 'nullable|string',
                'legal_opinions.*.link' => 'nullable|string',
                'legal_opinions.*.category' => 'nullable|string',
                'legal_opinions.*.reference' => 'required|string',
                'legal_opinions.*.date' => 'nullable|string',
                'legal_opinions.*.download_link' => 'nullable|string|url',
                'legal_opinions.*.extracted_texts' => 'nullable|string',
            ]);

            foreach ($validatedData['legal_opinions'] as $opinion) {
                Log::info('Processing legal opinion:', $opinion);

                // Store or update Legal Opinion
                $legalOpinion = Legal::updateOrCreate(
                    ['reference' => $opinion['reference']],
                    [
                        'title' => $opinion['title'],
                        'link' => $opinion['link'],
                        'category' => $opinion['category'],
                        'reference' => $opinion['reference'],
                        'date' => $opinion['date'],
                        'type' => 'Legal Opinions',
                        'download_link' => $opinion['download_link'],
                        'extracted_texts' => $opinion['extracted_texts'],
                    ]
                );
            }

            return response()->json(['message' => 'Legal opinions stored successfully'], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('An error occurred:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while processing'], 500);
        }
    }


    public function show($id)
    {
        $opinion = Legal::findOrFail($id);
        return view('legal.show', compact('opinion'));
    }

    public function index(Request $request)
    {
        // Check if request expects JSON (API request)
        if ($request->expectsJson()) {
            return $this->getLegalOpinionsJson($request);
        }

        // Otherwise, return the web view
        return $this->getLegalOpinionsView($request);
    }

    /**
     * Handle API request for legal opinions
     */


    // MAIN PAGINATION METHOD
    public function getLegalOpinionsJson(Request $request)
    {
        $search = $request->input('search');
        $selectedCategory = $request->input('category', 'All');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $legalsQuery = Legal::query();

        if ($search) {
            $legalsQuery->where(function ($query) use ($search) {
                $query->where('category', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%')
                    ->orWhere('date', 'like', '%' . $search . '%')
                    ->orWhere('extracted_texts', 'like', '%' . $search . '%');
            });

            $legals = $legalsQuery->orderBy('id', 'asc')->get();
        } else {
            if ($selectedCategory !== 'All') {
                $legalsQuery->where('category', $selectedCategory);
            }

            $legals = $legalsQuery->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);
        }

        $formattedLegals = $legals->map(function ($legal) {
            return [
                "id" => $legal->id,
                "title" => $legal->title,
                "link" => $legal->link,
                "category" => $legal->category,
                "reference" => $legal->reference,
                "date" => $legal->date,
                "download_link" => $legal->download_link,
                "extracted_texts" => $legal->extracted_texts,
            ];
        });

        return response()->json([
            'status' => 'success',
            'legals' => $formattedLegals,
            'pagination' => $search ? null : [
                'current_page' => $legals->currentPage(),
                'per_page' => $legals->perPage(),
                'total' => $legals->total(),
                'last_page' => $legals->lastPage(),
            ],
        ], 200);
    }

    /**
     * Helper function to remove invalid UTF-8 characters and trim strings.
     */

    private function cleanString($string)
    {
        return trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $string)); // Remove control characters
    }

    private function getLegalOpinionsView(Request $request)
    {
        $search = $request->input('search');
        $selectedCategory = $request->input('category', 'All');

        $legalsQuery = Legal::query();

        if ($search) {
            $legalsQuery->where(function ($query) use ($search) {
                $query->where('category', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%');
            });
        }

        if ($selectedCategory !== 'All') {
            $legalsQuery->where('category', $selectedCategory);
        }

        $legals = $legalsQuery->orderBy('id', 'asc')->paginate(10);
        $categories = Legal::whereNotNull('category')->pluck('category')->unique();

        return view('legal.index', compact('legals', 'search', 'categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'reference' => 'nullable|string',
            'date' => 'nullable|date',
            'url_link' => 'nullable|string',
            'category' => 'nullable|string',
            'keyword.*' => 'required|string',
            'responsible_office' => 'nullable|string'
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Create Issuances record
        $legal = Issuances::create([
            'title' => $data['title'],
            'reference' => $data['reference'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
            'type' => 'Legal Opinions', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $legal = Legal::create([
            'category' => $data['category'],
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $legal->id,
        ]);

        $log_entry = Auth::user()->name . " created a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));


        return redirect('/legal_opinions')->with('success', 'Legal Opinion successfully created');
    }

    public function edit(Legal $legal)
    {
        $legal->load(['issuance'])->get();
        return view('legal.edit', compact('legal'));
    }

    public function update(Request $request, Legal $legal)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'reference' => 'nullable|string',
            'date' => 'nullable|date',
            'url_link' => 'nullable|string',
            'keyword.*' => 'required|string',
            'category' => 'nullable|string',
            'responsible_office' => 'nullable|string'
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Update Issuances record
        $legal = $legal->issuance; // Assuming Joint model has a relationship to Issuances
        $legal->update([
            'title' => $data['title'],
            'reference' => $data['reference'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $legal->update([
            'category' => $data['category'],
            'responsible_office' => $data['responsible_office']

        ]);

        $log_entry = Auth::user()->name . " updated a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));

        return redirect('/legal_opinions')->with('success', 'Legal Opinion successfully updated');
    }

    public function destroy(Legal $legal)
    {
        $legal->issuance->delete();

        // Now, delete the legal
        $legal->delete();

        $log_entry = Auth::user()->name . " deleted a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));
        return redirect('/legal_opinions')->with('success', 'Legal Opinion deleted successfully.');
    }

}
