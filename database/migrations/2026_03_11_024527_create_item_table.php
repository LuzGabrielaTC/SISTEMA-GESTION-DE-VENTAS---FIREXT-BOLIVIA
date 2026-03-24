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
        Schema::create('item', function (Blueprint $table) {
            $table->id('id_item');

           // Ambos se definen como nullable para permitir la creación libre del Item
            $table->unsignedBigInteger('id_recepcion')->nullable();
            $table->unsignedBigInteger('id_entrega')->nullable();

            $table->string('marca')->nullable();
            $table->string('articulo')->nullable();
            $table->decimal('capacidad', 8, 2)->nullable(); // 6.00, 10.00
            $table->string('unidad')->nullable(); // kg, lb, unidades
            $table->string('serie')->nullable();
            
            $table->decimal('precio', 10, 2); 
            $table->text('descripcion')->nullable();
            
            $table->boolean('estado')->default(true);
            
            $table->timestamps();

            //Relaciones con comportamiento seguro
            $table->foreign('id_recepcion')
                  ->references('id_recepcion')
                  ->on('nota_recepcion')
                  ->onDelete('set null'); // Si se borra la nota, el item queda libre

            $table->foreign('id_entrega')
                  ->references('id_entrega')
                  ->on('nota_entrega')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item');
    }
};