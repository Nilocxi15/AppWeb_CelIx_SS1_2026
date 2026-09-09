<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Inserción de roles
            DB::table('roles')->insert([
                ['name' => 'ADMINISTRADOR',
                'description' => 'Administrador del sistema.'
                ],
                ['name' => 'RECEPCIONISTA',
                'description' => 'Encargado de recepción y atención al cliente.'
                ],
                ['name' => 'TECNICO',
                'description' => 'Encargado de reparación y mantenimiento de equipos.'
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
                    'state' => true                    
                ] 
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
