<?php

// Include Header file
define('PAY_PAGE_CONFIG', config('paypage-payment'));

/**
 * Use PaymentProcess Class
 * Use PaytmService Class
 * Use InstamojoService Class
 * Use IyzicoService Class
 * Use PaypalService Class
 * Use PaystackService Class
 * Use RazorpayService Class
 * Use StripeService Class
 * Use AuthorizeNetService Class
 * Use RavepayService Class
 * Use PagseguroService Class
 */

use App\Components\Payment\PaymentProcess;
use App\Models\UserModel\Payment;
use App\Service\AuthorizeNetService;
use App\Service\CoinGateService;
use App\Service\InstamojoService;
use App\Service\IyzicoService;
use App\Service\MercadopagoService;
use App\Service\MollieService;
use App\Service\PagseguroService;
use App\Service\PaypalCheckOutService;
use App\Service\PaypalService;
use App\Service\PaystackService;
use App\Service\PaytmService;
use App\Service\PayUmoneyService;
use App\Service\RavepayService;
use App\Service\RazorpayService;
use App\Service\StripeService;
use App\Service\TwoCheckoutService;
use Symfony\Component\HttpFoundation\Session\Session;

$config = configItem();

$gatewayConfiguration = $config['payments']['gateway_configuration'];

$paymentOption = null;
if (!empty($_POST)) {
    $paymentOption = $_POST['paymentOption'];
}

/** Get instance of paytm service */
$paytmService = null;
if ($paymentOption == 'paytm' and getArrayItem($gatewayConfiguration['paytm'], 'enable', [])) {
    $paytmService = new PaytmService();
}

/** Get instance of instamojo service */
$instamojoService = null;
if ($paymentOption == 'instamojo' and getArrayItem($gatewayConfiguration['instamojo'], 'enable', [])) {
    $instamojoService = new InstamojoService();
}

/** Get instance of iyzico service */
$iyzicoService = null;
if ($paymentOption == 'iyzico' and getArrayItem($gatewayConfiguration['iyzico'], 'enable', [])) {
    $iyzicoService = new IyzicoService();
}

/** Get instance of paypal service */
$paypalService = null;
if ($paymentOption == 'paypal' and getArrayItem($gatewayConfiguration['paypal'], 'enable', [])) {
    $paypalService = new PaypalService();
}

/** Get instance of paystack service */
$paystackService = null;
if ($paymentOption == 'paystack' and getArrayItem($gatewayConfiguration['paystack'], 'enable', [])) {
    $paystackService = new PaystackService();
}
/** Get instance of razorpay service */
$razorpayService = null;
if ($paymentOption == 'razorpay') {
    $razorpayService = new RazorpayService();
}

/** Get instance of stripe service */
$stripeService = null;
if ($paymentOption == 'stripe' and getArrayItem($gatewayConfiguration['stripe'], 'enable', [])) {
    $stripeService = new StripeService();
}

/** Get instance of authorize service */
$authorizeNetService = null;
if ($paymentOption == 'authorize-net' and getArrayItem($gatewayConfiguration['authorize-net'], 'enable', [])) {
    $authorizeNetService = new AuthorizeNetService();
}

/** Get instance of Mercadopago service */
$mercadopagoService = null;
if ($paymentOption == 'mercadopago' and getArrayItem($gatewayConfiguration['mercadopago'], 'enable', [])) {
    $mercadopagoService = new MercadopagoService();
}

/** Get instance of PayUmoney service */
$payUmoneyService = null;
if ($paymentOption == 'payumoney' and getArrayItem($gatewayConfiguration['payumoney'], 'enable', [])) {
    $payUmoneyService = new PayUmoneyService();
}

/** Get instance of mollie service */
$mollieService = null;
if ($paymentOption == 'mollie' and getArrayItem($gatewayConfiguration['mollie'], 'enable', [])) {
    $mollieService = new MollieService();
}

/** Get instance of ravepay service */
$ravepayService = null;
if ($paymentOption == 'ravepay' and getArrayItem($gatewayConfiguration['ravepay'], 'enable', [])) {
    $ravepayService = new RavepayService();
}

/** Get instance of pagseguro service */
$pagseguroService = null;
if ($paymentOption == 'pagseguro' and getArrayItem($gatewayConfiguration['pagseguro'], 'enable', [])) {
    $pagseguroService = new PagseguroService();
}

// Only for paypal checkout
$result = file_get_contents('php://input');
$paypalData = json_decode($result, TRUE);

