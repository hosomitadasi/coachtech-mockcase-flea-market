<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{

    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // コメントを書いたユーザーを示す外部キー user_id を定義。
            // foreignId('user_id')：外部キー（他テーブルとの紐づけ用のカラム）を作成。
            // constrained()：自動的に users.id を参照するように設定。
            // cascadeOnDelete()：ユーザーが削除されたら、そのユーザーのコメントも削除。
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            // 外部キーをitem_idを定義する。
            $table->string('comment');
            // コメントの本文を保存するためのカラム。string型は最大255文字の短めのテキスト用。長文を扱う場合はtext型を使う場合もあり。
            $table->timestamps();
            // created_at（作成日時）と updated_at（更新日時）の2つのカラムを自動で追加。Laravelが自動で管理。
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
