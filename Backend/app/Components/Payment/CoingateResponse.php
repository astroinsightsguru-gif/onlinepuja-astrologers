<?php

namespace App\Components\Payment;

use App\Service\CoinGateService;

class CoingateResponse
{
    /**
     * @var coinGateData - coinGateData
     */
    protected $coinGateService;

    //construt method
    public function __construct()
    {
        //create coingate instance
        $this->coinGateService = new CoinGateService();
    }

    public function getCoinGatePaymentData($requestData)
    {
        //get coingate payment request data
        $coinGateData = $this->coinGateService->prepareCoinGatePaymentRequest($requestData);

        //return response data
        return $coinGateData;
    }
}
