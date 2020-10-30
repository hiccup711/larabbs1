<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table){
//            topics 表的 user_id 与 users 表的 id字段关联，当 users 表发生删除事件，被删除的 id 会关联 topics 表 user_id 字段
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('replies', function (Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics', function (Blueprint $table){
//            移除外键
            $table->dropForeign('user_id');
        });

        Schema::table('replies', function (Blueprint $table){
//            移除外键
            $table->dropForeign('user_id');
            $table->dropForeign('topic_id');
        });
    }
}
