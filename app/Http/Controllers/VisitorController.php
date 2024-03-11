<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function increment(Request $request)
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        $userIdentifier = $request->input('user_identifier');

        // Check if user identifier has already been counted for today
        $dailyUserCount = Visitor::where('date', $today)
            ->where('user_identifier', $userIdentifier)
            ->first();

        if ($dailyUserCount) {
            // User has already been counted for today
            return response()->json(['message' => 'User already counted for today']);
        } else {
            // Create new entry for today with count 1
            Visitor::create([
                'date' => $today,
                'user_identifier' => $userIdentifier,
                'count' => 1,
            ]);

            return response()->json(['message' => 'Daily user count updated']);
        }
    }

    public function show()
    {

        $today = Carbon::today('Asia/Manila')->toDateString();
        $yesterday = Carbon::yesterday('Asia/Manila')->toDateString();

        // Get the first day and last day of the current month
        $firstDayOfMonth = Carbon::today('Asia/Manila')->startOfMonth();
        $lastDayOfMonth = Carbon::today('Asia/Manila')->endOfMonth();

        // Retrieve user counts for today and yesterday of the current month
        $todayCount = Visitor::whereDate('date', $today)->count();
        $yesterdayCount = Visitor::whereDate('date', $yesterday)->count();

        // Retrieve daily counts for the last 30 days of the current month
       // Retrieve daily counts for the last 30 days including today
        $dailyCounts = Visitor::selectRaw('DATE(date) as date, COUNT(*) as count')
        ->whereBetween('date', [Carbon::today()->subDays(29), $lastDayOfMonth])
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();


        $totalVisitorCount = Visitor::count();
        return view('visits.counter', [
            'todayCount' => $todayCount,
            'yesterdayCount' => $yesterdayCount,
            'dailyCounts' => $dailyCounts,
            'totalVisitorCount' => $totalVisitorCount,

        ]);
    }

}
