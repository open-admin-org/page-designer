<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageDesignerVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_designer_video', function (Blueprint $table) {
            $table->increments('id');
            $table->string('page_id')->nullable();
            $table->string('thumb')->nullable();
            $table->string('thumb_video')->nullable();
            $table->string('video')->nullable();
            $table->string('title')->nullable();
            $table->string('alt')->nullable();
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
        Schema::dropIfExists('page_designer_movie');
    }
}
