@extends('layouts.app')

@section('title', 'Expediente ' . $expediente->folioExpediente . ' - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs :links="[
                'Expedientes' => route('expedientes.index'),
                'Expediente ' . $expediente->folioExpediente => null
            ]" />

            {{-- Encabezado con Folio y Estado --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $expediente->estado === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($expediente->estado) }}
                    </span>
                    <h2 class="text-2xl font-bold text-slate-900 mt-1">Expediente: {{ $expediente->folioExpediente }}</h2>
                    <p class="text-sm text-slate-500">Registrado por: {{ $expediente->creador?->nombres ?? 'Sistema' }} el {{ $expediente->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-2">
                    <a href="{{ route('expedientes.edit', $expediente) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Editar expediente
                    </a>
                </div>
            </div>

            {{-- Datos Personales del Paciente --}}
            <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 border-b pb-2">Información del Paciente</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="block text-slate-400 text-xs uppercase">Nombre completo</span>
                        <span class="font-medium text-slate-800 text-base">{{ $expediente->paciente->nombres }} {{ $expediente->paciente->apellido1 }} {{ $expediente->paciente->apellido2 }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs uppercase">CURP</span>
                        <span class="font-medium text-slate-800 tracking-wider">{{ $expediente->paciente->curp }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs uppercase">Fecha de nacimiento / Edad</span>
                        <span class="font-medium text-slate-800">
                            {{ \Carbon\Carbon::parse($expediente->paciente->fechaNac)->format('d/m/Y') }}
                            ({{ \Carbon\Carbon::parse($expediente->paciente->fechaNac)->age }} años)
                        </span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs uppercase">Sexo</span>
                        <span class="font-medium text-slate-800">
                            @if($expediente->paciente->sexo === 'M') Masculino
                            @elseif($expediente->paciente->sexo === 'F') Femenino
                            @else Otro @endif
                        </span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs uppercase">Procedencia</span>
                        <span class="font-medium text-slate-800">{{ $expediente->paciente->lugarProcedencia ?: 'No especificado' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs uppercase">Residencia</span>
                        <span class="font-medium text-slate-800">{{ $expediente->paciente->lugarResidencia ?: 'No especificado' }}</span>
                    </div>
                </div>

                {{-- Teléfonos y Correos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 pt-4 border-t border-slate-100 text-sm">
                    <div>
                        <span class="block text-slate-400 text-xs uppercase mb-1">Teléfonos de contacto</span>
                        @forelse($expediente->paciente->telefonos as $tel)
                            <div class="text-slate-700 font-medium">{{ $tel->numero }} <span class="text-xs text-slate-400">({{ $tel->tipo }})</span></div>
                        @empty
                            <span class="text-slate-400 italic">Sin teléfonos registrados</span>
                        @endforelse
                    </div>
                    <div>
                        <span class="block text-slate-400 text-xs uppercase mb-1">Correos electrónicos</span>
                        @forelse($expediente->paciente->correos as $correo)
                            <div class="text-slate-700 font-medium">{{ $correo->correo }}</div>
                        @empty
                            <span class="text-slate-400 italic">Sin correos registrados</span>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- Alergias y Antecedentes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4 border-b pb-2">Alergias</h3>
                    @forelse($expediente->alergias as $alergia)
                        <div class="mb-3 pb-2 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                            <span class="font-semibold text-red-600">{{ $alergia->sustancia }}</span>
                            <span class="text-xs px-2 py-0.5 bg-red-50 text-red-700 rounded-full font-medium ml-2">{{ $alergia->severidad }}</span>
                            <p class="text-sm text-slate-600 mt-0.5">Reacción: {{ $alergia->reaccion ?: 'No especificada' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic">No se registraron alergias.</p>
                    @endforelse
                </section>

                <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4 border-b pb-2">Antecedentes Clínicos</h3>
                    @forelse($expediente->antecedentes as $ant)
                        <div class="mb-3 pb-2 border-b border-slate-100 last:border-0 last:mb-0 last:pb-0">
                            <span class="font-semibold text-slate-800">{{ $ant->nombre }}</span>
                            <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded font-medium ml-2">{{ $ant->tipo }}</span>
                            <p class="text-sm text-slate-600 mt-0.5">{{ $ant->descripcion ?: 'Sin descripción' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic">No se registraron antecedentes.</p>
                    @endforelse
                </section>
            </div>

            {{-- Historial de Visitas / Triage --}}
            <section class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 border-b pb-2">Historial de Visitas y Signos Vitales (Triage)</h3>

                @forelse($expediente->visitas as $visita)
                    <div class="mb-4 p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md">
                                Visita #{{ $visita->idVisita }} - {{ $visita->created_at ? $visita->fecha->format('d/m/Y H:i') : 'Fecha no registrada' }}
                            </span>
                            <span class="text-xs text-slate-500">Motivo: {{ $visita->motivo }}</span>
                        </div>

                        @if($visita->datosAntropometricos)
                            @php $t = $visita->datosAntropometricos; @endphp
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mt-3 pt-3 border-t border-slate-200">
                                <div><span class="text-slate-400 text-xs block">Peso:</span> <strong class="text-slate-700">{{ $t->peso ? $t->peso . ' kg' : 'N/A' }}</strong></div>
                                <div><span class="text-slate-400 text-xs block">Talla:</span> <strong class="text-slate-700">{{ $t->talla ? $t->talla . ' m' : 'N/A' }}</strong></div>
                                <div><span class="text-slate-400 text-xs block">IMC:</span> <strong class="text-slate-700">{{ $t->imc ?? 'N/A' }}</strong></div>
                                <div><span class="text-slate-400 text-xs block">Presión Arterial:</span> <strong class="text-slate-700">{{ $t->presionSistolica && $t->presionDiastolica ? $t->presionSistolica.'/'.$t->presionDiastolica : 'N/A' }}</strong></div>
                                <div><span class="text-slate-400 text-xs block">Pulso:</span> <strong class="text-slate-700">{{ $t->pulsoPpm ? $t->pulsoPpm . ' lpm' : 'N/A' }}</strong></div>
                                <div><span class="text-slate-400 text-xs block">Frec. Respiratoria:</span> <strong class="text-slate-700">{{ $t->frecuenciaRespiratoria ? $t->frecuenciaRespiratoria . ' rpm' : 'N/A' }}</strong></div>
                                <div><span class="text-slate-400 text-xs block">Sat. Oxígeno:</span> <strong class="text-slate-700">{{ $t->saturacionOxigeno ? $t->saturacionOxigeno . '%' : 'N/A' }}</strong></div>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic mt-2">Sin signos vitales registrados en esta visita.</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400 italic">No hay visitas registradas para este expediente.</p>
                @endforelse
            </section>

        </main>
    </div>
@endsection
