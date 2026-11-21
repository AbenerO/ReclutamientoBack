<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetearPasswordsUsuarios extends Command
{
    protected $signature   = 'usuarios:resetear-passwords
                                {--solo-inactivos : Resetear solo usuarios inactivos}
                                {--id=* : IDs específicos de usuarios a resetear}';

    protected $description = 'Resetea las contraseñas de los usuarios al patrón Cz{año}00 (ej: Cz202600)';

    public function handle(): int
    {
        $anio     = date('Y');
        $password = "Cz{$anio}00";

        $query = User::query();

        // Filtrar por IDs específicos si se pasaron
        $ids = $this->option('id');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        // Filtrar solo inactivos si se indicó
        if ($this->option('solo-inactivos')) {
            $query->where('estado', false);
        }

        $usuarios = $query->get();

        if ($usuarios->isEmpty()) {
            $this->warn('No se encontraron usuarios con los criterios indicados.');
            return self::SUCCESS;
        }

        $this->info("Se resetearán {$usuarios->count()} usuario(s) con la contraseña: {$password}");
        $this->newLine();

        // Tabla de preview
        $this->table(
            ['ID', 'Nombre', 'Email', 'Estado'],
            $usuarios->map(fn($u) => [
                $u->id,
                $u->name,
                $u->email,
                $u->estado ? 'Activo' : 'Inactivo',
            ])
        );

        $this->newLine();

        if (!$this->confirm('¿Confirmar el reseteo de contraseñas?', false)) {
            $this->line('Operación cancelada.');
            return self::SUCCESS;
        }

        $hash = Hash::make($password);
        $reseteados = 0;

        foreach ($usuarios as $usuario) {
            $usuario->update(['password' => $hash]);
            $this->line("  ✓ {$usuario->name} ({$usuario->email})");
            $reseteados++;
        }

        $this->newLine();
        $this->info("✓ {$reseteados} contraseña(s) reseteadas a: {$password}");

        return self::SUCCESS;
    }
}
