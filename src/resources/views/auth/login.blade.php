@extends('layouts.app')
@section('title','ログイン')
@section('header')
@include('partials.header', ['showSearch' => false,])
@endsection
@section('content')
<div class="card">
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}" novalidate autocomplete="off">
        @csrf

        <div class="form-row">
            <label class="label">メール</label>
            <input class="input" type="email" name="email" value="{{ old('email') }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <label class="label">パスワード</label>
            <input class="input" type="password" name="password">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <!-- <div class="form-row" style="display:flex;align-items:center;gap:8px;">
            <input id="remember" type="checkbox" name="remember">
            <label class="label" for="remember" style="margin:0">ログイン状態を保持</label>
        </div> -->

        <div class="actions">
            <button class="btn" type="submit">ログインする</button>
        </div>
    </form>

    <div class="helper"> <a href="{{ route('register') }}">会員登録はこちら</a></div>
</div>
@endsection