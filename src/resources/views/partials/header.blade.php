@php
$showSearch = $showSearch ?? false;
$showMypage = $showMypage ?? false;
$showLogout = $showLogout ?? false;
$showSell = $showSell ?? false;
$showLogin = $showLogin ?? false;
$showRegister = $showRegister ?? false;
@endphp

<header class="header">
    <div class="header__inner">
        <a href="{{ url('/') }}" class="brand">CT <span>COACHTECH</span></a>

        @if($showSearch)
        <form action="{{ url('/') }}" method="GET" class="header__search">
            <input type="text" name="q" placeholder="なにをお探しですか？" />
        </form>
        @endif

        <nav class="nav">
            @auth
            @if($showLogout)
            <form method="POST" action="{{ url('/logout') }}" class="nav__logout">@csrf
                <button class="btn btn--ghost btn--sm" type="submit">ログアウト</button>
            </form>
            @if($showMypage) <a class="nav__link" href="{{ route('mypage.show') }}">マイページ</a> @endif
            @if($showSell) <a class="nav__link" href="{{ url('/sell') }}">出品</a> @endif
            @endif
            @else
            @if($showLogin) <a class="nav__link" href="{{ route('login') }}">ログイン</a> @endif
            @if($showRegister) <a class="btn btn--ghost btn--sm" href="{{ route('register') }}">会員登録</a> @endif
            @endauth
        </nav>
    </div>
</header>