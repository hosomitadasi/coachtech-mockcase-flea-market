<header class="header">

    <!-- ヘッダーロゴ部分 -->
    <div class="header__logo">
        <!-- aタグ。href="/"でロゴ画像をクリックしたときにトップページ（/）に戻るように設定。{{ asset('img/logo.png') }}はlaravelの関数を使用しpublic/img/logo.pngのパスを自動生成。ファイルの画像を表示させる。 -->
        <a href="/"><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
    </div>
    <!-- 条件分岐。in_array(値, 配列)は配列の中に指定した値が含まれているか判定するもの。今回！が含まれているので配列に含まれていない場合のみ処理を実行という意味になる。in_array内の値であるRoute::currentRouteName()は、現在表示しているページのルート名を取得。配列にregister、login、verification.noticeがあるので、今回の場合は上記３つのページ以外の時に構文内のコードを表示するという意味。 -->
    @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )
    <!-- 検索バー。action="/"が検索キーワードをindex.blade.php上（トップページ）で表示させる。method="get"は検索キーワードをＵＲＬパラメータとして送る -->
    <form class="header_search" action="/" method="get">
        @csrf
        <!-- 検索ボックス。name="search"により、送信時に?search=入力内容の形で送信する。 -->
        <input id="inputElement" class="header_search--input" type="text" name="search" placeholder="なにをお探しですか？">
        <!-- 検索ボタン。imgタブと{{ asset('img/search_icon.jpeg') }}でpublic/img/search_icon.jpegを画像として処理する。押すことで検索処理を実行。 -->
        <button id="buttonElement" class="header_search--button">
            <img src="{{ asset('img/search_icon.jpeg') }}" alt="検索アイコン" style="height:100%;">
        </button>
    </form>
    <!-- ナビゲーション部分 -->
    <nav class="header__nav">
        <ul>
            <!-- ログインしているかを判定する。ログイン済みならtrueを返し、「ログアウト」「マイページ」リンクを表示。falseなら「ログイン」「会員登録」リンクを表示させる。各ボタンはaタグになっており各リンクに繋がっている。 -->
            @if(Auth::check())
            <li>
                <!-- ログアウトはpostリクエストを送るためformで実装する。 -->
                <form action="/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            <li><a href="/mypage">マイページ</a></li>
            @else
            <li><a href="/login">ログイン</a></li>
            <li><a href="/register">会員登録</a></li>
            @endif
            <a href="/sell">
                <li class="header__btn">出品</li>
            </a>
        </ul>
    </nav>
    @endif
</header>