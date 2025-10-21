<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryItemsTable extends Migration
{

    public function up()
    {
        Schema::create('category_items', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            // item_idカラムを作成し、foreign key（外部キー） として設定。constrained()により、自動的にitemsテーブルのidカラムと関連付けられる。cascadeOnDelete()にてitemsテーブルの同じidが削除された場合は、関連するcategory_itemsのデータも自動で削除する設定。
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            // category_id カラムを同様に定義。
            $table->unique(['item_id', 'category_id']);
            // 複合ユニークキー（複合主キー） を設定。同じ組み合わせ（同じアイテムが同じカテゴリに二重登録されること）を防ぐ。
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('category_items', function (Blueprint $table) {
            $table->dropForeign('category_items_item_id_foreign');
        });
        Schema::dropIfExists('category_items');
    }
}
