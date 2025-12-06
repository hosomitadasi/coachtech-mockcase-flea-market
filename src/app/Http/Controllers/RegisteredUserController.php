<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;

class RegisteredUserController
{
    // 新規ユーザー登録フォームのPOSTリクエストを処理するコントローラー
    public function store(
        Request $request,
        CreateNewUser $creator
        // Request $request,でユーザーがフォームに入力したすべてのデータをカプセル化したオブジェクト。これを通じて、POST /registerリクエストを受け付け。入力値全体（$request->all()）を取得できる。
        // Fortifyが提供する契約（CreatesNewUsers）を実装したアクションクラス。Laravelのサービスコンテナがこれを自動的にインスタンス化し、このコントローラーに渡します（依存性注入）。これにより、コントローラーは具体的なユーザー作成ロジックを知る必要がなくなります。
    ) {
        event(new Registered($user = $creator->create($request->all())));
        // $user = $creator->create($request->all())：$request->all()（フォームデータ全体）を、CreateNewUserクラスのcreateメソッドに渡す。createメソッド内でバリデーションとDBへのユーザーデータ保存が実行される。DBに保存された新しいUserモデルのインスタンスが返され、$user変数に代入される。

        // event(new Registered($user))：Illuminate\Auth\Events\Registeredイベントを、作成された$userオブジェクトを付けて発火する。aravelのデフォルト設定（特にEventServiceProvider）は、このイベントをリッスンしている。

        // Registeredイベントの発生をトリガーとして、Laravelが自動でリスナーを起動する。$userがMustVerifyEmailインターフェースを実装していることを確認し、認証リンクを含むメールをユーザーのメールアドレス宛に送信する。

        session()->put('unauthenticated_user', $user);
        // セッションにユーザー情報を一時保存。：認証はまだ完了していないため、メール認証画面で使用するために作成したユーザーインスタンス全体をセッションに一時保存する。

        return redirect()->route('verification.notice');
        // 認証督促画面へリダイレクト：verification.notice に定義されているURL（/email/verify）へ遷移させる。
    }
}