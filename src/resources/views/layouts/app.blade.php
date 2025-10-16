<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'アプリ')</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>

<body>

  {{-- ヘッダー --}}
  @if(View::hasSection('header'))
  @yield('header')
  @else
  @include('partials.header', [
  'showSearch' => false,
  'showMypage' => false,
  'showSell' => false,
  'showLogout' => false,
  'showLogin' => false,
  'showRegister' => false,
  ])
  @endif

  {{-- メイン本文 --}}
  <main class="main-container">
    @yield('content')
  </main>

</body>

</html>