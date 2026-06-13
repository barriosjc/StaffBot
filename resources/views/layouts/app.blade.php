<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>{{ config('app.name', 'Medi Nexus') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon_chatbot.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon_chatbot.png') }}">
    {{-- <link rel="icon" href="{{ asset('img/favicon.ico') }}"> --}}

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <!-- Include base CSS (optional) -->
    <link href="{{ asset('libs/sbadmin/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    @livewireStyles
</head>

<body class="nav-fixed">
    @include('includes.header')

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('includes.sidebar')
        </div>
        <div id="layoutSidenav_content" class="mt-3">
            @include('utiles.alerts')
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
            @include('includes.footer')
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 Bundle (incluye Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Feather Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0"></script>

    <!-- Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- SB Admin Scripts -->
    <script src="{{ asset('libs/sbadmin/js/scripts.js') }}"></script>

    <!-- Custom Util Scripts -->
    <script src="{{ asset('js/util.js') }}"></script>
    @stack('scripts')
    @livewireScripts
</body>
</html>