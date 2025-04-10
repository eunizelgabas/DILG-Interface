<?php

namespace App\Http\Controllers;

use App\Events\NewIssuanceEvent;
use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Joint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDO;

class JointController extends Controller
{
    public function receiveJointCirculars(Request $request)
    {
        set_time_limit(0);
        Log::info('Incoming webhook data:', $request->all());

        try {
            $validatedData = $request->validate([
                'joint_circulars' => 'required|array',
                'joint_circulars.*.title' => 'nullable|string',
                'joint_circulars.*.link' => 'nullable|string',
                'joint_circulars.*.reference' => 'nullable|string',
                'joint_circulars.*.date' => 'nullable|string',
                'joint_circulars.*.download_link' => 'nullable|string|url',
            ]);

            foreach ($validatedData['joint_circulars'] as $circular) {
                Log::info('Processing joint circulars:', $circular);

                $jointCircular = Joint::updateOrCreate(
                    ['reference' => $circular['reference']],
                    [
                        'title' => $circular['title'],
                        'link' => $circular['link'],
                        'reference' => $circular['reference'],
                        'date' => $circular['date'],
                        'download_link' => $circular['download_link'],
                    ]
                );
            }

            return response()->json(['message' => 'Joint circulars stored successfully'], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('An error occurred:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while processing'], 500);
        }
    }

    public function indexMobile(Request $request)
    {
        $search = $request->input('search');
        $selectedDate = $request->input('date', 'All');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        $jointsQuery = Joint::query();

        if ($search) {
            $jointsQuery->where(function ($query) use ($search) {
                $query->where('date', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('reference', 'like', '%' . $search . '%');
            });

            $joints = $jointsQuery->orderBy('id', 'asc')->get();
        } else {
            if ($selectedDate !== 'All') {
                $jointsQuery->where('date', $selectedDate);
            }

            $joints = $jointsQuery->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);
        }

        $formattedJoints = $joints->map(function ($joint) {
            return [
                "id" => $joint->id,
                "title" => $joint->title,
                "link" => $joint->link,
                "reference" => $joint->reference,
                "date" => $joint->date,
                "download_link" => $joint->download_link,
            ];
        });

        return response()->json([
            'status' => 'success',
            'joints' => $formattedJoints,
            'pagination' => $search ? null : [
                'current_page' => $joints->currentPage(),
                'per_page' => $joints->perPage(),
                'total' => $joints->total(),
                'last_page' => $joints->lastPage(),
            ],
        ], 200);
    }

    // public function index(Request $request)
    // {
    //     $search = $request->input('search');
    //     $selectedDate = $request->input('date', 'All');

    //     $jointsQuery = Joint::query();

    //     if ($search) {
    //         $jointsQuery->where(function ($query) use ($search) {
    //             $query->where('date', 'like', '%' . $search . '%')
    //                 ->orWhere('title', 'like', '%' . $search . '%')
    //                 ->orWhere('reference', 'like', '%' . $search . '%');
    //         });
    //     }

    //     if ($selectedDate !== 'All') {
    //         $jointsQuery->where('date', $selectedDate);
    //     }

    //     $joints = $jointsQuery->orderBy('id', 'asc')->paginate(10);
    //     $dates = Joint::whereNotNull('date')->pluck('date')->unique();

    //     return view('joint.index', compact('joints', 'search', 'dates', 'selectedDate'));
    // }

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
            'type' => 'Joint Circulars', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $joint = Joint::create([
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        $log_entry = Auth::user()->name . " created a Joint Circular  " . $joint->title . " with the id# " . $joint->id;
        event(new UserLog($log_entry));

        return redirect('/joint_circulars')->with('success', 'Joint Circular successfully created');
    }

    public function edit(Joint $joint)
    {
        $joint->load(['issuance'])->get();
        return view('joint.edit', compact('joint'));
    }

    public function update(Request $request, Joint $joint)
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
        $issuance = $joint->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $joint->update([
            'responsible_office' => $data['responsible_office']
        ]);


        $log_entry = Auth::user()->name . " updated a Joint Circular  " . $joint->title . " with the id# " . $joint->id;
        event(new UserLog($log_entry));

        return redirect('/joint_circulars')->with('success', 'Joint Circular successfully updated');
    }


    public function destroy(Joint $joint)
    {
        $joint->issuance->delete();

        // Now, delete the Joint

        $joint->delete();

        $log_entry = Auth::user()->name . " deleted a Joint Circular  " . $joint->title . " with the id# " . $joint->id;
        event(new UserLog($log_entry));

        return redirect('/joint_circulars')->with('success', 'Joint Circular deleted successfully.');
    }
}
