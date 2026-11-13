<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use App\Models\DatosAntropometricos;
use App\Models\Expediente;
use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VisitaRequest;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VisitaController extends Controller
{
    public function index(Request $request)
    {
        $vista = $request->query('vista') === 'agenda' ? 'agenda' : 'historial';
        $q = trim((string) $request->query('q', ''));

        $visitas = Visita::query()
            ->with(['expediente.paciente', 'usuario'])
            // Agenda: solo citas pendientes, de la más próxima a la más lejana
            ->when($vista === 'agenda', fn ($query) => $query
                ->where('estado', 'agendada')
                ->where('fecha', '>=', now()->startOfDay())
                ->orderBy('fecha'))
            ->when($vista === 'historial', fn ($query) => $query->orderByDesc('fecha'))
            // RF-05: búsqueda por datos del paciente o folios
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('folioVisita', 'ilike', $like)
                        ->orWhere('folioExpediente', 'ilike', $like)
                        ->orWhere('nombreVisitante', 'ilike', $like)
                        ->orWhereHas('expediente.paciente', function ($p) use ($like) {
                            $p->where('nombres', 'ilike', $like)
                                ->orWhere('apellido1', 'ilike', $like)
                                ->orWhere('apellido2', 'ilike', $like)
                                ->orWhere('curp', 'ilike', $like);
                        });
                });
            })
            ->when($request->filled('estado') && $vista === 'historial',
                fn ($query) => $query->where('estado', $request->estado))
            ->when($request->filled('tipo'), fn ($query) => $query->where('tipo', $request->tipo))
            ->when($request->filled('desde'), fn ($query) => $query->whereDate('fecha', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($query) => $query->whereDate('fecha', '<=', $request->hasta))
            ->paginate(15)
            ->withQueryString();

        return view('visitas.index', [
            'visitas' => $visitas,
            'vista'   => $vista,
            'q'       => $q,
        ]);
    }

    public function create()
    {
        return view('visitas.create', $this->catalogos());
    }

    public function store(VisitaRequest $request)
    {
        $visita = $this->guardar($request);

        // El estudiante no tiene acceso al historial
        if (! $request->user()->can('manage-clinical-data')) {
            return redirect()->route('dashboard')
                ->with('success', "Visita {$visita->folioVisita} registrada correctamente.");
        }

        return redirect()->route('visitas.show', $visita)
            ->with('success', "Visita {$visita->folioVisita} registrada correctamente.");
    }

    public function show(Visita $visita)
    {
        $visita->load(['expediente.paciente', 'usuario', 'datosAntropometricos', 'registrosMedicacion.medicamento']);
        return view('visitas.show', compact('visita'));
    }

    public function edit(Visita $visita)
    {
        $visita->load(['datosAntropometricos', 'registrosMedicacion']);
        return view('visitas.edit', ['visita' => $visita] + $this->catalogos($visita->registrosMedicacion->pluck('idMedicamento')));
    }

    public function update(VisitaRequest $request, Visita $visita)
    {
        DB::transaction(function () use ($request, $visita) {
            $visita->update($request->datosVisita());
            $this->guardarTriage($visita, $request->validated('triage', []));
            $this->guardarMedicacion($visita, $request->validated('medicamentos', []), reemplazar: true);
        });

        return redirect()->route('visitas.show', $visita)
            ->with('success', "Visita {$visita->folioVisita} actualizada correctamente.");
    }

    public function destroy(Visita $visita)
    {
        $visita->delete(); // Borrado lógico: triage y medicación se conservan

        return redirect()->route('visitas.index')
            ->with('success', "Visita {$visita->folioVisita} eliminada del historial.");
    }

    private function guardar(VisitaRequest $request): Visita
    {
        // Reintenta si dos usuarios generan el mismo folio al mismo tiempo
        return retry(3, function () use ($request) {
            return DB::transaction(function () use ($request) {
                $datos = $request->datosVisita();
                $visita = Visita::create($datos + [
                        'folioVisita' => Visita::generarFolio(Carbon::parse($datos['fecha'])),
                        'idUsuario'   => Auth::id(),
                    ]);

                $this->guardarTriage($visita, $request->validated('triage', []));
                $this->guardarMedicacion($visita, $request->validated('medicamentos', []));

                return $visita;
            });
        }, 0, fn ($e) => $e instanceof UniqueConstraintViolationException);
    }

    private function guardarTriage(Visita $visita, ?array $triage): void
    {
        $triage = array_filter($triage ?? [], fn ($v) => $v !== null && $v !== '');

        if (empty($triage)) {
            return; // No borra un triage existente si el formulario llegó vacío
        }

        $imc = null;
        if (!empty($triage['peso']) && !empty($triage['talla'])) {
            $imc = round($triage['peso'] / ($triage['talla'] ** 2), 2);
        }

        DatosAntropometricos::updateOrCreate(
            ['idVisita' => $visita->idVisita],
            [
                'peso'                   => $triage['peso'] ?? null,
                'talla'                  => $triage['talla'] ?? null,
                'imc'                    => $imc,
                'temperatura'            => $triage['temperatura'] ?? null,
                'presionSistolica'       => $triage['presionSistolica'] ?? null,
                'presionDiastolica'      => $triage['presionDiastolica'] ?? null,
                'pulsoPpm'               => $triage['pulsoPpm'] ?? null,
                'frecuenciaRespiratoria' => $triage['frecuenciaRespiratoria'] ?? null,
                'saturacionOxigeno'      => $triage['saturacionOxigeno'] ?? null,
            ]
        );
    }

    private function guardarMedicacion(Visita $visita, ?array $medicamentos, bool $reemplazar = false): void
    {
        if ($reemplazar) {
            $visita->registrosMedicacion()->delete();
        }

        foreach ($medicamentos ?? [] as $m) {
            $dosis = (filled($m['dosisValor'] ?? null) && filled($m['dosisUnidad'] ?? null))
                ? \App\Models\Medicamento::textoConcentracion($m['dosisValor'], $m['dosisUnidad'])
                : null;

            $visita->registrosMedicacion()->create([
                'idMedicamento'     => $m['idMedicamento'],
                'dosis'             => $dosis,
                'viaAdministracion' => $m['viaAdministracion'] ?? null,
                'frecuenciaUso'     => $m['frecuenciaUso'] ?? null,
                'indicaciones'      => $m['indicaciones'] ?? null,
            ]);
        }
    }

    private function catalogos(iterable $medicamentosEnUso = []): array
    {
        return [
            'expedientes'  => Expediente::where('estado', 'activo')->with('paciente')
                ->orderBy('folioExpediente')->get(),
            'medicamentos' => Medicamento::disponibles($medicamentosEnUso)->orderBy('nombre')
                ->get(['idMedicamento', 'nombre', 'concentracion', 'presentacion']),
        ];
    }
}
