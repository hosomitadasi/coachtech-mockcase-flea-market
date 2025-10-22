<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'brand',
        'description',
        'img_url',
        'user_id',
        'condition_id',
    ];
    // 配列で代入するための登録可能カラムを指定。

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    // ユーザーとの関係を示す。1つの商品は1人のユーザー（出品者）に属する。

    public function condition()
    {
        return $this->belongsTo('App\Models\Condition');
    }
    // 各商品は1つの「状態」に属する。

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }
    // 商品は複数の「いいね」を持つ（複数のユーザーと「いいね」で繋がる）1対多（hasMany）関係。

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }
    // 商品は複数のコメントが付けられる。1対多の関係。

    public function categoryItem()
    {
        return $this->hasMany('App\Models\CategoryItem');
    }
    // 商品は「中間テーブル（category_items）」を通して複数カテゴリに所属。

    public function categories()
    {
        $categories = $this->categoryItem->map(function ($item) {
            return $item->category;
        });
        return $categories;
    }
    // この商品（item）が属するカテゴリー一覧を取得するための関数。Items->CategoryItem->Categoryの流れで、商品が所属するカテゴリー一覧を取得している流れを示す。

    // $this->categoryItem部分は、$thisは現在のItemモデルインスタンスのこと。categoryItemはItemモデルのreturn $this->hasMany('App\Models\CategoryItem');のリレーションメソッドのこと。よってこの二つにより、「この商品に紐づく中間テーブルのレコード群」を取得しに向かうこと。

    // ->map(function ($item) { ... })の部分は、map()がLaravelコレクション（Collection）メソッド。$this->categoryItem はコレクションなので、map() を使って各要素に処理を適用可能。$itemは各CategoryItemモデルの1件を表す。

    // return $item->category;の部分は、CategoryItemモデルには category()というリレーションが定義されているため、$item->categoryで対応するCategoryモデルを取得できる。

    // 最後にreturnで$categoriesに格納した上記コードで対応するCategoryモデルを取得する。

    public function liked()
    {
        return Like::where(['item_id' => $this->id, 'user_id' => Auth::id()])->exists();
    }
    // 現在ログイン中のユーザーが「この商品にいいねをしているか」を真偽値（true/false）で返す。Like::where([...])でLikeモデルの中から条件に合うレコードを探し出す。([...])内のコードがその探し出す条件になる。今回は(['item_id' => $this->id, 'user_id' => Auth::id()])となっており、$this->idがこの商品（Item）のID、Auth::idが現在ログイン中のユーザーidが入る。これらから「このユーザーがこの商品に対していいねした記録」を検索している。

    // ->exists();でデータが1件でも存在すれば true を返し、なければ falseを返す。

    public function likeCount()
    {
        return Like::where('item_id', $this->id)->count();
    }
    // この商品が何件のいいねを獲得しているかを数値で返すもの。Like::where('item_id', $this->id)にてLikeテーブル内でitem_idがこの商品のIDと一致するレコードを取得。->countでレコード数を数えて表示する。

    public function getComments(){
        $comments = Comment::where('item_id', $this->id)->get();
        return $comments;
    }
    // この商品に紐づくコメント一覧を取得するもの。Comment::where('item_id', $this->id)部分でcommentsテーブルの中から商品IDが一致するものだけ抽出。->get();で条件に合うすべてのコメントを取得して一覧で返す。

    public function sold(){
        return SoldItem::where('item_id',$this->id)->exists();
    }
    // この商品が既に販売済みかどうかを判定する。SoldItem::where('item_id',$this->id)の部分でsold_itemsテーブルを検索->exists();条件に合うIDがあればtrueとなり販売済みの処理を実施。無ければfalseとなり未販売時の表示を処理する。

    public function mine(){
        return $this->user_id == Auth::id();
    }
    // この商品がログイン中のユーザーの商品かの判定を実施。$this->user_id部分が商品を登録した出品者のユーザーID。Auth::id();部分が現在ログインしているユーザーのID。==により2つが一致していれば「自分の出品物」であると判断。真なら商品を表示しない処理に、偽なら商品は表示する処理にする。

    public static function scopeItem($query, $item_name){
        return $query->where('name', 'like', '%'.$item_name.'%');
    }
    // スコープメソッド（query scope）と呼ばれるもの。Item::item('キーワード') のように書くことで、「商品名に特定の文字列を含む商品を検索」できる。

}
