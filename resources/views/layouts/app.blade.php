<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LembangFresh - Sistem Informasi GIS Hortikultura Lembang')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        :root {
            --forest-green: #245831;
            --forest-dark: #163d20;
            --forest-light: #2e7340;
            --lime-accent: #a3e635;
            --lime-hover: #bef264;
            --bg-canvas: #f8faf7;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-canvas);
            color: #1e293b;
        }

        .bg-forest {
            background-color: var(--forest-green) !important;
        }

        .bg-forest-dark {
            background-color: var(--forest-dark) !important;
        }

        .text-forest {
            color: var(--forest-green) !important;
        }

        .text-lime {
            color: var(--lime-accent) !important;
        }

        .bg-lime {
            background-color: var(--lime-accent) !important;
        }

        .btn-lime {
            background-color: var(--lime-accent);
            color: var(--forest-dark) !important;
            border: 1px solid var(--lime-accent);
            font-weight: 600;
        }

        .btn-lime:hover {
            background-color: var(--lime-hover);
            color: var(--forest-dark) !important;
        }

        .btn-forest {
            background-color: var(--forest-green);
            color: #ffffff !important;
            border: 1px solid var(--forest-green);
            font-weight: 600;
        }

        .btn-forest:hover {
            background-color: var(--forest-dark);
            color: #ffffff !important;
        }

        .hover-lime:hover {
            color: var(--lime-accent) !important;
        }

        .hover-green:hover {
            color: var(--forest-green) !important;
        }

        .transition-all {
            transition: all 0.25s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-card {
            border-left: 4px solid var(--forest-green);
        }

        #gisMap, #mainMap, #pickerMap {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            z-index: 1;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column h-100">

    @include('partials.navbar')

    <main class="flex-shrink-0 my-4">
        <div class="container">

            <x-alert />

            @yield('content')
        </div>
    </main>

    @include('partials.footer')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

