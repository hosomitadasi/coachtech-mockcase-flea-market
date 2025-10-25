<?php

namespace App\Http\Controllers;

use Storage;
// 画像などをストレージに保存するためのファザード
use Illuminate\Http\Request;
// フォームやURLパラメータから送られたデータを扱うクラス
use Illuminate\Support\Facades\Auth;
// ログイン中のユーザー情報を取得するための認証クラス
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CategoryItem;

class ItemController extends Controller
{
    public function index(Request $request){
        $tab = $request->query('tab', 'recommend');
        // URLパラメータ（例：?tab=mylist）を取得。もし何も取得されていなければ、'recommend'をデフォルト値として使用。
        $search = $request->query('search');
        // 検索ワードを取得（例：?search=時計）
        $query = Item::query();
        // Itemモデルに対してクエリビルダーを作成。次のコードで条件を追加して検索を実施。
        $query->where('user_id', '<>', Auth::id());
        // まず自分が出品した商品は一覧から除外する（<> は「等しくない」）。
        if ($tab === 'mylist'){
            $query->whereIn('id', function ($query) {
                $query->select('item_id')
                    ->from('likes')
                    ->where('user_id', auth()->id());
            });
        }
        // mylistかそうでないかを判定。mylistの場合はサブクエリで likes テーブルから自分がいいねした item_id を取得し、そのIDに一致する items のみ抽出する。

        if($search){
            $query->where('name', 'like', "%{$search}%");
        }
        // 検索語が入力されていた場合、条件の一致するものだけ表示させるようにする（%が部分一致フィルタとなっている）。

        $items = $query->get();
        // 実際にクエリを実行して結果を取得する。

        return view('index',compact('items','tab', 'search'));
        // 上記処理を実行した結果をindex.blade.phpに返す。
    }

    public function detail(Item $item){
        return view('detail', compact('item'));
    }
    // 引数の$itemはルートモデルバインディングにより自動的に取得される。return view('detail', compact('item'))にて商品詳細ページdetail.blade.phpに$itemを渡して表示。
    // 例：/item/5にアクセス -> $itemはid=5のレコードになる。

    public function search(Request $request){
        $search_word = $request->search_item;
        // フォーム入力から検索キーワードを取得。
        $query = Item::query();
        // 新しいクエリビルダーを作成。
        $query = Item::scopeItem($query, $search_word);
        // Itemモデルに定義されたスコープ関数を呼び出し、商品名に部分一致する検索条件を追加。
        $items = $query->get();
        // 条件に合う商品を取得。
        return view('index', compact('items'));
        // 検索結果を一覧ページに表示。
    }

    public function sellView(){
        $categories = Category::all();
        // カテゴリー一覧を取得。
        $conditions = Condition::all();
        // 商品の状態を取得。
        return view('sell',compact('categories', 'conditions'));
        // 出品フォーム画面にデータを渡して表示。
    }

    public function sellCreate(ItemRequest $request){

        $img = $request->file('img_url');
        // フォームでアップロードされた商品画像を取得。

        try {
            $img_url = Storage::disk('local')->put('public/img', $img);
            // 画像をstorage/app/public/img/ に保存。戻り値は保存されたファイルパス。
        } catch (\Throwable $th) {
            throw $th;
        }

        $item = Item::create([
            'name' => $request->name,
            'price' => $request->price,
            'brand' => $request->brand,
            'description' => $request->description,
            'img_url' => $img_url,
            'condition_id' => $request->condition_id,
            'user_id' => Auth::id(),
        ]);
        // 送信されたフォームデータを使い、itemsテーブルに新しい商品を登録。

        foreach ($request->categories as $category_id){
            CategoryItem::create([
                'item_id' => $item->id,
                'category_id' => $category_id
            ]);
        }
        // 選択されたカテゴリ（複数可）を繰り返し処理し、中間テーブルcategory_itemsに登録。

        return redirect()->route('item.detail',['item' => $item->id]);
        // 出品が完了したら、その商品の詳細ページへリダイレクト。
    }
}
