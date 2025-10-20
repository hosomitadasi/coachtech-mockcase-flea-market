<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// メール認証を必須にするための契約（インターフェイス）
use Illuminate\Database\Eloquent\Factories\HasFactory;
// ダミーデータ作成（Factory機能）を利用可能にする。
use Illuminate\Foundation\Auth\User as Authenticatable;
// Laravelの「ユーザー認証」機能を使うための基本クラス。
use Illuminate\Notifications\Notifiable;
// 通知機能（メール通知など）を使うための機能
use Laravel\Sanctum\HasApiTokens;
// APIトークン認証（Sanctum用）を使うための機能

// ユーザー認証に使うモデルであり、さらにメール認証を必須とする使用になっている。Authenticatable はログイン認証の基盤、implements MustVerifyEmail はメール認証機能の有効化。
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    // HasApiTokens, HasFactory, Notifiableをクラスに組込む。UserモデルはAPIトークン、ファクトリ、通知を使えるようになる。

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    // 「一括代入可能（mass assignment）」にする属性を定義。フォームや登録処理でUser::create([...])と書いたとき、ここに指定されたカラムだけがまとめて代入される。（セキュリティ対策のための指定。）

    protected $hidden = [
        'password',
        'remember_token',
    ];
    // JSONなどでUser情報を出力する際に「表示させたくない属性」を隠す。パスワードとトークンは外務に漏れないように非表示にする。

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // email_verifiedカラムの値を、取得時に自動的にCarbon(日時オブジェクト)として扱う設定。日付の比較やフォーマットが簡単に行えるようになる。

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }
    // 1対1のリレーション。1人のユーザーが1つのプロフィールを持つ関係。
    // users.id ⇆ profile.user_id

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }
    // 1対多のリレーション。1人のユーザーが複数の「いいね」を持てる。

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }
    // 1対多のリレーション。ユーザーは複数の商品にコメントができる。

    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }
    // 1対多のリレーション。ユーザー（出品者）が複数の商品を出品できる関係。
}
