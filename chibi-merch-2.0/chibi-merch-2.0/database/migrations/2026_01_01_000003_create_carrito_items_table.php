<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();
            
            // Si manejas usuarios logueados, descomenta esta línea:
            // $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Si manejas el carrito por sesión (invitados), usa esta:
            $table->string('session_id')->nullable()->index();

            // Relación con tu tabla productos
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('cascade');

            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito_items');
    }
};
