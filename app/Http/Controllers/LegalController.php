<?php

namespace App\Http\Controllers;

use App\Events\NewIssuanceEvent;
use App\Events\UserLog;
use App\Models\Issuances;
use App\Models\Legal;
use App\Models\LegalOpinionPdf;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;


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
                        'download_link' => $opinion['download_link'],
                        'extracted_texts' => $opinion['extracted_texts'],
                    ]
                );

                //AUTO-DOWNLOAD FILE IS WORKING, BUT COMMENTED
                // If a download link exists, download and process PDF
                // if (!empty($opinion['download_link'])) {
                //     try {
                //         Log::info('Downloading from:', ['url' => $opinion['download_link']]);
                //         // $response = Http::timeout(0)
                //         //     ->withOptions(['verify'=>storage_path('cacert.pem')])
                //         //     ->get($opinion['download_link']); // Increased timeout

                //         $response = Http::withOptions([
                //             'timeout' => 480, // Increase the timeout value
                //             'verify' => storage_path('cacert.pem'),
                //         ])
                //         ->get($opinion['download_link']);

                //         if ($response->successful()) {
                //             $pdfContent = $response->body();
                //             // $fileName = 'legal_opinions/' . uniqid() . '.pdf';
                //             // Sanitize title to be file-system friendly
                //             $title = !empty($opinion['title']) ? preg_replace('/[^A-Za-z0-9_-]/', '_', $opinion['title']) : 'legal_opinion';
                //             $fileName = "legal_opinions/{$title}.pdf";
                //             Storage::disk('public')->put($fileName, $pdfContent);
                //             $pdfPath = storage_path("app/public/{$fileName}");
                //         Log::info('Downloaded file:', ['url' => $opinion['download_link']]);
                //         } else {
                //             Log::error('Failed to download PDF', ['url' => $opinion['download_link']]);
                //         }
                //     } catch (\Exception $e) {
                //         Log::error('Error downloading file:', ['message' => $e->getMessage()]);
                //     }
                // }
            }

            return response()->json(['message' => 'Legal opinions stored successfully'], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('An error occurred:', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while processing'], 500);
        }
    }

    // private function extractTextFromImages($pdfPath)
// {
//     $text = '';

    //     try {
//         $pdf = new Pdf($pdfPath);
//         $totalPages = $pdf->getNumberOfPages();

    //         for ($page = 1; $page <= $totalPages; $page++) {
//             $imagePath = storage_path("app/temp_page_{$page}.jpg");

    //             // Convert PDF page to image
//             $pdf->setPage($page)->saveImage($imagePath);

    //             // Extract text from image
//             $ocrText = (new TesseractOCR($imagePath))->run();
//             $text .= $ocrText . "\n";

    //             // Delete image after extraction
//             unlink($imagePath);
//         }
//     } catch (\Exception $e) {
//         Log::error('Error extracting text from PDF:', ['message' => $e->getMessage()]);
//     }

    //     return $text;
