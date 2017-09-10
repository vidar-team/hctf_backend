<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSomeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("teams", function (Blueprint $table){
            $table->renameColumn("id", "team_id");
            $table->renameColumn("teamName", "team_name");
        });
        Schema::table("challenges", function (Blueprint $table){
            $table->renameColumn("id", "challenge_id");
        });
        Schema::table("flags", function (Blueprint $table){
            $table->renameColumn("id", "flag_id");
            $table->renameColumn("qid", "challenge_id");
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
