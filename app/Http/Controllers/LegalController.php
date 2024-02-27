<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Legal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LegalController extends Controller
{
    public function index(Request $request){

        $search = $request->input('search');
        $selectedCategory = $request->input('category', 'All');

        $legalsQuery = Legal::query();

        if ($search) {
            $legalsQuery->where(function ($query) use ($search) {
                $query->where('category', 'like', '%' . $search . '%')
                    ->orWhereHas('issuance', function ($issuanceQuery) use ($search) {
                        $issuanceQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('reference_no', 'like', '%' . $search . '%')
                            ->orWhere('keyword', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($selectedCategory !== 'All') {
            $legalsQuery->where('category', $selectedCategory);
        }

        $legals = $legalsQuery->with('issuance')->orderBy('created_at', 'desc');

        $categories = Legal::whereNotNull('category')->pluck('category')->unique();


        if ($request->expectsJson()) {
            $legals = $legalsQuery->get(); // Get all data for JSON API requests
        } else {
            $legals = $legalsQuery->paginate(5); // Paginate for web requests
        }

         if ($request->expectsJson()) {
            // Transform the data to include the foreign key relationship
            $formattedLegals = $legals->map(function ($legal) {
                return [
                    'id' => $legal->id,
                    'category' => $legal->category ?? 'N/A',
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
            'type' => 'Legal Opinions', // Automatically set the type
        ]);

        // Create Latest record associated with the Issuances
        $legal = Legal::create([
            'category' => $data['category'],
            'issuance_id' => $issuance->id,
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
            'category' => 'nullable|string'
        ]);

        $keywords = $data['keyword'];

        // Concatenate keywords as a comma-separated string
        $keywordString = implode(', ', $keywords);

        // Update Issuances record
        $issuance = $legal->issuance; // Assuming Joint model has a relationship to Issuances
        $issuance->update([
            'title' => $data['title'],
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'url_link' => $data['url_link'],
            'keyword' => $keywordString, // Save concatenated keywords
        ]);

        // Update or create Joint record associated with the Issuances
        $legal->update([
            'category' => $data['category'],

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
