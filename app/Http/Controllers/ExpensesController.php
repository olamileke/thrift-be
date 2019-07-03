<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\FormatDateTime;
use App\MonthlyIncome;

class ExpensesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }


    public function add(Request $request) {

        $this->validate($request, [
            'name'=>'required',
            'amount'=>'required'
        ]);

        $user=$request->auth;

        $income=MonthlyIncome::where('user_id', $user->id)
                             ->where('month', date('F'))
                             ->where('year', date('Y'))
                             ->first();


        $purchase=Purchase::where('user_id', $user->id)
                          ->where('monthlyincome_id', $income->id)
                          ->where('name',$request->input('name'))
                          ->whereDate('created_at','=',date('Y-m-d'))                          
                          ->first();

        if($purchase) {

            $purchase->amount=$purchase->amount + $request->input('amount');
            $purchase->created_at=date('Y-m-d h:i:s');
            $purchase->save();
        }                           
        else {

            Purchase::create([
                'user_id'=>$user->id,
                'monthlyincome_id'=>$income->id,
                'name'=>$request->input('name'),
                'amount'=>$request->input('amount')
            ]);
        }   
                            
        $income->final_amount=$income->final_amount - $request->input('amount');
        $income->save();

        return response()->json(['data'=>'Expense Record created successfully'],200);
    }


    // returning all expenses on a particular day

    public function fetchDaily(Request $request, $day) {

        $user=$request->auth;

        $purchases=Purchase::where('user_id', $user->id)
                           ->whereDate('created_at', $day)
                           ->get();

        $datetime=new FormatDateTime();

        return response()->json(['data'=>$datetime->formatCreatedAtString($purchases)],200);
    }


    // returning all expenses in a particular month

    public function fetchMonthly($month, $year, Request $request) {

        $user=$request->auth;

        $purchases=Purchase::where('user_id', $user->id)
                           ->whereMonth('created_at','=',$month)
                           ->whereYear('created_at','=',$year)
                           ->get();

        $datetime=new FormatDateTime();

        $monthIncome=$purchases[0]->monthlyincome;
        $savings=$monthIncome->savings;

        return response()->json(['purchases'=>$datetime->formatCreatedAtStringMonth($purchases), 'monthIncome'=>$monthIncome, 'savings'=>$savings],200);
    }


    // returning the amount spents on each day in the dates sent from the front end

    public function singlePeriodAnalysis(Request $request, $from, $to) {

        $user=$request->auth;

        $purchases=Purchase::where('user_id', $user->id)
                           ->select('created_at', 'amount')
                           ->whereDate('created_at', '>=', $from)
                           ->whereDate('created_at', '<=', $to)
                           ->get();

        return response()->json(['data'=>$this->getData($this->getDays($from,$to), $purchases)], 200);
    }


    // getting each day in the dates sent from the front end

    public function getDays($from, $to) {

        $days=[];

        $initialDateNum=strtotime($from);
        $finalDateNum=strtotime($to);
        $numDays=($finalDateNum - $initialDateNum)/86400;

        for($i=$numDays; $i >= 0; $i--) {

            $dateNum=$finalDateNum - (86400 * $i);

            $year=date('Y', $dateNum);
            $month=date('m', $dateNum);
            $date=date('d', $dateNum);

            $datestr=$year.'-'.$month.'-'.$date;

            array_push($days, $datestr);
        }

        return $days;
    }


    // getting the amount spent each day in the date sent from the front

    public function getData($days, $purchases) {

        $data=[];
        $amounts=[];

        foreach($days as $day) {

            $amount=0;

            foreach($purchases as $purchase) {

                if($day == explode(' ', $purchase->created_at)[0]) {

                    $amount+=$purchase->amount;
                }
            }

            array_push($amounts, $amount);
        }

        array_push($data, $days);
        array_push($data, $amounts);

        return $data;
    }


    public function comparison(Request $request, $period1Start, $period1End, $period2Start, $period2End) {

        $user=$request->auth;

        $period1Purchases=Purchase::where('user_id', $user->id)
                                  ->select('amount', 'created_at')
                                  ->whereDate('created_at', '>=', $period1Start)
                                  ->whereDate('created_at', '<=', $period1End)
                                  ->get();

         $period2Purchases=Purchase::where('user_id', $user->id)
                                  ->select('amount', 'created_at')
                                  ->whereDate('created_at', '>=', $period2Start)
                                  ->whereDate('created_at', '<=', $period2End)
                                  ->get();

        return response()->json(['data'=>['period1'=>
                                            $this->getData($this->getDays($period1Start,$period1End), $period1Purchases),
                                         'period2'=>$this->getData($this->getDays($period2Start,$period2End),
                                          $period2Purchases)
                                        ]],200);
    }


    public function search(Request $request,$searchTerm) {

        $user=$request->auth;

        $purchases=Purchase::where('user_id', $user->id)
                           ->where('name','LIKE','%'.$searchTerm.'%')
                           ->select('name', 'amount','created_at')
                           ->get();

        $datetime=new FormatDateTime();

        return response()->json(['data'=>$datetime->formatCreatedAtStringMonth($purchases)], 200);
    }
}
