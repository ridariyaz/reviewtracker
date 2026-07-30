<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QR · ReviewTracker</title>
  <style>
    body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#020617; }
    img { width:min(80vw, 420px); background:#fff; padding:16px; border-radius:16px; }
    a { position:fixed; top:16px; left:16px; color:#93c5fd; text-decoration:none; }
  </style>
</head>
<body>
  <a href="{{ route('employee.dashboard') }}">← Back</a>
  <img src="{{ asset('storage/qrcodes/'.$employeeId.'.png') }}" alt="Employee QR">
</body>
</html>
