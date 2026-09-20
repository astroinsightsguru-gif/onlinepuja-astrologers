<?php

//Set Access-Control-Allow-Origin with PHP
// header('Access-Control-Allow-Origin: http://site-a.com', false);

namespace App\Components\Payment;

class PaymentProcess
{
    /**
     * @var paytmService - Paytm Service
     */
    protected $paytmService;

    /**
     * @var instamojoService - Instamojo Service
     */
    protected $instamojoService;

    /**
     * @var iyzicoService - Iyzico Service
     */
    protected $iyzicoService;

    /**
     * @var paypalService - Paypal Service
     */
    protected $paypalService;

    /**
     * @var paystackService - Paystack Service
     */
    protected $paystackService;

    /**
     * @var razorpayService - Razorpay Service
     */
    protected $razorpayService;

    /**
     * @var stripeService - Stripe Service
     */
    protected $stripeService;

    /**
     * @var authorizeNetService - Authorize.Net Service
     */
    protected $authorizeNetService;

    /**
     * @var mercadopagoService - Mercadopago Service
     */
    protected $mercadopagoService;

    /**
     * @var payUmoneyService - Mercadopago Service
     */
    protected $payUmoneyService;

    /**
     * @var mollieService - mollie Service
     */
    protected $mollieService;

    /**
     * @var ravepayService - ravepay Service
     */
    protected $ravepayService;

    /**
     * @var pagseguroService - pagseguro Service
     */
    protected $pagseguroService;

    /**
     * @var PaypalCheckoutService - PaypalCheckout Service
     */
    protected $paypalCheckoutService;

    /**
     * @var CoinGateService - coingate Service
     */
    protected $coinGateService;

    public function __construct($paytmService, $instamojoService, $iyzicoService, $paypalService, $paystackService, $razorpayService, $stripeService, $authorizeNetService, $mercadopagoService, $payUmoneyService, $mollieService, $ravepayService, $pagseguroService, $paypalCheckoutService, $coinGateService)
    {
        $this->paytmService          = $paytmService;
        $this->instamojoService      = $instamojoService;
        $this->iyzicoService         = $iyzicoService;
        $this->paypalService         = $paypalService;
        $this->paystackService       = $paystackService;
        $this->razorpayService       = $razorpayService;
        $this->stripeService         = $stripeService;
        $this->authorizeNetService   = $authorizeNetService;
        $this->mercadopagoService    = $mercadopagoService;
        $this->payUmoneyService      = $payUmoneyService;
        $this->mollieService         = $mollieService;
        $this->ravepayService        = $ravepayService;
        $this->pagseguroService      = $pagseguroService;
        $this->paypalCheckoutService = $paypalCheckoutService;
        $this->coinGateService = $coinGateService;
    }

    public function getPaymentData($request)
    {
        $processResponse = [];
        if ($request['paymentOption'] == 'paytm') {
            //get paytm request data
            $processResponse = $this->paytmService->handlePaytmRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'instamojo') {
            //get instamojo request data
            $processResponse = $this->instamojoService->processInstamojoRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'iyzico') {
            //get iyzico request data
            $processResponse = $this->iyzicoService->processIyzicoRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'paypal') {
            //get paypal request data
            $processResponse = $this->paypalService->processPaypalRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'stripe') {
            // Get Stripe request Data
            $processResponse = $this->stripeService->processStripeRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'paystack') {
            // Get Stripe request Data
            $processResponse = $this->paystackService->processPaystackRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'razorpay') {
            // Get Stripe request Data
            $processResponse = $this->razorpayService->processRazorpayRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'authorize-net') {
            // Get authorize.net request Data
            $processResponse = $this->authorizeNetService->processAuthorizeNetRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'mercadopago') {
            // Get Mercadopago request Data
            $processResponse = $this->mercadopagoService->processMercadopagoRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'payumoney') {
            // Get PayUmoney request Data
            $processResponse = $this->payUmoneyService->processPayUmoneyRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'mollie') {
            // Get mollie request Data
            $processResponse = $this->mollieService->processMollieRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'ravepay') {
            // Get Ravepay request Data
            $processResponse = $this->ravepayService->processRavepayRequest($request);
            return $processResponse;
        } elseif ($request['paymentOption'] == 'pagseguro') {
            // Get PaymentOption request Data
            $processResponse = $this->pagseguroService->processPagseguroRequest($request);
            return $processResponse;
        }else if ($request['paymentOption'] == 'paypal-checkout') {
            // Get paypal checkout request Data
            $processResponse = $this->paypalCheckoutService->processPaypalCheckoutRequest($request);
            return $processResponse;
        }else if ($request['paymentOption'] == 'coingate') {
            // Get coingate checkout request Data
            $processResponse = $this->coinGateService->processCoinGateRequest($request);
            return $processResponse;
        }
    }

    public function capturePaypal($inputData)
    {
        $processResponse = $this->paypalCheckoutService->processCapturePaypalOrder($inputData);
        return $processResponse;
    }
}