// }




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


    public function getLegalOpinionsJson(Request $request)
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

        $legals = $legalsQuery->orderBy('id', 'asc')->get();

        // ✅ Ensure all fields are properly formatted and cleaned
        $formattedLegals = $legals->map(function ($legal) {
            return [
                "id" => intval($legal->id),
                "title" => $this->cleanString((string) ($legal->title ?? 'None')),
                "link" => empty($legal->link) ? "N/A" : trim((string) $legal->link),

                // "link" => filter_var($this->cleanString((string) ($legal->link ?? 'None')), FILTER_SANITIZE_URL),
                "category" => $this->cleanString((string) ($legal->category ?? 'None')),
                "reference" => $this->cleanString((string) ($legal->reference ?? 'None')),
                "date" => $legal->date && \Carbon\Carbon::hasFormat($legal->date, 'Y-m-d')
                    ? \Carbon\Carbon::parse($legal->date)->format('F d, Y')
                    : $this->cleanString((string) ($legal->date ?? 'N/A')),
                "download_link" => empty($legal->download_link) ? "N/A" : $this->cleanString((string) $legal->download_link),
                "extracted_texts" => empty($legal->extracted_texts) ? "N/A" : $this->cleanString((string) $legal->extracted_texts),

            ];
        });

        $jsonResponse = [
            'status' => 'success',
            'legals' => $formattedLegals
        ];

        Log::info('Legal Opinions JSON: ' . json_encode($jsonResponse));

        $finalJson = json_encode($jsonResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Encoding Error: ' . json_last_error_msg());
            return response()->json(['status' => 'error', 'message' => 'Invalid JSON format'], 500);
        }

        return response($finalJson, 200)->header('Content-Type', 'application/json');

        // return response()->json($jsonResponse, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Helper function to remove invalid UTF-8 characters and trim strings.
     */

    private function cleanString($string)
    {
        return trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $string)); // Remove control characters
    }



    //Working but still has an error
    // public function getLegalOpinionsJson(Request $request)
    // {
    //     $search = $request->input('search');
    //     $selectedCategory = $request->input('category', 'All');

    //     $legalsQuery = Legal::query();

    //     if ($search) {
    //         $legalsQuery->where(function ($query) use ($search) {
    //             $query->where('category', 'like', '%' . $search . '%')
    //                 ->orWhere('title', 'like', '%' . $search . '%')
    //                 ->orWhere('reference', 'like', '%' . $search . '%');
    //         });
    //     }

    //     if ($selectedCategory !== 'All') {
    //         $legalsQuery->where('category', $selectedCategory);
    //     }

    //     $legals = $legalsQuery->orderBy('id', 'asc')->get();

    //     // ✅ Ensure all fields are properly formatted
    //     $formattedLegals = $legals->map(function ($legal) {
    //         return [
    //             "id" => intval($legal->id),
    //             "title" => trim((string) ($legal->title ?? 'None')),
    //             "link" => filter_var(trim((string) ($legal->link ?? 'None')), FILTER_SANITIZE_URL), // ✅ Sanitize URLs
    //             "category" => trim((string) ($legal->category ?? 'None')),
    //             "reference" => trim((string) ($legal->reference ?? 'None')),
    //             "date" => $legal->date && \Carbon\Carbon::hasFormat($legal->date, 'Y-m-d')
    //                 ? \Carbon\Carbon::parse($legal->date)->format('F d, Y')
    //                 : (string) ($legal->date ?? 'N/A'),
    //             "download_link" => trim((string) ($legal->download_link ?? "N/A")),
    //             "extracted_texts" => trim((string) ($legal->extracted_texts ?? "N/A"))
    //         ];
    //     });

    //     $jsonResponse = [
    //         'status' => 'success',
    //         'legals' => $formattedLegals
    //     ];

    //     Log::info('Legal Opinions JSON: ' . json_encode($jsonResponse));

    //     // In your controller method
    //     return response()->json($jsonResponse, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    // }


    //baw unsa ni
    // public function getLegalOpinionsJson(Request $request)
    // {
    //     $search = $request->input('search');
    //     $selectedCategory = $request->input('category', 'All');

    //     $legalsQuery = Legal::query();

    //     if ($search) {
    //         $legalsQuery->where(function ($query) use ($search) {
    //             $query->where('category', 'like', '%' . $search . '%')
    //                 ->orWhere('title', 'like', '%' . $search . '%')
    //                 ->orWhere('reference', 'like', '%' . $search . '%');
    //         });
    //     }

    //     if ($selectedCategory !== 'All') {
    //         $legalsQuery->where('category', $selectedCategory);
    //     }

    //     $legals = $legalsQuery->orderBy('id', 'asc')->get();

    //     $formattedLegals = $legals->map(function ($legal) {
    //         return [
    //             "id" => intval($legal->id),
    //             'title' => trim((string) ($legal->title ?? 'None')),
    //             'link' => trim((string) ($legal->link ?? 'None')),
    //             'category' => trim((string) ($legal->category ?? 'None')),
    //             'reference' => trim((string) ($legal->reference ?? 'None')),
    //             "date" => $legal->date && \Carbon\Carbon::hasFormat($legal->date, 'Y-m-d')
    //                 ? \Carbon\Carbon::parse($legal->date)->format('F d, Y')
    //                 : (string) ($legal->date ?? 'N/A'),
    //             "download_link" => (string) ($legal->download_link ?? "N/A"),
    //             "extracted_texts" => (string) ($legal->extracted_texts ?? "N/A")
    //         ];
    //     });



    //     // $formattedLegals = $legals->map(function ($legal) {
    //     //     return [
    //     //         "id" => intval($legal->id),
    //     //         'title' => (string) $legal->title ?? 'None',
    //     //         'link' => (string) $legal->link ?? 'None',
    //     //         'category' => !empty($legal->category) ? (string) $legal->category : "None",
    //     //         'reference' => !empty($legal->reference) ? (string) $legal->reference : "None",
    //     //         // 'category' => (string) $legal->category ?? 'None',
    //     //         // 'reference' => (string) $legal->reference ?? 'None',
    //     //         // "date" => (string) $legal->date ? $legal->date->format('F d, Y') : "N/A",
    //     //         "date" => $legal->date && \Carbon\Carbon::hasFormat($legal->date, 'Y-m-d')
    //     //             ? \Carbon\Carbon::parse($legal->date)->format('F d, Y')
    //     //             : (string) $legal->date,
    //     //         "download_link" => !empty($legal->download_link) ? (string) $legal->download_link : "N/A",
    //     //         "extracted_texts" => !empty($legal->extracted_texts) ? (string) $legal->extracted_texts : "N/A"
    //     //         // "download_link" => (string) $legal->download_link ?? "N/A",
    //     //         // "extracted_texts" => (string) $legal->extracted_texts ?? "N/A",
    //     //     ];
    //     // });

    //     $jsonResponse = [
    //         'status' => 'success',
    //         'legals' => $formattedLegals
    //     ];

    //     $jsonData = json_encode($jsonResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    //     if ($jsonData === false) {
    //         return response()->json(['error' => 'JSON Encoding Failed: ' . json_last_error_msg()], 500);
    //     }


    //     // return response([
    //     //     'status' => 'success',
    //     //     $jsonData,
    //     //     200
    //     // ])
    //     //     ->header('Content-Type', 'application/json; charset=utf-8')
    //     //     ->header('Cache-Control', 'no-cache, must-revalidate');

    //     return response()->json([
    //         'status' => 'success',
    //         'legals' => $formattedLegals
    //     ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
    //         ->header('Content-Type', 'application/json; charset=utf-8')
    //         ->header('Cache-Control', 'no-cache, must-revalidate');
    //     ;


    //     // return response()->json($jsonResponse, 200);
    //     // return response()->json($jsonResponse, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    // }

    // public function getLegalOpinionsJson(Request $request)
    // {
    //     $search = $request->input('search');
    //     $selectedCategory = $request->input('category', 'All');

    //     $legalsQuery = Legal::query();

    //     if ($search) {
    //         $legalsQuery->where(function ($query) use ($search) {
    //             $query->where('category', 'like', '%' . $search . '%')
    //                 ->orWhere('title', 'like', '%' . $search . '%')
    //                 ->orWhere('reference', 'like', '%' . $search . '%');
    //         });
    //     }

    //     if ($selectedCategory !== 'All') {
    //         $legalsQuery->where('category', $selectedCategory);
    //     }

    //     $legals = $legalsQuery->orderBy('id', 'asc')->paginate(50); // 👈 Paginate results (50 per page)

    //     return response()->json($legals, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // }



    // public function getLegalOpinionsJson(Request $request)
    // {
    //     $search = $request->input('search');
    //     $selectedCategory = $request->input('category', 'All');

    //     $legalsQuery = Legal::query();

    //     if ($search) {
    //         $legalsQuery->where(function ($query) use ($search) {
    //             $query->where('category', 'like', '%' . $search . '%')
    //                 ->orWhere('title', 'like', '%' . $search . '%')
    //                 ->orWhere('reference', 'like', '%' . $search . '%');
    //         });
    //     }

    //     if ($selectedCategory !== 'All') {
    //         $legalsQuery->where('category', $selectedCategory);
    //     }

    //     $legals = $legalsQuery->orderBy('id', 'asc')->get();

    //     // ✅ Format data to ensure no JSON errors
    //     $formattedLegals = $legals->map(function ($legal) {
    //         return [
    //             "id" => (int) $legal->id,
    //             'title' => $legal->title ?? 'None',
    //             'link' => $legal->link ?? 'None',
    //             'category' => $legal->category ?? 'None',
    //             'reference' => $legal->reference ?? 'None',
    //             "date" => $legal->date ?? "N/A",
    //             "download_link" => $legal->download_link ?? "N/A",
    //             "extracted_texts" => $legal->extracted_texts ?? "N/A",
    //         ];
    //     });
    //     Log::info('Legal Opinions JSON Size: ' . strlen(json_encode($formattedLegals)));

    //     // return response()->json($formattedLegals, 200);
    //     return response()->json([
    //         'status' => 'success',
    //         'legals' => $formattedLegals
    //     ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // }

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


    //     public function index(Request $request)
