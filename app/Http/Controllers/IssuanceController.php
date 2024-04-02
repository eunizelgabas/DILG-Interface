<?php

namespace App\Http\Controllers;

use App\Events\NewIssuanceEvent;
use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Latest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssuanceController extends Controller
{
    // public function index(Request $request){

    //     $search = $request->input('search');

    //     $latests = Latest::when($search, function ($query) use ($search) {
    //         $query->where('outcome', 'like', '%' . $search . '%')
    //             ->orWhere('category', 'like', '%' . $search . '%')
    //             ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
    //                 $issuanceQuery->where('title', 'like', '%' . $search . '%')
    //                     ->orWhere('reference_no', 'like', '%' . $search . '%')
    //                     ->orWhere('keyword', 'like', '%' . $search . '%');
    //             });
    //      })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

    //      if ($request->expectsJson()) {
    //         // Transform the data to include the foreign key relationship
    //         $formattedLatests = $latests->map(function ($latest) {
    //             return [
    //                 'id' => $latest->id,
    //                 'category' => $latest->category,
    //                 'outcome' => $latest->outcome,
    //                 'issuance' => [
    //                     'id' => $latest->issuance->id,
    //                     'date' => $latest->issuance->date,
    //                     'title' => $latest->issuance->title,
    //                     'reference_no' => $latest->issuance->reference_no,
    //                     'keyword' => $latest->issuance->keyword,
    //                     'url_link' => $latest->issuance->url_link,
    //                 ],
    //             ];
    //         });

    //         return response()->json(['latests' => $formattedLatests]);
    //     } else {
    //         // If the request is from the web view, return a Blade view
    //         return view('latest.index',compact('latests' ,'search'));
    //     }

    // }
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedOutcome = $request->input('outcome');

        $latestsQuery = Latest::query();

        if ($search) {
            $latestsQuery->where(function ($query) use ($search) {
                $query->where('category', 'like', '%' . $search . '%')
                    ->orWhere('outcome', 'like', '%' . $search . '%')
                    ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                        $issuanceQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('reference_no', 'like', '%' . $search . '%')
                            ->orWhere('keyword', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($selectedOutcome && $selectedOutcome !== 'All') {
            // $latestsQuery->where('outcome', 'like', '%' . $selectedOutcome . '%');
            $latestsQuery->where('outcome', $selectedOutcome);
        }



        $latests = $latestsQuery->with('issuance')->orderBy('created_at', 'desc');

        if ($request->expectsJson()) {
            $latests = $latestsQuery->get(); // Get all data for JSON API requests
        } else {
            $latests = $latestsQuery->paginate(10); // Paginate for web requests
        }

        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedLatests = $latests->map(function ($latest) {
                return [
                    'id' => $latest->id,
                    'category' => $latest->category ?? 'N/A',
                    'outcome' => $latest->outcome,
                    'issuance' => [
                        'id' => $latest->issuance->id,
                        'date' => $latest->issuance->date ?? 'N/A',
                        'title' => $latest->issuance->title,
                        'reference_no' => $latest->issuance->reference_no ?? 'N/A',
                        'keyword' => $latest->issuance->keyword,
                        'url_link' => $latest->issuance->url_link ?? 'N/A',
                        'type' => $latest->issuance->type
                    ],

                ];
            });

            return response()->json(['latests' => $formattedLatests]);
        } else {
            // If the request is from the web view, return a Blade view
            $outcomeOptions = [
                "ACCOUNTABLE, TRANSPARENT, PARTICIPATIVE, AND EFFECTIVE LOCAL GOVERNANCE",
                "PEACEFUL, ORDERLY AND SAFE LGUS STRATEGIC PRIORITIES",
                "SOCIALLY PROTECTIVE LGUS",
                "ENVIRONMENT-PROTECTIVE, CLIMATE CHANGE ADAPTIVE AND DISASTER RESILIENT LGUS",
                "BUSINESS-FRIENDLY AND COMPETITIVE LGUS",
                "STRENGTHENING OF INTERNAL GOVERNANCE",
            ];

            return view('latest.index', compact('latests', 'search', 'outcomeOptions', 'selectedOutcome'));
        }
    }

//     public function index(Request $request)
// {
//     $search = $request->input('search');
//     $selectedOutcome = $request->input('outcome');

//     $latestsQuery = Latest::query();

//     if ($search) {
//         $latestsQuery->where(function ($query) use ($search) {
//             $query->where('category', 'like', '%' . $search . '%')
//                 ->orWhere('outcome', 'like', '%' . $search . '%')
//                 ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
//                     $issuanceQuery->where('title', 'like', '%' . $search . '%')
//                         ->orWhere('reference_no', 'like', '%' . $search . '%')
//                         ->orWhere('keyword', 'like', '%' . $search . '%');
//                 });
//         });
//     }

//     if ($selectedOutcome && $selectedOutcome !== 'All') {
//         $latestsQuery->where('outcome', $selectedOutcome);
//     }

//     // Apply pagination if it's a web request
//     if (!$request->expectsJson()) {
//         $latests = $latestsQuery->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

//         // Return a Blade view
//         $outcomeOptions = [
//             "ACCOUNTABLE, TRANSPARENT, PARTICIPATIVE, AND EFFECTIVE LOCAL GOVERNANCE",
//             "PEACEFUL, ORDERLY AND SAFE LGUS STRATEGIC PRIORITIES",
//             "SOCIALLY PROTECTIVE LGUS",
//             "ENVIRONMENT-PROTECTIVE, CLIMATE CHANGE ADAPTIVE AND DISASTER RESILIENT LGUS",
//             "BUSINESS-FRIENDLY AND COMPETITIVE LGUS",
//             "STRENGTHENING OF INTERNAL GOVERNANCE",
//         ];

//         return view('latest.index', compact('latests', 'search', 'outcomeOptions', 'selectedOutcome'));
//     }

//     // For JSON API requests
//     $latests = $latestsQuery->with('issuance')->orderBy('created_at', 'desc')->get();

//     // Transform the data
//     $formattedLatests = $latests->map(function ($latest) {
//         return [
//             'id' => $latest->id,
//             'category' => $latest->category ?? 'N/A',
//             'outcome' => $latest->outcome,
//             'issuance' => [
//                 'id' => $latest->issuance->id,
//                 'date' => $latest->issuance->date,
//                 'title' => $latest->issuance->title,
//                 'reference_no' => $latest->issuance->reference_no,
//                 'keyword' => $latest->issuance->keyword,
//                 'url_link' => $latest->issuance->url_link,
//                 'type' => $latest->issuance->type
//             ],
//         ];
//     });

//     return response()->json(['latests' => $formattedLatests]);
// }


    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'nullable|string',
            'date' => 'nullable|date',
            'url_link' => 'nullable|string',
            'category' => 'nullable|string',
            'outcome' => 'required',
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
            'type' => 'Latest Issuance', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $latest = Latest::create([
            'category' => $data['category'],
            'outcome' => $data['outcome'],
            'issuance_id' => $issuance->id,
        ]);


        // dd($request->all());
        $log_entry = Auth::user()->name . " created a Latest Issuances  " . $latest->title . " with the id# " . $latest->id;
        event(new UserLog($log_entry));

        return redirect('/latest_issuances')->with('success', 'Latest Issuance successfully created');
    }

    public function edit(Latest $latest){
        $latest->load([ 'issuance'])->get();
        return view('latest.edit', compact('latest'));
    }

    public function update(Request $request, Latest $latest){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'nullable|string',
            'date' => 'nullable|date',
            'url_link' => 'nullable|string',
            'keyword.*' => 'required|string',
            'outcome' => 'required|string',
            'category' => 'nullable|string'

        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Update Issuances record
        $issuance = $latest->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $latest->update([
            'category' => $data['category'],
            'outcome' => $data['outcome']
        ]);

        $log_entry = Auth::user()->name . " updated a Latest Issuances  " . $latest->title . " with the id# " . $latest->id;
        event(new UserLog($log_entry));

        return redirect('/latest_issuances')->with('success', 'Latest Issuance successfully updated');
    }

    public function destroy(Latest $latest){
        $latest->issuance->delete();

        // Now, delete the latest
        $latest->delete();

        // $log_entry = Auth::user()->name . " deleted a Latest Issuances  " . $latest->title . " with the id# " . $latest->id;
        // event(new UserLog($log_entry));

        return redirect('/latest_issuances')->with('success','Latest Issuance deleted successfully.');
    }

    public function recent(Request $request)
    {
        // Get recent issuances for today, yesterday, and last 7 days
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $last7Days = Carbon::today()->subDays(7);

        $todayIssuances = Issuances::whereDate('created_at', '=', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $yesterdayIssuances = Issuances::whereDate('created_at', '=', $yesterday)
            ->orderBy('created_at', 'desc')->take(10)
            ->get();

        $last7DaysIssuances = Issuances::whereDate('created_at', '>=', $last7Days)
            ->whereDate('created_at', '<', $yesterday) // Exclude today and yesterday
            ->orderBy('created_at', 'desc')->take(7)
            ->get();

        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedLatests = [
                'today' => $todayIssuances->map(function ($issuance) {
                    return [
                        'id' => $issuance->id,
                        'date' => $issuance->date ?? 'N/A',
                        'title' => $issuance->title,
                        'reference_no' => $issuance->reference_no ?? 'N/A',
                        'keyword' => $issuance->keyword,
                        'url_link' => $issuance->url_link ?? 'N/A',
                        'type' => $issuance->type
                    ];
                }),
                'yesterday' => $yesterdayIssuances->map(function ($issuance) {
                    return [
                        'id' => $issuance->id,
                        'date' => $issuance->date ?? 'N/A',
                        'title' => $issuance->title,
                        'reference_no' => $issuance->reference_no ?? 'N/A',
                        'keyword' => $issuance->keyword,
                        'url_link' => $issuance->url_link ?? 'N/A',
                        'type' => $issuance->type
                    ];
                }),
                'last7Days' => $last7DaysIssuances->map(function ($issuance) {
                    return [
                        'id' => $issuance->id,
                        'date' => $issuance->date ?? 'N/A',
                        'title' => $issuance->title,
                        'reference_no' => $issuance->reference_no ?? 'N/A',
                        'keyword' => $issuance->keyword,
                        'url_link' => $issuance->url_link ?? 'N/A',
                        'type' => $issuance->type
                    ];
                }),
            ];

            return response()->json(['recentIssuances' => $formattedLatests]);
        }
    }

    // public function getNewIssuancesCount(Request $request)
    // {
    //     // Get the count of newly added issuances since the specified time
    //     $newIssuancesCount = Issuances::where('created_at', '>=', $request->input('since_time'))
    //         ->count();

    //     return response()->json(['count' => $newIssuancesCount]);
    // }
}
