<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body>
<h1>{{ $subject }}</h1>

<div class="text">
    @if (is_array($data))
        @foreach ($data as $key => $value)
            <strong>{{ $key }}:</strong> {{ $value }}<br>
        @endforeach
    @endif
</div>
</body>
</html>
