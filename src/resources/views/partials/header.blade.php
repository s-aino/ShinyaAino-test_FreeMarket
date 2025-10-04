{{-- resources/views/partials/header.blade.php --}}
@php
$showSearch = $showSearch ?? false;
$showMypage = $showMypage ?? false;
$showSell = $showSell ?? false;
$showLogin = $showLogin ?? false;
// 会員登録ボタンはもう使わないなら削除
@endphp

<header class="header">
    <div class="header__inner">
        <a href="{{ url('/') }}" class="brand logo">
            <img src="/img/logo.svg" alt="COACHTECH Logo" height="24">
        </a> @if($showSearch)
        <form action="{{ url('/') }}" method="GET" class="header__search">
            <input type="text" name="q" placeholder="なにをお探しですか？">
        </form>
        @endif

        <nav class="nav">
            {{-- 1) ログイン/ログアウト（左/右はCSSで制御） --}}
            @auth
            <form method="POST" action="{{ url('/logout') }}" class="nav__logout">
                @csrf
                <button type="submit" class="btn btn--ghost btn--sm">ログアウト</button>
            </form>
            @endauth

            @guest
            @if($showLogin)
            <a class="nav__link" href="{{ route('login') }}">ログイン</a>
            @endif
            @endguest

            {{-- 2) マイページ --}}
            @if($showMypage)
            <a class="nav__link" href="{{ auth()->check() ? route('mypage.show') : route('login') }}">マイページ</a>
            @endif

            {{-- 3) 出品 --}}
            @if($showSell)
            <a class="nav__link btn-white"
                href="{{ auth()->check() ? route('items.create') : route('login') }}">出品</a>
            @endif
        </nav>
    </div>
</header>