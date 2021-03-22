<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ URL::asset('img/site.webmanifest') }}">

    <title>{{ config('app.name') }} - @yield('title')</title>
    <meta property="og:title" content="{{ config('app.name') }} - @yield('title')" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:image" content="{{ URL::asset('img/og-image.png') }}" />
    <meta property="og:image:secure_url" content="{{ URL::asset('img/og-image.png') }}" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="1200" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="@yield('description', 'The Nuclear Project website provides you with a set of tools created by its faction members to aid in the Torn journey')" />

    <!-- Fonts -->
{{--        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">--}}

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script type="text/javascript" charset="utf8" src="{{ mix('/js/app.js') }}"></script>
    @stack('scripts')

</head>
<body>
    @yield('content')
</body>
</html>
