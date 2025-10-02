<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'アプリ')</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}"?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="bg-gray">
  {{-- ページ側が @section('header') を定義していればそれを表示、なければデフォルト --}}
  @hasSection('header')
    @yield('header')
  @else
    @include('partials.header', [
      'showSearch' => false,
      'showMypage' => false,
      'showSell'   => false,
      'showLogout' => false,
      'showLogin'  => false,
      'showRegister' => false,
    ])
  @endif

  {{-- ====== 本文 ====== --}}
  <main class="container">
    @yield('content')
  </main>
</body>

</html>