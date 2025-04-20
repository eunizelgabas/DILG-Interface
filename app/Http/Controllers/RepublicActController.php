<?php

namespace App\Http\Controllers;

use App\Models\Republic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RepublicActController extends Controller
{
    public function receiveRepublicAct(Request $request)
    {
        set_time_limit(0);
        Log::info('Incoming webhook data:', $request->all());

        try {
            $validatedData = $request->validate([
                'republic_acts' => 'required|array',
                'republic_acts.*.title' => 'nullable|string',
                'republic_acts.*.link' => 'nullable|string',
                'republic_acts.*.reference' => 'required|string',
                'republic_acts.*.date' => 'nullable|string',
                'republic_acts.*.download_link' => 'nullable|string|url',
            ]);

            foreach ($validatedData['republic_acts'] as $act) {
                Log::info('Processing republic act:', $act);

                $republicAct = Republic::updateOrCreate(
                    ['reference' => $act['reference']],
                    [
                        'title' => $act['title'],
                        'link' => $act['link'],
                        'reference' => $act['reference'],
                        'date' => $act['date'],
                        'type' => 'Republic Acts',
                        'download_link' => $act['download_link'],
                    ]
                );
            }

            return response()->json(['message' => 'Republic acts stored successfully'], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('An error occurred:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while processing'], 500);
        }
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedDate = $request->input('date', 'All');

        $republicsQuery = Republic::query();

        if ($search) {
            $republicsQuery->where(function ($query) use ($search) {
                $query->where('date', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%');
            });
        }

        if ($selectedDate !== 'All') {
            $republicsQuery->where('date', $selectedDate);
        }

        $republics = $republicsQuery->orderBy('id', 'asc')->paginate(10);
        $dates = Republic::whereNotNull('date')->pluck('date')->unique();

        return view('republic.index', compact('republics', 'search', 'dates', 'selectedDate'));
    }
}
