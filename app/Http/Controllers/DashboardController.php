<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Auth;
use App\MonthlyIncome;
use App\FormatDateTime;
use App\Purchase;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $request;

    public function __construct(Request $request) {
        
        $this->request=$request;
    }

    // getting the user's monthly income details along with today's purchases
    
    public function fetchCurrentDetails() {

        $user=$this->request->auth;

        $income=MonthlyIncome::where('user_id', $user->id)
                             ->where('month', date('F'))
                             ->where('year', date('Y'))
                             ->first();

        if(!$income) {

            return response()->json(['data'=>'No income details for the month'],200);
        }

        return response()->json(['data'=>new UserResource($user)], 200);
    }


    public function fetchReports($from, $to) {

        $user=$this->request->auth;

        $purchases=Purchase::where('user_id', $user->id)
                           ->whereDate('created_at', '>=', $from)
                           ->whereDate('created_at', '<=', $to)
                           ->select('name','amount','created_at')
                           ->get();

        $datetime=new FormatDateTime();

        return response()->json(['data'=>$datetime->formatCreatedAtStringMonth($purchases)], 200);
    }


    public function overview() {

        $user=$this->request->auth;

        $incomeData=DB::select('select SUM(original_amount) as totalIncome, SUM(final_amount) as remIncome from monthly_income where user_id=?', [$user->id])[0];

        $daySignedUp=strtotime($user->created_at);

        $now=strtotime(date('Y-m-d h:i:s'));

        $timeSinceSignup=$now - $daySignedUp;

        $day=60 * 60 * 24;

        $daysSinceSignup=round($timeSinceSignup/$day);

        $incomeData->amountSpent=$incomeData->totalIncome - $incomeData->remIncome;

        $avgSpend=round($incomeData->amountSpent/$daysSinceSignup);

        return response()->json(['incomeData'=>$incomeData, 'avgSpend'=>$avgSpend, 'frequentItems'=>$this->getMostBoughtItems($user->id), 'dateJoined'=>$this->formatdDate($user->getFirstPurchaseDate())], 200);
    }


    public function getMostBoughtItems($id) {

        $frequentItems=Purchase::where('user_id',$id)
                               ->select('name', DB::raw('SUM(amount) as total'))
                               ->groupBy('name')
                               ->orderBy('total','desc')
                               ->limit(3)
                               ->get();

        return $frequentItems;
    }


    public function formatdDate($obj) {

        $months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $joinedDateNum=strtotime($obj->created_at);

        $month=$months[(int)date('m', $joinedDateNum) - 1];

        return date('D', $joinedDateNum).', '.$month.' '.date('d', $joinedDateNum).', '.date('Y', $joinedDateNum);
    }
}
