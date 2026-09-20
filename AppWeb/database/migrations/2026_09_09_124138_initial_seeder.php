<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();

            // Inserción de roles
            DB::table('roles')->insert([
                [
                    'name' => 'ADMINISTRADOR',
                    'description' => 'Administrador del sistema.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'RECEPCIONISTA',
                    'description' => 'Encargado de recepción y atención al cliente.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'TECNICO',
                    'description' => 'Encargado de reparación y mantenimiento de equipos.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

            DB::table('users')->insert([
                [
                    'id_rol' => 1,
                    'name' => 'Nelson',
                    'lastname' => 'Ixcolín',
                    'username' => 'administrador',
                    'email' => 'iservicesanmarcos@gmail.com',
                    'password' => bcrypt('admin123'),
                    'state' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);

            DB::table('device_types')->insert([
                ['name' => 'Teléfono', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Tablet', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Laptop', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Consola', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Otros', 'created_at' => $now, 'updated_at' => $now],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
