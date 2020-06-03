<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Авторизация</title>

    <!-- Fonts -->
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> --}}

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ mix('/css/app.css') }}">
</head>
<body>
    <div id="app">
        @include('partials.header')

        <div class="content-block auth-content-block">
            @yield('content')
        </div>

        @include('partials.footer')
    </div>
</body>
</html>
