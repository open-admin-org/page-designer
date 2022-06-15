<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageDesignerEmbedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_designer_embed', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->nullable();
            $table->string('thumb')->nullable();
            $table->string('thumb_video')->nullable();
            $table->text('embed')->nullable();
            $table->text('embed_data')->nullable();
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
        Schema::dropIfExists('page_designer_vimeo');
    }
}
