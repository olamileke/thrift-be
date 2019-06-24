<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
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
                          ->where('day',date('d'))
                          ->where('month', date('F'))
                          ->where('year', date('Y'))
                          ->first();

        if($purchase) {

            $purchase->amount=$purchase->amount + $request->input('amount');
            $purchase->save();
        }                           
        else {

            Purchase::create([
                'user_id'=>$user->id,
                'monthlyincome_id'=>$income->id,
                'name'=>$request->input('name'),
                'amount'=>$request->input('amount'),
                'day'=>date('d'),
                'month'=>date('F'),
                'year'=>date('Y')
            ]);
        }   
                            
        $income->final_amount=$income->final_amount - $request->input('amount');
        $income->save();

        return response()->json(['data'=>'Expense Record created successfully'],200);
    }
}
