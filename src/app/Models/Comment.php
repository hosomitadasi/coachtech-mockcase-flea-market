<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    // Laravelの「モデルファクトリ」機能を利用可能にするトレイト。テストやダミーデータ生成（php artisan tinker など）に使用。
    protected $fillable = [
        'user_id',
        'item_id',
        'comment'
    ];
    // 一括代入できるカラムを指定。以下のように配列で一気に登録可能。
    // php Comment::create([ 'user_id' => 1, 'item_id' => 5, 'comment' => 'とても良い商品でした！' ]);

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    // コメントは 1人のユーザーに属する（belongsTo） 関係。「このコメントを書いたのはどのユーザーか？」を取得可能。

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }
    // コメントは 1つの商品に属する（belongsTo） 関係。「このコメントが書かれた商品はどれか？」を取得可能。
}
