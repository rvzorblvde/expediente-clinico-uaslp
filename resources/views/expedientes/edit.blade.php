@extends('layouts.app')
@section('title', 'Editar expediente')
@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold text-slate-800 mb-6">Editar expediente</h1>
        <form method="POST" action="{{ route('expedientes.update', $expediente) }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @method('PUT')
            @include('expedientes._form')
        </form>
    </div>
@endsection
