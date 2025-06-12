<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/sass/app.scss'])
</head>

<body>
    <div>
        {{-- <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="" style="width: 150px; height: 150px;"> --}}

        @php
            $specialRoutes = ['login', 'register'];
        @endphp

        <!-- HEADER -->
        @if (in_array(Route::currentRouteName(), $specialRoutes))
            @include('header')
        @endif

        @if (!in_array(Route::currentRouteName(), $specialRoutes))
            <div class="flex flex-col items-center mt-5">
                <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="" style="width: 150px; height: 150px;">
            </div>
        @endif


        <div class="container flex flex-col items-center">
            <div class="w-full sm:max-w-md mt-6 shadow-md overflow-hidden sm:rounded-lg my-4 "
                style="background-color: #DBEAD5; padding: 2rem;">
                {{ $slot }}
            </div>
        </div>

        <!-- FOOTER -->
        @if (in_array(Route::currentRouteName(), $specialRoutes))
            @include('footer')
        @endif
    </div>
</body>

</html>
