<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
</head>
<body>
<h1>QR Code Generator</h1>
<form action="{{ url('qr-code') }}" method="GET">
    <label for="count">Введите количество QR-кодов:</label>
    <input type="number" id="count" name="count" min="1" required>
    <button type="submit">Сгенерировать</button>
</form>

@if(isset($urls))
    <h2>Сгенерированные QR-коды:</h2>
    <ul>
        @foreach($urls as $url)
            <li>{{ $url }}</li>
        @endforeach
    </ul>
@endif
</body>
</html>
