<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function create($item_id, CommentRequest $request)
    {
        // $item_id：コメント対象の商品ID。URLのパラメータ(例：/item/5/comment)から自動的に渡される。
        $comment = new Comment();
        // Commentモデルの新しいインスタンスを作成。「まだ保存していないコメントデータの入れ物」となっている。この時点ではまだDBには反映されていない。
        $comment->user_id = Auth::id();
        // コメントを投稿したユーザーを記録する。
        $comment->item_id = $item_id;
        // どの商品に対するコメントかを示す。ルートから受け取った$item_idをセット。
        $comment->comment = $request->comment;
        // 実際のコメント本文を格納。フォームで入力された内容は$request->commentに入っている。
        $comment->save();
        // ここで初めてデータベースに保存する。save()はEloquentモデルが提供する基本的な保存メソッドで、INSERT INTO comments ...のようなSQLを自動生成して実行する。

        return back()->with('flashSuccess', 'コメントを送信しました！');;
        // back()でユーザーがコメントを送信したページへ戻る。
    }
}
