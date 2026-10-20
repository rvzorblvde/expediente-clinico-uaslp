@php
    // Definimos una paleta de colores profesionales para los avatares
    $coloresAvatar = [
        'bg-indigo-600',
        'bg-emerald-600',
        'bg-violet-600',
        'bg-fuchsia-600',
        'bg-amber-600',
        'bg-teal-600',
        'bg-rose-600',
        'bg-cyan-700'
    ];

    // Seleccionamos un color basado en el ID del usuario (así su color es consistente)
    // O si prefieres que cambie siempre, usa array_rand($coloresAvatar)
    $colorAleatorio = $coloresAvatar[auth()->user()->idUsuario % count($coloresAvatar)];
@endphp

<nav class="bg-[#004a98] shadow-sm relative z-50 border-b-4 border-[#00b5e2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center">
                <span class="font-bold text-xl text-white tracking-wide">Unifarmacia</span>
            </a>

            <!-- Información del usuario -->
            <div class="flex items-center space-x-4 relative">
                <!-- Información del usuario -->
                <div class="text-right hidden sm:block">
                    <p class="text-sm text-white font-medium">
                        {{ auth()->user()->nombres }} {{ auth()->user()->apellido1 }}
                    </p>
                    <p class="text-xs text-blue-200">
                        {{ auth()->user()->correo }}
                    </p>
                </div>

                <!-- Ícono con inicial y color dinámico -->
                <button type="button" onclick="document.getElementById('userDropdown').classList.toggle('hidden')"
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $colorAleatorio }} text-white font-bold hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00b5e2] transition-all shadow-sm">
                    {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                </button>

                <!-- Menu desplegable -->
                <div id="userDropdown"
                    class="hidden absolute right-0 top-12 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-slate-200 ring-1 ring-black ring-opacity-5">
                    <!-- Rol del usuario -->
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            @if(auth()->user()->idRol == 1) Administrador
                            @elseif(auth()->user()->idRol == 2) Trabajador
                            @else Alumno
                            @endif
                        </p>
                    </div>

                    <!-- Opción de Cambiar Contraseña -->
                    <a href="{{ route('password.change') }}"
                        class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        Cambiar Contraseña
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-50 transition-colors">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('click', function (e) {
        const menu = document.getElementById('userDropdown');
        if (menu) {
            const btn = menu.previousElementSibling;
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        }
    });
</script>