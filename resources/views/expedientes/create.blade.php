@extends('layouts.app')
@section('title', 'Nuevo expediente')
@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold text-slate-800 mb-6">Nuevo expediente</h1>
        <form method="POST" action="{{ route('expedientes.store') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            @include('expedientes._form')
        </form>
    </div>
@endsection
