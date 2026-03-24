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
        Schema::create('nota_recepcion', function (Blueprint $table) {
            $table->id("id_recepcion");

            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_usuario');

            $table->date('fecha');
            $table->integer('cantidad');
            $table->decimal('precio_total', 10, 2); 
            $table->decimal('a_cuenta', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2);
            $table->string('tipo_pago')->default('Efectivo'); // Efectivo, QR, Transferencia
            $table->text('observacion')->nullable();
            $table->string('tipoReserva')->default('En tienda'); // "En tienda" o "Mobil"
            $table->boolean('estado')->default(true); 
            $table->timestamps();

            $table->foreign('id_cliente')
                  ->references('id_cliente')
                  ->on('cliente')
                  ->onDelete('restrict'); // Evita borrar un cliente si tiene notas

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_recepcion');
    }
};
