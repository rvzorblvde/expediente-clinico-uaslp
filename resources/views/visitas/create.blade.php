@extends('layouts.app')

@section('title', 'Registrar visita - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs />

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-1">Registrar visita o agendar cita</h2>
                <p class="text-sm text-slate-600 mb-6">Los campos con * son obligatorios.</p>

                <form method="POST" action="{{ route('visitas.store') }}" id="form-visita">
                    @csrf

                    {{-- Identidad del paciente --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-700 mb-2">¿El paciente tiene expediente? <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-6 mb-3">
                            <label class="inline-flex items-center text-sm cursor-pointer">
                                <input type="radio" name="identidad" value="expediente" id="id-expediente"
                                       @checked(old('identidad', old('folioExpediente') ? 'expediente' : 'externo') === 'expediente')
                                       class="h-4 w-4 text-blue-600">
                                <span class="ml-2">Sí, buscarlo</span>
                            </label>
                            <label class="inline-flex items-center text-sm cursor-pointer">
                                <input type="radio" name="identidad" value="externo" id="id-externo"
                                       @checked(old('identidad', old('folioExpediente') ? 'expediente' : 'externo') === 'externo')
                                       class="h-4 w-4 text-blue-600">
                                <span class="ml-2">No, visitante sin expediente</span>
                            </label>
                        </div>

                        <div id="grupo-expediente" hidden>
                            <input type="search" id="buscar-expediente" autocomplete="off"
                                   placeholder="Buscar por folio, nombre o CURP..."
                                   class="w-full px-3 py-2 mb-2 rounded-lg border border-slate-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            <select name="folioExpediente" id="folioExpediente"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="">Selecciona un expediente...</option>
                                @foreach ($expedientes as $exp)
                                    @php
                                        $nombreExp = trim("{$exp->paciente?->nombres} {$exp->paciente?->apellido1} " . ($exp->paciente?->apellido2 ?? ''));
                                        $busqueda = mb_strtolower("{$exp->folioExpediente} {$nombreExp} {$exp->paciente?->curp}");
                                    @endphp
                                    <option value="{{ $exp->folioExpediente }}" data-busqueda="{{ $busqueda }}"
                                        @selected(old('folioExpediente') === $exp->folioExpediente)>
                                        {{ $exp->folioExpediente }} — {{ $nombreExp }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="sin-coincidencias" class="text-xs text-amber-600 mt-1" hidden>Ningún expediente coincide con la búsqueda.</p>
                            @error('folioExpediente') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div id="grupo-externo" class="grid grid-cols-1 sm:grid-cols-2 gap-3" hidden>
                            <div>
                                <input type="text" name="nombreVisitante" value="{{ old('nombreVisitante') }}"
                                       minlength="2" maxlength="150" placeholder="Nombre completo del visitante *"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                @error('nombreVisitante') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <input type="text" name="contactoVisitante" value="{{ old('contactoVisitante') }}"
                                       maxlength="150" placeholder="Teléfono o correo (opcional)"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                @error('contactoVisitante') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    @include('visitas._form', [
                        'visita' => null,
                        'estados' => ['completada' => 'Atención realizada (ahora)', 'agendada' => 'Agendar cita'],
                        'tipos' => \App\Models\Visita::TIPOS,
                    ])

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route(auth()->user()->can('manage-clinical-data') ? 'visitas.index' : 'dashboard') }}"
                           class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">Cancelar</a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            Guardar visita
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        (() => {
            const expediente = document.getElementById('id-expediente');
            const gExp = document.getElementById('grupo-expediente'), gExt = document.getElementById('grupo-externo');
            const selExp = document.getElementById('folioExpediente');
            const nombre = document.querySelector('[name=nombreVisitante]');
            const buscador = document.getElementById('buscar-expediente');
            const sinCoincidencias = document.getElementById('sin-coincidencias');

            // Copia de todas las opciones para poder filtrar y restaurar
            const opciones = Array.from(selExp.options).slice(1).map((o) => ({
                value: o.value, texto: o.textContent.trim(), busqueda: o.dataset.busqueda || '',
            }));

            function filtrarExpedientes() {
                const q = buscador.value.trim().toLowerCase();
                const seleccion = selExp.value;
                const visibles = q ? opciones.filter((o) => o.busqueda.includes(q)) : opciones;

                selExp.length = 1; // conserva solo el placeholder
                visibles.forEach((o) => selExp.add(new Option(o.texto, o.value)));

                // Mantiene la selección si sigue visible; si solo queda una opción, la elige
                selExp.value = visibles.some((o) => o.value === seleccion) ? seleccion
                    : (visibles.length === 1 ? visibles[0].value : '');
                sinCoincidencias.hidden = visibles.length > 0;
            }
            buscador.addEventListener('input', filtrarExpedientes);

            function sincronizarIdentidad() {
                const conExp = expediente.checked;
                gExp.hidden = !conExp;
                gExt.hidden = conExp;
                selExp.required = conExp;
                nombre.required = !conExp;
                if (conExp) { nombre.value = ''; document.querySelector('[name=contactoVisitante]').value = ''; }
                else { selExp.value = ''; buscador.value = ''; filtrarExpedientes(); }
            }
            document.querySelectorAll('[name=identidad]').forEach((r) => r.addEventListener('change', sincronizarIdentidad));
            sincronizarIdentidad();
        })();
    </script>
@endsection
