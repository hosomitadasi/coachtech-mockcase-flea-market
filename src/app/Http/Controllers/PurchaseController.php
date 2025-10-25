<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use App\Models\Profile;
use Stripe\StripeClient;

class PurchaseController extends Controller
{
    public function index($item_id, Request $request){
        // $item_id：購入対象の商品のIDをURLから受け取る。
        $item = Item::find($item_id);
        // Itemモデルを使い、そのＩＤの商品データをデータベースから取得する。
        $user = User::find(Auth::id());
        // 現在ログイン中のユーザー情報を取得する。
        return view('purchase',compact('item','user'));
        // 取得した情報を、purchase.blade.phpに渡して、購入確認画面を表示する。
    }

    public function purchase($item_id, Request $request){
        $item = Item::find($item_id);
        $stripe = new StripeClient(config('stripe.stripe_secret_key'));
        // Stripeの決済クライアントを作成。.envの設定値を利用。下記の配列代入[] = []により、まとめて変数を定義。それぞれ購入時に必要な情報をセットする。

        [
            $user_id,
            $amount,
            $sending_postcode,
            $sending_address,
            $sending_building
        ] = [
            Auth::id(),
            $item->price,
            $request->destination_postcode,
            //ASCIIコードに日本語はないため、住所と建物名はエンコードする必要あり
            urlencode($request->destination_address),
            urlencode($request->destination_building) ?? null
        ];
        // urlencode()：日本語住所をURLに含めるためのエンコード。

        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => [$request->payment_method],
            'payment_method_options' => [
                'konbini' => [
                    'expires_after_days' => 7,
                ],
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => "http://localhost/purchase/{$item_id}/success?user_id={$user_id}&amount={$amount}&sending_postcode={$sending_postcode}&sending_address={$sending_address}&sending_building={$sending_building}",
        ]);
        // Stripeの決済セッションを作成。支払い方法や金額を指定する。

        return redirect($checkout_session->url);
    }

    public function success($item_id, Request $request){
        //無事決済が成功した後に動くメソッドのため、決済以外でHTTPリクエストが送られた時用にクエリパラメータを検閲
        if(!$request->user_id || !$request->amount || !$request->sending_postcode || !$request->sending_address){
            throw new Exception("You need all Query Parameters (user_id, amount, sending_postcode, sending_address)");
        }

        $stripe = new StripeClient(config('stripe.stripe_secret_key'));

        $stripe->charges->create([
            'amount' => $request->amount,
            'currency' => 'jpy',
            'source' => 'tok_visa',
        ]);
        // 実際にStripeで「支払いを確定」

        SoldItem::create([
            'user_id' => $request->user_id,
            'item_id' => $item_id,
            'sending_postcode' => $request->sending_postcode,
            'sending_address' => $request->sending_address,
            'sending_building' => $request->sending_building ?? null,
        ]);
        // 購入履歴（配送情報を含む）をsold_itemsテーブルに保存。

        return redirect('/')->with('flashSuccess', '決済が完了しました！');
        // トップページへリダイレクトし、「決済成功」メッセージを表示。
    }

    public function address($item_id, Request $request){
        $user = User::find(Auth::id());
        return view('address', compact('user','item_id'));
    }
    // 購入時に配送先住所を入力する画面を表示。Auth::id()から現在のユーザーを取得し、その情報をaddress.blade.phpに渡す。

    public function updateAddress(AddressRequest $request){

        $user = User::find(Auth::id());
        Profile::where('user_id', $user->id)->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building
        ]);

        return redirect()->route('purchase.index', ['item_id' => $request->item_id]);
    }
}
