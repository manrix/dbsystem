<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}@hasSection('title') - @yield('title')@endif</title>
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Styles -->
    @section('head_styles')
        <link href="{{ asset(mix('app.css')) }}" rel="stylesheet">
    @show
    @section('styles')@show
    @section('head_scripts')
        <script>
            window.APP = {
                name: '{{ config('app.name') }}',
                version: '{{ config('app.version') }}',
                baseUrl: '{{ route('backend') }}',
                user: {!! auth()->user() ? json_encode(auth()->user()) : null !!},
            }
        </script>
    @show
</head>
<body>
    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset(mix('vendor.js')) }}"></script>
    <script src="{{ asset(mix('font-awesome.js')) }}"></script>
    @section('scripts')
        <script src="{{ asset(mix('app.js')) }}"></script>
    @show
</body>
</html>
