<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryItem extends Model
{
    use HasFactory;

    protected $table = 'category_items';

    protected $primaryKey = ['item_id', 'category_id'];
    // 通常は単一のカラム（id）を主キーになるが、このテーブルでは item_id と category_id の 複合主キー を使用。

    public $incrementing = false;
    // 自動インクリメントではない（つまり連番ではない）ことを指定。主キーが複数あるため、IDを自動生成できない構造にする。

    protected $fillable = [
        'item_id',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }
}
