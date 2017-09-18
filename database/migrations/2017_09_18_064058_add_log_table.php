<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("logs", function (Blueprint $table){
           $table->increments("log_id");
           $table->integer("team_id");
           $table->integer("challenge_id");
           $table->integer("level_id");
           $table->integer("category_id");
           $table->string("status");
           $table->string("flag");
           $table->decimal("score");
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
        //
    }
}
