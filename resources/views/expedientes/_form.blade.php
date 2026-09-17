@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nombres</label>
        <input type="text" name="nombres" value="{{ old('nombres', $expediente->nombres ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @error('nombres') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Apellidos</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $expediente->apellidos ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @error('apellidos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', isset($expediente) ? $expediente->fecha_nacimiento->format('Y-m-d') : '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @error('fecha_nacimiento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Sexo</label>
        <select name="sexo" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">Selecciona…</option>
            @foreach (['hombre' => 'Hombre', 'mujer' => 'Mujer', 'otro' => 'Otro'] as $value => $label)
                <option value="{{ $value }}" @selected(old('sexo', $expediente->sexo ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('sexo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">CURP</label>
        <input type="text" name="curp" maxlength="18"
               value="{{ old('curp', $expediente->curp ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono uppercase">
        <p class="text-xs text-slate-400 mt-1">Ingrésalo manualmente conforme al documento oficial.</p>
        @error('curp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Peso (kg)</label>
        <input type="number" step="0.01" name="peso" id="peso"
               value="{{ old('peso', $expediente->peso ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @error('peso') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Altura (m)</label>
        <input type="number" step="0.01" name="altura" id="altura"
               value="{{ old('altura', $expediente->altura ?? '') }}"
               class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @error('altura') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 bg-slate-50 rounded-lg p-4 border border-slate-200">
        <p class="text-sm text-slate-500">IMC calculado</p>
        <p id="imc-resultado" class="text-2xl font-bold text-slate-800 mt-1">—</p>
        <p id="imc-categoria" class="text-xs text-slate-400 mt-1"></p>
    </div>
</div>

<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('expedientes.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
        Cancelar
    </a>
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
        Guardar
    </button>
</div>

<script>
    function calcularIMC() {
        const peso = parseFloat(document.getElementById('peso').value);
        const altura = parseFloat(document.getElementById('altura').value);
        const resultado = document.getElementById('imc-resultado');
        const categoria = document.getElementById('imc-categoria');

        if (!peso || !altura || altura <= 0) {
            resultado.textContent = '—';
            categoria.textContent = '';
            return;
        }

        const imc = peso / (altura * altura);
        resultado.textContent = imc.toFixed(2);

        let texto = '';
        if (imc < 18.5) texto = 'Bajo peso';
        else if (imc < 25) texto = 'Peso normal';
        else if (imc < 30) texto = 'Sobrepeso';
        else texto = 'Obesidad';

        categoria.textContent = texto;
    }

    document.getElementById('peso').addEventListener('input', calcularIMC);
    document.getElementById('altura').addEventListener('input', calcularIMC);
    document.addEventListener('DOMContentLoaded', calcularIMC);
</script>
