<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WhatsApp Web')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            background-color: #d1d7db; /* WhatsApp Web background gray */
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }

        #app {
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app">
        @yield('main-content')
    </div>

    @stack('scripts')
</body>
</html>
