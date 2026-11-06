@extends('layouts.app')

@section('title', 'Nuevo Reporte - Unifarmacia')

@section('content')
    <div class="min-h-screen bg-slate-50" x-data="{ 
        reacciones: [{ sintoma_reaccion: '', fecha_inicio: '', fecha_fin: '', duracion: '', resultado_reaccion: '', es_grave: false }],
        concomitantes: [{ nombre_medicamento: '', dosis: '', frecuencia: '', fecha_inicio: '' }] 
    }">
        @include('layouts.navigation')

        <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            <x-breadcrumbs />

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900">Notificación de Reacción Adversa a Medicamentos</h2>
                <p class="text-slate-600 mt-1">Formulario de reporte de Farmacovigilancia.</p>
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

            <form action="{{ route('farmacovigilancia.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Seccion 1: Consumidor del medicamento --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">1. Consumidor del medicamento</h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">ID del Paciente en Sistema</label>
                        <input type="number" name="paciente_id" value="{{ old('paciente_id') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej. 1045">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Iniciales</label>
                            <input type="text" name="iniciales" value="{{ old('iniciales') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej. ABCD">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Sexo biológico</label>
                            <div class="flex items-center gap-4 mt-2">
                                <label class="inline-flex items-center text-sm"><input type="radio" name="sexo_biologico" value="Masculino" class="text-blue-600"> <span class="ml-2">Masculino</span></label>
                                <label class="inline-flex items-center text-sm"><input type="radio" name="sexo_biologico" value="Femenino" class="text-blue-600"> <span class="ml-2">Femenino</span></label>
                                <label class="inline-flex items-center text-sm"><input type="radio" name="sexo_biologico" value="Desconocido" class="text-blue-600"> <span class="ml-2">Desconocido</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso_kg" value="{{ old('peso_kg') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej. 70.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento_paciente" value="{{ old('fecha_nacimiento_paciente') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Edad al comienzo de la reacción</label>
                            <input type="text" name="edad_comienzo_reaccion" value="{{ old('edad_comienzo_reaccion') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej. 24 años">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">País donde ocurrió la reacción</label>
                        <select name="pais_ocurrencia" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="México" selected>México</option>
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
                                  placeholder="Describa con sus propias palabras los signos y síntomas sospechados...">{{ old('descripcion_reaccion') }}</textarea>
                    </div>
                </div>

                {{-- Seccion 3: Reacción / Síntomas / Signos --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-2">
                        <h3 class="text-lg font-semibold text-slate-900">3. Reacción(es) / Síntoma(s) / Signo(s) / Malestar(es)</h3>
                        <button type="button" id="btn-agregar-reaccion"
                                class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg hover:bg-slate-200 transition-colors">
                            + Agregar otra reacción
                        </button>
                    </div>
                    <p class="text-xs text-slate-500">Describa cada malestar o reacción con sus propias palabras. Presione el botón si necesita describir más de una.</p>

                    {{-- Contenedor donde se duplican las filas --}}
                    <div id="lista-reacciones" class="space-y-4">
                        <div class="fila p-4 bg-slate-50 rounded-lg border border-slate-200 relative space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Reacción (síntoma, signo o malestar)</label>
                                <input type="text" name="reacciones[0][sintoma_reaccion]" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej. Somnolencia">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Fecha de inicio de la reacción</label>
                                    <input type="date" name="reacciones[0][fecha_inicio]"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Fecha de finalización de la reacción</label>
                                    <input type="date" name="reacciones[0][fecha_fin]"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Duración de la reacción</label>
                                    <input type="text" name="reacciones[0][duracion]"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej. 2 Días">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Resultado de la reacción</label>
                                    <select name="reacciones[0][resultado_reaccion]"
                                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione una opción...</option>
                                        <option value="Recuperado/Resuelto">Recuperado/Resuelto</option>
                                        <option value="En recuperación/En resolución">En recuperación/En resolución</option>
                                        <option value="No recuperado/No resuelto">No recuperado/No resuelto</option>
                                        <option value="Recuperado con secuelas">Recuperado con secuelas</option>
                                        <option value="Muerte">Muerte</option>
                                        <option value="Desconocido">Desconocido</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">¿El evento fue grave?</label>
                                    <div class="flex items-center gap-4">
                                        <label class="inline-flex items-center text-sm">
                                            <input type="radio" name="reacciones[0][es_grave]" value="1" class="text-blue-600">
                                            <span class="ml-2">Sí</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm">
                                            <input type="radio" name="reacciones[0][es_grave]" value="0" checked class="text-blue-600">
                                            <span class="ml-2">No</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="button" class="btn-quitar px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-xs font-medium">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Script local para clonar y eliminar las filas de reacciones dinámicamente --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const btnAgregar = document.getElementById('btn-agregar-reaccion');
                        const listaReacciones = document.getElementById('lista-reacciones');

                        if (btnAgregar && listaReacciones) {
                            btnAgregar.addEventListener('click', function() {
                                const filas = listaReacciones.getElementsByClassName('fila');
                                const nuevoIndice = filas.length;
                                
                                // Clonamos la primera fila como plantilla
                                const clone = filas[0].cloneNode(true);
                                
                                // Limpiamos los valores de los inputs clonados y actualizamos los índices del name="..."
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

                            // Delegación de eventos para el botón de quitar
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
                        <input type="text" name="medicamento_sospechoso" value="{{ old('medicamento_sospechoso') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nombre completo como en el empaque">
                        <label class="inline-flex items-center mt-2 text-sm text-slate-600">
                            <input type="checkbox" name="es_probable_causante" value="1" checked class="rounded border-slate-300 text-blue-600">
                            <span class="ml-2">Probable causante de la reacción</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Compañía fabricante / distribuidora</label>
                            <input type="text" name="fabricante_distribuidora" value="{{ old('fabricante_distribuidora') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Número de lote</label>
                            <input type="text" name="lote" value="{{ old('lote') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Dosis</label>
                            <input type="text" name="dosis" value="{{ old('dosis') }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej. 2 comprimidos de 50 mg c/8h">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Vía de administración</label>
                            <select name="via_administracion" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione opción...</option>
                                <optgroup label="MÁS COMÚNMENTE UTILIZADOS">
                                    <option value="Intramuscular (inyección en el músculo)">Intramuscular (inyección en el músculo)</option>
                                    <option value="Intravenosa (no especificada)">Intravenosa (no especificada)</option>
                                    <option value="Oral">Oral</option>
                                </optgroup>
                                <optgroup label="TODOS">
                                    <option value="Cutánea">Cutánea</option>
                                    <option value="Desconocida">Desconocida</option>
                                    <option value="Intramuscular (inyección en el músculo)">Intramuscular (inyección en el músculo)</option>
                                    <option value="Intravenosa (no especificada)">Intravenosa (no especificada)</option>
                                    <option value="Nasal">Nasal</option>
                                    <option value="Oftálmica">Oftálmica</option>
                                    <option value="Oral">Oral</option>
                                    <option value="Otra">Otra</option>
                                    <option value="Rectal">Rectal</option>
                                    <option value="Respiratoria (inhalación)">Respiratoria (inhalación)</option>
                                    <option value="Subcutánea (inyección bajo la piel)">Subcutánea (inyección bajo la piel)</option>
                                    <option value="Vaginal">Vaginal</option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Acción tomada con el medicamento</label>
                            <select name="accion_tomada" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione opción...</option>
                                <option value="Medicamento retirado">Medicamento retirado</option>
                                <option value="Dosis reducida">Dosis reducida</option>
                                <option value="Dosis aumentada">Dosis aumentada</option>
                                <option value="Dosis no modificada">Dosis no modificada</option>
                                <option value="Desconocida">Desconocida</option>
                                <option value="No aplica">No aplica</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Indicación del medicamento</label>
                        <input type="text" name="indicacion_medicamento" value="{{ old('indicacion_medicamento') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="¿Por qué tomó el medicamento? (Ej. dolor de cabeza)">
                    </div>
                </div>

                {{-- Seccion 5: Información adicional --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">5. Información adicional</h3>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Enfermedades previas o actuales</label>
                        <textarea name="enfermedades_previas" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('enfermedades_previas') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Comentarios adicionales</label>
                        <textarea name="comentarios_adicionales" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comentarios_adicionales') }}</textarea>
                    </div>
                </div>

                {{-- Seccion 6: Información de contacto --}}
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">6. Información de contacto</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Entidad federativa de la notificación</label>
                            <input type="text" name="notificador_entidad" value="San Luis Potosí" readonly class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Profesión</label>
                            <select name="notificador_profesion" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" selected>Seleccione profesión...</option>
                                <option value="Médico/a">Médico/a</option>
                                <option value="Farmacéutico/a">Farmacéutico/a</option>
                                <option value="Otro/a profesional de la salud">Otro/a profesional de la salud</option>
                                <option value="Paciente u otro/a profesional no sanitario">Paciente u otro/a profesional no sanitario</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre (s)</label>
                            <input type="text" name="notificador_nombre" value="{{ old('notificador_nombre') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Apellido (s)</label>
                            <input type="text" name="notificador_apellido" value="{{ old('notificador_apellido') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Establecimiento de Salud</label>
                            <input type="text" name="notificador_establecimiento" value="" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
                            <input type="email" name="notificador_correo" value="{{ old('notificador_correo') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
                            <input type="text" name="notificador_telefono" value="{{ old('notificador_telefono') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        Guardar notificación
                    </button>
                </div>
            </form>
        </main>
    </div>
@endsection