<?php
// Include header file
define('PAY_PAGE_CONFIG', config('paypage-payment'));
// Get config data
$configData = configItem();
?>
<!-- Page HTML Start here -->
<!DOCTYPE html>
<html>
<!-- Index page head start here -->

<head>
    <!-- Page Title -->
    <title>Pay Page</title>
    <!-- Page Title -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Load JQuery, bootstrap, font-awesome, and custom css -->
    <script src="public/assets-pg/js/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="public/assets-pg/css/custom.css" type="text/css">
    <!-- /Load JQuery, bootstrap, font-awesome, and custom css -->
</head>
<!-- /Index page head end here -->
<!-- Page body start from here... -->

<body>
    <!-- Show loader when process payment request -->
    <div class="d-flex justify-content-center">
        <div class="lw-page-loader lw-show-till-loading">
            <div class="spinner-border" role="status"></div>
        </div>
    </div>
    @php
    $logo = DB::table('systemflag')
        ->where('name', 'AdminLogo')
        ->select('value')
        ->first();
    $appName = DB::table('systemflag')
        ->where('name', 'AppName')
        ->select('value')
        ->first();
@endphp
    <!-- Show loader when process payment request -->

    <!-- Payment form start from here -->
    <div class="pt-4 mb-5 container" id="lwCheckoutForm">
        <div class="row" style="text-align: -webkit-center;display:block">

            <!-- col-md-8 -->
            <div class="col-md-8">
                <!-- payment gateway start -->
                <form method="post" id="lwPaymentForm">
                    <!-- Card Start here -->
                    <div class="card">
                        <!-- Payment Page header -->
                        <div class="card-header">
                            <h3 class="text-center">Complete your payment</h3>
                        </div>
                        <!-- /Payment Page header -->

                        <!-- Card body start here -->
                        <div class="card-body">
                            <!-- Info Message -->

                            <!-- Info Message -->

                            <!-- show validation message block -->
                            <div id="lwValidationMessage" class="lw-validation-message"></div>
                            <!-- / show validation message block -->
                            <?php
                            // Get config data
                            // $configData = configItem();

                            $userDetails = [
                                'amounts' => [  // at least one currency amount is required
                                    // 'INR'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate),
                                    // 'USD'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate),
                                    // 'NGN'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate),
                                    // 'TRY'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate),
                                    // 'EUR'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate),
                                    // 'BRL'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate),
                                    // 'ZAR'   => $user->countryCode == '+91' ? $user->amount : convertusdtoinr($user->amount, $user->inr_usd_conversion_rate)
                                    'INR' => $user->rechargeAmount,
                                    'USD' => $user->rechargeAmount,
                                    'NGN' => $user->rechargeAmount,
                                    'TRY' => $user->rechargeAmount,
                                    'EUR' => $user->rechargeAmount,
                                    'BRL' => $user->rechargeAmount,
                                    'ZAR' => $user->rechargeAmount
                                ],
                                'order_id' => 'ORDS' . uniqid(),  // required in instamojo, Iyzico, Paypal, Paytm gateways
                                'customer_id' => 'CUSTOMER' . uniqid(),  // required in Iyzico, Paytm gateways
                                'item_name' => 'Sample Product',  // required in Paypal gateways
                                'item_qty' => 1,
                                'item_id' => 'ITEM' . uniqid(),  // required in Iyzico, Paytm gateways
                                'payer_email' => $user->email,  // required in instamojo, Iyzico, Stripe gateways
                                'payer_name' => $user->name,  // required in instamojo, Iyzico gateways
                                'payer_mobile' => $user->contactNo,
                                'mobile_code' => '91',  // (code) required in pagseguro gateways
                                'mobile_number' => $user->contactNo,  // (number) required in pagseguro gateways
                                'shipping_address' => [
                                    'address' => 'Av. Brig. Faria Lima',  // address
                                    'week_number' => '1384',  // house number
                                    'name' => 'Jardim Paulistano',  // name
                                    'zip_code' => '01452002',  // zip zode
                                    'state' => 'São Paulo',  // zip zode
                                    'highway_code' => 'SP',  // highway zode
                                    'country_code' => 'BRA',  // country zode
                                    'appartment_number' => 'apto. 114',  // country zode
                                ],  // required in pagseguro gateways
                                'shipping_cost' => '20.00',  // required in pagseguro gateways
                                'cpf_number' => '92354201567',  // required in pagseguro gateways
                                'description' => 'Lorem ipsum dolor sit amet, constetur adipisicing',  // Required for stripe
                                'ip_address' => getUserIpAddr(),  // required only for iyzico
                                'address' => $user->addressLine1,  // required in Iyzico gateways
                                'city' => 'Tigard',  // required in Iyzico gateways
                                'country' => 'United States',  // required in Iyzico gateways
                                'pay_id' => request()->payid  // required in Iyzico gateways
                            ];

                            if (!$configData) {
                                // If config data not found then show error
                                echo '<div class="alert alert-warning text-center">Unable to load configuration.</div>';
                            } else {
                                // Get Gateway configuration
                                $configItem = $configData['payments']['gateway_configuration'];
                                ?>

                                <h5 class="mt-4">Choose your payment method</h5>
                                <hr>
                                <?php
                                // Show all enable payment Gateway

                                foreach ($configItem as $key => $value) {
                                    // Check if payment gateway is enable from config
                                    if ($value['enable']) {
                                        ?>
                                        <!-- Payment gateway radio buttons with image -->
                                        <div class="form-check form-check-inline">
                                            <!-- radio button label -->
                                            <label class="form-check-label" for="paymentOption-<?= $key ?>">
                                                <!-- Fieldset for big image -->
                                                <fieldset class="lw-fieldset mr-3 mb-3">
                                                    <!-- small image legend -->
                                                    <legend class="lw-fieldset-legend-font">
                                                        <input class="form-check-input" type="radio" required="true" id="paymentOption-<?= $key ?>" name="paymentOption" value="<?= $key ?>">
                                                        <!-- payment gateway small image -->
                                                        <img class="lw-payment-gateway-icon-small" src="public/assets-pg/imgs/payment-images/<?= $key ?>-small.png">
                                                        <!-- /payment gateway small image -->
                                                    </legend>
                                                    <!-- /small image legend -->
                                                    <!-- payment gateway big image -->
                                                    <img class="lw-payment-gateway-icon" src="public/assets-pg/imgs/payment-images/<?= $key ?>-big.jpg">
                                                    <!-- /payment gateway big image -->
                                                </fieldset>
                                                <!-- /Fieldset for big image -->
                                            </label>
                                            <!-- /radio button label -->
                                        </div> <?php
                                        // Payment gateway radio buttons with image
                                    }
                                }
                                ?>
                                <br><br>
                                <!-- Iyzico Merchant Form Start here -->
                                <div class="lw-iyzico-form card mb-3" id="cardCheckoutForm">
                                    <!-- Iyzico payment method header -->
                                    <div class="card-header">
                                        <h5 class="card-title display-td">Your Card Details</h5>
                                        <small class="text-danger">All fields are required.</small>
                                    </div>
                                    <!-- /Iyzico payment method header -->

                                    <!-- Card Body start here -->
                                    <div class="card-body mb-3">
                                        <!-- name of card -->
                                        <div class="form-group">
                                            <label for="cname">Name on Card</label>
                                            <input type="text" class="form-control" id="cname" name="cardname" placeholder="John More Doe">
                                        </div>
                                        <!-- / name of card -->

                                        <!-- Card number -->
                                        <div class="form-group">
                                            <label for="cardNumber">Card Number</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="cardNumber" name="cardnumber">
                                                <div class="input-group-append" id="basic-addon1">
                                                    <span class="input-group-text"><i class="fa fa-credit-card"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- / Card number -->

                                        <div class="form-row">
                                            <div class="col">
                                                <!-- Set card expiry month -->
                                                <label for="expmonth">Exp Month</label>
                                                <input type="number" class="form-control" id="expmonth" name="expmonth">
                                                <!-- / Set card expiry month -->
                                            </div>
                                            <div class="col">
                                                <!-- Set card expiry year -->
                                                <label for="expyear">Exp Year</label>
                                                <input type="number" class="form-control" id="expyear" name="expyear">
                                                <!-- / Set card expiry year -->
                                            </div>
                                            <div class="col">
                                                <!-- Set card cvv number -->
                                                <label for="cvv">CVV</label>
                                                <input type="number" class="form-control" id="cvv" name="cvv">
                                                <!-- Set card cvv number -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Card Body start here -->
                                </div>

                                <!-- / Iyzico Merchant Form  -->
                                <h3 id="lwPaymentAmount"></h3>
                                <!-- PayPal Checkout Btn -->
                                <div id="payPalCheckoutForm">
                                    <div class="mt-3" id="paypal-button-container" style="position: relative;z-index: 9;padding: 1px 21px;"></div>
                                </div>
                                <!--  checkout form submit button -->
                                <button type="submit" value="Proceed to Pay" id="ProceedToPayBtn" class="btn btn-lg btn-block btn-success">Proceed to Pay</button>
                                <!-- / checkout form submit button -->
                        </div><?php } ?>
                    <!-- Card body end here -->
                    </div>
                    <!-- /Card end here -->
            </div>
            <!-- /col-md-8 -->
            </form>
            <!-- /payment gateway end -->
        </div>
    </div>
    </div>
    <!-- /Payment form end from here -->
    {{-- <footer class="footer bg-light p-4 text-center">
        PayPage - 2.0.0 - <small class="text-muted"> PHP ready to use Payment Gateway Integrations</small> Design & Developed by <a href="https://livelyworks.net">livelyworks</a>
    </footer> --}}
