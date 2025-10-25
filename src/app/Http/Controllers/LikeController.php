<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function create($item_id, Request $request){
        Like::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id
        ]);
        // Auth::id()で現在ログイン中のユーザーIDを取得。し、Like::createでlikesテーブルに新しいレコードを挿入する。
        return back();
    }
    // 「いいね」を実施した時に呼び出されるメソッド。

    public function destroy($item_id, Request $request){
        Like::where(['user_id' => Auth::id(), 'item_id' => $item_id])->delete();
        return back();
    }
    // 「いいね」を解除するメソッド。
}
