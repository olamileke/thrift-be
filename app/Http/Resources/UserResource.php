<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	/**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'total_income'=>$this->getInitialIncome(),
            'current_income'=>$this->getCurrentIncome(),
            'savings_target'=>$this->getSavingsAmount(),
            'purchases'=>$this->getTodayPurchases()
        ];
    }
}