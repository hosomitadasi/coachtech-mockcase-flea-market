<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{

    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            // foreignId('user_id')：user_id カラムを unsignedBigInteger 型で作成(ユーザーを参照するためのID)
            // ->constrained()：自動的に外部キー制約が作成される。usersテーブルのidを参照する。
            // ->cascadeOnDelete()：usersレコードが削除されたら、紐づくprofileレコードも自動で削除される。
            // ->unique()：ユニーク制約の付属。このテーブルが「ユーザー1件につき最大1件のプロフィール」を保証するため、1対1の関係をDBレベルで担保する。
            $table->string('img_url')->nullable();
            $table->string('postcode');
            $table->string('address');
            $table->string('building')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