</body>
<!-- /Page body end from here... -->

</html>
<!-- /Page HTML end here -->
<!-- get configuration data -->
<?php
// Get config data
$config = getPublicConfigItem();
// Get app URL
$paymentPagePath = getAppUrl();

$gatewayConfiguration = $config['payments']['gateway_configuration'];

// get paystack config data
$paystackConfigData = getArrayItem($gatewayConfiguration, 'paystack', []);
// Get paystack callback ur
$paystackCallbackUrl = getAppUrl(getArrayItem($paystackConfigData, 'callbackUrl', ''));
// $paystackCallbackUrl = route('payment-success');

// Get stripe config data
$stripeConfigData = getArrayItem($gatewayConfiguration, 'stripe', []);
// Get stripe callback ur
$stripeCallbackUrl = getAppUrl(getArrayItem($stripeConfigData, 'callbackUrl', ''));
// $stripeCallbackUrl  = route('payment-success');

// Get razorpay config data
$razorpayConfigData = getArrayItem($gatewayConfiguration, 'razorpay', []);
// Get razorpay callback url
$razorpayCallbackUrl = getAppUrl(getArrayItem($razorpayConfigData, 'callbackUrl'));
// $razorpayCallbackUrl = route('payment-success');

// Get Authorize.Net config Data
$authorizeNetConfigData = getArrayItem($gatewayConfiguration, 'authorize-net', []);
// Get Authorize.Net callback url
// $authorizeNetCallbackUrl = route('payment-success');
$authorizeNetCallbackUrl = getAppUrl(getArrayItem($authorizeNetConfigData, 'callbackUrl'));

