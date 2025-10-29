@extends('layouts.app')
@section('title','ログイン')
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
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate autocomplete="off">
        @csrf
        <input type="hidden" name="redirect" value="{{ session('url.intended') }}">

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


        <div class="form-actions">
            <button class="btn" type="submit">ログインする</button>
        </div>
    </form>

    <div class="helper"> <a href="{{ route('register') }}" class="auth-link">会員登録はこちら</a></div>
</div>
@push('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush
@endsection