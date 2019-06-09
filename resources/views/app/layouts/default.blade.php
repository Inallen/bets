<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/weui.css') }}" rel="stylesheet">
    @yield('link')
</head>
<body ontouchstart>
    <div class="container" id="container">
        <div class="page" id="app">
            <router-view></router-view>
            {{--<div class="page__hd">
                <h1 class="page__title">Grid</h1>
                <p class="page__desc">九宫格</p>
            </div>--}}
            {{--<div class="page__bd" style="height: 100%;">
                @yield('content')
            </div>--}}
        </div>


    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
