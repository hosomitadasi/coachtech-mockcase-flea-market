<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// テスト用やダミーデータ作成のファクトリを使えるようにする。
use Illuminate\Database\Eloquent\Model;
// Eloquent（LaravelのORM）の基底クラス。

class Profile extends Model
{
    use HasFactory;
    // Profile モデルクラスの宣言。HasFactory トレイトを使ってファクトリを利用可能にしている。

    protected $fillable = [
        'user_id',
        'img_url',
        'postcode',
        'address',
        'building',
    ];
    // 一括代入（mass assignment）可能な属性 を定義している。Profile::create($data) のように配列で渡した場合、ここに列挙されたカラムだけが安全に代入される仕組み。

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
