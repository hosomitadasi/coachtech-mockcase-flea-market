<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionsTable extends Migration
{

    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            // 各レコードを識別するための主キー(Primary key)
            $table->string('condition');
            // 状態の説明を保存。
            $table->timestamps();
        });
    }
    // 商品状態をマスター登録しておき、itemsテーブルなどでcondition_idを外部キーとして参照することで、状態を一元管理可能。

    public function down()
    {
        Schema::dropIfExists('conditions');
    }
}
