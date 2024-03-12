<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $dailyCounts = DB::table(DB::raw('(SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY as date
        FROM (SELECT 0 as a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) as a
        CROSS JOIN (SELECT 0 as a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) as b
        CROSS JOIN (SELECT 0 as a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) as c
        ORDER BY date ASC) AS dates'))
    ->leftJoin('visitors', function ($join) {
        $join->on(DB::raw('DATE(visitors.date)'), '=', 'dates.date');
    })
    ->selectRaw('dates.date as date, COALESCE(COUNT(visitors.id), 0) as count')
    ->whereBetween('dates.date', [Carbon::today()->subDays(29)->toDateString(), $lastDayOfMonth->toDateString()])
    ->whereDate('dates.date', '<=', Carbon::today())
    ->groupBy('dates.date')
    ->orderBy('dates.date', 'asc')
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
