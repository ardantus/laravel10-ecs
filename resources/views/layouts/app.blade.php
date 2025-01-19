<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <header>
        <h1>{{ config('app.name', 'Laravel App') }}</h1>
    </header>
    <main>
        @yield('content')
    </main>
    <footer>
        <p class="text-center">&copy; {{ date('Y') }} Laravel App</p>
    </footer>
</body>
</html>
