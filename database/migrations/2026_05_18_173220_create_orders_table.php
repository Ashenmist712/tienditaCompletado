<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // nullable() y 'set null' es una excelente práctica que usaste. 
            // Si el usuario borra su cuenta, no pierdes tu historial de ventas.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pendiente');
            
            // --- NUEVOS CAMPOS RECOMENDADOS PARA EL ENVÍO ---
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('notas')->nullable(); // Por si el cliente deja instrucciones
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
