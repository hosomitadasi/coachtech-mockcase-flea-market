<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{

    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // 各商品を一意に識別するためのキー。
            $table->string('name');
            // 商品名を保存。
            $table->integer('price');
            // 商品の価格を整数型で保存。
            $table->string('brand')->nullable();
            // ブランド名を保存。nullableにより空でも登録可能。ハンドメイドなどブランドがない場合に対応。
            $table->string('description');
            // 商品の説明文を保存。
            $table->string('img_url');
            //商品画像のURLを保存。storage/app/public やクラウドストレージ上のファイルパスを格納。
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 外部キー user_id を作成。constrained() により、users テーブルの id カラムと関連付け。cascadeOnDelete()でユーザーが削除されたら関連する商品項目も削除。
            $table->foreignId('condition_id')->constrained()->cascadeOnDelete();
            // 外部キーcondition_id を作成。conditions テーブルの id カラムと関連付けし、商品の状態を紐づけ。こちらも該当する状態が削除されたら関連商品も削除される設定。
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
