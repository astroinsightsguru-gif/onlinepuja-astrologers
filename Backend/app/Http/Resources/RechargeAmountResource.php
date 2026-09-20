<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RechargeAmountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $amount = $this->amount;
        $amount_usd = $this->amount_usd;
        $cashback = $this->cashback;
        // if (systemflag('walletType') == 'Coin') {
        //     $amount = convertinrtocoin($this->amount);
        //     $amount_usd = convertinrtocoin($this->amount_usd);
        //     $cashback = convertinrtocoin($this->cashback);
        // }
        return [
            'id' => $this->id,
            'amount' => $amount,
            'amount_usd' => $amount_usd,
            'cashback' => $cashback,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
