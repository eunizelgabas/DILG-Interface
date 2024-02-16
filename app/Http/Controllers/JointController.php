<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Joint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;

class JointController extends Controller
{
    // public function index(Request $request){

    //     $search = $request->input('search');

    //     $joints = Joint::when($search, function ($query) use ($search) {
    //         $query->where('responsible_office', 'like', '%' . $search . '%')
    //             ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
    //                 $issuanceQuery->where('title', 'like', '%' . $search . '%')
    //                     ->orWhere('reference_no', 'like', '%' . $search . '%')
    //                     ->orWhere('keyword', 'like', '%' . $search . '%');
    //             });
    //     })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

    //     if ($request->expectsJson()) {
    //         // Transform the data to include the foreign key relationship
    //         $formattedJoints = $joints->map(function ($joint) {
    //             return [
    //                 'id' => $joint->id,
    //                 'responsible_office' => $joint->responsible_office ?? 'N/A',
    //                 'issuance' => [
    //                     'id' => $joint->issuance->id,
    //                     'date' => $joint->issuance->date,
    //                     'title' => $joint->issuance->title,
    //                     'reference_no' => $joint->issuance->reference_no,
    //                     'keyword' => $joint->issuance->keyword,
    //                     'url_link' => $joint->issuance->url_link,
    //                     'type' => $joint->issuance->type
    //                 ],
    //             ];
    //         });

    //         return response()->json(['joints' => $formattedJoints]);
    //     } else {
    //         // If the request is from the web view, return a Blade view
    //         return view('joint.index', compact('joints', 'search'));
    //     }

    //     // return view('joint.index', compact('joints', 'search'));
    // }
    public function index(Request $request){

        $search = $request->input('search');

        $jointsQuery = Joint::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('created_at', 'desc');

        // Check if the request is from a mobile device
        if ($request->header('User-Agent') === 'Mobile') {
            $joints = $jointsQuery->get(); // Return all data for mobile
        } else {
            $joints = $jointsQuery->paginate(5); // Paginate for web requests
        }

        if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedJoints = $joints->map(function ($joint) {
                return [
                    'id' => $joint->id,
                    'responsible_office' => $joint->responsible_office ?? 'N/A',
                    'issuance' => [
                        'id' => $joint->issuance->id,
                        'date' => $joint->issuance->date,
                        'title' => $joint->issuance->title,
                        'reference_no' => $joint->issuance->reference_no,
                        'keyword' => $joint->issuance->keyword,
                        'url_link' => $joint->issuance->url_link,
                        'type' => $joint->issuance->type
                    ],
                ];
            });

            return response()->json(['joints' => $formattedJoints]);
        } else {
            // If the request is from the web view, return a Blade view
            return view('joint.index', compact('joints', 'search'));
        }

        // return view('joint.index', compact('joints', 'search'));
    }


    public function store(Request $request){
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
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

    public function edit(Joint $joint){
        $joint->load([ 'issuance'])->get();
        return view('joint.edit', compact('joint'));
    }

    public function update(Request $request, Joint $joint) {
        $data = $request->validate([
            'title' => 'required|string',
            'reference_no' => 'required|string',
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


    public function destroy(Joint $joint){
        $joint->issuance->delete();

        // Now, delete the Joint

        $joint->delete();

        $log_entry = Auth::user()->name . " deleted a Joint Circular  " . $joint->title . " with the id# " . $joint->id;
        event(new UserLog($log_entry));

        return redirect('/joint_circulars')->with('success','Joint Circular deleted successfully.');
    }
}
