<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','FreeMarket')</title>
  <style>
    body{font-family:system-ui,sans-serif;background:#f7f7f9;margin:0}
    .wrap{max-width:720px;margin:48px auto;background:#fff;padding:24px;border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,.06)}
    label{display:block;margin-top:12px} input{width:100%;padding:10px;border:1px solid #ddd;border-radius:8px}
    button{margin-top:16px;padding:10px 16px;border:0;border-radius:10px;cursor:pointer}
    a{color:#0a7;text-decoration:none}
  </style>
</head>
<body>
  <div class="wrap">
    {{-- ※ここは@が1個だけ！ --}}
    @yield('content')
  </div>
</body>
</html>
