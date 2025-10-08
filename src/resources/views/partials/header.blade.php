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
            <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH Logo" height="24">
        </a>

        @if($showSearch)
        <form action="{{ url('/') }}" method="GET" class="header__search" role="search">
            {{-- ★ いま表示中のタブを維持（recommend / likes） --}}
            <input type="hidden" name="tab" value="{{ request('tab','recommend') }}">
            {{-- ★ 検索語を保持（検索後もボックスに残す） --}}
            <input type="text" name="q" value="{{ request('q') }}" placeholder="なにをお探しですか？" autocomplete="off">
            {{-- ボタンは見た目不要なら省略可。Enterで送信できます --}}
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