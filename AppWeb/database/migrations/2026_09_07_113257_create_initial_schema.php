<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // 1. Catálogo de Roles
            Schema::create('roles', function (Blueprint $table) {
                $table->id('id_rol');
                $table->string('name', 50)->unique();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });

            // 2. Usuarios del Sistema
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_rol')->constrained('roles', 'id_rol')->onUpdate('cascade')->onDelete('restrict');
                $table->string('name', 100);
                $table->string('lastname', 100);
                $table->string('username', 50)->unique();
                $table->string('email', 150)->unique();
                $table->string('password');
                $table->boolean('state')->default(true);
                $table->timestamps();
            });

            // 3. Categorías de Productos
            Schema::create('category_products', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->text('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            // 4. Catálogo de Productos / Accesorios
            Schema::create('products', function (Blueprint $table) {
                $table->string('bar_code', 100)->primary();
                $table->foreignId('id_category')->constrained('category_products')->onUpdate('cascade')->onDelete('restrict');
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->integer('stock')->default(0);
                $table->integer('minium_stock')->default(5);
                $table->decimal('price', 10, 2);
                $table->string('image')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            // 5. Clientes
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('lastname', 100);
                $table->string('phone', 20)->nullable();
                $table->string('dpi', 14)->nullable();
                $table->timestamps();
            });

            // 6. Tipos de Dispositivos (Celular, Consola, Tablet, etc.)
            Schema::create('device_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            // 7. Dispositivos Físicos
            Schema::create('devices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_client')->constrained('clients')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId('id_device_type')->constrained('device_types')->onUpdate('cascade')->onDelete('restrict');
                $table->string('serial_number', 100)->nullable();
                $table->string('brand', 50);
                $table->string('model', 50);
                $table->timestamps();
            });

            // 8. Tickets de Reparación
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_user_receptionist')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignId('id_device')->constrained('devices')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId('id_user_technician')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
                $table->enum('state', [
                    'Recibido',
                    'Diagnóstico',
                    'Reparación',
                    'Finalizado',
                    'Entregado'
                ])->default('Recibido');

                // Problema reportado y datos de acceso
                $table->text('reported_issue'); // Falla reportada
                $table->string('device_password', 100)->nullable(); // Contraseña / PIN / Patrón
                $table->text('reception_notes')->nullable(); // Observaciones iniciales (golpes, estado estético)
                $table->text('technical_diagnosis')->nullable(); // Diagnóstico del técnico

                // Estructura de Cobro
                $table->decimal('total_charged', 10, 2)->default(0.00); // Precio total pactado
                $table->decimal('deposit', 10, 2)->default(0.00); // Anticipo entregado
                // Columna calculada de saldo para PostgreSQL (total_charged - deposit)
                $table->decimal('remaining_balance', 10, 2)->virtualAs('total_charged - deposit');

                // Trazabilidad y Fechas
                $table->uuid('qr_token')->unique();
                $table->timestamp('intake_date')->useCurrent();
                $table->timestamp('return_date')->nullable();
                $table->timestamps();
            });

            // 9. Notas de Seguimiento Técnico
            Schema::create('ticket_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_ticket')->constrained('tickets')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId('id_user')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
                $table->text('note');
                $table->timestamp('creation_date')->useCurrent();
                $table->timestamps();
            });

            // 10. Cabecera de Ventas
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_user_receptionist')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
                $table->timestamp('sale_date')->useCurrent();
                $table->decimal('total_sale', 10, 2)->default(0.00);
                $table->timestamps();
            });

            // 11. Detalle de Ventas (Maestro-Detalle)
            Schema::create('sale_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_sale')->constrained('sales')->onUpdate('cascade')->onDelete('cascade');
                $table->string('product_bar_code', 100);
                $table->foreign('product_bar_code')->references('bar_code')->on('products')->onUpdate('cascade')->onDelete('restrict');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();
            });

            // 12. Movimientos de Inventario (Entradas y Salidas)
            Schema::create('inventory_movements', function (Blueprint $table) {
                $table->id();
                $table->string('product_bar_code', 100);
                $table->foreign('product_bar_code')->references('bar_code')->on('products')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignId('id_user')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignId('id_sale')->nullable()->constrained('sales')->onUpdate('cascade')->onDelete('set null');
                $table->integer('quantity');
                $table->enum('movement_type', ['ENTRADA', 'SALIDA', 'AJUSTE']);
                $table->string('reason', 255)->nullable();
                $table->timestamp('date')->useCurrent();
                $table->timestamps();
            });

            // 13. Transacciones Financieras (Libro Mayor ERP)
            Schema::create('financial_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_sale')->nullable()->constrained('sales')->onUpdate('cascade')->onDelete('set null');
                $table->foreignId('id_ticket')->nullable()->constrained('tickets')->onUpdate('cascade')->onDelete('set null');
                $table->decimal('amount', 10, 2);
                $table->enum('type', ['INGRESO', 'EGRESO']);
                $table->string('concept', 255);
                $table->timestamp('date')->useCurrent();
                $table->timestamps();
            });

            // 14. Códigos de Recuperación de Contraseña
            Schema::create('password_reset_codes', function (Blueprint $table) {
                $table->id();
                $table->string('email', 150)->index();
                $table->foreign('email')->references('email')->on('users')->onUpdate('cascade')->onDelete('restrict');
                $table->string('code', 255);
                $table->timestamp('expires_at');
                $table->boolean('is_used')->default(false);
                $table->timestamps();
            });

            // 15. Tabla de Sesiones (Laravel)
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            Schema::dropIfExists('password_reset_codes');
            Schema::dropIfExists('financial_transactions');
            Schema::dropIfExists('inventory_movements');
            Schema::dropIfExists('sale_details');
            Schema::dropIfExists('sales');
            Schema::dropIfExists('ticket_notes');
            Schema::dropIfExists('tickets');
            Schema::dropIfExists('devices');
            Schema::dropIfExists('device_types');
            Schema::dropIfExists('clients');
            Schema::dropIfExists('products');
            Schema::dropIfExists('category_products');
            Schema::dropIfExists('users');
            Schema::dropIfExists('roles');
            Schema::dropIfExists('sessions');
        });
    }
};
