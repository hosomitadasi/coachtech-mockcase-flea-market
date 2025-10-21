<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Like extends Model
{
    use HasFactory;
    // Laravelの「モデルファクトリ」機能を利用可能にするトレイト。主にテストデータの自動生成に使用。

    protected $primaryKey = ['user_id', 'item_id'];
    // 主キーを user_id と item_id の組み合わせに設定。一人のユーザーが一つのアイテムに１つしかいいねできなためこの仕様に。
    public $incrementing = false;
    // 主キー（primary key）が自動で数字を増やす設定を無効にする。likesテーブルではidカラムを使わずにuser_idとitem_idの複合になるため。

    protected $fillable = [
        'user_id',
        'item_id',
    ];
    // 一括代入が許可されるカラムを指定。これによりLike::create(['user_id' => 1,'item_id' => 3]);のように登録可能になる。

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    // LikeモデルはUserモデルに属する（belongsTo）関係。「このいいねはどのユーザーがつけたか？」を取得できる。
    // 例）$like->user->name

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }
    // Like モデルは Itemモデルにも属する関係。「このいいねはどの商品に対するものか？」を取得できる。
    // 例）$like->item->name

    public function liked($item_id)
    {
        $count = Like::where('item_id', $item_id)->where('user_id', Auth::id())->count();
        return $count > 0;
    }
    // 「ログイン中のユーザーが指定した商品を既にいいねしているか？」を判定する関数。
    // Like::where('item_id', $item_id)：指定した商品IDのレコードを検索。
    // where('user_id', Auth::id())：現在ログイン中のユーザー（Auth::id()）に限定。
    // count()：該当レコード数を取得（0なら未いいね、1なら既にいいね済み）
    // return $count > 0;：true/falseで返す。trueなら「既にいいねしている」。
}
