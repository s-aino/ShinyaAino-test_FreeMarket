@php
// ===============================
// デフォルト表示設定
// ===============================

// ロゴは常に表示
$showLogo = $showLogo ?? true;

// 検索バー・マイページ・出品ボタンはデフォルトで表示
$showSearch = $showSearch ?? true;
$showMypage = $showMypage ?? true;
$showSell = $showSell ?? true;

// ログインボタンのみデフォルト非表示（ログインページで個別にtrue指定）
$showLogin = $showLogin ?? false;
@endphp


<header class="header">
  <div class="header_inner">

      {{-- ロゴ --}}
      @if($showLogo ?? true)
      <a href="{{ url('/') }}" class="brand_logo">
        <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH Logo" height="24">
      </a>
      @endif


      {{-- 検索バー（ログイン／登録ページでは非表示） --}}
      @if($showSearch ?? true)
      <form action="{{ url('/') }}" method="GET" class="header_search" role="search">
        <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="なにをお探しですか？" autocomplete="off">
      </form>
      @endif

    {{-- ナビゲーション（ログイン/登録ページでは制御） --}}
    @if(($showLogin ?? false) || ($showMypage ?? true) || ($showSell ?? true))
    <nav class="nav">

      {{-- ログアウト（ログイン中のみ） --}}
      @auth
      <form method="POST" action="{{ url('/logout') }}" class="nav_logout">
        @csrf
        <button type="submit" class="nav_link nav_logout_btn">ログアウト</button>
      </form>
      @endauth

      {{-- ログイン（ゲスト時のみ） --}}
      @guest
      @if($showLogin ?? false)
      <a class="nav_link nav_login_btn" href="{{ route('login') }}">ログイン</a>
      @endif
      @endguest

      {{-- マイページ --}}
      @if($showMypage ?? true)
      <a class="nav_link nav_mypage_btn"
        href="{{ auth()->check() ? route('mypage.show') : route('login') }}">
        マイページ
      </a>
      @endif

      {{-- 出品ボタン --}}
      @if($showSell ?? true)
      <a class="nav_link nav_sell_btn btn btn--white"
        href="{{ auth()->check() ? route('items.create') : route('login') }}">
        出品
      </a>
      @endif

    </nav>
    @endif

  </div>
</header>