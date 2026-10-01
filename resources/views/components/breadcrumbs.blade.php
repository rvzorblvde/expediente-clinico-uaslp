@props(['links' => null])

@php
    // Se construye un mapa automatico segun la ruta actual
    if (!$links) {
        $routeName = request()->route()?->getName();

        $links = match ($routeName) {
            'dashboard' => [],
            'expedientes.index' => ['Expedientes' => null],
            'expedientes.create' => ['Expedientes' => route('expedientes.index'), 'Nuevo expediente' => null],
            'expedientes.edit' => ['Expedientes' => route('expedientes.index'), 'Editar expediente' => null],
            'usuarios.index' => ['Usuarios del sistema' => null],
            default => [],
        };
    }
@endphp

@if(count($links) > 0)
    <nav class="flex text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Inicio
                </a>
            </li>
            @foreach($links as $label => $url)
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        @if($url)
                            <a href="{{ $url }}" class="ml-1 md:ml-2 hover:text-blue-600 transition-colors">{{ $label }}</a>
                        @else
                            <span class="ml-1 md:ml-2 text-slate-700 font-medium">{{ $label }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>
@endif
