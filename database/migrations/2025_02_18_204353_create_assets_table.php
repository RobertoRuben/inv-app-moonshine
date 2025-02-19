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
        Schema::disableForeignKeyConstraints();
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('bar_code')->nullable();
            $table->text('code')->unique();
            $table->text('name');
            $table->unsignedBigInteger('asset_category_id');
            $table->foreign('asset_category_id')->references('id')->on('asset_categories');
            $table->text('label')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('quantity')->default(1);
            $table->longText('image')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
