<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicamento;

class MedicamentoController extends Controller
{
    public function index()
{
    $medicamentos = Medicamento::all();
    return view('medicamentos.index', compact('medicamentos'));
}

public function create()
{
    return view('medicamentos.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:150',
        'principioActivo' => 'required|string|max:150',
        'presentacion' => 'nullable|string|max:100',
        'concentracion' => 'nullable|string|max:100',
        'requiereReceta' => 'boolean',
        'esControlado' => 'boolean',
    ]);

    Medicamento::create($validated);

    return redirect()->route('medicamentos.index')
                     ->with('success', 'Medicamento creado exitosamente.');
}

public function edit(Medicamento $medicamento)
{
    return view('medicamentos.edit', compact('medicamento'));
}

public function update(Request $request, Medicamento $medicamento)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:150',
        'principioActivo' => 'required|string|max:150',
        'presentacion' => 'nullable|string|max:100',
        'concentracion' => 'nullable|string|max:100',
        'requiereReceta' => 'boolean',
        'esControlado' => 'boolean',
    ]);

    $medicamento->update($validated);

    return redirect()->route('medicamentos.index')
                     ->with('success', 'Medicamento actualizado exitosamente.');
}

public function destroy(Medicamento $medicamento)
{
    $medicamento->delete();

    return redirect()->route('medicamentos.index')
                     ->with('success', 'Medicamento eliminado exitosamente.');
}
}
