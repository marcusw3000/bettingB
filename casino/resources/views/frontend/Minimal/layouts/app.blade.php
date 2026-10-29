<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-prefetch" content="true">
    <title>@yield('page-title') - {{ settings('app_name') }}</title>
    <link rel="stylesheet" href="/minimal/css/style.css?v={{ @filemtime(base_path('../minimal/css/style.css')) ?: 1 }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="/minimal/js/turbo.es2017-umd.js?v={{ @filemtime(base_path('../minimal/js/turbo.es2017-umd.js')) ?: 1 }}" data-turbo-track="reload"></script>
    @yield('styles')
</head>

<body>
    <div class="app-container">
        @include('frontend.Minimal.partials.navbar')

        <main class="main-content">
            @yield('content')
        </main>

        <footer class="main-footer">
            <p>&copy; {{ date('Y') }} {{ settings('app_name') }}. All rights reserved.</p>
        </footer>
    </div>

    @include('frontend.Minimal.partials.modals')

    <script src="/frontend/Default/js/jquery-3.4.1.min.js"></script>
    <script src="/minimal/js/app.js?v={{ @filemtime(base_path('../minimal/js/app.js')) ?: 1 }}"></script>
    <script>
        if (window.Turbo) {
            Turbo.config.forms.mode = 'off';
            document.addEventListener('turbo:before-visit', function (event) {
                const destination = new URL(event.detail.url, window.location.origin);
                if (destination.pathname.startsWith('/game/')) {
                    event.preventDefault();
                    window.location.assign(destination.href);
                }
            });
        }
    </script>
    @yield('scripts')
</body>

</html>
