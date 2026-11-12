<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Http\Requests\MedicamentoRequest;
use App\Models\Medicamento;

class MedicamentoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $filtro = $request->query('filtro');

        $medicamentos = Medicamento::query()

            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(fn ($w) => $w->where('nombre', 'ilike', $like)
                    ->orWhere('principioActivo', 'ilike', $like));
                $query->where(fn ($w) => $w->where('nombre', 'ilike', $like)
                    ->orWhere('principioActivo', 'ilike', $like)
                    ->orWhere('laboratorio', 'ilike', $like));
            })
            ->when($filtro === 'controlado', fn ($query) => $query->where('esControlado', true))
            ->when($filtro === 'receta', fn ($query) => $query->where('requiereReceta', true))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();



        return view('medicamentos.index', compact('medicamentos', 'q', 'filtro'));
    }

    public function create()
    {
        return view('medicamentos.create', $this->catalogos());
    }

    public function store(MedicamentoRequest $request)
    {
        try {
            $medicamento = Medicamento::create($request->datos());
        } catch (UniqueConstraintViolationException) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['nombre' => ['Ya existe un medicamento con estos mismos datos.']]], 422);
            }
            return back()->withInput()->withErrors(['nombre' => 'Ya existe un medicamento con estos mismos datos.']);
        }

        // Alta rápida desde el modal del expediente
        if ($request->expectsJson()) {
            return response()->json([
                'idMedicamento' => $medicamento->idMedicamento,
                'texto'         => trim($medicamento->nombre . ' ' . ($medicamento->concentracion ?? ''))
                    . ($medicamento->presentacion ? ' · ' . $medicamento->presentacion : ''),
            ], 201);
        }

        // Los estudiantes no tienen acceso al catálogo, solo a registrar
        $destino = $request->user()->can('manage-clinical-data') ? 'medicamentos.index' : 'dashboard';

        return redirect()->route($destino)->with('success', 'Medicamento creado exitosamente.');
    }

    public function edit(Medicamento $medicamento)
    {
        return view('medicamentos.edit', ['medicamento' => $medicamento] + $this->catalogos());
    }


    public function update(MedicamentoRequest $request, Medicamento $medicamento)
    {
        try {
            $medicamento->update($request->datos());
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe un medicamento con estos mismos datos.']);
        }

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento actualizado exitosamente.');
    }

    public function destroy(Medicamento $medicamento)
    {
        try {
            $medicamento->delete();
        } catch (QueryException $e) {
            // 23503 = violación de llave foránea (ya usado en registros de medicación)
            if ((string) $e->getCode() === '23503') {
                return back()->with('error', 'No se puede eliminar: el medicamento ya está registrado en visitas.');
            }
            throw $e;
        }

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento eliminado exitosamente.');
    }

    private function catalogos(): array
    {
        return [
            'principiosActivos' => Medicamento::query()->distinct()->orderBy('principioActivo')->pluck('principioActivo'),
        ];
    }
}
