<!DOCTYPE html>
<html>
<head>
    <title>QR-код</title>
    <style>
        body { text-align: center; font-family: Arial, sans-serif; }
        img { max-width: 300px; }
    </style>
</head>
<body>
<h1>QR-код</h1>
<img src="{{ $qr_url }}" alt="QR Code">
<p>{{ $qr_url }} </p>
</body>
</html>