// Individual payment gateway url
$individualPaymentGatewayAppUrl = getAppUrl('individual-payment-gateways');

// Get payUmoney config data
$payUmoneyConfigData = getArrayItem($gatewayConfiguration, 'payumoney', []);

// Get ravepay config data
$ravepayConfigData = getArrayItem($gatewayConfiguration, 'ravepay', []);
// Get ravepay callback url
$ravepayCallbackUrl = getAppUrl(getArrayItem($ravepayConfigData, 'callbackUrl'));
// $ravepayCallbackUrl = route('payment-success');
// Get Paypal-checkout config data
$paypalCheckOutConfigData = getArrayItem($gatewayConfiguration, 'paypal-checkout', []);
// Get Paypal-checkout callback url
// $paypalCallbackUrl =route('payment-success');
$paypalCallbackUrl = getAppUrl(getArrayItem($paypalCheckOutConfigData, 'callbackUrl'));

// New For Phonepe
// Get razorpay config data
$phonepeConfigData = getArrayItem($gatewayConfiguration, 'phonepe', []);
// Get razorpay callback url
$phonepeCallbackUrl = getAppUrl(getArrayItem($phonepeConfigData, 'callbackUrl'));

?>
<!-- / get configuration data -->

<!-- apply validation for iyzico form data -->
<script type="text/javascript">
    // On load hide iyzico form
    $("#cardCheckoutForm").hide();
    $("#payPalCheckoutForm").hide();

    // Check if payment options are get changed and if iyzico payment gateway is selected then
    // add validation error to form element
    var gatewayConfiguration = <?= json_encode($gatewayConfiguration) ?>,
        userDetails = <?= json_encode($userDetails) ?>;
    $('input[name=paymentOption]').change(function() {
        // Get value of payment option
        var selectedPayment = $('input[name=paymentOption]:checked').val();
        if(selectedPayment != 'paypal-checkout'){
            $("#payPalCheckoutForm").hide();
            $("#ProceedToPayBtn").show();
            $("#paypal-button-container").addClass("disable-btn");
        }else{
            $("#payPalCheckoutForm").show();
            $("#ProceedToPayBtn").hide();
            $("#paypal-button-container").removeClass("disable-btn");
        }
        // Check if iyzico payment method is selected
        var gatewayCurrency = gatewayConfiguration[selectedPayment]['currency'],
            currencySymbol = gatewayConfiguration[selectedPayment]['currencySymbol'],
            formattedAmount = '<hr>' + currencySymbol + ' ' + userDetails['amounts'][gatewayCurrency] + ' ' + gatewayCurrency + '<hr>';



        $('#lwPaymentAmount').html(formattedAmount);
        // then show iyzico form
        if (selectedPayment == 'iyzico' || selectedPayment == 'authorize-net') {
            // Show form element
            $("#cardCheckoutForm").show();
            // add required validation to cname input
            $("#cname").attr("required", "true");
            // add required validation to cardNumber input
            $("#cardNumber").attr("required", "true");
            // add required validation to expmonth input
            $("#expmonth").attr("required", "true");
            // add required validation to expyear input
            $("#expyear").attr("required", "true");
            // add required validation to cvv input
            $("#cvv").attr("required", "true");
        } else {
            // Hide iyzico form
            $("#cardCheckoutForm").hide();
            // Remove validation from cname input
            $("#cname").removeAttr("required", "true");
            // Remove validation from cardNumber input
            $("#cardNumber").removeAttr("required", "true");
            // Remove validation from expmonth input
            $("#expmonth").removeAttr("required", "true");
            // Remove validation from expyear input
            $("#expyear").removeAttr("required", "true");
            // Remove validation from cvv input
            $("#cvv").removeAttr("required", "true");
        }
    });
