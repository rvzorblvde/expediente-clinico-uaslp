@props(['links' => null])

@php
    // Se construye un mapa automatico segun la ruta actual
    if (!$links) {
        $routeName = request()->route()?->getName();
        $esPersonal = auth()->user()?->can('manage-clinical-data');

        $links = match ($routeName) {
            'dashboard' => [],
            'expedientes.index' => ['Expedientes' => null],
            'expedientes.create' => $esPersonal
                ? ['Expedientes' => route('expedientes.index'), 'Nuevo expediente' => null]
                : ['Nuevo expediente' => null],
            'expedientes.edit' => ['Expedientes' => route('expedientes.index'), 'Editar expediente' => null],
            'usuarios.index' => ['Usuarios del sistema' => null],
            'medicamentos.index' => ['Medicamentos' => null],
            'medicamentos.create' => ['Medicamentos' => route('medicamentos.index'), 'Nuevo medicamento' => null],
            'medicamentos.edit' => ['Medicamentos' => route('medicamentos.index'), 'Editar medicamento' => null],
            'reportes.index' => ['Estadísticas y reportes' => null],
            'farmacovigilancia.index' => ['Farmacovigilancia' => null],
            'farmacovigilancia.create' => $esPersonal
                ? ['Farmacovigilancia' => route('farmacovigilancia.index'), 'Nuevo reporte' => null]
                : ['Nuevo reporte' => null],
            'farmacovigilancia.edit' => ['Farmacovigilancia' => route('farmacovigilancia.index'), 'Editar reporte' => null],
            'farmacovigilancia.show' => ['Farmacovigilancia' => route('farmacovigilancia.index'), 'Detalles del reporte' => null],
            default => [],
        };
    }
@endphp