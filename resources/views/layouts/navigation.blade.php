<nav class="bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center">
                <span class="font-bold text-xl text-slate-800">Unifarmacia</span>
            </a>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm text-slate-700 font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800 transition-colors">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
