<?php

namespace App\Http\Controllers;

use App\Events\NewIssuanceEvent;
use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LegalController extends Controller
{
    public function receiveLegalOpinion(Request $request)
{
    // Log incoming payload
    Log::info('Incoming webhook data:', $request->all());

    try {
        $validatedData = $request->validate([
            'legal_opinions' => 'required|array',
            'legal_opinions.*.title' => 'nullable|string',
            'legal_opinions.*.link' => 'nullable|string',
            'legal_opinions.*.category' => 'nullable|string',
            'legal_opinions.*.reference' => 'required|string',
            'legal_opinions.*.date' => 'nullable|string',
        ]);

        Log::info('Validation successful:', $validatedData);

        foreach ($validatedData['legal_opinions'] as $opinion) {
            Log::info('Processing legal opinion:', $opinion);

            Legal::updateOrCreate(
                ['reference' => $opinion['reference']],
                [
                    'title' => $opinion['title'],
                    'link' => $opinion['link'],
                    'category' => $opinion['category'],
                    'date' => $opinion['date'],
                ]
            );
        }

        return response()->json(['message' => 'Legal opinions stored successfully'], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed:', $e->errors());

        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('An error occurred:', ['message' => $e->getMessage()]);

        return response()->json(['message' => 'An error occurred while processing'], 500);
    }
}

    
//     public function receiveLegalOpinion(Request $request)
// {
//     // Log the entire request payload to see what is being sent
//     Log::info('Incoming webhook data:', $request->all());

//     try {
//         // Validate the incoming array of legal opinions
//         $validatedData = $request->validate([
//             'legal_opinions' => 'required|array',
//             'legal_opinions.*.title' => 'required|string',
//             'legal_opinions.*.link' => 'required|string',
//             'legal_opinions.*.category' => 'nullable|string',
//             'legal_opinions.*.reference' => 'nullable|string',
//             'legal_opinions.*.date' => 'nullable|string',
//         ]);

//         Log::info('Validation successful:', $validatedData);

//         foreach ($validatedData['legal_opinions'] as $opinion) {
//             // Log each opinion being processed
//             Log::info('Processing legal opinion:', $opinion);

//             $legal = Legal::create([
//                 'title' => $opinion['title'],
//                 'link' => $opinion['link'],
//                 'category' => $opinion['category'],
//                 'reference' => $opinion['reference'],
//                 'date' => $opinion['date'],
//             ]);
//         }

//         return response()->json(['message' => 'Legal opinions stored successfully'], 200);
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         // Log validation errors
//         Log::error('Validation failed:', $e->errors());

//         return response()->json(['errors' => $e->errors()], 422);
//     } catch (\Exception $e) {
//         // Log any other exceptions
//         Log::error('An error occurred:', ['message' => $e->getMessage()]);

//         return response()->json(['message' => 'An error occurred while processing'], 500);
//     }
// }

    
//     public function receiveLegalOpinion(Request $request)
// {
//     // Log the incoming request data
//     Log::info('Incoming webhook data:', $request->all());

//     try {
//         // Validate the incoming array of legal opinions
//         $validatedData = $request->validate([
//             'legal_opinions' => 'required|array',
//             'legal_opinions.*.title' => 'required|string',
//             'legal_opinions.*.link' => 'required|string',
//             'legal_opinions.*.category' => 'nullable|string',
//             'legal_opinions.*.reference' => 'nullable|string',
//             'legal_opinions.*.date' => 'nullable|string',
//         ]);

//         Log::info('Validation successful:', $validatedData);

//         foreach ($validatedData['legal_opinions'] as $opinion) {
//             // Log each opinion being processed
//             Log::info('Processing legal opinion:', $opinion);

//             $legal = Legal::create([
//                 'title' => $opinion['title'],
//                 'link' => $opinion['link'],
//                 'category' => $opinion['category'],
//                 'reference' => $opinion['reference'],
//                 'date' => $opinion['date'],
//             ]);
//         }

//         return response()->json(['message' => 'Legal opinions stored successfully'], 200);
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         // Log validation errors
//         Log::error('Validation failed:', $e->errors());

//         return response()->json(['errors' => $e->errors()], 422);
//     } catch (\Exception $e) {
//         // Log any other exceptions
//         Log::error('An error occurred:', ['message' => $e->getMessage()]);

//         return response()->json(['message' => 'An error occurred while processing'], 500);
//     }
// }

    
    public function show()
    {
        // Get all legal opinions with the associated issuance data
        // dd(config('database.connections.dilg_bohol'));  // This will dump the connection config array
        $opinions = Legal::with('issuance')->get();


        return view('legal.index', compact('opinions'));
    }
    
    public function index(Request $request){

        $search = $request->input('search');
        $selectedCategory = $request->input('category', 'All');

        $legalsQuery = Legal::query();

        if ($search) {
            $legalsQuery->where(function ($query) use ($search) {
                $query->where('category', 'like', '%' . $search . '%')
                    ->orWhereHas('issuance', function ($legalQuery) use ($search) {
                        $legalQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('reference_no', 'like', '%' . $search . '%')
                            ->orWhere('keyword', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($selectedCategory !== 'All') {
            $legalsQuery->where('category', $selectedCategory);
        }

        $legals = $legalsQuery->with('issuance')->orderBy('id', 'desc');

        $categories = Legal::whereNotNull('category')->pluck('category')->unique();


        if ($request->expectsJson()) {
            $legals = $legalsQuery->get(); // Get all data for JSON API requests
        } else {
            $legals = $legalsQuery->paginate(10); // Paginate for web requests
        }

         if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedLegals = $legals->map(function ($legal) {
                return [
                    'id' => $legal->id,
                    'category' => $legal->category ?? 'N/A',
                    'responsible_office' => $legal->responsible_office ?? 'N/A',
                    'issuance' => [
                        'id' => $legal->issuance->id,
                        'date' => $legal->issuance->date ?? 'N/A',
                        'title' => $legal->issuance->title,
                        'reference_no' => $legal->issuance->reference_no ?? 'N/A',
                        'keyword' => $legal->issuance->keyword,
                        'url_link' => $legal->issuance->url_link ?? 'N/A',
                        'type' => $legal->issuance->type
                    ],
                ];
            });

            return response()->json(['legals' => $formattedLegals]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('legal.index',compact('legals' ,'search', 'categories' ,'selectedCategory'));
        }
    }

    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'nullable|string',
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
            'reference_no' => $data['reference_no'],
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

    public function edit(Legal $legal){
        $legal->load([ 'issuance'])->get();
        return view('legal.edit', compact('legal'));
    }

    public function update(Request $request, Legal $legal){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'nullable|string',
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
            'reference_no' => $data['reference_no'],
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

    public function destroy(Legal $legal){
        $legal->issuance->delete();

        // Now, delete the legal
        $legal->delete();

        $log_entry = Auth::user()->name . " deleted a Legal Opinion  " . $legal->title . " with the id# " . $legal->id;
        event(new UserLog($log_entry));
        return redirect('/legal_opinions')->with( 'success','Legal Opinion deleted successfully.');
    }

}
