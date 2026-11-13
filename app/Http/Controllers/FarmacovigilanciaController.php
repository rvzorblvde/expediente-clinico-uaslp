<?php

namespace App\Http\Controllers;

use App\Http\Requests\FarmacovigilanciaRequest;
use App\Models\Auditoria;
use App\Models\Expediente;
use App\Models\Farmacovigilancia;
use App\Models\Medicamento;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

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
        return view('farmacovigilancia.create', $this->catalogos());
    }

    public function store(FarmacovigilanciaRequest $request)
    {
        $reporte = retry(3, function () use ($request) {
            return DB::transaction(function () use ($request) {
                $reporte = Farmacovigilancia::create(
                    $this->datosReporte($request) + ['folio' => Farmacovigilancia::generarFolio()]
                );
                $this->guardarDetalles($reporte, $request);
                return $reporte;
            });
        }, 0, fn ($e) => $e instanceof UniqueConstraintViolationException);

        return redirect()->route('farmacovigilancia.index')
            ->with('success', "Reporte {$reporte->folio} registrado exitosamente.");
    }

    public function show($id)
    {
        $reporte = Farmacovigilancia::with(['reacciones', 'medicamentosConcomitantes'])->findOrFail($id);

        // Registro de auditoría
        $paciente = $reporte->paciente ?? null;
        $nombrePaciente = $paciente
            ? trim("{$paciente->nombres} {$paciente->apellido1} " . ($paciente->apellido2 ?? ''))
            : 'No especificado';

        Auditoria::create([
            'idUsuario'     => auth()->id() ?? 1,
            'accion'        => 'Consulta de reporte de farmacovigilancia',
            'tablaAfectada' => 'farmacovigilancia',
            'detalles'      => [
                'tipo'      => 'consulta',
                'idReporte' => $reporte->id,
                'folio'     => $reporte->folio,
                'paciente'  => $nombrePaciente,
            ],
            'fechaAccion'   => now(),
        ]);

        return view('farmacovigilancia.show', compact('reporte'));
    }

    public function edit($id)
    {
        $reporte = Farmacovigilancia::with(['reacciones', 'medicamentosConcomitantes'])->findOrFail($id);

        return view('farmacovigilancia.edit', ['reporte' => $reporte] + $this->catalogos([$reporte->medicamento_id]));
    }

    public function update(FarmacovigilanciaRequest $request, Farmacovigilancia $farmacovigilancia)
    {
        DB::transaction(function () use ($request, $farmacovigilancia) {
            $datos = $this->datosReporte($request);
            // Al editar, el vínculo con el expediente no se modifica
            unset($datos['folioExpediente'], $datos['paciente_id']);

            $farmacovigilancia->update($datos);
            $farmacovigilancia->reacciones()->delete();
            $farmacovigilancia->medicamentosConcomitantes()->delete();
            $this->guardarDetalles($farmacovigilancia, $request);
        });

        return redirect()->route('farmacovigilancia.index')
            ->with('success', "Reporte {$farmacovigilancia->folio} actualizado exitosamente.");
    }

    public function destroy(Farmacovigilancia $farmacovigilancia)
    {
        $farmacovigilancia->medicamentosConcomitantes()->delete();
        $farmacovigilancia->reacciones()->delete();
        $farmacovigilancia->delete();

        return redirect()->route('farmacovigilancia.index')->with('success', 'Reporte eliminado exitosamente.');
    }

    private function datosReporte(FarmacovigilanciaRequest $request): array
    {
        $d = $request->validated();
        $usuario = $request->user();

        $folio = $d['folioExpediente'] ?? null;
        $paciente = $folio ? Expediente::with('paciente')->find($folio)?->paciente : null;

        // Hasta 4 iniciales: dos nombres + dos apellidos
        $iniciales = $paciente
            ? mb_strtoupper(collect(preg_split('/\s+/u', trim($paciente->nombres)))->take(2)
                ->map(fn ($w) => mb_substr($w, 0, 1))
                ->push(mb_substr($paciente->apellido1, 0, 1))
                ->push(mb_substr($paciente->apellido2 ?? '', 0, 1))
                ->filter()->implode(''))
            : ($d['iniciales'] ?? null);

        $sexo = $paciente
            ? match ($paciente->sexo) {
                'M'     => 'Masculino',
                'F'     => 'Femenino',
                default => 'Desconocido', // 'O' u otro valor → Desconocido
            }
            : ($d['sexo_biologico'] ?? null);

        $med = !empty($d['medicamento_id']) ? Medicamento::find($d['medicamento_id']) : null;
        $esGrave = (bool) ($d['es_grave'] ?? false);

        return [
            'folioExpediente'           => $folio,
            'paciente_id'               => $paciente?->idPaciente,
            'iniciales'                 => $iniciales,
            'sexo_biologico'            => $sexo,
            'fecha_nacimiento_paciente' => $paciente?->fechaNac
                ?? FarmacovigilanciaRequest::aISO($d['fecha_nacimiento_paciente'] ?? null),
            'peso_kg'                   => $d['peso_kg'] ?? null,
            'edad_comienzo_reaccion'    => $d['edad_comienzo_reaccion'] ?? null,
            'pais_ocurrencia'           => $d['pais_ocurrencia'] ?? 'México',

            'medicamento_id'            => $med?->idMedicamento,
            'medicamento_sospechoso'    => $med ? trim("{$med->nombre} " . ($med->concentracion ?? '')) : $d['medicamento_sospechoso'],
            'fabricante_distribuidora'  => $d['fabricante_distribuidora'] ?? $med?->laboratorio,
            'es_probable_causante'      => (bool) ($d['es_probable_causante'] ?? false),
            'lote'                      => $d['lote'] ?? null,
            'dosis'                     => $d['dosis'] ?? '',
            'via_administracion'        => $d['via_administracion'] ?? null,
            'fecha_inicio_admin'        => $d['fecha_inicio_admin'] ?? null,
            'fecha_fin_admin'           => $d['fecha_fin_admin'] ?? null,
            'duracion_admin'            => $d['duracion_admin'] ?? null,
            'indicacion_medicamento'    => $d['indicacion_medicamento'] ?? null,
            'accion_tomada'             => $d['accion_tomada'] ?? null,

            'descripcion_reaccion'      => $d['descripcion_reaccion'],
            'fecha_inicio_reaccion'     => $d['fecha_inicio_reaccion'] ?? now(),
            'fecha_fin_reaccion'        => $d['fecha_fin_reaccion'] ?? null,
            'duracion_reaccion'         => $d['duracion_reaccion'] ?? null,
            'resultado_reaccion'        => $d['resultado_reaccion'] ?? null,
            'es_grave'                  => $esGrave,
            'gravedad'                  => $esGrave ? 'Grave' : 'Leve',
            'evolucion'                 => $d['resultado_reaccion'] ?? 'En proceso',
            'observaciones'             => $d['observaciones'] ?? null,
            'enfermedades_previas'      => $d['enfermedades_previas'] ?? null,
            'comentarios_adicionales'   => $d['comentarios_adicionales'] ?? null,

            'notificador_entidad'         => 'San Luis Potosí',
            'notificador_profesion'       => 'Farmacéutico',
            'notificador_nombre'          => $usuario->nombres,
            'notificador_apellido'        => trim("{$usuario->apellido1} " . ($usuario->apellido2 ?? '')),
            'notificador_establecimiento' => 'Unifarmacia UASLP',
            'notificador_correo'          => 'farmacovigilancia@unifarmacia.example',
            'notificador_telefono'        => '444 000 0000',
            'enviar_acuse_correo'         => $request->boolean('enviar_acuse_correo'),
        ];
    }

    private function guardarDetalles(Farmacovigilancia $reporte, FarmacovigilanciaRequest $request): void
    {
        foreach ($request->validated('reacciones', []) as $r) {
            $reporte->reacciones()->create([
                'sintoma_reaccion'   => $r['sintoma_reaccion'],
                'fecha_inicio'       => FarmacovigilanciaRequest::aISO($r['fecha_inicio'] ?? null),
                'fecha_fin'          => FarmacovigilanciaRequest::aISO($r['fecha_fin'] ?? null),
                'duracion'           => $r['duracion'] ?? null,
                'resultado_reaccion' => $r['resultado_reaccion'] ?? null,
                'es_grave'           => (bool) ($r['es_grave'] ?? false),
            ]);
        }

        foreach ($request->validated('concomitantes', []) as $c) {
            $reporte->medicamentosConcomitantes()->create([
                'nombre_medicamento' => $c['nombre_medicamento'],
                'dosis'              => $c['dosis'] ?? '',
                'frecuencia'         => $c['frecuencia'] ?? '',
                'fecha_inicio'       => FarmacovigilanciaRequest::aISO($c['fecha_inicio'] ?? null),
            ]);
        }
    }

    private function catalogos(iterable $medicamentosEnUso = []): array
    {
        return [
            // visitas.datosAntropometricos: para precargar el último peso registrado
            'expedientes'  => Expediente::where('estado', 'activo')
                ->with(['paciente', 'visitas.datosAntropometricos'])
                ->orderBy('folioExpediente')->get(),
            'medicamentos' => Medicamento::disponibles($medicamentosEnUso)->orderBy('nombre')
                ->get(['idMedicamento', 'nombre', 'concentracion', 'presentacion', 'laboratorio']),
        ];
    }
}
