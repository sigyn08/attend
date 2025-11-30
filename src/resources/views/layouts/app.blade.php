<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}?v={{ time() }}">
    @yield('css')
</head>

<body>
    <main>
        @yield('content')
    </main>
</body>

</html>