@extends('layouts.app')

@section('title', 'Editar Reporte - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50">
        @include('layouts.navigation')

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Editar Reporte de Farmacovigilancia #{{ $reporte->id }}</h2>
                <p class="text-slate-600 mt-1">Actualice la información necesaria del reporte.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <p class="font-semibold mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('farmacovigilancia.update', $reporte->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Seccion 1: Consumidor del medicamento --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">1. Consumidor del medicamento</h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">ID del Paciente en Sistema</label>
                        <input type="number" name="paciente_id" value="{{ old('paciente_id', $reporte->paciente_id) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Iniciales</label>
                            <input type="text" name="iniciales" value="{{ old('iniciales', $reporte->iniciales) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Sexo biológico</label>
                            <div class="flex items-center gap-4 mt-2">
                                <label class="inline-flex items-center text-sm"><input type="radio" name="sexo_biologico" value="Masculino" {{ old('sexo_biologico', $reporte->sexo_biologico) == 'Masculino' ? 'checked' : '' }} class="text-blue-600"> <span class="ml-2">Masculino</span></label>
                                <label class="inline-flex items-center text-sm"><input type="radio" name="sexo_biologico" value="Femenino" {{ old('sexo_biologico', $reporte->sexo_biologico) == 'Femenino' ? 'checked' : '' }} class="text-blue-600"> <span class="ml-2">Femenino</span></label>
                                <label class="inline-flex items-center text-sm"><input type="radio" name="sexo_biologico" value="Desconocido" {{ old('sexo_biologico', $reporte->sexo_biologico) == 'Desconocido' ? 'checked' : '' }} class="text-blue-600"> <span class="ml-2">Desconocido</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso_kg" value="{{ old('peso_kg', $reporte->peso_kg) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento_paciente" value="{{ old('fecha_nacimiento_paciente', $reporte->fecha_nacimiento_paciente) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Edad al comienzo de la reacción</label>
                            <input type="text" name="edad_comienzo_reaccion" value="{{ old('edad_comienzo_reaccion', $reporte->edad_comienzo_reaccion) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">País donde ocurrió la reacción</label>
                        <select name="pais_ocurrencia" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="México" {{ old('pais_ocurrencia', $reporte->pais_ocurrencia) == 'México' ? 'selected' : '' }}>México</option>
                        </select>
                    </div>
                </div>

                {{-- Seccion 2: Describa lo sucedido --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">2. Describa lo sucedido</h3>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción de lo sucedido</label>
                        <textarea name="descripcion_reaccion" rows="4" required
                                  class="w-full rounded-lg border-slate-300 shadow-sm text-sm"
                                  placeholder="Describa con sus propias palabras los signos y síntomas sospechados...">{{ old('descripcion_reaccion', $reporte->descripcion_reaccion) }}</textarea>
                    </div>
                </div>

                {{-- Seccion 3: Reacción / Síntomas / Signos (100% PHP Nativo para evitar errores de Blade) --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-2">
                        <h3 class="text-lg font-semibold text-slate-900">3. Reacción(es) / Síntoma(s) / Signo(s) / Malestar(es)</h3>
                        <button type="button" id="btn-agregar-reaccion"
                                class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg hover:bg-slate-200 transition-colors">
                            + Agregar otra reacción
                        </button>
                    </div>
                    <p class="text-xs text-slate-500">Describa cada malestar o reacción con sus propias palabras.</p>

                    <div id="lista-reacciones" class="space-y-4">
                        <?php
                            $reaccionesList = ($reporte->reacciones && count($reporte->reacciones) > 0) 
                                ? $reporte->reacciones 
                                : [(object)['sintoma_reaccion' => '', 'fecha_inicio' => '', 'fecha_fin' => '', 'duracion' => '', 'resultado_reaccion' => '', 'es_grave' => 0]];
                        ?>

                        <?php foreach($reaccionesList as $i => $reac): ?>
                            <div class="fila p-4 bg-slate-50 rounded-lg border border-slate-200 relative space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Reacción (síntoma, signo o malestar)</label>
                                    <input type="text" name="reacciones[<?php echo $i; ?>][sintoma_reaccion]" value="<?php echo htmlspecialchars($reac->sintoma_reaccion ?? ''); ?>" required
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Fecha de inicio de la reacción</label>
                                        <input type="date" name="reacciones[<?php echo $i; ?>][fecha_inicio]" value="<?php echo htmlspecialchars($reac->fecha_inicio ?? ''); ?>"
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Fecha de finalización de la reacción</label>
                                        <input type="date" name="reacciones[<?php echo $i; ?>][fecha_fin]" value="<?php echo htmlspecialchars($reac->fecha_fin ?? ''); ?>"
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Duración de la reacción</label>
                                        <input type="text" name="reacciones[<?php echo $i; ?>][duracion]" value="<?php echo htmlspecialchars($reac->duracion ?? ''); ?>"
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Resultado de la reacción</label>
                                        <select name="reacciones[<?php echo $i; ?>][resultado_reaccion]"
                                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Seleccione una opción...</option>
                                            <option value="Recuperado/Resuelto" <?php echo (($reac->resultado_reaccion ?? '') == 'Recuperado/Resuelto') ? 'selected' : ''; ?>>Recuperado/Resuelto</option>
                                            <option value="En recuperación/En resolución" <?php echo (($reac->resultado_reaccion ?? '') == 'En recuperación/En resolución') ? 'selected' : ''; ?>>En recuperación/En resolución</option>
                                            <option value="No recuperado/No resuelto" <?php echo (($reac->resultado_reaccion ?? '') == 'No recuperado/No resuelto') ? 'selected' : ''; ?>>No recuperado/No resuelto</option>
                                            <option value="Recuperado con secuelas" <?php echo (($reac->resultado_reaccion ?? '') == 'Recuperado con secuelas') ? 'selected' : ''; ?>>Recuperado con secuelas</option>
                                            <option value="Muerte" <?php echo (($reac->resultado_reaccion ?? '') == 'Muerte') ? 'selected' : ''; ?>>Muerte</option>
                                            <option value="Desconocido" <?php echo (($reac->resultado_reaccion ?? '') == 'Desconocido') ? 'selected' : ''; ?>>Desconocido</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-700 mb-1">¿El evento fue grave?</label>
                                        <div class="flex items-center gap-4">
                                            <label class="inline-flex items-center text-sm">
                                                <input type="radio" name="reacciones[<?php echo $i; ?>][es_grave]" value="1" <?php echo (($reac->es_grave ?? 0) == 1) ? 'checked' : ''; ?> class="text-blue-600">
                                                <span class="ml-2">Sí</span>
                                            </label>
                                            <label class="inline-flex items-center text-sm">
                                                <input type="radio" name="reacciones[<?php echo $i; ?>][es_grave]" value="0" <?php echo (($reac->es_grave ?? 0) == 0) ? 'checked' : ''; ?> class="text-blue-600">
                                                <span class="ml-2">No</span>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="button" class="btn-quitar px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-xs font-medium">
                                        Quitar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                {{-- Script local para clonar filas en Editar --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const btnAgregar = document.getElementById('btn-agregar-reaccion');
                        const listaReacciones = document.getElementById('lista-reacciones');

                        if (btnAgregar && listaReacciones) {
                            btnAgregar.addEventListener('click', function() {
                                const filas = listaReacciones.getElementsByClassName('fila');
                                const nuevoIndice = filas.length;
                                const clone = filas[0].cloneNode(true);
                                
                                const inputs = clone.querySelectorAll('input, select');
                                inputs.forEach(input => {
                                    if (input.type === 'radio') {
                                        if (input.value === '0') input.checked = true;
                                        else input.checked = false;
                                    } else {
                                        input.value = '';
                                    }
                                    if (input.name) {
                                        input.name = input.name.replace(/\[\d+\]/, `[${nuevoIndice}]`);
                                    }
                                });

                                listaReacciones.appendChild(clone);
                            });

                            listaReacciones.addEventListener('click', function(e) {
                                if (e.target.classList.contains('btn-quitar')) {
                                    const filas = listaReacciones.getElementsByClassName('fila');
                                    if (filas.length > 1) {
                                        e.target.closest('.fila').remove();
                                    } else {
                                        alert('Debe haber al menos una reacción registrada.');
                                    }
                                }
                            });
                        }
                    });
                </script>

                {{-- Seccion 4: Medicamentos --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">4. Medicamento Sospechoso</h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del medicamento</label>
                        <input type="text" name="medicamento_sospechoso" value="{{ old('medicamento_sospechoso', $reporte->medicamento_sospechoso) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <label class="inline-flex items-center mt-2 text-sm text-slate-600">
                            <input type="checkbox" name="es_probable_causante" value="1" {{ old('es_probable_causante', $reporte->es_probable_causante) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                            <span class="ml-2">Probable causante de la reacción</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Compañía fabricante / distribuidora</label>
                            <input type="text" name="fabricante_distribuidora" value="{{ old('fabricante_distribuidora', $reporte->fabricante_distribuidora) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Número de lote</label>
                            <input type="text" name="lote" value="{{ old('lote', $reporte->lote) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Dosis</label>
                            <input type="text" name="dosis" value="{{ old('dosis', $reporte->dosis) }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Vía de administración</label>
                            <select name="via_administracion" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione opción...</option>
                                <option value="Oral" {{ old('via_administracion', $reporte->via_administracion) == 'Oral' ? 'selected' : '' }}>Oral</option>
                                <option value="Intravenosa (no especificada)" {{ old('via_administracion', $reporte->via_administracion) == 'Intravenosa (no especificada)' ? 'selected' : '' }}>Intravenosa (no especificada)</option>
                                <option value="Intramuscular (inyección en el músculo)" {{ old('via_administracion', $reporte->via_administracion) == 'Intramuscular (inyección en el músculo)' ? 'selected' : '' }}>Intramuscular (inyección en el músculo)</option>
                                <option value="Cutánea" {{ old('via_administracion', $reporte->via_administracion) == 'Cutánea' ? 'selected' : '' }}>Cutánea</option>
                                <option value="Nasal" {{ old('via_administracion', $reporte->via_administracion) == 'Nasal' ? 'selected' : '' }}>Nasal</option>
                                <option value="Oftálmica" {{ old('via_administracion', $reporte->via_administracion) == 'Oftálmica' ? 'selected' : '' }}>Oftálmica</option>
                                <option value="Rectal" {{ old('via_administracion', $reporte->via_administracion) == 'Rectal' ? 'selected' : '' }}>Rectal</option>
                                <option value="Subcutánea (inyección bajo la piel)" {{ old('via_administracion', $reporte->via_administracion) == 'Subcutánea (inyección bajo la piel)' ? 'selected' : '' }}>Subcutánea (inyección bajo la piel)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Acción tomada con el medicamento</label>
                            <select name="accion_tomada" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione opción...</option>
                                <option value="Medicamento retirado" {{ old('accion_tomada', $reporte->accion_tomada) == 'Medicamento retirado' ? 'selected' : '' }}>Medicamento retirado</option>
                                <option value="Dosis reducida" {{ old('accion_tomada', $reporte->accion_tomada) == 'Dosis reducida' ? 'selected' : '' }}>Dosis reducida</option>
                                <option value="Dosis aumentada" {{ old('accion_tomada', $reporte->accion_tomada) == 'Dosis aumentada' ? 'selected' : '' }}>Dosis aumentada</option>
                                <option value="Dosis no modificada" {{ old('accion_tomada', $reporte->accion_tomada) == 'Dosis no modificada' ? 'selected' : '' }}>Dosis no modificada</option>
                                <option value="Desconocida" {{ old('accion_tomada', $reporte->accion_tomada) == 'Desconocida' ? 'selected' : '' }}>Desconocida</option>
                                <option value="No aplica" {{ old('accion_tomada', $reporte->accion_tomada) == 'No aplica' ? 'selected' : '' }}>No aplica</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Indicación del medicamento</label>
                        <input type="text" name="indicacion_medicamento" value="{{ old('indicacion_medicamento', $reporte->indicacion_medicamento) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Seccion 5: Información adicional --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">5. Información adicional</h3>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Enfermedades previas o actuales</label>
                        <textarea name="enfermedades_previas" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('enfermedades_previas', $reporte->enfermedades_previas) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Comentarios adicionales</label>
                        <textarea name="comentarios_adicionales" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comentarios_adicionales', $reporte->comentarios_adicionales) }}</textarea>
                    </div>
                </div>

                {{-- Seccion 6: Información de contacto --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">6. Información de contacto</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Entidad federativa de la notificación</label>
                            <input type="text" name="notificador_entidad" value="{{ old('notificador_entidad', $reporte->notificador_entidad ?? 'San Luis Potosí') }}" readonly class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Profesión</label>
                            <select name="notificador_profesion" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione profesión...</option>
                                <option value="Médico/a" {{ old('notificador_profesion', $reporte->notificador_profesion) == 'Médico/a' ? 'selected' : '' }}>Médico/a</option>
                                <option value="Farmacéutico/a" {{ old('notificador_profesion', $reporte->notificador_profesion) == 'Farmacéutico/a' ? 'selected' : '' }}>Farmacéutico/a</option>
                                <option value="Otro/a profesional de la salud" {{ old('notificador_profesion', $reporte->notificador_profesion) == 'Otro/a profesional de la salud' ? 'selected' : '' }}>Otro/a profesional de la salud</option>
                                <option value="Paciente u otro/a profesional no sanitario" {{ old('notificador_profesion', $reporte->notificador_profesion) == 'Paciente u otro/a profesional no sanitario' ? 'selected' : '' }}>Paciente u otro/a profesional no sanitario</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre (s)</label>
                            <input type="text" name="notificador_nombre" value="{{ old('notificador_nombre', $reporte->notificador_nombre) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Apellido (s)</label>
                            <input type="text" name="notificador_apellido" value="{{ old('notificador_apellido', $reporte->notificador_apellido) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Establecimiento de Salud</label>
                            <input type="text" name="notificador_establecimiento" value="{{ old('notificador_establecimiento', $reporte->notificador_establecimiento) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
                            <input type="email" name="notificador_correo" value="{{ old('notificador_correo', $reporte->notificador_correo) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
                            <input type="text" name="notificador_telefono" value="{{ old('notificador_telefono', $reporte->notificador_telefono) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('farmacovigilancia.index') }}"
                       class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Actualizar notificación
                    </button>
                </div>
            </form>
        </main>
    </div>
@endsection