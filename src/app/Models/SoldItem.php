<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    // 通常、Laravelはidカラムを主キーとするのだが、SoldItemではitem_idを主キーとして扱う。
    //「一つの商品につき一件の販売情報」になるようにするための設計。
    public $incrementing = false;
    // item_id は自動連番（オートインクリメント）ではないため、false に設定。外部キーを主キーとして利用する際に必須の指定。

    protected $fillable = [
        'user_id',
        'item_id',
        'sending_postcode',
        'sending_address',
        'sending_building'
    ];
    // 一括代入（SoldItem::create([...])）で代入できるカラムを指定。ここで指定されていないカラムはcreate() や update() 時に無視される。

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    // SoldItemが「一人のユーザー（購入者）に属する」ことを示す。
    // belongToは「外部キーを持つ側」から「親モデル」への関係。

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }
    // 販売データが「どの商品に対応しているか」を示す。「販売情報」は「商品」に属している関係。
}
