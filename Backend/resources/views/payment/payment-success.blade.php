<?php
// include header file
// include 'header.php';
define('PAY_PAGE_CONFIG', config('paypage-payment'));
// Get config data
$configData = configItem();
?>
<!DOCTYPE html>
<!-- Html Start -->
<html>
<!-- Head Start -->

<head>
    <!-- Page Title -->
    <title>Pay Page</title>
    <!-- /Page Title -->
    <!-- Load load bootstrap and fontawesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- /Load load bootstrap and fontawesome -->
</head>
<!-- /Head End -->
<!-- Body Start -->
@php
    if(astroauthcheck()){
        $redirect_url = route('front.getAstrologerWallet');
    } else {
        $redirect_url = route('front.getMyWallet');
    }
@endphp

<body>
    <div class="text-center mt-5">
        <div class="col-lg-12 text-center">
            <!-- Thanks message -->
            <h3>Thanks for your payment</h3>
            <!-- Success Icon -->
            <i class="fa fa-check-square-o fa-5x text-success"></i>
            <!-- /Success Icon -->
            <h1>Payment succeed</h1>
            <!-- /Thanks message -->
            <p>You will be redirected in <span id="countdown">5</span> seconds...</p>
            <!-- URL for back to checkout form -->
            <a href="<?= $redirect_url ?>" title="Back to Checkout Form">Back to Checkout Form</a>
            <!-- /URL for back to checkout form -->
        </div>
    </div>

    <script>
        let countdown = 5;
        const countdownElement = document.getElementById("countdown");

        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = "<?= $redirect_url ?>";
            }
        }, 1000);
    </script>
</body>

<!-- /Body Start -->

<script>
      setTimeout(function() {
        if (window.opener) {
            window.close();
        }
    }, 3000);  // Close after 5 seconds
</script>


</html>
<!-- /Html End -->
