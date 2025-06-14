<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset('assets/img/TSTShop/LogoTSTFashionShop.webp')}}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="/">
    {{-- <title>Trang chủ</title> --}}
    <title>@yield('title', 'Trang Chủ')</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('client/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/chatbox.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('client/css/modal.css') }}" />
    @yield('css')
</head>

<body>

    @include('sites.components._header')
    @include('sites.components._chatbox_and_search')
    @yield('content')
    @include('sites.components._footer')

    <!-- Js Plugins -->
    <script src="{{ asset('client/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('client/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('client/js/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('client/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('client/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('client/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('client/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('client/js/owl.carousel.min.js') }}"></script>

    <script src="{{ asset('client/js/main.js') }}"></script>

    @yield('js')
    <script src="{{ asset('client/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('client/js/generate-star.js') }}"></script> --}}


</body>

</html>
