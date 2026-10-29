<!doctype html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b0e14">
    <title>{{ $spaMeta['title'] }}</title>
    <meta name="description" content="{{ $spaMeta['description'] }}">
    <link rel="stylesheet" href="/minimal/css/tailwind.css?v={{ @filemtime(base_path('../minimal/css/tailwind.css')) ?: 1 }}">
    @foreach($spaCss as $stylesheet)
        <link rel="stylesheet" href="{{ $stylesheet }}">
    @endforeach
</head>
<body class="min-h-screen bg-[#0b0e14] text-slate-50">
    <div id="promex-spa"></div>
    <script>window.__PROMEX_SPA_PATH__ = @json($spaInitialPath);</script>
    <script type="module" src="{{ $spaEntry }}"></script>
</body>
</html>
