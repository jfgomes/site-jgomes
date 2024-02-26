<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsPtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('locations_pt', function (Blueprint $table) {
            $table->increments('id');
            $table->text('district_code');
            $table->text('district_name');
            $table->text('municipality_code');
            $table->text('municipality_name');
            $table->text('parish_code');
            $table->text('parish_name');
            $table->integer('population');
            $table->text('rural')->nullable();
            $table->text('coastal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('locations_pt');
    }
}
