<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/',[ItemController::class, 'index'])->name('items.list');
// URL:/ コントローラ:ItemController@index
// 出品された商品一覧をトップページに表示する。商品データをデータベースから取得し、一覧ページ（items/index.blade.php）へ渡す。

Route::get('/item/{item}',[ItemController::class, 'detail'])->name('item.detail');
// URL:/item/1のように商品IDを指定 コントローラ:ItemController@detail
// 特定の商品詳細ページを表示。指定された{item}IDに該当する商品の情報を取得し、商品詳細ページを表示。

Route::get('/item', [ItemController::class, 'search']);
// URL:/item?search=キーワード コントローラ:ItemController@search
// 検索フォームで入力されたキーワードをもとに商品を検索し、検索結果一覧を表示。

// 以下ルートは全てログイン済み(auth)かつメール認証済み(verified)のユーザーだけアクセス可能。
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/sell',[ItemController::class, 'sellView']);
    // URL:/sell コントローラ:ItemController@sellView
    // 出品ページの表示。商品登録フォームを表示する。

    Route::post('/sell',[ItemController::class, 'sellCreate']);
    // URL:/sell コントローラ:ItemController@sellCreate
    // 出品フォームの送信処理。入力内容（商品名、価格、画像など）をDBに保存して商品を出品。

    Route::post('/item/like/{item_id}',[LikeController::class, 'create']);
    // URL:/item/like/1のように商品IDを指定 コントローラ:LikeController@create
    // 指定した商品に「いいね」を追加。likesテーブルにレコードを登録。

    Route::post('/item/unlike/{item_id}',[LikeController::class, 'destroy']);
    // URL:/item/unlike/1のように商品IDを指定 コントローラ:LikeController@destroy
    // 「いいね」を取り消す。いいねテーブルから該当レコードを削除。

    Route::post('/item/comment/{item_id}',[CommentController::class, 'create']);
    // URL: コントローラ:
    // 商品詳細ページから投稿されたコメントを保存する。入力内容をcommentsテーブルに登録。

    Route::get('/purchase/{item_id}',[PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
    // URL: コントローラ:
    // 購入確認ページを表示。purchase ミドルウェアにより「自分の商品を購入できない」「売り切れ商品は購入不可」などのチェックを実施。

    Route::post('/purchase/{item_id}',[PurchaseController::class, 'purchase'])->middleware('purchase');
    // URL: コントローラ:
    // 実際の購入処理（在庫更新、購入履歴登録など）を行う。

    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success']);
    // URL: コントローラ:
    // 購入完了ページを表示。「購入が完了しました」などの確認画面。

    Route::get('/purchase/address/{item_id}',[PurchaseController::class, 'address']);
    // URL: コントローラ:
    // 購入時に配送先住所を確認・変更する画面を表示。

    Route::post('/purchase/address/{item_id}',[PurchaseController::class, 'updateAddress']);
    // URL: コントローラ:
    // 入力された新しい住所情報をデータベースに保存し、購入処理に反映。

    Route::get('/mypage', [UserController::class, 'mypage']);
    // URL: コントローラ:
    // 自分のマイページを表示（購入履歴・出品履歴など）。

    Route::get('/mypage/profile', [UserController::class, 'profile']);
    // URL: コントローラ:
    // プロフィール編集画面を表示。

    Route::post('/mypage/profile', [UserController::class, 'updateProfile']);
    // URL: コントローラ:
    // 入力されたプロフィール情報をDBに保存。

});

Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('email');
// URL: コントローラ:
// Fortifyのログイン処理を実行。email ミドルウェアで「メール認証が済んでいるか」をチェック。

Route::post('/register', [RegisteredUserController::class, 'store']);
// URL: コントローラ:
// 新規会員登録を処理。入力されたユーザー情報を登録し、確認メールを送信。

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');
// URL: コントローラ:
// メール認証がまだ済んでいないユーザーに「認証メールを確認してください」画面を表示。

Route::post('/email/verification-notification', function (Request $request) {
    session()->get('unauthenticated_user')->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', 'Verification link sent!');
})->name('verification.send');
// URL: コントローラ:
// 再度メール認証リンクを送信する。（「メールが届かない場合はこちら」などのボタンで使用）

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    session()->forget('unauthenticated_user');
    return redirect('/mypage/profile');
})->name('verification.verify');
// URL: コントローラ:
// メールのリンクをクリックした際に呼ばれる。認証を完了させ、マイページへリダイレクト。
