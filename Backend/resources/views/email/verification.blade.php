<!--<h1>Forget Password Email</h1>-->


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Account Verification Notification</title>
    <style>
        /* Inline styles for simplicity, consider using CSS classes for larger templates */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f1f1f1;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
        }

        .message {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
        }

        .message p {
            margin-bottom: 10px;
        }


        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
@php
     $appName = DB::table('systemflag')
            ->where('name', 'AppName')
            ->select('value')
            ->first();

        $logo = DB::table('systemflag')
        ->where('name', 'AdminLogo')
        ->select('value')
        ->first();

        $company_email=DB::table('systemflag')->where('name','siteemail')->first();
            $company_address=DB::table('systemflag')->where('name','siteaddress')->first();
            $company_number=DB::table('systemflag')->where('name','sitenumber')->first();
@endphp

<body>
    <div class="container" style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border: 1px solid #e5e5e5;">
        <div class="logo" style="text-align: center; margin-bottom: 20px;">
            <img src="{{asset($logo->value)}}" alt="Logo" style="max-width: 90px;">
        </div>

        <div class="message" style="padding: 20px; background-color: #ffffff; border-radius: 5px;">
            <p style="font-size: 16px;">Hello {{ $name }},
                 Your Account has been {{$verifyStatus}} From Admin.</p>
            
            <p style="font-size: 16px;">Thank you,</p>
            <p style="font-size: 16px;">{{$appName->value}}</p>
        </div>

      

        <div class="footer" style="font-size: 12px; text-align: center; color: #555555; margin-top: 20px;">
            <p>Contact us: {{$company_email->value}}</p>
            <p>{{$company_address->value}}</p>
            <p>&copy; {{date('Y')}} {{$appName->value}}. All rights reserved.</p>
        </div>
    </div>
</body>


</html>
