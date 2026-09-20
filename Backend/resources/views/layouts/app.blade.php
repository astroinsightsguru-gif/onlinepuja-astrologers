<!doctype html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ZEGO Demo</title>
  <script src="https://cdn.zegocloud.com/libs/zego-express-rtc/2.20.0/ZegoExpressWebRTC.min.js"></script>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; padding: 20px; }
    .user-card { border:1px solid #ddd; padding:10px; margin:8px 0; display:flex; justify-content:space-between; align-items:center; }
    button { padding:8px 12px; cursor:pointer; }
  </style>
</head>
<body>
  
  <hr>
  <div>
    @yield('content')
  </div>

  <script>
  // Setup global AJAX headers
  (function(){
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    window._csrf = token;
  })();
  </script>
</body>
</html>
