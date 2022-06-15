<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageDesignerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_designer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rank')->nullable();
            $table->string('type')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->string('title_content')->nullable();
            $table->text('body')->nullable();
            $table->text('data')->nullable();
            $table->text('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
