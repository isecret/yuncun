<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hot_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('song_id')->index();
            $table->integer('user_id');
            $table->string('nickname');
            $table->string('avatar_url');
            $table->integer('comment_id');
            $table->integer('liked_count');
            $table->text('content');
            $table->timestamp('published_date')->nullable();
            $table->timestamp('checkout_date')->nullable();
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
        Schema::dropIfExists('hot_comments');
    }
}