</script>
 <!-- / apply validation for iyzico form data -->

<!-- Jquery Form submit in script tag -->
<script type="text/javascript">
    $(document).ready(function() {
        // redirect on individual payment page
        $('select#individualPaymentGateway').on('change', function() {
            var paymentMethod = this.value;
            if (paymentMethod != '') {
                window.location.href = "<?= $individualPaymentGatewayAppUrl ?>/" + this.value + '-payment.php';
            } else {
                window.location.href = "<?= $paymentPagePath ?>";
            }
        });

        //submit checkout form
        $('#lwPaymentForm').on('submit', function(e) {
            // Prevent form
            e.preventDefault();
            // Get value of selected payment method
            var paymentOption = $('input[name=paymentOption]:checked').val();
            // Paypal, Paytm, Instamojo or iyzico script for send ajax request to server side start
            if (paymentOption == 'paypal' || paymentOption == 'paytm' || paymentOption == 'instamojo' || paymentOption == 'iyzico' || paymentOption == 'authorize-net' | paymentOption == 'mercadopago' || paymentOption == 'payumoney' || paymentOption == 'mollie' || paymentOption == 'pagseguro' || paymentOption == 'coingate' || paymentOption == 'twoCheckout' || paymentOption == 'phonepe') {

                //show loader before ajax request
                $(".lw-show-till-loading").show();

                //send ajax request with form data to server side processing
                $.ajax({
                    type: 'post', //form method
                    context: this,
                    url: '{{route('payment-process')}}', // post data url
                    dataType: "JSON",
                    data: $('#lwPaymentForm').serialize() + '&' + $.param(JSON.parse('<?php echo json_encode($userDetails) ?>')), // form serialize data
                    error: function(err) {

                        var error = err.responseText,
                        string = '';

                        //on error show alert message
                        string += '<div class="alert alert-danger" role="alert">' + err.responseText + '</div>';

                        $('#lwValidationMessage').html(string);
                        //alert("AJAX error in request: " + JSON.stringify(err.responseText, null, 2));

                        //hide loader after ajax request complete
                        $(".lw-show-till-loading").hide();

                    },
                    success: function(response) {


                        //check server side validation
                        if (typeof(response.validationMessage)) {

                            var messageData = [],
                                string = '';

                            //validation message
                            $.each(response.validationMessage, function(index, value) {
                                messageData = value;
                                string += '<div class="alert alert-danger" role="alert">' + messageData + '</div>';
                            });

                            //print error mesaages
                            $('#lwValidationMessage').html(string);

                            //hide loader after ajax request complete
                            $(".lw-show-till-loading").hide();
                        }

                        //load paytm merchant form
                        if (response.paymentOption == "paytm") {

                            $('body').html(response.merchantForm);

                        } else if (response.paymentOption == "instamojo") {

                            //on success load instamojo long url page else show error message
                            if (response.message == 'success') {
                                //show loader before page load
                                $(".lw-show-till-loading").show();

                                window.location.href = response.longurl;
                            } else {
                                //error message
                                string += '<div class="alert alert-danger" role="alert">' + response.errorMessage + '</div>';

                                //print error mesaages
                                $('#lwValidationMessage').html(string);
                            }

                        } else if (response.paymentOption == "iyzico") {

                            //on success load html content page on iyzico else show error message
                            if (response.status == 'success') {
                                $('body').html(response.htmlContent);

                            } else if (response.message == 'failed') {

                                string += '<div class="alert alert-danger" role="alert">' + response.errorMessage + '</div>';
                            } else {
                                //error message
                                //validation message
                                $.each(response.validationMessage, function(index, value) {
                                    messageData = value;
                                    string += '<div class="alert alert-danger" role="alert">' + messageData + '</div>';
                                });
                            }
                            //print error mesaages
                            $('#lwValidationMessage').html(string);

                        } else if (response.paymentOption == "paypal") {

                            //show loader before page load
                            $(".lw-show-till-loading").show();

                            //on success load paypalUrl page
                            window.location.href = response.paypalUrl;
                        } else if (response.paymentOption == 'authorize-net') {
                            var authorizeNetCallbackUrl = <?php echo json_encode($authorizeNetCallbackUrl); ?>;
                            // console.log(response);
                            if (response.status == "success") {
                                $('body').html("<form action='" + authorizeNetCallbackUrl + "' method='post'><input type='hidden' name='response' value='" + JSON.stringify(response) + "'><input type='hidden' name='paymentOption' value='authorize-net'></form>");
                                $('body form').submit();
                            } else if (response.status == "error") {
                                string = response.message;
                            } else {
                                $.each(response.validationMessage, function(index, value) {
                                    messageData = value;
                                    string += '<div class="alert alert-danger" role="alert">' + messageData + '</div>';
                                });
                            }
                            $('#lwValidationMessage').html(string);
                        } else if (response.paymentOption == 'mercadopago') {
                            if (response.status == 'success') {
                                window.location.href = response.redirect_url;
                            } else if (response.status == 'error') {
                                $(".lw-show-till-loading").hide();
                                var string = '';
                                //on error show alert message
                                string += '<div class="alert alert-danger" role="alert">' + response.message + '</div>';
                                $('#lwValidationMessage').html(string);
                            }
                        } else if (response.paymentOption == 'payumoney') {
                            $('body').html(response.merchantForm);

                        } else if (response.paymentOption == "mollie") {
                            if (response.message == 'success') {
                                window.location.href = response.checkoutUrl;
                            } else if (response.message == 'failed') {
                                $(".lw-show-till-loading").hide();
                                var string = '';
                                //on error show alert message
                                string += '<div class="alert alert-danger" role="alert">' + response.errorMessage + '</div>';
                                $('#lwValidationMessage').html(string);
                            }
                        } else if (response.paymentOption == "pagseguro") {
                            //check status is success
                            if (response.status == 'success') {
                                window.location.href = response.redirect_url;
                            } else if (response.status == 'error') {
                                $(".lw-show-till-loading").hide();
                                var string = '';
                                //on error show alert message
                                string += '<div class="alert alert-danger" role="alert">' + response.message + '</div>';
                                $('#lwValidationMessage').html(string);
                            }
                        }else if (response.paymentOption == "coingate"){
                            window.location.href = response.payment_url;
                        }

                        else if (response.paymentOption == "phonepe"){
                            window.location.href = response.payment_url;
                        }
                    }
                });
                // Paypal, Paytm, Instamojo or iyzico script for send ajax request to server side end

                // Paystack script for send ajax request to server side start
            } else if (paymentOption == 'paystack') {

                //config data
                var configData = <?php echo json_encode($config); ?>,
                    paymentPagePath = <?php echo json_encode($paymentPagePath); ?>,
                    configItem = configData['payments']['gateway_configuration']['paystack'],
                    paystackCallbackUrl = configItem.callbackUrl,
                    userDetails = <?php echo json_encode($userDetails); ?>;

                const amount = userDetails['amounts'][configItem['currency']];

                var paystackPublicKey = '';

                //check paystack test or production mode
                if (configItem['testMode']) {
                    paystackPublicKey = configItem['paystackTestingPublicKey'];
                } else {
                    paystackPublicKey = configItem['paystackLivePublicKey'];
                }

                var paystackAmount = amount.toFixed(2) * 100,
                    handler = PaystackPop.setup({
                        key: paystackPublicKey, // add paystack public key
                        email: userDetails['payer_email'], // add customer email
                        amount: paystackAmount, // add order amount
                        currency: configItem['currency'], // add currency
                        callback: function(response) {
                            // after successful paid amount return paystack reference Id
                            var paystackReferenceId = response.reference;

                            //show loader before ajax request
                            $(".lw-show-till-loading").show();

                            var paystackData = {
                                'paystackReferenceId': paystackReferenceId,
                                'paystackAmount': paystackAmount
                            };

                            var paystackData = $('#lwPaymentForm').serialize() + '&' + $.param(userDetails) + '&' + $.param(paystackData);

                            $.ajax({
                                type: 'post', //form method
                                context: this,
                                url: '{{route('payment-process')}}', // post data url
                                dataType: "JSON",
                                data: paystackData, // form serialize data
                                error: function(err) {
                                    var error = err.responseText
                                    string = '';

                                    //on error show alert message
                                    string += '<div class="alert alert-danger" role="alert">' + err.responseText + '</div>';

                                    $('#lwValidationMessage').html(string);
                                    //alert("AJAX error in request: " + JSON.stringify(err.responseText, null, 2));

                                    //hide loader after ajax request complete
                                    $(".lw-show-till-loading").hide();
                                },
                                success: function(response) {
                                    if (response.status == true) {
                                        $('body').html("<form action='" + paystackCallbackUrl + "' method='post'><input type='hidden' name='response' value='" + JSON.stringify(response) + "'><input type='hidden' name='paymentOption' value='paystack'></form>");
                                        $('body form').submit();
                                    }
                                }
                            });

                        },
                        onClose: function() {
                            //on close paystack inline widget then load back to checkout form page
                            // window.location.href = paymentPagePath;
                        }
                    });

                //open paystack inline widen using iframe
                handler.openIframe();
                // Paystack script for send ajax request to server side end


                // Stripe script for send ajax request to server side start
            } else if (paymentOption == 'stripe') {

                //config data
                var configData = <?php echo json_encode($config); ?>,
                    configItem = configData['payments']['gateway_configuration']['stripe'],
                    userDetails = <?php echo json_encode($userDetails); ?>,
                    stripePublishKey = '';
                $(".lw-show-till-loading").show();

                //check stripe test or production mode
                if (configItem['testMode']) {
                    stripePublishKey = configItem['stripeTestingPublishKey'];
                } else {
                    stripePublishKey = configItem['stripeLivePublishKey'];
                }

                userDetails['paymentOption'] = paymentOption;

                // Stripe script for send ajax request to server side start
                $.ajax({
                    type: 'post', //form method
                    context: this,
                    url: '{{route('payment-process')}}', // post data url
                    dataType: "JSON",
                    data: userDetails, // form serialize data
                    error: function(err) {
                        var error = err.responseText
                        string = '';

                        //on error show alert message
                        string += '<div class="alert alert-danger" role="alert">' + err.responseText + '</div>';

                        $('#lwValidationMessage').html(string);
                        //alert("AJAX error in request: " + JSON.stringify(err.responseText, null, 2));

                        //hide loader after ajax request complete
                        $(".lw-show-till-loading").hide();
                    },
                    success: function(response) {

                        var stripe = Stripe(stripePublishKey);
                        if (typeof response.id !== "undefined") {
                            stripe.redirectToCheckout({
                                // Make the id field from the Checkout Session creation API response
                                // available to this file, so you can provide it as parameter here
                                // instead of the CHECKOUT_SESSION_ID placeholder.
                                sessionId: response.id,
                            }).then(function(result) {
                                // If `redirectToCheckout` fails due to a browser or network
                                // error, display the localized error message to your customer
                                // using `result.error.message`.
                                var string = '';
                                //on error show alert message
                                string += '<div class="alert alert-danger" role="alert">' + result.error.message + '</div>';

                                $('#lwValidationMessage').html(string);
                            });
                        } else if (response.message == "failed") {
                            $(".lw-show-till-loading").hide();
                            var string = '';
                            //on error show alert message
                            string += '<div class="alert alert-danger" role="alert">' + response.errorMessage + '</div>';
                            $('#lwValidationMessage').html(string);
                        }
                    }
                });

                // Razorpay script for send ajax request to server side start
            } else if (paymentOption == 'razorpay') {

                //config data
                var configData = <?php echo json_encode($config); ?>,
                    razorpayCallbackUrl = <?php echo json_encode($razorpayCallbackUrl); ?>,
                    paymentPagePath = <?php echo json_encode($paymentPagePath); ?>,
                    configItem = configData['payments']['gateway_configuration']['razorpay'],
                    userDetails = <?php echo json_encode($userDetails); ?>,
                    razorpayKeyId = '';

                const amount = userDetails['amounts'][configItem['currency']];

                //check razorpay test or production mode
                if (configItem['testMode']) {

                    razorpayKeyId = configItem['razorpayTestingkeyId'];
                } else {
                    razorpayKeyId = configItem['razorpayLivekeyId'];
                }

                var razorpayAmount = amount * 100,

                    razorpayPaymentId = null,
                    options = {
                        "key": razorpayKeyId, // add razorpay Api Key Id
                        "amount": razorpayAmount, // 2000 paisa = INR 20
                        "currency": configItem['currency'], // add currency
                        "name": configItem['merchantname'], // add merchant user name
                        "handler": function(response) {
                            // after successful paid amount return razorpay payment Id
                            razorpayPaymentId = response.razorpay_payment_id;
                            var encodeRazorpayAmount = window.btoa(razorpayAmount);

                            //show loader before ajax request
                            $(".lw-show-till-loading").show();

                            var razorpayData = {
                                'razorpayPaymentId': razorpayPaymentId,
                                'razorpayAmount': encodeRazorpayAmount
                            };

                            var razorpayData = $('#lwPaymentForm').serialize() + '&' + $.param(userDetails) + '&' + $.param(razorpayData);


                            $.ajax({
                                type: 'post', //form method
                                context: this,
                                url: '{{route('payment-process')}}',
                                dataType: "JSON",
                                data: razorpayData, // form serialize data
                                error: function(err) {
                                    var error = err.responseText
                                    string = '';

                                    //on error show alert message
                                    string += '<div class="alert alert-danger" role="alert">' + err.responseText + '</div>';

                                    $('#lwValidationMessage').html(string);
                                    //alert("AJAX error in request: " + JSON.stringify(err.responseText, null, 2));

                                    //hide loader after ajax request complete
                                    $(".lw-show-till-loading").hide();
                                },
                                success: function(response) {
                                    if (response.status == "captured") {
                                        // razorpayCallbackUrl = razorpayCallbackUrl + '?orderId=' + userDetails['order_id'];
                                        razorpayCallbackUrl = razorpayCallbackUrl + '?orderId=' + userDetails['order_id'] + '&payid=' + userDetails['pay_id'];

                                        $('body').html("<form action='" + razorpayCallbackUrl + "' method='post'><input type='hidden' name='response' value='" + JSON.stringify(response) + "'><input type='hidden' name='paymentOption' value='razorpay'></form>");
                                        $('body form').submit();
                                    }
                                }
                            });

                            //after successful payment go to response page
                            /* window.location.href = razorpayCallbackUrl+'?paymentOption='+paymentOption+'&razorpayPaymentId='+razorpayPaymentId+'&amount='+encodeRazorpayAmount; */
                        },
                        "prefill": {
                            "name": userDetails['payer_name'], // add user name
                            "email": userDetails['payer_email'], // add user email
                        },
                        "theme": {
                            "color": configItem['themeColor'], // add widget theme color
                        },
                        "modal": {
                            "ondismiss": function(e) {
                                // if razorpay payment Id is not received on onDismiss razorpay inline widget then load Url back to checkout form page
                                if (razorpayPaymentId == null) {
                                    //window.location.href = paymentPagePath;
                                }
                            }
                        }
                    };
                var rzp1 = new Razorpay(options);
                rzp1.open();

                // Ravepay script for send ajax request to server side start
            } else if (paymentOption == 'ravepay') {
                //config data
                var configData = <?php echo json_encode($config); ?>,
                    ravepayCallbackUrl = <?php echo json_encode($ravepayCallbackUrl); ?>,
                    paymentPagePath = <?php echo json_encode($paymentPagePath); ?>,
                    configItem = configData['payments']['gateway_configuration']['ravepay'],
                    userDetails = <?php echo json_encode($userDetails); ?>,
                    ravepayPublicKeyId = '';

                const amount = userDetails['amounts'][configItem['currency']];

                //check ravepay test or production mode
                if (configItem['testMode']) {
                    ravepayPublicKeyId = configItem['testPublicApiKey'];
                } else {
                    ravepayPublicKeyId = configItem['livePublicApiKey'];
                }

                var ravepayAmount = amount,
                    x = getpaidSetup({
                        PBFPubKey: ravepayPublicKeyId,
                        customer_email: userDetails['payer_email'], // add customer email
                        amount: ravepayAmount, // add order amount
                        currency: configItem['currency'], // add currency
                        txref: configItem['txn_reference_id'], // Pass your UNIQUE TRANSACTION REFERENCE HERE.
                        onclose: function() {
                            //on close paystack inline widget then load back to checkout form page
                            // window.location.href = paymentPagePath;
                        },
                        callback: function(response) {

                            var ravepayTxnRefId = response.tx.txRef; // collect txRef returned and pass to a server page to complete status check.

                            if (
                                response.tx.chargeResponseCode == "00" ||
                                response.tx.chargeResponseCode == "0"
                            ) {
                                // redirect to a success page
                                //show loader before ajax request
                                $(".lw-show-till-loading").show();

                                var ravepayData = {
                                    'ravepayTxnRefId': ravepayTxnRefId,
                                    'ravepayAmount': ravepayAmount
                                };

                                var ravepayData = $('#lwPaymentForm').serialize() + '&' + $.param(userDetails) + '&' + $.param(ravepayData);

                                $.ajax({
                                    type: 'post', //form method
                                    context: this,
                                    url: '{{route('payment-process')}}', // post data url
                                    dataType: "JSON",
                                    data: ravepayData, // form serialize data
                                    error: function(err) {
                                        var error = err.responseText
                                        string = '';

                                        //on error show alert message
                                        string += '<div class="alert alert-danger" role="alert">' + err.responseText + '</div>';

                                        $('#lwValidationMessage').html(string);
                                        //alert("AJAX error in request: " + JSON.stringify(err.responseText, null, 2));

                                        //hide loader after ajax request complete
                                        $(".lw-show-till-loading").hide();
                                    },
                                    success: function(response) {
                                        //check response code is success
                                        if (response.body.status == 'success') {
                                            $('body').html("<form action='" + ravepayCallbackUrl + "' method='post'><input type='hidden' name='response' value='" + JSON.stringify(response) + "'><input type='hidden' name='paymentOption' value='ravepay'></form>");
                                            $('body form').submit();
                                        }
                                    }
                                });

                            } else {
                                // redirect to a failure page.
                            }

                            x.close(); // use this to close the modal immediately after payment.
                        }
                    });
            }

        });


        var configData = <?php echo json_encode($config); ?>,
        configItem = configData['payments']['gateway_configuration']['paypal-checkout'];
        if (configItem['enable'] == true) {
            var userDetails = <?php echo json_encode($userDetails); ?>;
                userDetails['paymentOption'] = 'paypal-checkout';

            paypal.Buttons({
                createOrder() {
                    return fetch('{{route('payment-process')}}', {
                            method: "post",
                            headers: {
                                'content-type': 'application/json',
                            },
                            body: JSON.stringify(userDetails),
                        })
                        .then((response) => {
                            return response.json();
                        })
                        .then((order) => {
                            return order.id;
                        });
                },
                // Finalize the transaction on the server after payer approval
                onApprove(responseData) {
                    return fetch('{{route('payment-process')}}', {
                            method: "post",
                            headers: {
                                'content-type': 'application/json'
                            },
                            body: JSON.stringify({
                                'responseData' : responseData,
                                'userDetails' : userDetails,
                            })
                        })
                        .then((response) => {
                            return response.json();
                        })
                        .then((orderData) => {
                            if(orderData.status == 'COMPLETED'){
                                //on success load paypalUrl page
                                window.location.href = orderData.successUrl;
                            }
                        });
                },
                onError: function(err) {
                    // Show an error page here, when an error occurs
                    console.log(err.message);
                },
                onCancel: function(oncancel) {
                    console.log(oncancel);
                }
            }).render('#paypal-button-container');
        }
    });
