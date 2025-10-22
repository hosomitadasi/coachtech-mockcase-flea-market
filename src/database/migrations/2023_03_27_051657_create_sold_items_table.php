<?php

use Illuminate\Database\Migrations\Migration;
// Laravel の マイグレーション機能を使うためのクラスを読み込み。
use Illuminate\Database\Schema\Blueprint;
// テーブル定義（どんなカラムを作るか）を記述するための Blueprint（設計図）クラスを読み込み。
use Illuminate\Support\Facades\Schema;
// Schemaファサードを使うことで、データベースに対してcreate・drop などの操作を実行できる。

class CreateSoldItemsTable extends Migration
{

    public function up()
    {
        Schema::create('sold_items', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            // foreignId('item_id')：item_idという外部キー（整数型）を作成。
            // constrained()：関連先は自動的にitemsテーブルのid カラムと認識される。
            // cascadeOnDelete()：もし関連する商品（item）が削除された場合、自動的にこのテーブルの対応レコードも削除。
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // item_idと同様の処理。
            $table->string('sending_postcode');
            // 配送先の郵便番号を保存するためのカラム。
            $table->string('sending_address');
            // 配送先の住所（都道府県・市区町村など）を保存。
            $table->string('sending_building')->nullable();
            // 建物名・部屋番号などの任意入力欄。nullableにより任意入力となっている。
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sold_items');
    }
}