// {
//     // if (!auth()->check()) {
//     //     return response()->json(['message' => 'Unauthorized'], 401);
//     // }
//     if ($request->wantsJson()) {
//         $search = $request->input('search');
//         $selectedCategory = $request->input('category', 'All');

    //         $legalsQuery = Legal::query();

    //         if ($search) {
//             $legalsQuery->where(function ($query) use ($search) {
//                 $query->where('category', 'like', '%' . $search . '%')
//                       ->orWhere('title', 'like', '%' . $search . '%')
//                       ->orWhere('reference', 'like', '%' . $search . '%');
//             });
//         }

    //         if ($selectedCategory !== 'All') {
//             $legalsQuery->where('category', $selectedCategory);
//         }

    //         $legals = $legalsQuery->orderBy('id', 'asc')->get();

    //         // ✅ Ensure all columns are included and handle null values
//         $formattedLegals = $legals->map(function ($legal) {
//             return [
//                 'id' => $legal->id,
//                 'title' => $legal->title ?? 'None', // Default for null values
//                 'link' => $legal->link ?? 'N/A',
//                 'category' => $legal->category ?? 'None',
//                 'reference' => $legal->reference ?? 'N/A',
//                 'date' => $legal->date ?? 'N/A',
//             ];
//         });

    //         return response()->json([
