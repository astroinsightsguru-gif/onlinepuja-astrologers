<?php

namespace App\Service;

use GuzzleHttp\Psr7\Request;

/**
 * This MailService class for manage globally -
 * mail service in application.
 *---------------------------------------------------------------- */
class PaypalCheckOutService
{
    /**
     * @var configData - configData
     */
    protected $configData;

    /**
     * @var configItem - configItem
     */
    protected $configItem;


    /**
     * @var Holds Info Url
     */
    protected $paypalUrl;

    /**
     * @var Holds CLient Id
     */
    protected $paypalClientId;

    /**
     * @var Holds Secret key
     */
    protected $paypalClientSecret;

    /**
     * Constructor.
     *
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        $this->configData = configItem();
        $configItem = [];

        //check config data is exist
        if (isset($this->configData)) {
            $configItem = $this->configData['payments']['gateway_configuration']['paypal-checkout'];
        }


        $paypalUrl = "";

        if ($configItem['testMode'] == true) {
            $paypalUrl = $configItem['paypalSandboxUrl'];
            $paypalClientId = $configItem['paypalTestingClientKey'];
            $paypalClientSecret = $configItem['paypalTestingSecretKey'];
        } else {
            $paypalUrl = $configItem['paypalProdUrl'];
            $paypalClientId = $configItem['paypalLiveClientKey'];
            $paypalClientSecret = $configItem['paypalLiveSecretKey'];
        }

        $this->paypalUrl = $paypalUrl;
        $this->paypalClientId = $paypalClientId;
        $this->paypalClientSecret = $paypalClientSecret;
    }


    /**
     * Generate Access Token
     */
    public function generateAccessToken()
    {
        $url = "$this->paypalUrl/v1/oauth2/token";
        $headers = [
            "Accept: application/json",
            "Accept-Language: en_US"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERPWD, $this->paypalClientId . ":" . $this->paypalClientSecret);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        $response = curl_exec($ch);
        curl_close($ch);

        // Process the response from the PayPal API endpoint
        $responseData = json_decode($response, true);
        $accessToken = $responseData['access_token'];
        return $accessToken;
    }

    /**
     * Process Paypal Checkout Request
     *
     * @param   array  $request
     *
     * @return json object
     */
    public function processPaypalCheckoutRequest($request)
    {
        $configItem = [];
        //check config data is exist
        if (isset($this->configData)) {
            $configItem = $this->configData['payments']['gateway_configuration']['paypal-checkout'];
        }

        $accessToken = $this->generateAccessToken();
        $orderTotalAmount = $request['amounts'][$configItem['currency']];
        $currency     = $configItem['currency'];

        // Set up the API endpoint URL and request headers
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ];

        // Build the request payload in JSON format
        $payload = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "value" => $orderTotalAmount,
                    "currency_code" => $currency
                ]
            ]],
        ];
        $jsonPayload = json_encode($payload);

        // Send the request to the PayPal API endpoint using cURL
        $ch = curl_init("$this->paypalUrl/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        $response = curl_exec($ch);
        curl_close($ch);

        // Process the response from the PayPal API endpoint
        $responseData = json_decode($response, true);

        if ($responseData['status'] == 'CREATED') {
            return $responseData;
        } else {
            return false;
        }
    }

    /**
     * Process Capture Paypal Order
     *
     * @param   array  $paypalData
     *
     * @return  json object
     */
    public function processCapturePaypalOrder($paypalData)
    {
        $orderUID = $paypalData['responseData']['orderID'];
        $accessToken = $this->generateAccessToken();


        // Set the request headers and body to capture the payment
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken
        ];

        $configItem = [];
        //check config data is exist
        if (isset($this->configData)) {
            $configItem = $this->configData['payments']['gateway_configuration']['paypal-checkout'];
        }

        $orderTotalAmount = $paypalData['userDetails']['amounts'][$configItem['currency']];

        $amount = [
            "currency_code" => $configItem['currency'],
            "value" => $orderTotalAmount
        ];

        $data = [
            "amount" => $amount
        ];

        $json_data = json_encode($data);

        // Send a POST request using cURL
        $curl = curl_init("$this->paypalUrl/v2/checkout/orders/{$orderUID}/capture");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            // Parse the response to obtain the capture ID
            $json_response = json_decode($response, true);
            $response = json_encode($json_response);

            $successUrl = getAppUrl($configItem['successUrl']) . '?paymentOption=' . $paypalData['userDetails']['paymentOption'] . '&orderId=' . $paypalData['userDetails']['order_id'] . '&response=' . $response; //add success Url request

            $json_response['successUrl'] = $successUrl;

            return $json_response;
        }
    }
}
