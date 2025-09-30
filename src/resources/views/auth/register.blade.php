@extends('layouts.app')
@section('title','会員登録')
@section('content')
  <h1>会員登録</h1>
  <form method="POST" action="{{ route('register') }}">@csrf
    <label>ユーザー名
      <input type="text" name="name" value="{{ old('name') }}" required>
    </label>
    <label>メール
      <input type="email" name="email" value="{{ old('email') }}" required>
    </label>
    <label>パスワード
      <input type="password" name="password" required>
    </label>
    <label>パスワード（確認）
      <input type="password" name="password_confirmation" required>
    </label>
    @foreach ($errors->all() as $e)<div style="color:#c00">{{ $e }}</div>@endforeach
    <button type="submit">登録する</button>
  </form>
  <p style="margin-top:12px"><a href="{{ route('login') }}">ログインはこちら</a></p>
@endsection
