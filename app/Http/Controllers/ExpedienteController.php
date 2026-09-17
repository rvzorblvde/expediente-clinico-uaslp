<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function index()
    {
        $expedientes = Expediente::withTrashed()
            ->orderBy('expedientes.id')
            ->paginate(15);

        return view('expedientes.index', compact('expedientes'));
    }

    public function create()
    {
        return view('expedientes.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        Expediente::create($data);

        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente creado correctamente.');
    }

    public function show(Expediente $expediente)
    {
        return view('expedientes.show', compact('expediente'));
    }

    public function edit(Expediente $expediente)
    {
        return view('expedientes.edit', compact('expediente'));
    }

    public function update(Request $request, Expediente $expediente)
    {
        $data = $this->validarDatos($request, $expediente->id);
        $expediente->update($data);

        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente actualizado.');
    }

    // "Desactivar" = soft delete
    public function destroy(Expediente $expediente)
    {
        $expediente->delete();
        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente desactivado.');
    }

    // Reactivar un expediente
    public function restore($id)
    {
        Expediente::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente reactivado.');
    }

    private function validarDatos(Request $request, $ignorarId = null): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'sexo' => ['required', 'in:hombre,mujer,otro'],
            'curp' => [
                'required', 'string', 'size:18',
                'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[A-Z0-9]{2}$/',
                'unique:expedientes,curp' . ($ignorarId ? ",$ignorarId" : ''),
            ],
            'peso' => ['required', 'numeric', 'min:1', 'max:500'],
            'altura' => ['required', 'numeric', 'min:0.3', 'max:2.5'],
        ], [
            'curp.regex' => 'El CURP no tiene un formato válido.',
        ]);
    }
}
