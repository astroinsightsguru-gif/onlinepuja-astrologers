<?php

namespace App\Service;

/**
 * This MailService class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class CoinGateService
{
    /**
     * @var configData - configData
     */
    protected $configData;

    /**
     * @var paymentUrl - paymentUrl
     */
    protected $paymentUrl;

    /**
     * @var coingateToken - coingateToken
     */
    protected $coingateToken;

    /**
     * @var configItem - configItem
     */
    protected $configItem;

    /**
     * Constructor.
     *
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        $this->configData = configItem();

        //collect CoinGate data in config array
        $this->configItem = getArrayItem($this->configData, 'payments.gateway_configuration.coingate', []);

        //check test mode or product mode set coinGateToken and paymentUrl
        if (!empty($this->configItem)) {
            if ($this->configItem['testMode'] == true) {
                $this->coingateToken = $this->configItem['testToken'];
                $this->paymentUrl        = 'https://api-sandbox.coingate.com/v2/orders';
            } else {
                $this->coingateToken = $this->configItem['liveToken'];
                $this->paymentUrl        = 'https://api.coingate.com/v2/orders';
            }
        }
    }


    public function processCoinGateRequest($request)
    {
        $amount = $request['amounts'][$this->configItem['currency']];

        $success_url = getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=' . $request['paymentOption'] . '&orderId=' . $request['order_id'] . '&amount=' . $amount . '&status=success'; // success_url

        $cancel_url = getAppUrl($this->configItem['callbackUrl']) . '?paymentOption=' . $request['paymentOption'] . '&orderId=' . $request['order_id'] . '&amount=' . $amount . '&status=cancel'; // cancel_url

        $headers = [
            "Authorization: Token " . $this->coingateToken,
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        //WHILE IN WORKING PLEASE USE NGROK URL FOR PAYMENT CALLBACK
        // $callback_url =  "https://0896-2401-4900-1c9b-df4c-b5d2-4b-a49f-3e12.ngrok-free.app/lw-projects/lw-paypage/__CODEFIELD/example/payment-response.php";

        $callback_url = getAppUrl($this->configItem['callbackUrl']); // callbackUrl

        $curl = curl_init();
        $data = [
            "order_id" => $request['order_id'],
            "price_amount" => $amount,
            "price_currency" => $this->configItem['currency'],
            "receive_currency" => $this->configItem['currency'],
            "callback_url" => $callback_url,
            "cancel_url" => $cancel_url,
            "success_url" => $success_url
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->paymentUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $result = json_decode($response, true);
        $result['paymentOption'] = 'coingate';

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $result;
        }
    }
}
