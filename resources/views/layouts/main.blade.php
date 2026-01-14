<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <title>Kandang Kopi - @yield('title')</title>
</head>
<body>
    @include('partials.sidebar') {{-- Memanggil sidebar dari patrial --}}

    <main class="main-content">
        @include('partials.navbar') {{-- Memanggil sidebar dari patrial --}}

        @yield('container')
    </main>

    @include('partials.logout-modal')
    
    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('scripts') 
</body>
</html>