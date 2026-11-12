<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $formas = [
        'Tableta', 'Tableta masticable', 'Tableta de liberación prolongada', 'Cápsula',
        'Solución oral', 'Suspensión oral', 'Jarabe', 'Gotas orales', 'Granulado / polvo oral',
        'Solución inyectable', 'Suspensión inyectable', 'Polvo para solución inyectable',
        'Crema', 'Ungüento', 'Gel', 'Solución tópica', 'Parche transdérmico',
        'Supositorio', 'Óvulo', 'Solución oftálmica', 'Solución ótica',
        'Solución nasal', 'Aerosol / inhalador',
    ];

    private array $unidadesConc = [
        'mg', 'g', 'mcg', 'UI', 'mEq', '%',
        'mg/ml', 'mcg/ml', 'UI/ml', 'mg/5 ml', 'g/100 ml', 'mg/g', 'mg/dosis', 'mcg/dosis',
    ];

    private array $unidadesEnvase = ['piezas', 'ml', 'g', 'dosis'];

    private function lista(array $v): string
    {
        return implode(', ', array_map(fn ($x) => "'" . str_replace("'", "''", $x) . "'", $v));
    }

    public function up(): void
    {
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->string('formaFarmaceutica', 40)->nullable();
            $table->decimal('concentracionValor', 10, 4)->nullable();
            $table->string('concentracionUnidad', 20)->nullable();
            $table->unsignedInteger('cantidadEnvase')->nullable();
            $table->string('unidadEnvase', 20)->nullable();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Normalizar datos existentes
        DB::statement('UPDATE medicamentos SET "requiereReceta" = TRUE WHERE "esControlado" = TRUE');
        DB::statement('UPDATE medicamentos SET nombre = btrim(nombre), "principioActivo" = btrim("principioActivo"),
                       presentacion = NULLIF(btrim(presentacion), \'\'), concentracion = NULLIF(btrim(concentracion), \'\')');

        $cortos = DB::selectOne('SELECT count(*) c FROM medicamentos WHERE char_length(nombre) < 2 OR char_length("principioActivo") < 2')->c;
        if ($cortos > 0) {
            throw new RuntimeException("Hay {$cortos} medicamento(s) con nombre o principio activo de menos de 2 caracteres. Corrígelos y vuelve a migrar.");
        }

        $dups = DB::select('SELECT lower(nombre) n, lower("principioActivo") p, lower(coalesce(presentacion, \'\')) pr,
                                   lower(coalesce(concentracion, \'\')) c, count(*) total
                            FROM medicamentos GROUP BY 1, 2, 3, 4 HAVING count(*) > 1');
        if ($dups) {
            $detalle = collect($dups)->map(fn ($d) => "{$d->n} / {$d->p} / {$d->pr} / {$d->c} (x{$d->total})")->implode('; ');
            throw new RuntimeException("Hay medicamentos duplicados, elimínalos o únelos antes de migrar: {$detalle}");
        }

        DB::statement('CREATE UNIQUE INDEX medicamentos_unico_idx ON medicamentos
            (lower(nombre), lower("principioActivo"), lower(coalesce(presentacion, \'\')), lower(coalesce(concentracion, \'\')))');

        $formas = $this->lista($this->formas);
        $uConc = $this->lista($this->unidadesConc);
        $uEnv = $this->lista($this->unidadesEnvase);

        DB::statement("ALTER TABLE medicamentos
            ADD CONSTRAINT medicamentos_nombre_chk CHECK (char_length(btrim(nombre)) >= 2),
            ADD CONSTRAINT medicamentos_principio_chk CHECK (char_length(btrim(\"principioActivo\")) >= 2),
            ADD CONSTRAINT medicamentos_controlado_receta_chk CHECK (NOT \"esControlado\" OR \"requiereReceta\"),
            ADD CONSTRAINT medicamentos_conc_valor_chk CHECK (\"concentracionValor\" IS NULL OR \"concentracionValor\" > 0),
            ADD CONSTRAINT medicamentos_conc_par_chk CHECK ((\"concentracionValor\" IS NULL) = (\"concentracionUnidad\" IS NULL)),
            ADD CONSTRAINT medicamentos_envase_cant_chk CHECK (\"cantidadEnvase\" IS NULL OR \"cantidadEnvase\" > 0),
            ADD CONSTRAINT medicamentos_envase_par_chk CHECK ((\"cantidadEnvase\" IS NULL) = (\"unidadEnvase\" IS NULL)),
            ADD CONSTRAINT medicamentos_envase_forma_chk CHECK (\"cantidadEnvase\" IS NULL OR \"formaFarmaceutica\" IS NOT NULL),
            ADD CONSTRAINT medicamentos_forma_chk CHECK (\"formaFarmaceutica\" IS NULL OR \"formaFarmaceutica\" IN ({$formas})),
            ADD CONSTRAINT medicamentos_unidad_conc_chk CHECK (\"concentracionUnidad\" IS NULL OR \"concentracionUnidad\" IN ({$uConc})),
            ADD CONSTRAINT medicamentos_unidad_envase_chk CHECK (\"unidadEnvase\" IS NULL OR \"unidadEnvase\" IN ({$uEnv}))");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS medicamentos_unico_idx');
            foreach (['nombre', 'principio', 'controlado_receta', 'conc_valor', 'conc_par', 'envase_cant',
                         'envase_par', 'envase_forma', 'forma', 'unidad_conc', 'unidad_envase'] as $c) {
                DB::statement("ALTER TABLE medicamentos DROP CONSTRAINT IF EXISTS medicamentos_{$c}_chk");
            }
        }

        Schema::table('medicamentos', function (Blueprint $table) {
            $table->dropColumn(['formaFarmaceutica', 'concentracionValor', 'concentracionUnidad', 'cantidadEnvase', 'unidadEnvase']);
        });
    }
};
