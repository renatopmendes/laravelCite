<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('color')->nullable();
            $table->string('family')->nullable();
            $table->text('textSize')->nullable();
            $table->text('message')->nullable();
            $table->string('youtube')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->text('commentary')->nullable();
            $table->unsignedInteger('views')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
