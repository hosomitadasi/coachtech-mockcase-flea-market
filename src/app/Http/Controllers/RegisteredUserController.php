<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;

class RegisteredUserController
{
    public function store(
        Request $request,
        // $requestでフォームで送信されたリクエストデータ（name, email, password）を格納。
        CreateNewUser $creator
        // $creatorでFortifyのユーザー作成アクションを使用。ユーザーをDBに保存するロジックを担当。
    ) {
        event(new Registered($user = $creator->create($request->all())));
        // $request->all()の部分でフォームから送信された全てのデータを取得。creator->create(...)はApp\Actions\Fortify\CreateNewUser クラスの create メソッドを実行。ここで入力データのバリデーション、パスワードの葉syス化、Userモデルへの登録が実施される。戻り値として$userが返る。

        session()->put('unauthenticated_user', $user);
        // 登録直後はまだ「メール認証前」なのでログイン状態にできないため、ユーザー情報を一時的にセッションに保存。

        return redirect()->route('verification.notice');
    }
    // ユーザー登録フォームの送信を受け取り、実際にユーザーの新規作成、登録完了後メール認証画面へ案内するメソッド。
}