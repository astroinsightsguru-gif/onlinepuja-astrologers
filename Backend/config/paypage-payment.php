<?php

$techAppConfig = [

    /* Base Path of app
    ------------------------------------------------------------------------- */
    'base_url' => env('APP_URL').'/',


    /* Amount - if null amount input open in form
    ------------------------------------------------------------------------- */
    'amount' => null,

    'payments' => [
        /* Gateway Configuration key
        ------------------------------------------------------------------------- */
        'gateway_configuration' => [
            'paypal' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'Paypal', //payment gateway name
                'paypalSandboxBusinessEmail'        => 'Enter Paypal Sandbox Email', //paypal sandbox business email
                'paypalProductionBusinessEmail'     => 'Enter Paypal Business Email', //paypal production business email
                'currency'                  => 'USD', //currency
                'currencySymbol'              => '$',
                'paypalSandboxUrl'          => 'https://www.sandbox.paypal.com/cgi-bin/webscr', //paypal sandbox test mode Url
                'paypalProdUrl'             => 'https://www.paypal.com/cgi-bin/webscr', //paypal production mode Url
                'notifyIpnURl'              => "payment-response", //paypal ipn request notify Url
                'cancelReturn'              => "payment-response", //cancel payment Url
                'callbackUrl'               => "payment-response", //callback Url after payment successful
                'privateItems'              => []
            ],
            'paytm' => [
                'enable'                    => true,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Paytm', //payment gateway name
                'currency'                  => 'INR', //currency
                'currencySymbol'              => '₹',
                'paytmMerchantTestingMidKey'       => 'Enter your Test Mid Key', //paytm testing Merchant Mid key
                'paytmMerchantTestingSecretKey'    => 'Enter your Test Secret Key', //paytm testing Merchant Secret key
                'paytmMerchantLiveMidKey'       => 'Enter your Live Mid Key', //paytm live Merchant Mid key
                'paytmMerchantLiveSecretKey'    => 'Enter your Live Secret Key', //paytm live Merchant Secret key
                'industryTypeID'            => 'Retail', //industry type
                'channelID'                 => 'WEB', //channel Id
                'website'                   => 'WEBSTAGING',
                'paytmTxnUrl'               => 'https://securegw-stage.paytm.in/theia/processTransaction', //paytm transaction Url
                'callbackUrl'               => "payment-response", //callback Url after payment successful or cancel payment
                'privateItems'              => [
                    'paytmMerchantTestingSecretKey',
                    'paytmMerchantLiveSecretKey'
                ]
            ],
            'instamojo' => [
                'enable'                    => true,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Instamojo', //payment gateway name
                'currency'                  => 'INR', //currency
                'currencySymbol'              => '₹',
                'sendEmail'                 => false, //send mail (true or false)
                'instamojoTestingApiKey'           => 'Enter your Test Api Key', // instamojo testing API Key
                'instamojoTestingAuthTokenKey'     => 'Enter your Test Auth Token Key', // instamojo testing Auth token Key
                'instamojoLiveApiKey'           => 'Enter your Live Api Key', // instamojo live API Key
                'instamojoLiveAuthTokenKey'     => 'Enter your Live Auth Token Key', // instamojo live Auth token Key
                'instamojoSandboxRedirectUrl'   => 'https://test.instamojo.com/api/1.1/', // instamojo Sandbox redirect Url
                'instamojoProdRedirectUrl'      => 'https://www.instamojo.com/api/1.1/', // instamojo Production mode redirect Url
                'webhook'                   => 'http://instamojo.com/webhook/', // instamojo Webhook Url
                'callbackUrl'               => "payment-response", //callback Url after payment successful
                'privateItems'              => [
                    'instamojoTestingApiKey',
                    'instamojoTestingAuthTokenKey',
                    'instamojoLiveApiKey',
                    'instamojoLiveAuthTokenKey',
                    'instamojoSandboxRedirectUrl',
                    'instamojoProdRedirectUrl'
                ]
            ],
            'paystack' => [
                'enable'                    => true,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Paystack', //payment gateway name
                'currency'                  => 'NGN', //currency
                'currencySymbol'              => '₦',
                'paystackTestingSecretKey'         => 'sk_test_7caa258d571a72cfdb0f2aa42abd5badb4e64da0', //paystack testing secret key
                'paystackTestingPublicKey'         => 'pk_test_3e54d41d058c5691a8f939180969ecdfd1708375', //paystack testing public key
                'paystackLiveSecretKey'         => 'Enter your Live Secret Key', //paystack live secret key
                'paystackLivePublicKey'         => 'Enter your Live Publish Key', //paystack live public key
                'callbackUrl'               => "payment-response", //callback Url after payment successful
                'privateItems'              => [
                    'paystackTestingSecretKey',
                    'paystackLiveSecretKey'
                ]
            ],
            'stripe'    => [
                'enable'                    => true,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Stripe', //payment gateway name
                'locale'                    => 'auto', //set local as auto
                'allowRememberMe'           => false, //set remember me ( true or false)
                'currency'                  => 'USD', //currency
                'currencySymbol'              => '$',
                'paymentMethodTypes'         => [
                    // before activating additional payment methods
                    // make sure that these methods are enabled in your stripe account
                    // https://dashboard.stripe.com/settings/payments
                    'card',
                    // 'ideal',
                    // 'bancontact',
                    // 'giropay',
                    // 'p24',
                    // 'eps'
                ],
                'stripeTestingSecretKey'    => 'sk_test_51MX0jLSF2flS4SttXmn9WBUWjiC6a4ZtwFUTIl6rrW0Tq6nd3ePO7DfxGjzdr2F8aNjJiviZY1gdPwBsCx5fG1Al00bYej9FiP', //Stripe testing Secret Key
                'stripeTestingPublishKey'   => 'Enter your Test Publish Key', //Stripe testing Publish Key
                'stripeLiveSecretKey'       => 'sk_test_51MX0jLSF2flS4SttXmn9WBUWjiC6a4ZtwFUTIl6rrW0Tq6nd3ePO7DfxGjzdr2F8aNjJiviZY1gdPwBsCx5fG1Al00bYej9FiP', //Stripe Secret live Key
                'stripeLivePublishKey'      => 'Enter your Live Publish Key', //Stripe live Publish Key
                'callbackUrl'               => "payment-response", //callback Url after payment successful
                'privateItems'              => [
                    'stripeTestingSecretKey',
                    'stripeLiveSecretKey'
                ]
            ],
            'razorpay'    => [
                'enable'                    => true,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Razorpay', //payment gateway name
                'merchantname'              => 'Online Puja', //merchant name
                'themeColor'                => '#bff0c4', //set razorpay widget theme color
                'currency'                  => 'INR', //currency
                'currencySymbol'              => '₹',
                'razorpayTestingkeyId'      => 'Enter Test Key', //razorpay testing Api Key
                'razorpayTestingSecretkey'  => 'Enter Test Secret Key', //razorpay testing Api Secret Key
                'razorpayLivekeyId'         => 'Enter Live Key', //razorpay live Api Key
                'razorpayLiveSecretkey'     => 'Enter Live Secret Key', //razorpay live Api Secret Key
                'callbackUrl'               => "payment-response", //callback Url after payment successful'
                'privateItems'              => [
                    'razorpayTestingSecretkey',
                    'razorpayLiveSecretkey'
                ]
            ],
            'iyzico'    => [
                'enable'                    => true,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Iyzico', //payment gateway name
                'conversation_id'           => 'CONVERS' . uniqid(), //generate random conversation id
                'currency'                  => 'TRY', //currency
                'currencySymbol'              => '₺',
                'subjectType'               => 1, // credit
                'txnType'                   => 2, // renewal
                'subscriptionPlanType'      => 1, //txn status
                'iyzicoTestingApiKey'       => 'Enter your Test Api Key', //iyzico testing Api Key
                'iyzicoTestingSecretkey'    => 'Enter your Test Secret Key', //iyzico testing Secret Key
                'iyzicoLiveApiKey'          => 'Enter your Live Api Key', //iyzico live Api Key
                'iyzicoLiveSecretkey'       => 'Enter your Live Secret Key', //iyzico live Secret Key
                'iyzicoSandboxModeUrl'      => 'https://sandbox-api.iyzipay.com', //iyzico Sandbox test mode Url
                'iyzicoProductionModeUrl'   => 'https://api.iyzipay.com', //iyzico production mode Url
                'callbackUrl'               => "payment-response", //callback Url after payment successful
                'privateItems'              => [
                    'iyzicoTestingApiKey',
                    'iyzicoTestingSecretkey',
                    'iyzicoLiveApiKey',
                    'iyzicoLiveSecretkey'
                ]
            ],
            'authorize-net'    => [
                'enable'                         => true,
                'testMode'                       => true, //test mode or product mode (boolean, true or false)
                'gateway'                        => 'Authorize.net', //payment gateway name
                'reference_id'                   => 'REF' . uniqid(), //generate random conversation id
                'currency'                       => 'USD', //currency
                'currencySymbol'                 => '$',
                'type'                           => 'individual',
                'txnType'                        => 'authCaptureTransaction',
                'authorizeNetTestApiLoginId'     => 'Enter your Test API Login Id', //authorize-net testing Api login id
                'authorizeNetTestTransactionKey' => 'Enter your Test Secret Transaction Key', //Authorize.net testing transaction key
                'authorizeNetLiveApiLoginId'     => 'Enter your Live API Login Id', //Authorize.net live Api login id
                'authorizeNetLiveTransactionKey' => 'Enter your Live Secret Transaction Key', //Authorize.net live transaction key
                'callbackUrl'                    => "payment-response", //callback Url after payment successful
                'privateItems'                  => [
                    'authorizeNetTestApiLoginId',
                    'authorizeNetTestTransactionKey',
                    'authorizeNetLiveApiLoginId',
                    'authorizeNetLiveTransactionKey'
                ]
            ],
            'mercadopago' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'Mercado Pago', //payment gateway name
                'currency'                      => 'USD', //currency
                'currencySymbol'                => '$', //currency Symbol
                'testAccessToken'               => 'Your Test Access Token',
                'liveAccessToken'               => 'Your Live Access Token',
                'callbackUrl'                   => "payment-response", //callback Url after payment successful
                'privateItems'                  => ['testAccessToken', 'liveAccessToken']
            ],
            'payumoney' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'PayUmoney', //payment gateway name
                'currency'                      => 'INR', //currency
                'currencySymbol'                => '₹', //currency Symbol
                'txnId'                         => "Txn" . rand(10000, 99999999),
                'merchantTestKey'               => 'Your Test Merchant Key',
                'merchantTestSalt'              => 'Your Test Salt Key',
                'merchantLiveKey'               => 'Your Live Merchant Key',
                'merchantLiveSalt'              => 'Your Live Salt Key',
                'callbackUrl'                   => "payment-response", //callback Url after payment successful
                'checkoutColor'                 => 'e34524',
                'checkoutLogo'                  => 'http://boltiswatching.com/wp-content/uploads/2015/09/Bolt-Logo-e14421724859591.png',
                'privateItems'                  => ['merchantTestKey', 'merchantTestSalt', 'merchantLiveKey', 'merchantLiveSalt']
            ],
            'mollie' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'Mollie', //payment gateway name
                'currency'                      => 'EUR', //currency
                'currencySymbol'                => '€', //currency Symbol
                'testApiKey'                    => 'Your Test API Key',
                'liveApiKey'                    => 'Your Live API Key',
                'callbackUrl'                   => "payment-response", //callback Url after payment successful
                'privateItems'                  => ['testApiKey', 'liveApiKey']
            ],
            'ravepay' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'Ravepay', //payment gateway name
                'currency'                      => 'NGN', //currency
                'currencySymbol'                => '₦', //currency Symbol
                'txn_reference_id'              => 'REF' . uniqid(), //generate random conversation id
                'testPublicApiKey'              => 'Your Test Public API Key',
                'testSecretApiKey'              => 'Your Test Secret API Key',
                'livePublicApiKey'              => 'Your Live Public API Key',
                'liveSecretApiKey'              => 'Your Live Secret API Key',
                'callbackUrl'                   => "payment-response", //callback Url after payment successful
                'sandboxVerifyPaymentUrl'       => 'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify', //sandbox staging server verify payment url.
                'productionVerifyPaymentUrl'    => 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify', //production staging server verify payment url.
                'privateItems'                  => ['testSecretApiKey', 'liveSecretApiKey']
            ],
            'pagseguro' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'Pagseguro', //payment gateway name
                'environment'                   => 'sandbox', //production, sandbox
                'currency'                      => 'BRL', //currency
                'currencySymbol'                => 'R$', //currency Symbol
                'reference_id'                  => 'REF' . uniqid(), //generate random reference id
                'email'                         => 'Your PagSeguro Email id', //your pagseguro email id for create account credentials
                'testToken'                     => 'Your Test Production Token', //your sandbox pagseguro token for create account credentials
                'liveToken'                     => 'Your Live Production Token', //your production pagseguro token for create account credentials
                'callbackUrl'                   => "payment-response", //callback Url after payment successful
                'notificationUrl'               => "payment-response", //notification url when payment successfully user collect notfication data
                'privateItems'                  => ['liveToken', 'testToken']
            ],
            'coingate' => [
                'enable'                        => true,
                'testMode'                      => true, //test mode or product mode (boolean, true or false)
                'gateway'                       => 'Coingate', //payment gateway name
                'environment'                   => 'sandbox', //production, sandbox
                'currency'                      => 'USD', //currency
                'currencySymbol'                => '$', //currency Symbol
                'reference_id'                  => 'REF' . uniqid(), //generate random reference id
                'testToken'                     => 'Your Sandbox Token',//your sandbox coingate token for create account credentials
                'liveToken'                     => 'Your Live Production Token', //your production coingate token for create account credentials
                'callbackUrl'                   => "payment-response", //callback Url after payment successful
                'notificationUrl'               => "payment-response", //notification url when payment successfully user collect notification data
                'privateItems'                  => ['liveToken', 'testToken']
            ],
            'paypal-checkout' => [
                'enable'                    => false,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Paypal Checkout', //payment gateway name
                'paypalTestingClientKey'    => 'Enter your Test Client Key', //paypal Testing Client key
                'paypalTestingSecretKey'    => 'Enter your Test Secret Key', //paypal Testing Secret key
                'paypalLiveClientKey'       => 'Enter your Live Client Key', //paypal Live Client key
                'paypalLiveSecretKey'       => 'Enter your Live Secret Key', //paypal Live Secret key
                'currency'                  => 'USD', //currency
                'currencySymbol'              => '$',
                'paypalSandboxUrl'          => 'https://api-m.sandbox.paypal.com', //paypal sandbox test mode Url
                'paypalProdUrl'             => 'https://api-m.paypal.com', //paypal production mode Url
                'successUrl'              => "payment-response", //paypal ipn request notify Url
                'cancelReturn'              => "payment-response", //cancel payment Url
                'callbackUrl'               => "payment-response", //callback Url after payment successful
                'privateItems'              => [
                    'paypalTestingSecretKey',
                    'paypalLiveSecretKey'
                ]
            ],
            'phonepe' => [
                'enable'                    => false,
                'testMode'                  => true, //test mode or product mode (boolean, true or false)
                'gateway'                   => 'Phonepe', //payment gateway name
                'phonepeMerchantId'    => 'Enter your Merchant Id', //paypal Testing Client key
                'phonepeSaltIndex'    => 'Enter your Salt Index', //paypal Testing Secret key
                'phonepeSaltKey'       => 'Enter your Salt Key', //paypal Live Client key
                'phonepeMerchantUserId'       => 'Enter your Merchant User Id', //paypal Live Secret key
                'currency'                  => 'INR', //currency
                'currencySymbol'              => '₹',
                'successUrl'              => "payment-response", //paypal ipn request notify Url
                'cancelReturn'              => "payment-response", //cancel payment Url
                'callbackUrl'               => "payment-response", //callback Url after payment successful
            ],
        ],
    ],
];

return compact("techAppConfig");