/** Get instance of paypal checkout service */
$paypalCheckOutService = null;
if ((isset($paypalData['paymentOption']) and $paypalData['paymentOption'] == 'paypal-checkout') || (isset($paypalData['userDetails']) == true and $paypalData['userDetails']['paymentOption'] == 'paypal-checkout') and getArrayItem($gatewayConfiguration['paypal-checkout'], 'enable', [])) {
    $paypalCheckOutService = new PaypalCheckOutService();
}

/** Get instance of paypal checkout service */
$coingateService = null;
if ($paymentOption == 'coingate' and getArrayItem($gatewayConfiguration['coingate'], 'enable', [])) {
    $coingateService = new CoinGateService();
}

/** Get instance of two checkout service */
$twoCheckoutService = null;
if ($paymentOption == 'twoCheckout' and getArrayItem($gatewayConfiguration['twoCheckout'], 'enable', [])) {
    $twoCheckoutService = new TwoCheckoutService();
}

/** Process a payment with anyone service */
$paymentProcess = new PaymentProcess(
    $paytmService,
    $instamojoService,
    $iyzicoService,
    $paypalService,
    $paystackService,
    $razorpayService,
    $stripeService,
    $authorizeNetService,
    $mercadopagoService,
    $payUmoneyService,
    $mollieService,
    $ravepayService,
    $pagseguroService,
    $paypalCheckOutService,
    $coingateService,
    $twoCheckoutService,
);
/** Get instance of GUMP, its a validation library for PHP */
$gump = new GUMP();

// check paypal checkout data is not empty
if (isset($paypalData) && count($paypalData) > 0) {
    if (isset($paypalData['responseData']['orderID'])) {
        $paymentData = $paymentProcess->capturePaypal($paypalData);
        echo json_encode($paymentData);
        return;
    }

    $insertData = $gump->sanitize($paypalData);
    $paymentOption = $insertData['paymentOption'];
    $paymentData = $paymentProcess->getPaymentData($insertData);

    // set select payment option in return paymentData array
    $paymentData['paymentOption'] = $paymentOption;

    if ($paymentOption == 'paypal-checkout') {
        echo json_encode($paymentData);
        return;
    }
}

