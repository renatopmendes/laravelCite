<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDenouncesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denounces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('page_id')->nullable();
            $table->unsignedBigInteger('denouncer_id')->nullable();
            $table->text('denounce');
            $table->timestamps();
        });

        Schema::table('denounces', function (Blueprint $table) {
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
            $table->foreign('denouncer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('denounces');
    }
}
