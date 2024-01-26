<?php

namespace App\Http\Controllers;

use App\Models\Issuances;
use App\Models\Joint;
use Illuminate\Http\Request;
use PDO;

class JointController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');

        $joints = Joint::when($search, function ($query) use ($search) {
            $query->where('responsible_office', 'like', '%' . $search . '%')
                ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                    $issuanceQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('keyword', 'like', '%' . $search . '%');
                });
        })->with('issuance')->orderBy('created_at', 'desc')->paginate(5);

        return view('joint.index', compact('joints', 'search'));
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
            'type' => 'Joint Circulars', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $joint = Joint::create([
            'responsible_office' => $data['responsible_office'],
            'issuance_id' => $issuance->id,
        ]);

        // dd($request->all());
        return redirect('/joint_circulars')->with('success', 'Latest Issuance successfully created');
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
            'url_link' => 'required|string',
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

        return redirect('/joint_circulars')->with('success', 'Latest Issuance successfully updated');
    }


    public function destroy(Joint $joint){
        $joint->issuance->delete();

        // Now, delete the Joint
        $joint->delete();


        return redirect('/joint_circulars')->with('Joint Circular deleted successfully.');
    }
}
