<?php

namespace App;

class FormatDateTime
{

    private $months=['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
                      'November', 'December'];


    public function formatCreatedAtString($purchases) {

        foreach($purchases as $purchase) {

           $purchase->time=$this->getTime($purchase->created_at);
           unset($purchase->created_at);
        }

        return $purchases;
    }


    // getting the time the purchase was created in 12 hour form

    public function getTime($timestamp) {

        $time=substr($timestamp->format('H:i:s'),0,5);
        $hour=(int)substr($time,0,2) + 1;
        $formattedtime='';

        if($hour < 12) {

            $formattedtime=$hour.substr($time,2).' AM';
        }
        elseif($hour == 12) {

            $formattedtime=$hour.substr($time,2).' PM';                
        }
        else {

            $hour=$hour-12;
            $formattedtime=$hour.substr($time,2).' PM';                
        }

        return $formattedtime;
    }
    

    // getting the day the purchase was created in the form day-date-month-year

    public function getDay($timestamp) {

        $timestamp=strtotime($timestamp);

        $date=date('d', $timestamp);
        $day=date('D', $timestamp);
        $month=date('m', $timestamp);
        $year=date('Y', $timestamp);

        $month=$this->months[$month - 1];

        return $day.' '.$date.' '.$month.', '.$year;
    }


    // getting the time of the purchase in day and time form

    public function formatCreatedAtStringMonth($purchases) {

        foreach($purchases as $purchase) {

            $purchase->time=$this->getDay($purchase->created_at).' '.$this->getTime($purchase->created_at);
            unset($purchase->created_at);
        }

        return $purchases;
    }

}


?>