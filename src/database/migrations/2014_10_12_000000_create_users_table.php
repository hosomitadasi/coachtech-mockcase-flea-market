<?php

use Illuminate\Database\Migrations\Migration;
// マイグレーション（テーブル作成や削除の設計）を行うための基本クラス。
use Illuminate\Database\Schema\Blueprint;
// テーブル構造（カラムの型や属性）を定義するために使うクラス。
use Illuminate\Support\Facades\Schema;
// テーブルの作成・削除などを操作するためのファザード（命令の窓口）。

// クラス名はCreateUsersTable。このクラスのup()とdown()を呼び出して、テーブルの作成、削除を実行する。extends Migration により、LaravelのMigration機能を継承している。
class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
