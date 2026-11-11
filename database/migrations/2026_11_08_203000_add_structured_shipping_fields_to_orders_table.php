<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_street', 150)->nullable();
            $table->string('shipping_exterior_number', 30)->nullable();
            $table->string('shipping_interior_number', 30)->nullable();
            $table->string('shipping_neighborhood', 120)->nullable();
            $table->string('shipping_postal_code', 5)->nullable();
            $table->string('shipping_municipality', 120)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->text('shipping_references')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_street',
                'shipping_exterior_number',
                'shipping_interior_number',
                'shipping_neighborhood',
                'shipping_postal_code',
                'shipping_municipality',
                'shipping_state',
                'shipping_references',
            ]);
        });
    }
};
