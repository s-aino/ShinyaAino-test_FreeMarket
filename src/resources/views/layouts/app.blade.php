<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'アプリ')</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
  @stack('styles')
  @stack('css')
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
  @if (session('message'))
  <p id="flash-message" class="flash-message">
    {{ session('message') }}
  </p>
  @endif

  {{-- メイン本文 --}}
  <main class="main-container">
    @yield('content')
  </main>

  <script>
    // メッセージを3秒後にフェードアウト
    document.addEventListener('DOMContentLoaded', () => {
      const flash = document.getElementById('flash-message');
      if (flash) {
        setTimeout(() => {
          flash.style.transition = 'opacity 0.5s ease';
          flash.style.opacity = '0';
          // フェードアウト後も高さを維持して「カクッ」と上がらないようにする
          setTimeout(() => {
            flash.style.visibility = 'hidden';
          }, 500);
        }, 3000);
      }
    });
  </script>
</body>

</html>