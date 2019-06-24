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
                           ->where('day', date('d'))
                           ->where('month', date('F'))
                           ->where('year', date('Y'))
                           ->select('name','amount','created_at')
                           ->get();
        // return $purchases;

        return $this->getFormattedTimeString($purchases);
    }


    public function getFormattedTimeString($purchases) {

        // getting the time the purchase was created in 12 hour form

        foreach($purchases as $purchase) {

            date_default_timezone_set('Africa/Lagos');
            $time=substr($purchase->created_at->format('H:i:s'),0,5);
            $hour=(int)substr($time,0,2) + 1;

            if($hour < 12) {

                $purchase->time_created=$time.' AM';
            }
            elseif($hour == 12) {

                $purchase->time_created=$time.' PM';                
            }
            else {

                $purchase->time_created=((int)$hour - 12).substr($time,2).' PM';                
            }
        }

        return $purchases;
    }
    
}
