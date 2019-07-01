<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Auth;
use App\MonthlyIncome;
use App\FormatDateTime;
use App\Purchase;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    // getting the user's monthly income details along with today's purchases
    
    public function fetchCurrentDetails(Request $request) {

        $user=$request->auth;

        $income=MonthlyIncome::where('user_id', $user->id)
                             ->where('month', date('F'))
                             ->where('year', date('Y'))
                             ->first();

        if(!$income) {

            return response()->json(['data'=>'No income details for the month'],200);
        }

        return response()->json(['data'=>new UserResource($user)], 200);
    }


    public function fetchReports(Request $request, $from, $to) {

        $user=$request->auth;

        $purchases=Purchase::where('user_id', $user->id)
                           ->whereDate('created_at', '>=', $from)
                           ->whereDate('created_at', '<=', $to)
                           ->select('name','amount','created_at')
                           ->get();

        $datetime=new FormatDateTime();

        return response()->json(['data'=>$datetime->formatCreatedAtStringMonth($purchases)], 200);
    }
}
