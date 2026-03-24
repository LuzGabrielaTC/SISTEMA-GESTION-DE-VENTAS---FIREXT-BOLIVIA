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
        Schema::create('ingreso', function (Blueprint $table) {
            $table->id('id_ingreso');
            $table->unsignedBigInteger('id_entrega')->nullable();
            $table->unsignedBigInteger('id_recepcion')->nullalble(); 

            $table->string('tipo_pago');
            $table->decimal('monto', 10, 2);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('id_entrega')
                  ->references('id_entrega')
                  ->on('nota_entrega')
                  ->onDelete('restrict'); // Evita borrar una nota de entrega si tiene ingresos
            $table->foreign('id_recepcion')
                  ->references('id_recepcion')
                  ->on('nota_recepcion')
                  ->onDelete('restrict'); // Evita borrar una nota de recepción si tiene ingresos

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingreso');
    }
};
