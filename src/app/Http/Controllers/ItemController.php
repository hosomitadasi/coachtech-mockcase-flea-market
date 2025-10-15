<?php

namespace App\Http\Controllers;

use Storage;
// laravelのファザードStorageを使ってファイル操作（ファイル保存）を実施する。
use Illuminate\Http\Request;
// HTTPリクエスト（GET/POST）の内容を受け取るためのクラス。
use Illuminate\Support\Facades\Auth;
// 認証情報を取得するために使用。
use App\Http\Requests\ItemRequest;
// 出品フォームのバリデーションを行うカスタムリクエストクラス(rules()が定義されている想定)。
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CategoryItem;
// 各モデルを扱う。

class ItemController extends Controller
{
    public function index(Request $request){
        $tab = $request->query('tab', 'recommend');
        // クエリパラメータ?tab=…を取得。無ければ'recommend'をデフォルトにする
        $search = $request->query('search');
        // 検索バーに検索事項があるなら検索語(?search=×××)を取得。未指定ならnullで表記。
        $query = Item::query();
        // Eloquent のクエリビルダを開始（まだSQLは発行されていない）。
        $query->where('user_id', '<>', Auth::id());
        // まず自分が出品した商品は一覧から除外する（<> は「等しくない」）。Auth::id() が null
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

    public function search(Request $request){
        $search_word = $request->search_item;
        $query = Item::query();
        $query = Item::scopeItem($query, $search_word);

        $items = $query->get();
        return view('index', compact('items'));
    }

    public function sellView(){
        $categories = Category::all();
        $conditions = Condition::all();
        return view('sell',compact('categories', 'conditions'));
    }

    public function sellCreate(ItemRequest $request){

        $img = $request->file('img_url');

        try {
            //code...
            $img_url = Storage::disk('local')->put('public/img', $img);
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

        foreach ($request->categories as $category_id){
            CategoryItem::create([
                'item_id' => $item->id,
                'category_id' => $category_id
            ]);
        }

        return redirect()->route('item.detail',['item' => $item->id]);
    }
}
