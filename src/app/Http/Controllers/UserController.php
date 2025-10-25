<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(){

        $profile = Profile::where('user_id', Auth::id())->first();
        // Auth::id()でログイン中ユーザーのIDを取得。Profile::where('user_id', ...)で、そのユーザーのプロフィールを検索。first()により、1件目のレコード（該当ユーザーのプロフィール）を取得。
        // 現在ログイン中のユーザーのプロフィール情報を1件取得。

        return view('profile',compact('profile'));
        // resources/views/profile.blade.php を表示する。compact('profile') は変数 $profile をビューに渡すと同じ意味。
    }

    public function updateProfile(ProfileRequest $request){

        $img = $request->file('img_url');
        // フォームから送信された画像ファイルを取得。ProfileRequestによって、このデータは既にバリデーション済。

        if (isset($img)){
            $img_url = Storage::disk('local')->put('public/img', $img);
        }else{
            $img_url = '';
        }
        // 画像が送信されていれば保存を実施し、なければ空文字を格納。Storage::disk('local')->put('public/img', $img); はstorage/app/public/img/ ディレクトリに画像を保存し、そのパスを返します。

        $profile = Profile::where('user_id', Auth::id())->first();
        if ($profile){
            $profile->update([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }else{
            Profile::create([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }
        // Profile::where('user_id', Auth::id())->first();で現在ログイン中のプロフィールを検索。次のif文で既にプロフィールが存在すればupdate()で更新。存在しなければcreate()で新規作成を実施。

        User::find(Auth::id())->update([
            'name' => $request->name
        ]);
        // ユーザーの名前を同時に更新することを実施（プロフィールと別テーブル）
        return redirect('/');
        // 更新完了後、トップページにリダイレクト
    }

    public function mypage(Request $request){
        $user = User::find(Auth::id());
        // ログイン中のユーザー情報を取得。
        if ($request->page == 'buy'){
            $items = SoldItem::where('user_id', $user->id)->get()->map(function ($sold_item) {
                return $sold_item->item;
            });
        }else {
            $items = Item::where('user_id', $user->id)->get();
        }
        // page=buyを指定されていた場合は購入履歴を表示。SoldItemテーブルから「そのユーザーが購入した商品」を取得し、各sold_itemから関連するitemモデルを取り出して配列化。
        // page=buyが指定されていない場合は、出品した商品をそのまま表示。

        return view('mypage', compact('user', 'items'));
    }
}
