<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Helpers globales
|--------------------------------------------------------------------------
| Registrado en composer.json → autoload.files. Solo funciones genéricas
| y transversales al dominio. Nada específico de un módulo.
*/

if (! function_exists('usuarioAutenticado')) {
    /**
     * Usuario autenticado, ya sea por sesión (web) o por el guard `api`.
     */
    function usuarioAutenticado(): User|Authenticatable|null
    {
        return auth()->user() ?? auth('api')->user();
    }
}

if (! function_exists('entornoEstaEnProduccion')) {
    function entornoEstaEnProduccion(): bool
    {
        return app()->environment('production');
    }
}

if (! function_exists('entornoEstaEnCapacitacion')) {
    function entornoEstaEnCapacitacion(): bool
    {
        return app()->environment('staging');
    }
}

if (! function_exists('entornoEstaLocal')) {
    function entornoEstaLocal(): bool
    {
        return app()->environment('local');
    }
}

/*
|--------------------------------------------------------------------------
| Texto
|--------------------------------------------------------------------------
*/

if (! function_exists('primeraLetraMayuscula')) {
    /**
     * "juan PÉREZ" → "Juan pérez"
     */
    function primeraLetraMayuscula(?string $texto): string
    {
        return ucfirst(mb_strtolower((string) $texto, 'UTF-8'));
    }
}

if (! function_exists('cadaPrimerLetraMayuscula')) {
    /**
     * "juan de leÓn" → "Juan De León"
     */
    function cadaPrimerLetraMayuscula(?string $texto): string
    {
        if (empty($texto)) {
            return '';
        }

        return Str::title(mb_strtolower($texto, 'UTF-8'));
    }
}

if (! function_exists('capitalizarNombre')) {
    /**
     * Capitaliza un nombre respetando preposiciones ("juan de la cruz" → "Juan de la Cruz").
     */
    function capitalizarNombre(?string $nombre): string
    {
        if (empty($nombre)) {
            return '';
        }

        $preposiciones = ['de', 'del', 'la', 'las', 'los', 'y'];
        $palabras = explode(' ', mb_strtolower(trim($nombre), 'UTF-8'));

        $resultado = array_map(function ($palabra, $indice) use ($preposiciones) {
            if ($indice > 0 && in_array($palabra, $preposiciones, true)) {
                return $palabra;
            }

            return mb_convert_case($palabra, MB_CASE_TITLE, 'UTF-8');
        }, $palabras, array_keys($palabras));

        return implode(' ', $resultado);
    }
}

if (! function_exists('aMayuscula')) {
    function aMayuscula(?string $texto): string
    {
        return mb_strtoupper((string) $texto, 'UTF-8');
    }
}

if (! function_exists('aMinusculas')) {
    function aMinusculas(?string $texto): string
    {
        return mb_strtolower((string) $texto, 'UTF-8');
    }
}

if (! function_exists('unirTexto')) {
    /**
     * Une elementos ignorando nulos y cadenas vacías.
     * unirTexto(['Juan', null, 'Pérez']) → "Juan Pérez"
     */
    function unirTexto(array $elementos, string $separador = ' '): string
    {
        $elementos = array_filter(
            array_map(fn ($el) => trim((string) $el), $elementos),
            fn ($el) => $el !== '',
        );

        return implode($separador, $elementos);
    }
}

/*
|--------------------------------------------------------------------------
| Fechas
|--------------------------------------------------------------------------
*/

if (! function_exists('fechaLatina')) {
    /**
     * Cualquier fecha → "d/m/Y". Cadena vacía si es null.
     */
    function fechaLatina(?string $fecha = null): string
    {
        return $fecha ? Carbon::parse($fecha)->format('d/m/Y') : '';
    }
}

if (! function_exists('horaMinuto')) {
    /**
     * Cualquier fecha/hora → "H:i". Cadena vacía si es null.
     */
    function horaMinuto($fecha): string
    {
        return $fecha ? Carbon::parse($fecha)->format('H:i') : '';
    }
}

/*
|--------------------------------------------------------------------------
| Números
|--------------------------------------------------------------------------
*/