// check post data is not empty
if (isset($_POST) && count($_POST) > 0) {
    // Sanitize form input data, remove tags for security purpose
    $insertData = $gump->sanitize($_POST);
    // Apply validation rule for post request.
    $validation = GUMP::is_valid($insertData, array(
        // 'amount'        => 'required|numeric|min_numeric,0',
        'paymentOption' => 'required'
    ));

    $paymentOption = $insertData['paymentOption'];

    // Check if iyzico or authorize-net payment method is used then check iyzico or authorize-net form data like
    // amount, option, cardname, card number, expiry month, expiry year, cvv etc and validate it
    if ($paymentOption == 'iyzico' or $paymentOption == 'authorize-net') {
        $validation = GUMP::is_valid($insertData, array(
            // 'amount'        => 'required|numeric',
            'paymentOption' => 'required',
            'cardname' => 'required',
            'cardnumber' => 'required',
            'expmonth' => 'required',
            'expyear' => 'required',
            'cvv' => 'required'
        ));
    }

    // Check server side validation success then process for next step
    if ($validation === true) {
        // Then send data to payment process service for process payment
        // This service will return payment data
        $paymentData = $paymentProcess->getPaymentData($insertData);

        // set select payment option in return paymentData array
        $paymentData['paymentOption'] = $paymentOption;

        if ($paymentOption == 'payumoney') {
            $paymentData['merchantForm'] = getPayUmoneyMerchantForm($paymentData);
            // return payment array on ajax request
            echo json_encode($paymentData);
            return;
        }
        if ($paymentOption == 'paytm') {
            // If paytm payment method are selected then get payment merchant form
            $paymentData['merchantForm'] = getPaytmMerchantForm($paymentData);

            // return payment array on ajax request
            echo json_encode($paymentData);

            // on success instamojo, paystack, stripe, razorpay, iyzico & paypal response
            // } else if () {
        } elseif (
            $paymentOption == 'instamojo' ||
            $paymentOption == 'paystack' ||
            $paymentOption == 'iyzico' ||
            $paymentOption == 'paypal' ||
            $paymentOption == 'stripe' ||
            $paymentOption == 'authorize-net' ||
            $paymentOption == 'mercadopago' ||
            $paymentOption == 'payumoney' ||
            $paymentOption == 'mollie' ||
            $paymentOption == 'ravepay' ||
            $paymentOption == 'pagseguro' ||
            $paymentOption == 'coingate'
        ) {
            // return payment array on ajax request
            echo json_encode($paymentData);
        } elseif ($paymentOption == 'razorpay') {
            echo json_encode(array_values($paymentData)[0]);
        }
    } else {
        // If Validation errors occurred then show it on the form
        $validationMessage = [];

        // get collection of validation messages
        foreach ($validation as $valid) {
            $validationMessage['validationMessage'][] = strip_tags($valid);
        }

        // return validation array on ajax request
        echo json_encode($validationMessage);

        exit();
    }

    if ($paymentOption == 'phonepe' && getArrayItem($gatewayConfiguration['phonepe'], 'enable', [])) {
        // Get PhonePe credentials from database
        $phonepeMerchantId = DB::table('systemflag')
            ->where('name', 'phonepeMerchantId')
            ->select('value')
            ->first();

        $phonepeClientSecret = DB::table('systemflag')
            ->where('name', 'phonepeSaltKey')
            ->select('value')
            ->first();

        $phonepeClientId = DB::table('systemflag')
            ->where('name', 'phonepeMerchantUserId')
            ->select('value')
            ->first();

        $merchantId = $phonepeMerchantId->value;
        $clientId = $phonepeClientId->value;
        $clientSecret = $phonepeClientSecret->value;

        $session = new Session();
        $redirectUrl = route('payment-response') . '?paymentOption=phonepe&logtoken=' . ($session->get('token') ?: null) . '&pay_id=' . ($session->get('pay_id') ?: null);

        $payment = Payment::where('payment.id', $session->get('pay_id'))
            ->join('users', 'users.id', 'payment.userId')
            ->select('payment.*', 'users.id as userId', 'users.name', 'users.email', 'users.contactNo')
            ->where('payment.paymentStatus', 'pending')
            ->first();

        // Set transaction details
        $order_id = uniqid();
        $mobile = $payment->contactNo ?? '9999999999';
        $amount = $payment->amount;
        $session->set('phonepe_merchant_order_id', $order_id);  // Store it in session

        // Step 1: Get Access Token
        $authUrl = 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token';  // Sandbox: https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token

        $authResponse = Http::asForm()->withHeaders([
            'Accept' => 'application/json'
        ])->post($authUrl, [
            'client_id' => $merchantId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'client_version' => '1'
        ]);

        if (!$authResponse->successful()) {
            $errorResponse = $authResponse->json();
            error_log('PhonePe Auth Error: ' . json_encode($errorResponse));
            echo 'Failed to authenticate with PhonePe. Error: ' . ($errorResponse['message'] ?? 'Unknown error');
            return;
        }

        $authData = $authResponse->json();
        $accessToken = $authData['access_token'] ?? null;
        $tokenType = $authData['token_type'] ?? 'Bearer';

        if (!$accessToken) {
            echo 'Failed to get access token from PhonePe';
            return;
        }

        // Step 2: Create Payment Request using V2 Checkout API
        $paymentData = [
            'merchantOrderId' => $order_id,
            'amount' => $amount * 100,  // Amount in paise
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'message' => 'Payment for order ' . $order_id,
                'merchantUrls' => [
                    'redirectUrl' => $redirectUrl
                ]
            ],
            'merchantUserId' => $phonepeMerchantId->value,
            'mobileNumber' => $mobile,
            'merchantId' => $merchantId
        ];

        // Make the payment request
        $paymentUrl = 'https://api.phonepe.com/apis/pg/checkout/v2/pay';  // Sandbox: https://api-preprod.phonepe.com/apis/pg-sandbox/checkout/v2/pay

        $paymentResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'O-Bearer ' . $accessToken  // Note the 'O-Bearer' prefix
        ])->post($paymentUrl, $paymentData);
        // dd($paymentResponse->json());
        if ($paymentResponse->successful()) {
            $responseData = $paymentResponse->json();
            if (isset($responseData['redirectUrl'])) {
                $payUrl = $responseData['redirectUrl'];

                $result = [
                    'payment_url' => $payUrl,
                    'paymentOption' => 'phonepe',
                    'merchantOrderId' => $order_id
                ];

                echo json_encode($result);
            } else {
                $errorMsg = $responseData['message'] ?? json_encode($responseData);
                error_log('PhonePe Payment Failed: ' . $errorMsg);
                echo '<p>Payment initiation failed. ' . htmlspecialchars($errorMsg) . '</p>';
            }
        } else {
            $errorMsg = $paymentResponse->body();
            error_log('PhonePe API Error: ' . $errorMsg);
            echo '<p>Payment request failed. Status: ' . $paymentResponse->status() . '</p>';
        }
    }
}
