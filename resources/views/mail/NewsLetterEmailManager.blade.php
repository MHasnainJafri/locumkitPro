<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>Testing Mail</p>
    @isset($customMessage)
        <p>{{ $customMessage }}</p>
    @else
        <p>No message provided.</p>
    @endisset
</body>
</html>
