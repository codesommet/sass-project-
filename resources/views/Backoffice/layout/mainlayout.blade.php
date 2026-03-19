<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Dreams Rent | Template</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('/build/img/favicon.png') }}">

    @if(app()->getLocale() === 'ar')
        <!-- Arabic Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    @endif

    @include('layout.partials.head')

    @if(app()->getLocale() === 'ar')
        <!-- RTL CSS -->
        <link rel="stylesheet" href="{{ URL::asset('admin_assets/css/rtl.css') }}">
    @endif
</head>
@if (!Route::is(['coming-soon', 'error-404', 'error-500', 'maintenance','index-4']))

    <body>
@endif
@if (Route::is(['coming-soon', 'error-404', 'error-500', 'maintenance']))

    <body class="error-page">
@endif
@if (Route::is(['index-4']))

    <body class="home-two">
@endif
@if (!Route::is(['forgot-password', 'login', 'register', 'reset-password', 'index-3', 'listing-grid', 'listing-list','listing-map']))
    <div class="main-wrapper">
@endif
@if (Route::is(['forgot-password', 'login', 'register', 'reset-password']))
    <div class="main-wrapper login-body">
@endif
@if (Route::is(['index-3', 'index']))
    <div class="main-wrapper home-three">
@endif
@if (Route::is(['listing-grid', 'listing-list']))
    <div class="main-wrapper listing-page">
@endif
@if (Route::is(['listing-map']))
    <div class="main-wrapper listing-page map-page">
@endif
@if (!Route::is(['coming-soon', 'error-404', 'error-500', 'maintenance']))
    @include('layout.partials.header')
@endif
@yield('content')
@if (!Route::is(['coming-soon', 'error-404', 'error-500', 'maintenance']))
    @include('layout.partials.footer')
@endif
</div>
@component('components.scrolltotop')
@endcomponent
@include('layout.partials.footer-scripts')
@component('components.modalpopup')
@endcomponent
</body>

</html>