if (! function_exists('cerosIzquierda')) {
    /**
     * 42 → "00042" (folios, expedientes, correlativos).
     */
    function cerosIzquierda(int $numero, int $longitud = 5): string
    {
        return str_pad((string) $numero, $longitud, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('porcentaje')) {
    /**
     * Porcentaje de `$parte` sobre `$total`, con 2 decimales. 0 si el total es 0.
     */
    function porcentaje($parte, $total): string|int
    {
        return ($total && $total != 0)
            ? number_format($parte * 100 / $total, 2)
            : 0;
    }
}

if (! function_exists('redondearMonto')) {
    function redondearMonto($valor, int $precision = 2, int $mode = PHP_ROUND_HALF_UP): float
    {
        if (is_string($valor)) {
            $valor = str_replace(',', '', $valor);
        }

        return round((float) $valor, $precision, $mode);
    }
}

if (! function_exists('comparar')) {
    /**
     * Compara dos números con un operador dado como string.
     */
    function comparar(int|float $valorA, string $operador, int|float $valorB): bool
    {
        return match ($operador) {
            '>' => $valorA > $valorB,
            '>=' => $valorA >= $valorB,
            '<' => $valorA < $valorB,
            '<=' => $valorA <= $valorB,
            '==', '=' => $valorA == $valorB,
            '!=', '<>' => $valorA != $valorB,
            default => throw new InvalidArgumentException("Operador inválido: $operador"),
        };
    }
}

/*
|--------------------------------------------------------------------------
| Utilidades varias
|--------------------------------------------------------------------------
*/

if (! function_exists('base64ABinario')) {
    /**
     * Convierte un data URI / base64 de imagen en binario (uploads de avatar, firmas, etc.).
     */
    function base64ABinario(string $base64): string|false
    {
        $limpio = preg_replace('/^data:image\/\w+;base64,/', '', $base64);

        return base64_decode($limpio);
    }
}

if (! function_exists('sanitizeBusqueda')) {
    /**
     * Interpreta un texto de búsqueda: DPI (13 dígitos), código (<=9 dígitos) o nombre.
     *
     * @return array{tipo: 'dpi'|'codigo'|'nombre'|'error', valor: string}
     */
    function sanitizeBusqueda(?string $input): array
    {
        if ($input === null || $input === '') {
            return ['tipo' => 'error', 'valor' => 'Entrada no válida'];
        }

        $input = trim($input);
        $soloDigitos = preg_replace('/\D+/', '', $input);

        if (strlen($soloDigitos) > 2) {
            if (strlen($soloDigitos) === 13) {
                return ['tipo' => 'dpi', 'valor' => $soloDigitos];
            }

            if (strlen($soloDigitos) <= 9) {
                return ['tipo' => 'codigo', 'valor' => $soloDigitos];
            }

            return ['tipo' => 'error', 'valor' => 'Entrada no válida'];
        }

        $nombre = preg_replace("/[^\p{L}\s]/u", '', $input);
        $nombre = preg_replace('/\s+/', ' ', $nombre ?? '');
        $nombre = str_replace(' ', '%', trim($nombre));

        if (strlen($nombre) < 3) {
            return ['tipo' => 'error', 'valor' => 'Nombre demasiado corto'];
        }

        return ['tipo' => 'nombre', 'valor' => $nombre];
    }
}

if (! function_exists('normalizarAColeccion')) {
    /**
     * Envuelve cualquier valor (modelo, array, null, Collection) en una Collection.
     */
    function normalizarAColeccion($valor): Collection
    {
        return match (true) {
            is_null($valor) => collect(),
            $valor instanceof Collection => $valor,
            $valor instanceof Model => collect([$valor]),
            is_array($valor) => collect($valor),
            default => collect([$valor]),
        };
    }
}

if (! function_exists('aplicarMultiplesScopes')) {
    /**
     * Aplica una lista de scopes a un query builder.
     * Cada scope puede ser: callable, [nombre, [params]] o string (nombre sin params).
     */
    function aplicarMultiplesScopes(Builder $query, array $scopes): Builder
    {
        foreach ($scopes as $scope) {
            if (is_callable($scope)) {
                $query = $scope($query);
            } elseif (is_array($scope)) {
                [$nombre, $params] = $scope;
                $query = $query->{$nombre}(...$params);
            } elseif (is_string($scope)) {
                $query = $query->{$scope}();
            }
        }

        return $query;
    }
}

if (! function_exists('toArrayProfundo')) {
    /**
     * Convierte un modelo/estructura a array recursivamente (vía JSON).
     */
    function toArrayProfundo($valor): array
    {
        return json_decode(json_encode($valor), true);
    }
}
