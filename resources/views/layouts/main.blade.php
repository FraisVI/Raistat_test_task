<!doctype html>
<html lang="ru">

<head>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Сделка</title>
</head>
<body>
    @yield('content')
<script src="{{ asset( 'js/bootstrap.min.js' ) }}"></script>
</body>
</html>
