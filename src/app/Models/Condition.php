<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;
    // Laravelの「モデルファクトリ機能」を使うためのトレイト。テストやダミーデータ作成時にCondition::factory() を使えるようにする。

    public static $UNUSED = 1;
    public static $HARMLESS = 2;
    public static $HARMED = 3;
    public static $BAD_CONDITION = 4;
    // 静的プロパティ（定数のような扱い）
    // データベース上でid=1が「未使用」id=2が「目立った傷なし」など決まっている場合、コード上で数字ではなく「名前」で扱えるようになる。
    protected $fillable = [
        'condition',
    ];
    // ここに記載されたカラムだけが、create() や update() で代入可能になる。
    // Condition::create(['condition' => '未使用']);
    // OK(fillableに含まれる)
    // Condition::create(['id' => 10, 'condition' => '新品']);
    // idは含まれないので、自動的に無視される。
}
