@extends('layouts.app')
@section('title','ログイン')
@section('content')
  <h1>ログイン</h1>
  <form method="POST" action="{{ route('login') }}">@csrf
    <label>メール
      <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    </label>
    <label>パスワード
      <input type="password" name="password" required>
    </label>
    <label><input type="checkbox" name="remember"> ログイン状態を保持</label>
    @error('email')<div style="color:#c00">{{ $message }}</div>@enderror
    @error('password')<div style="color:#c00">{{ $message }}</div>@enderror
    <button type="submit">ログインする</button>
  </form>
  <p style="margin-top:12px">未登録？ <a href="{{ route('register') }}">会員登録はこちら</a></p>
@endsection