</script>


<?php
if (getArrayItem($paystackConfigData, 'enable', false)) {
    ?>
    <!-- load paystack inline widget script -->
    <script type="text/javascript" src="https://js.paystack.co/v1/inline.js"></script>
    <!-- / load Paystack inline widget script -->
<?php } ?>

<?php
if (getArrayItem($stripeConfigData, 'enable', false)) {
    ?>
    <!-- load stripe inline widget script -->
    <script type="text/javascript" src="https://js.stripe.com/v3"></script>
    <!-- load stripe inline widget script -->
<?php } ?>

<?php
if (getArrayItem($razorpayConfigData, 'enable', false)) {
    ?>
    <!-- load razorpay inline widget script -->
    <script type="text/javascript" src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <!-- load razorpay inline widget script -->
<?php } ?>

<?php
if (getArrayItem($ravepayConfigData, 'enable', false)) {
    ?>
    <!-- load ravepay inline widget script -->
    <script type="text/javascript" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <!-- load ravepay inline widget script -->
<?php } ?>

<?php
if (getArrayItem($paypalCheckOutConfigData, 'enable', false)) {
    $paypalClientId = '';
    if (configItemData('payments.gateway_configuration.paypal-checkout.testMode')) {
        $paypalClientId = configItemData('payments.gateway_configuration.paypal-checkout.paypalTestingClientKey');
    } else {
        $paypalClientId = configItemData('payments.gateway_configuration.paypal-checkout.paypalLiveClientKey');
    }
    $currency = configItemData('payments.gateway_configuration.paypal-checkout.currency');
    ?>
    <script type="text/javascript" src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClientId ?>&currency=<?= $currency ?>"></script>
<?php } ?>
