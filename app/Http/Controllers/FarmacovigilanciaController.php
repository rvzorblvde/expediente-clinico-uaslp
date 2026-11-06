<?php

namespace App\Http\Controllers;

use App\Models\Farmacovigilancia;
use App\Models\MedicamentoConcomitante;
use Illuminate\Http\Request;

class FarmacovigilanciaController extends Controller
{
    public function index()
    {
        // RF-06.2: Histórico de reportes de RAM
        $reportes = Farmacovigilancia::with('paciente', 'medicamentosConcomitantes')->latest()->paginate(10);
        return view('farmacovigilancia.index', compact('reportes'));
    }

    public function create()
    {
        // Aquí puedes mandar los pacientes si los necesitas en un select
        return view('farmacovigilancia.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => 'required|integer',
            'medicamento_sospechoso' => 'required|string|max:255',
            'descripcion_reaccion' => 'required|string',
            'fecha_inicio_reaccion' => 'nullable|date',
            'iniciales' => 'nullable|string|max:10',
            'sexo_biologico' => 'nullable|string',
            'peso_kg' => 'nullable|numeric',
            'pais_ocurrencia' => 'nullable|string',
            'resultado_reaccion' => 'nullable|string',
            'via_administracion' => 'nullable|string',
            'accion_tomada' => 'nullable|string',
            'notificador_profesion' => 'nullable|string',
            'notificador_correo' => 'nullable|email',
        ]);

        $reporte = Farmacovigilancia::create([
            'paciente_id' => $request->paciente_id,
            'iniciales' => $request->iniciales,
            'sexo_biologico' => $request->sexo_biologico,
            'peso_kg' => $request->peso_kg,
            'fecha_nacimiento_paciente' => $request->fecha_nacimiento_paciente,
            'edad_comienzo_reaccion' => $request->edad_comienzo_reaccion,
            'pais_ocurrencia' => $request->pais_ocurrencia ?? 'México',
            'medicamento_sospechoso' => $request->medicamento_sospechoso,
            'es_probable_causante' => $request->has('es_probable_causante'),
            'fabricante_distribuidora' => $request->fabricante_distribuidora,
            'lote' => $request->lote,
            'dosis' => $request->dosis ?? '',
            'via_administracion' => $request->via_administracion,
            'fecha_inicio_admin' => $request->fecha_inicio_admin,
            'fecha_fin_admin' => $request->fecha_fin_admin,
            'duracion_admin' => $request->duracion_admin,
            'indicacion_medicamento' => $request->indicacion_medicamento,
            'accion_tomada' => $request->accion_tomada,
            'fecha_inicio_reaccion' => $request->fecha_inicio_reaccion ?? now(),
            'fecha_fin_reaccion' => $request->fecha_fin_reaccion,
            'duracion_reaccion' => $request->duracion_reaccion,
            'descripcion_reaccion' => $request->descripcion_reaccion,
            'resultado_reaccion' => $request->resultado_reaccion,
            'gravedad' => $request->es_grave ? 'Grave' : 'Leve',
            'es_grave' => $request->has('es_grave'),
            'evolucion' => $request->resultado_reaccion ?? 'En proceso',
            'observaciones' => $request->observaciones,
            'enfermedades_previas' => $request->enfermedades_previas,
            'comentarios_adicionales' => $request->comentarios_adicionales,
            'notificador_entidad' => $request->notificador_entidad ?? 'San Luis Potosí',
            'notificador_profesion' => $request->notificador_profesion,
            'notificador_nombre' => $request->notificador_nombre,
            'notificador_apellido' => $request->notificador_apellido,
            'notificador_establecimiento' => $request->notificador_establecimiento,
            'notificador_correo' => $request->notificador_correo,
            'notificador_telefono' => $request->notificador_telefono,
            'enviar_acuse_correo' => $request->has('enviar_acuse_correo'),
        ]);

        // Guardar las reacciones múltiples del apartado 3
        if ($request->has('reacciones')) {
            foreach ($request->reacciones as $reaccion) {
                if (!empty($reaccion['sintoma_reaccion'])) {
                    $reporte->reacciones()->create([
                        'sintoma_reaccion' => $reaccion['sintoma_reaccion'],
                        'fecha_inicio' => $reaccion['fecha_inicio'] ?? null,
                        'fecha_fin' => $reaccion['fecha_fin'] ?? null,
                        'duracion' => $reaccion['duracion'] ?? null,
                        'resultado_reaccion' => $reaccion['resultado_reaccion'] ?? null,
                        'es_grave' => isset($reaccion['es_grave']) ? 1 : 0,
                    ]);
                }
            }
        }

        // Medicamentos Concomitantes
        if ($request->has('concomitantes')) {
            foreach ($request->concomitantes as $concomitante) {
                if (!empty($concomitante['nombre_medicamento'])) {
                    $reporte->medicamentosConcomitantes()->create([
                        'nombre_medicamento' => $concomitante['nombre_medicamento'],
                        'dosis' => $concomitante['dosis'] ?? '',
                        'frecuencia' => $concomitante['frecuencia'] ?? '',
                        'fecha_inicio' => $concomitante['fecha_inicio'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('farmacovigilancia.index')->with('success', 'Reporte registrado exitosamente.');
    }

    public function show($id)
    {
        $reporte = Farmacovigilancia::with(['reacciones', 'medicamentosConcomitantes'])->findOrFail($id);
        
        return view('farmacovigilancia.show', compact('reporte'));
    }

    public function edit($id)
    {
        $reporte = Farmacovigilancia::with(['reacciones', 'medicamentosConcomitantes'])->findOrFail($id);
        
        return view('farmacovigilancia.edit', compact('reporte'));
    }

    public function update(Request $request, Farmacovigilancia $farmacovigilancia)
    {
        $request->validate([
            'paciente_id' => 'required|integer',
            'medicamento_sospechoso' => 'required|string|max:255',
            'dosis' => 'required|string|max:100',
            'fecha_inicio_reaccion' => 'nullable|date',
            'descripcion_reaccion' => 'required|string',
            'gravedad' => 'nullable|in:Leve,Moderada,Grave',
        ]);

        // Actualizar el reporte principal
        $farmacovigilancia->update($request->only([
            'paciente_id', 'medicamento_sospechoso', 'lote', 'dosis', 
            'fecha_inicio_reaccion', 'descripcion_reaccion', 'gravedad', 'evolucion', 'observaciones'
        ]));

        // Sincronizar medicamentos concomitantes (eliminamos los anteriores y guardamos los nuevos del formulario)
        $farmacovigilancia->medicamentosConcomitantes()->delete();
        if ($request->has('concomitantes')) {
            foreach ($request->concomitantes as $concomitante) {
                if (!empty($concomitante['nombre_medicamento'])) {
                    $farmacovigilancia->medicamentosConcomitantes()->create([
                        'nombre_medicamento' => $concomitante['nombre_medicamento'],
                        'dosis' => $concomitante['dosis'] ?? '',
                        'frecuencia' => $concomitante['frecuencia'] ?? '',
                        'fecha_inicio' => $concomitante['fecha_inicio'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('farmacovigilancia.index')->with('success', 'Reporte de farmacovigilancia actualizado exitosamente.');
    }

    public function destroy(Farmacovigilancia $farmacovigilancia)
    {
        // Elimina el reporte (los concomitantes se borran en cascada si está configurado, o los borramos explícitamente)
        $farmacovigilancia->medicamentosConcomitantes()->delete();
        $farmacovigilancia->delete();

        return redirect()->route('farmacovigilancia.index')->with('success', 'Reporte eliminado exitosamente.');
    }
}