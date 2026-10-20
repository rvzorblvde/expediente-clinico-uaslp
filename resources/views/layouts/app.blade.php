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

    {{-- Formulario oculto para el cierre de sesión por inactividad --}}
    @auth
        <form id="logout-inactivity-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <script>
            (function() {
                // 10 minutos en milisegundos (10 * 60 * 1000)
                const INACTIVITY_TIMEOUT = 10 * 60 * 1000; 
                let inactivityTimer;

                function logoutUser() {
                    // Evitar múltiples envíos si ya expiró
                    clearTimeout(inactivityTimer);
                    // Alerta antes de salir
                    alert('Tu sesión ha expirado por inactividad.');
                    document.getElementById('logout-inactivity-form').submit();
                }

                function resetTimer() {
                    clearTimeout(inactivityTimer);
                    inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT);
                }

                // Eventos que consideran actividad del usuario
                const events = ['mousemove', 'keydown', 'mousedown', 'touchstart', 'scroll'];

                events.forEach(event => {
                    window.addEventListener(event, resetTimer, true);
                });

                // Inicializar el temporizador al cargar la página
                resetTimer();
            })();
        </script>
    @endauth
</body>
</html>