<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'category'
    ];
    // 一括代入を許可するカラム定義。これに入っていないと配列にしてデータ保存する際にエラーが発生する。

    public function categoryItem()
    {
        return $this->hasMany('App\Models\CategoryItem');
    }
    // 1対多(hasMany)のリレーション。「1つのカテゴリーが複数のアイテムを持つ」構造を表す。（カテゴリ「食品」 → 複数の商品に付けられている。（お菓子、飲料など））
    // ここで関連付けられているのはApp\Models\CategoryItem モデル。CategoryItemモデルがCategoryとItemの中間になり商品が複数のカテゴリーを付けた場合の対応を実施する。
}
