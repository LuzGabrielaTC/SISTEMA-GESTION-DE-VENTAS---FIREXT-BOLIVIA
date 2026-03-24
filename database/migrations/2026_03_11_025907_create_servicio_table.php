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
        Schema::create('servicio', function (Blueprint $table) {
            $table->unsignedBigInteger('id_item_servicio')->primary();
            
            $table->string('tipo_gas'); 
            
            $table->timestamps();

            $table->foreign('id_item_servicio')
                  ->references('id_item')
                  ->on('item')
                  ->onDelete('cascade'); // Si se borra el Item, se borra el detalle del servicio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicio');
    }
};