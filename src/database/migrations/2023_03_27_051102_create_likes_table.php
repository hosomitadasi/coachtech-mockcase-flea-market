<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{

    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // user_idカラムを作成し、usersテーブルのidと関連付ける。foreignId()は外部キー（別テーブルと結びつくカラム）を作る命令。constrained()は自動的に「users.id」を参照する（外部キー制約）。cascadeOnDelete()はユーザーが削除されたらその人の言い値情報も自動で削除するという設定。
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            // こちらはitemsテーブルのidを参照。
            $table->unique(['user_id', 'item_id']);
            // user_idとitem_idの組み合わせを一意（ユニーク）制約にする。「同じユーザーが同じ商品に二重にいいねできない」ように制限。
            $table->timestamps();
            // created_at（作成日時）と updated_at（更新日時）の2つのカラムを自動で追加。Laravelで自動的に管理してくれる。
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
