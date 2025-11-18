@extends('layouts.app')
@section('title','会員登録')
@section('header')
@include('partials.header', [
'showSearch' => false,
'showLogin' => false,
'showMypage' => false,
'showSell' => false,
])
@endsection
@section('content')
<div class="card">
  <h1>会員登録</h1>

  <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate autocomplete="off">
    @csrf

    <div class="form-row">
      <label class="label">ユーザー名</label>
      <input class="input" type="text" name="name" value="{{ old('name') }}">
      @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="form-row">
      <label class="label">メールアドレス</label>
      <input class="input" type="email" name="email" value="{{ old('email') }}">
      @error('email') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="form-row">
      <label class="label">パスワード</label>
      <input class="input" type="password" name="password" autocomplete="new-password">
      @error('password') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="form-row">
      <label class="label">確認用パスワード</label>
      <input class="input" type="password" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="form-actions">
      <button type="submit" class="btn">登録する</button>
    </div>

    <div class="helper">
      <a href="{{ route('login') }}" class="auth-link">ログインはこちら</a>
    </div>
  </form>
</div>
@push('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush
@endsection