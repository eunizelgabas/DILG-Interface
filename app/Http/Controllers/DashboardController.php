<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\Issuances;
use App\Models\Joint;
use App\Models\Latest;
use App\Models\Legal;
use App\Models\Memo;
use App\Models\Presidential;
use App\Models\Republic;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $latest = Latest::count();
        $joint = Joint::count();
        $memo = Memo::count();
        $presidential = Presidential::count();
        $draft = Draft::count();
        $republic = Republic::count();
        $legal = Legal::count();
        $user = User::count();

        $issuance = Issuances::orderBy('created_at', 'desc')->paginate(4);
        return view('dashboard', compact( 'issuance','user','latest', 'joint' , 'memo', 'presidential' , 'draft', 'republic', 'legal'));
    }
}
