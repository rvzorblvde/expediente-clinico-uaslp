<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Unifarmacia')</title>
    <!-- Asegúrate de tener configurado app.css en tu vite.config.js con Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
@yield('content')
</body>
</html>