//             'legals' => $formattedLegals
//         ]);
//     }

    //     $search = $request->input('search');
//     $selectedCategory = $request->input('category',     'All');

    //     // Update query to only fetch legal opinions
//     $legalsQuery = Legal::query();

    //     if ($search) {
//         $legalsQuery->where(function ($query) use ($search) {
//             $query->where('category', 'like', '%' . $search . '%')
//                 ->orWhere('title', 'like', '%' . $search . '%') // Adjust according to the legal opinion columns
//                 ->orWhere('reference', 'like', '%' . $search . '%');
//         });
//     }

    //     if ($selectedCategory !== 'All') {
//         $legalsQuery->where('category', $selectedCategory);
//     }

    //     $legals = $legalsQuery->orderBy('id', 'asc')->paginate(10); // Paginate for web requests

    //     $categories = Legal::whereNotNull('category')->pluck('category')->unique();

    //     // Return the data for the web view
//     return view('legal.index', compact('legals', 'search', 'categories', 'selectedCategory'));
// }


    //OLD
    // public function index(Request $request){

    //     $search = $request->input('search');
    //     $selectedCategory = $request->input('category', 'All');

    //     $legalsQuery = Legal::query();

    //     if ($search) {
    //         $legalsQuery->where(function ($query) use ($search) {
    //             $query->where('category', 'like', '%' . $search . '%')
    //                 ->orWhereHas('issuance', function ($legalQuery) use ($search) {
    //                     $legalQuery->where('title', 'like', '%' . $search . '%')
    //                         ->orWhere('reference', 'like', '%' . $search . '%')
    //                         ->orWhere('keyword', 'like', '%' . $search . '%');
    //                 });
    //         });
    //     }

    //     if ($selectedCategory !== 'All') {
    //         $legalsQuery->where('category', $selectedCategory);
    //     }

    //     $legals = $legalsQuery->with('issuance')->orderBy('id', 'desc');

    //     $categories = Legal::whereNotNull('category')->pluck('category')->unique();


    //     if ($request->expectsJson()) {
    //         $legals = $legalsQuery->get(); // Get all data for JSON API requests
    //     } else {
    //         $legals = $legalsQuery->paginate(10); // Paginate for web requests
    //     }

    //      if ($request->expectsJson()) {
    //         // Transform the data to include the foreign key relationship
    //         $formattedLegals = $legals->map(function ($legal) {
    //             return [
    //                 'id' => $legal->id,
    //                 'category' => $legal->category ?? 'N/A',
    //                 'responsible_office' => $legal->responsible_office ?? 'N/A',
    //                 'issuance' => [
    //                     'id' => $legal->issuance->id,
    //                     'date' => $legal->issuance->date ?? 'N/A',
    //                     'title' => $legal->issuance->title,
    //                     'reference' => $legal->issuance->reference ?? 'N/A',
    //                     'keyword' => $legal->issuance->keyword,
    //                     'url_link' => $legal->issuance->url_link ?? 'N/A',
    //                     'type' => $legal->issuance->type
    //                 ],
    //             ];
    //         });

    //         return response()->json(['legals' => $formattedLegals]);
    //     } else {
    //         // If the request is from the web view, return a Blade view
    //         return view('legal.index',compact('legals' ,'search', 'categories' ,'selectedCategory'));
    //     }
    // }

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
