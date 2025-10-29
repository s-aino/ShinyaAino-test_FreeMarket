@php
$showSearch = $showSearch ?? false;
$showMypage = $showMypage ?? false;
$showSell = $showSell ?? false;
$showLogin = $showLogin ?? false;
@endphp

<header class="header">
  <div class="header_inner">
    {{-- ロゴ --}}
    <a href="{{ url('/') }}" class="brand_logo">
      <img src="{{ asset('img/logo.svg') }}" alt="COACHTECH Logo" height="24">
    </a>

    {{-- 検索バー（ログイン/登録ページでは非表示） --}}
    @if($showSearch ?? false)
      <form action="{{ url('/') }}" method="GET" class="header_search" role="search">
        <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="なにをお探しですか？" autocomplete="off">
      </form>
    @endif

    {{-- ナビ（ログイン/登録ページでは非表示） --}}
    @if(($showLogin ?? false) || ($showMypage ?? false) || ($showSell ?? false))
      <nav class="nav">

        {{-- ログイン・ログアウト --}}
        @auth
          <form method="POST" action="{{ url('/logout') }}" class="nav_logout">
            @csrf
            <button type="submit" class="nav_link nav_logout_btn">ログアウト</button>
          </form>
        @endauth

        @guest
          @if($showLogin ?? false)
            <a class="nav_link nav_login_btn" href="{{ route('login') }}">ログイン</a>
          @endif
        @endguest

        {{-- マイページ --}}
        @if($showMypage ?? false)
          <a class="nav_link nav_mypage_btn" href="{{ auth()->check() ? route('mypage.show') : route('login') }}">マイページ</a>
        @endif

        {{-- 出品 --}}
        @if($showSell ?? false)
          <a class="nav_link btn btn--white" href="{{ auth()->check() ? route('items.create') : route('login') }}">出品</a>
        @endif

      </nav>
    @endif
  </div>
</header>
