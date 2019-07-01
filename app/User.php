<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use App\MonthlyIncome;
use App\Saving;
use App\Purchases;
use App\FormatDateTime;
use Carbon\Carbon;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','password','activation_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public function getLastName() {

        return explode(' ', $this->name)[1];
    }


    /*
        Returning the User's total income for the month
    */

    public function getInitialIncome() {

        $income=MonthlyIncome::where('user_id', $this->id)
                             ->where('month', date('F'))
                             ->where('year', date('Y'))
                             ->select('original_amount')
                             ->first();

         return $income->original_amount;          
    }


    /*
        Returning the amount left of the user's income
    */

    public function getCurrentIncome() {

        $income=MonthlyIncome::where('user_id', $this->id)
                             ->where('month', date('F'))
                             ->where('year', date('Y'))
                             ->select('final_amount')
                             ->first();

        return $income->final_amount;                  
    }

    /*
        Returning the Savings Target of the User
    */

    public function getSavingsAmount() {

        $saving=Saving::where('user_id', $this->id)
                      ->where('month', date('F'))
                      ->where('year', date('Y'))
                      ->select('amount')
                      ->first();

        return $saving->amount;
    }


    /*
        Getting all of the user's purchases for the day
    */

    public function getTodayPurchases() {

        $purchases=Purchase::where('user_id', $this->id)
                           ->whereDay('created_at','=',date('d'))
                           ->whereMonth('created_at','=', date('m'))
                           ->whereYear('created_at','=',date('Y'))
                           ->select('name','amount','created_at')
                           ->get();

        $datetime=new FormatDateTime();

        return $datetime->formatCreatedAtString($purchases);
    }
   
}
