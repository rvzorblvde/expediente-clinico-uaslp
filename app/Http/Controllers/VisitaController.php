<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitaController extends Controller
{
    public function index()
    {
        $visitas = Visita::with(['usuario', 'expediente'])->orderBy('fecha', 'desc')->paginate(10);
        return view('visitas.index', compact('visitas'));
    }

    public function create()
    {
        return view('visitas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'folioExpediente' => 'nullable|string|max:50',
            'fecha' => 'required|date|after_or_equal:now', // Evita fechas y horas anteriores al momento actual
            'motivo' => 'required|string',
            'estado' => 'required|string|max:20',
        ]);

        Visita::create([
            'folioExpediente' => $request->folioExpediente,
            'idUsuario' => Auth::id() ?? 1, // Toma el usuario logueado actualmente
            'fecha' => $request->fecha,
            'motivo' => $request->motivo,
            'estado' => $request->estado ?? 'completada',
        ]);

        return redirect()->route('visitas.index')->with('success', 'La visita se ha registrado correctamente.');
    }

    public function show(Visita $visita)
    {
        // Carga la visita con sus relaciones si existen
        $visita->load(['usuario', 'expediente']);
        return view('visitas.show', compact('visita'));
    }

    public function edit(Visita $visita)
    {
        return view('visitas.edit', compact('visita'));
    }

    public function update(Request $request, Visita $visita)
    {
        $request->validate([
            'folioExpediente' => 'nullable|string|max:50',
            'fecha' => 'required|date',
            'motivo' => 'required|string',
            'estado' => 'required|string|max:20',
        ]);

        $visita->update([
            'folioExpediente' => $request->folioExpediente,
            'fecha' => $request->fecha,
            'motivo' => $request->motivo,
            'estado' => $request->estado,
        ]);

        return redirect()->route('visitas.index')->with('success', 'La visita se ha actualizado correctamente.');
    }

    public function destroy(Visita $visita)
    {
        $visita->delete();
        return redirect()->route('visitas.index')->with('success', 'Visita eliminada del historial.');
    }
}