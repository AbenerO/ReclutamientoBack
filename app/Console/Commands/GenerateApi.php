<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateApi extends Command
{
    protected $signature = 'make:api {table : Nombre de la tabla} {modelo : Nombre del modelo} {connection? : Nombre de la conexión (opcional)}';
    protected $description = 'Generar API incluyendo Modelo, Controlador y Requests';

    private string $conexion;
    private $schema;
    private string $nombreTabla;
    private string $nombreModelo;
    private string $variable;
    private string $variablePlural;
    private string $permisos;
    private string $namespace = 'App\\Models';
    private string $controllerNamespace = 'App\\Http\\Controllers\\Api';
    private string $requestNamespace = 'App\\Http\\Requests\\Api';

    private array $castFormatoOriginal = [];
    private array $columnasDeTabla = [];
    private array $uniqueColumns = [];

    public function handle(): void
    {
        $this->conexion = $this->argument('connection') ?? config('database.default');
        $this->schema = Schema::connection($this->conexion);
        $this->nombreTabla = $this->argument('table');
        $this->nombreModelo = Str::studly($this->argument('modelo'));
        $this->permisos = preg_replace('/([a-z])([A-Z])/', '$1 $2', $this->nombreModelo);

        // variable en camelCase (ej: TipoSangre → tipoSangre), variablePlural = tabla
        $this->variable = Str::camel($this->argument('modelo'));
        $this->variablePlural = $this->nombreTabla;

        if (! $this->schema->hasTable($this->nombreTabla)) {
            $this->error("La tabla '{$this->nombreTabla}' no existe.");

            return;
        }

        $this->columnasDeTabla = $this->schema->getColumnListing($this->nombreTabla);
        $fillable = $this->generateFillable();
        $validationRules = $this->generateValidationRules();
        $relationships = $this->generateRelationships();
        $casts = $this->generateCasts();

        $this->generateFiles($fillable, $validationRules, $relationships, $casts);
        $this->addRoute();
        $this->addSeeder();

        $this->info('🚀 ¡Generación de archivos completada con éxito!');
    }

    private function generateFiles(string $fillable, string $validationRules, string $relationships, string $casts): void
    {
        $modelo = $this->nombreModelo;
        $controlador = "{$this->nombreModelo}ApiController";
        $createRequest = "Create{$this->nombreModelo}ApiRequest";
        $updateRequest = "Update{$this->nombreModelo}ApiRequest";
        $seedername = "{$this->nombreModelo}TableSeeder";

        $modelPath = app_path("Models/{$modelo}.php");
        $controllerPath = app_path("Http/Controllers/Api/{$controlador}.php");
        $createRequestPath = app_path("Http/Requests/Api/{$createRequest}.php");
        $updateRequestPath = app_path("Http/Requests/Api/{$updateRequest}.php");
        $seederPath = "database/seeders/{$seedername}.php";

        $modelStub = File::get(base_path('stubs/api/model.stub'));
        $controllerStub = File::get(base_path('stubs/api/controller.stub'));
        $createRequestStub = File::get(base_path('stubs/api/request-create.stub'));
        $updateRequestStub = File::get(base_path('stubs/api/request-update.stub'));
        $seederStub = File::get(base_path('stubs/api/seeder.stub'));

        // Auto-detectar SoftDeletes desde la columna deleted_at
        $tieneSoftDeletes = in_array('deleted_at', $this->columnasDeTabla);
        $useSoftDeletesCode = $tieneSoftDeletes ? 'use Illuminate\\Database\\Eloquent\\SoftDeletes;' : '';
        $softDeletesTrait = $tieneSoftDeletes ? 'use SoftDeletes;' : '';

        $nombreTabla = str_contains($this->nombreTabla, '.') ? explode('.', $this->nombreTabla)[1] : $this->nombreTabla;

        $fillableCompacto = trim(str_replace(["\n", "\r", " "], '', $fillable));

        $replacements = [
            '{{ updateRulesRequest }}' => $this->uniqueUpdateRequest() ?? "{$this->nombreModelo}::\$rules;",
            '{{ modelNamespace }}' => $this->namespace,
            '{{ requestNamespace }}' => $this->requestNamespace,
            '{{ controllerNamespace }}' => $this->controllerNamespace,
            '{{ controlador }}' => $controlador,
            '{{ model }}' => $this->nombreModelo,
            '{{ variable }}' => $this->variable,
            '{{ variableMayusculaP }}' => ucfirst($this->variable),
            '{{ variable_plural }}' => $this->variablePlural,
            '{{ permisos }}' => $this->permisos,
            '{{ tableNameM }}' => $seedername,
            '{{ createRequest }}' => $createRequest,
            '{{ updateRequest }}' => $updateRequest,
            '{{ fillable }}' => $fillable,
            '{{ validationRules }}' => $validationRules,
            '{{ relationships }}' => $relationships,
            '{{ tableName }}' => $this->nombreTabla,
            '{{ useSoftDeletes }}' => $useSoftDeletesCode,
            '{{ softDeletesTrait }}' => $softDeletesTrait,
            '{{ casts }}' => $casts,
            '{{ seeder }}' => str_replace(',', "=>'',", $fillableCompacto),
            '{{ otro }}' => $fillableCompacto,
        ];

        File::put($modelPath, str_replace(array_keys($replacements), $replacements, $modelStub));
        File::put($controllerPath, str_replace(array_keys($replacements), $replacements, $controllerStub));
        File::put($createRequestPath, str_replace(array_keys($replacements), $replacements, $createRequestStub));
        File::put($updateRequestPath, str_replace(array_keys($replacements), $replacements, $updateRequestStub));
        File::put($seederPath, str_replace(array_keys($replacements), $replacements, $seederStub));

        $this->callSilent('ide-helper:models', [
            'model' => ["App\\Models\\{$modelo}"],
            '--reset' => true,
            '--write' => true,
            '--no-interaction' => true,
        ]);

        $this->newLine();
        $this->info('📁 Estructura de Archivos:');
        $this->line("  ├── <fg=blue>📂 app</>");
        $this->line("  │   ├── <fg=blue>📂 Models</>");
        $this->line("  │   │   └── <fg=green>📄 {$modelo}.php</>");
        $this->line("  │   ├── <fg=blue>📂 Http</>");
        $this->line("  │   │   ├── <fg=blue>📂 Controllers/Api</>");
        $this->line("  │   │   │   └── <fg=green>📄 {$controlador}.php</>");
        $this->line("  │   │   └── <fg=blue>📂 Requests/Api</>");
        $this->line("  │   │       ├── <fg=green>📄 {$createRequest}.php</>");
        $this->line("  │   │       └── <fg=green>📄 {$updateRequest}.php</>");
        $this->line("  ├── <fg=blue>📂 database/seeders</>");
        $this->line("  │   └── <fg=green>📄 {$seedername}.php</>");
        $this->line("  └── <fg=blue>📂 routes</>");
        $this->line("      └── <fg=green>📄 api.php</>");
        $this->newLine();

        // Generar JSON para el generador de CRUD del frontend
        $datosJson = json_encode([
            'Modelo' => $modelo,
            'Ruta' => "api/{$nombreTabla}",
            'columnastypes' => $this->camposTipadosSinTildes(),
            'columnas' => $this->camposTipados(),
            'modeloSeparado' => $this->permisos,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Guardar JSON en storage/generate/
        $storagePath = storage_path("generate/{$modelo}.json");
        if (! is_dir(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }
        File::put($storagePath, $datosJson);
        $this->info("📁 Datos guardados en: {$storagePath}");

        // Copiar JSON al portapapeles según SO
        if (PHP_OS_FAMILY === 'Darwin') {
            Process::run('echo '.escapeshellarg($datosJson).' | pbcopy');
            $this->info('📋 Datos copiados al portapapeles (macOS)');
        } elseif (PHP_OS_FAMILY === 'Windows') {
            // Lee desde el archivo guardado para evitar problemas con caracteres especiales en cmd
            $storagePathSlash = str_replace('\\', '/', $storagePath);
            Process::run("powershell -command \"Get-Content -Raw -Path '{$storagePathSlash}' | Set-Clipboard\"");
            $this->info('📋 Datos copiados al portapapeles (Windows)');
        } else {
            $this->info('⚠️  Copia manual desde: '.$storagePath);
        }

        $this->newLine();
        $this->info('💡 Ahora ejecuta: npm run generarCrudV3 en el frontend');
        $this->newLine();
    }

    private function addRoute(): void
    {
        $routePath = base_path('routes/api.php');

        if (! File::exists($routePath)) {
            $this->error("El archivo de rutas 'routes/api.php' no existe.");

            return;
        }

        $controllerName = "{$this->nombreModelo}ApiController";
        $namespace = 'App\\Http\\Controllers\\Api\\';
        $nombreTabla = str_contains($this->nombreTabla, '.') ? explode('.', $this->nombreTabla)[1] : $this->nombreTabla;

        $rutaColumnas = "Route::get('{$nombreTabla}/getColumnas', [{$namespace}{$controllerName}::class, 'getColumnas'])->name('{$nombreTabla}.getColumnas');";
        $rutaRecursos = "Route::apiResource('{$nombreTabla}', {$namespace}{$controllerName}::class)->parameters(['{$nombreTabla}' => '{$this->variable}']);";

        $content = File::get($routePath);

        if (str_contains($content, $rutaRecursos)) {
            $this->warn("Las rutas para '{$nombreTabla}' ya existen en api.php.");

            return;
        }

        File::append($routePath, PHP_EOL.$rutaColumnas.PHP_EOL.$rutaRecursos.PHP_EOL);

        $this->newLine();
        $this->info("🌐 Rutas añadidas a 'routes/api.php'");
        $this->newLine();
    }

    private function addSeeder(): void
    {
        $seederPath = base_path('database/seeders/DatabaseSeeder.php');
        $seederClass = "{$this->nombreModelo}TableSeeder::class,";
        $seederLine = "            {$seederClass}";

        if (! File::exists($seederPath)) {
            $this->error('El archivo DatabaseSeeder.php no existe.');

            return;
        }

        $content = File::get($seederPath);

        if (str_contains($content, $seederClass)) {
            $this->warn("El seeder '{$this->nombreModelo}TableSeeder' ya existe en DatabaseSeeder.php.");

            return;
        }

        preg_match('/^\s*\]\);/m', $content, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches)) {
            $this->error('No se encontró el lugar para insertar el seeder en DatabaseSeeder.php.');

            return;
        }

        $position = $matches[0][1];
        $content = substr_replace($content, $seederLine.PHP_EOL, $position, 0);

        File::put($seederPath, $content);

        $this->newLine();
        $this->info("🌱 Seeder añadido a 'DatabaseSeeder.php'");
        $this->newLine();
    }

    private function generateFillable(): string
    {
        $excluded = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $fillable = array_diff($this->columnasDeTabla, $excluded);

        return '['.PHP_EOL."            '".implode("',\n            '", $fillable)."'".PHP_EOL.'        ]';
    }

    private function generateValidationRules(): string
    {
        $columns = $this->schema->getColumns($this->nombreTabla);
        $indexes = $this->schema->getIndexes($this->nombreTabla);
        $uniqueIndexes = array_filter($indexes, fn ($index) => $index['unique']);

        $rules = [];

        foreach ($columns as $column) {
            $columnName = $column['name'];

            if (in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $typeName = $column['type_name'];
            $nullable = $column['nullable'];
            $typeDetails = $column['type'];

            $columnRules = [$nullable ? 'nullable' : 'required'];

            switch ($typeName) {
                case 'varchar':
                case 'char':
                case 'text':
                case 'longtext':
                case 'mediumtext':
                case 'tinytext':
                    $columnRules[] = 'string';
                    if (preg_match('/\((\d+)\)/', $typeDetails, $matches)) {
                        $columnRules[] = "max:{$matches[1]}";
                    }
                    break;

                case 'int':
                case 'bigint':
                case 'smallint':
                case 'tinyint':
                case 'mediumint':
                    $columnRules[] = 'integer';
                    break;

                case 'decimal':
                case 'float':
                case 'double':
                    $columnRules[] = 'numeric';
                    break;

                case 'date':
                case 'datetime':
                case 'timestamp':
                    $columnRules[] = 'date';
                    break;

                case 'boolean':
                    $columnRules[] = 'boolean';
                    break;

                case 'enum':
                    $columnRules[] = 'string';
                    break;

                default:
                    $columnRules[] = 'string';
            }

            foreach ($uniqueIndexes as $index) {
                if (in_array($columnName, $index['columns'])) {
                    $this->uniqueColumns[] = $columnName;
                    $columnRules[] = "unique:{$this->nombreTabla},{$columnName}";
                    break;
                }
            }

            $rules[$columnName] = implode('|', $columnRules);
        }

        $formattedRules = '    ['.PHP_EOL;
        foreach ($rules as $field => $rule) {
            $formattedRules .= "            '{$field}' => '{$rule}',".PHP_EOL;
        }
        $formattedRules .= '        ]';

        return $formattedRules;
    }

    private function generateCasts(): string
    {
        $columns = $this->schema->getColumns($this->nombreTabla);
        $casts = [];

        foreach ($columns as $column) {
            $casts[$column['name']] = match ($column['type_name']) {
                'int', 'bigint', 'smallint', 'tinyint', 'mediumint' => 'integer',
                'decimal', 'float', 'double' => 'float',
                'boolean' => 'boolean',
                'date' => 'date',
                'datetime' => 'datetime',
                'timestamp' => 'timestamp',
                default => 'string',
            };
        }

        $this->castFormatoOriginal = $casts;

        $formattedCasts = '['.PHP_EOL;
        foreach ($casts as $field => $cast) {
            $formattedCasts .= "            '{$field}' => '{$cast}',".PHP_EOL;
        }
        $formattedCasts .= '        ]';

        return $formattedCasts;
    }

    private function generateRelationships(): string
    {
        $foreignKeys = $this->schema->getForeignKeys($this->nombreTabla);
        $relationships = [];

        foreach ($foreignKeys as $foreignKey) {
            $columnName = $foreignKey['columns'][0];
            $referencedTable = $foreignKey['foreign_table'];
            $referencedColumn = $foreignKey['foreign_columns'][0];

            $relationType = $this->determineRelationshipType($foreignKey, $this->nombreTabla);
            $relatedModel = Str::studly(Str::singular($referencedTable));
            $functionName = ($relationType === 'hasMany' || $relationType === 'belongsToMany')
                ? Str::camel(Str::plural($referencedTable))
                : Str::camel(Str::singular($referencedTable));

            $relationships[] = <<<EOT
                public function {$functionName}()
                    {
                        return \$this->{$relationType}({$relatedModel}::class, '{$columnName}', '{$referencedColumn}');
                    }
                EOT;
        }

        return implode("\n\n    ", $relationships);
    }

    private function determineRelationshipType(array $foreignKey, string $nombreTabla): string
    {
        $referencedTable = $foreignKey['foreign_table'];
        $columnName = $foreignKey['columns'][0];
        $referencedColumn = $foreignKey['foreign_columns'][0];

        if ($nombreTabla !== $referencedTable) {
            return 'belongsTo';
        }

        if ($referencedColumn === 'id') {
            return 'hasMany';
        }

        if ($this->isUniqueForeignKey($nombreTabla, $columnName)) {
            return 'hasOne';
        }

        if ($this->isPivotTable($nombreTabla)) {
            return 'belongsToMany';
        }

        if ($this->isPolymorphic($foreignKey)) {
            return 'morphTo';
        }

        return '';
    }

    private function isPivotTable(string $nombreTabla): bool
    {
        $foreignKeys = $this->schema->getForeignKeys($nombreTabla);

        return count($foreignKeys) === 2 && $this->schema->getColumnCount($nombreTabla) <= 3;
    }

    private function isUniqueForeignKey(string $nombreTabla, string $nombreColumna): bool
    {
        foreach ($this->schema->getIndexes($nombreTabla) as $index) {
            if (in_array($nombreColumna, $index['columns']) && $index['unique']) {
                return true;
            }
        }

        return false;
    }

    private function isPolymorphic(array $foreignKey): bool
    {
        $cols = $foreignKey['columns'] ?? [];

        return in_array('morph_id', $cols) && in_array('morph_type', $cols);
    }

    /**
     * Retorna array asociativo campo → tipo PHP (sin id/timestamps)
     */
    private function camposTipados(): array
    {
        $camposOk = array_diff($this->columnasDeTabla, ['id', 'created_at', 'updated_at', 'deleted_at']);

        return array_filter(
            $this->castFormatoOriginal,
            fn ($_, $key) => in_array($key, $camposOk, true),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * Retorna string con campos tipados en TypeScript (sin tildes)
     */
    private function camposTipadosSinTildes(): string
    {
        $camposOk = array_diff($this->columnasDeTabla, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $filtrados = array_filter(
            $this->castFormatoOriginal,
            fn ($_, $key) => in_array($key, $camposOk, true),
            ARRAY_FILTER_USE_BOTH,
        );

        $map = [
            'int' => 'number', 'integer' => 'number',
            'real' => 'number', 'float' => 'number', 'double' => 'number',
            'string' => 'string',
            'bool' => 'boolean', 'boolean' => 'boolean',
            'array' => 'any[]', 'json' => 'any',
            'object' => 'Record<string, unknown>',
            'datetime' => 'string', 'date' => 'string', 'timestamp' => 'string',
        ];

        $lineas = [];
        foreach ($filtrados as $campo => $tipoPhp) {
            $campo = Str::ascii($campo);
            $tipoTs = $map[$tipoPhp] ?? 'any';
            $lineas[] = "  {$campo}: {$tipoTs};";
        }

        return implode("\n", $lineas);
    }

    /**
     * Genera reglas de validación para Update con Rule::unique()->ignore() en columnas únicas.
     * Retorna null si no hay columnas únicas (el stub usará {{ model }}::$rules).
     */
    private function uniqueUpdateRequest(): ?string
    {
        if (empty($this->uniqueColumns)) {
            return null;
        }

        $columns = $this->schema->getColumns($this->nombreTabla);
        $rules = [];

        foreach ($columns as $column) {
            $name = $column['name'];
            $typeName = $column['type_name'];
            $typeDef = $column['type'];
            $nullable = $column['nullable'];

            if (in_array($name, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $columnRules = [$nullable ? 'nullable' : 'required'];

            switch ($typeName) {
                case 'varchar':
                case 'char':
                case 'text':
                case 'longtext':
                    $columnRules[] = 'string';
                    if (preg_match('/\((\d+)\)/', $typeDef, $m)) {
                        $columnRules[] = "max:{$m[1]}";
                    }
                    break;
                case 'int':
                case 'bigint':
                case 'smallint':
                case 'tinyint':
                    $columnRules[] = 'integer';
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                    $columnRules[] = 'numeric';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $columnRules[] = 'date';
                    break;
                case 'boolean':
                    $columnRules[] = 'boolean';
                    break;
                default:
                    $columnRules[] = 'string';
            }

            if (in_array($name, $this->uniqueColumns, true)) {
                $columnRules[] = "Rule::unique('{$this->nombreTabla}', '{$name}')"
                    ."->ignore(\$this->route('{$this->variable}')?->id ?? null, 'id')";
            }

            $rules[$name] = $columnRules;
        }

        $formatted = "[\n";
        foreach ($rules as $field => $fieldRules) {
            $formatted .= "            '{$field}' => [\n";
            foreach ($fieldRules as $r) {
                if (str_starts_with($r, 'Rule::')) {
                    $formatted .= "                {$r},\n";
                } else {
                    $formatted .= "                '{$r}',\n";
                }
            }
            $formatted .= "            ],\n";
        }
        $formatted .= "        ];\n";

        return $formatted;
    }
}