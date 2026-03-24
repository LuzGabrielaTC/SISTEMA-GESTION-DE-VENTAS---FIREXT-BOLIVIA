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
        Schema::create('nota_entrega', function (Blueprint $table) {
            $table->id('id_entrega');

            $table->unsignedBigInteger('id_recepcion');
            $table->unsignedBigInteger('id_usuario'); // Usuario que entrega

            $table->date('fecha');
            $table->integer('cantidad');
            $table->decimal('precio_total', 10, 2);
            $table->decimal('a_cuenta', 10, 2)->default(0);
            $table->string('tipo_pago')->default('Efectivo'); // Efectivo, QR, Transferencia
            $table->decimal('saldo', 10, 2);
            $table->text('observacion')->nullable();
            $table->string('tipoEntrega')->default('En tienda'); // "En tienda" o "Mobil"
            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('id_recepcion')
                  ->references('id_recepcion')
                  ->on('nota_recepcion')
                  ->onDelete('restrict'); // Evita borrar una nota de recepción si tiene entregas
            
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('restrict'); // Evita borrar un usuario si tiene entregas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_entrega');
    }
};
