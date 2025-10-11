{{-- resources/views/mypage/show.blade.php --}}
@extends('layouts.app')
@section('title','マイページ')
@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showSell' => true,])
@endsection
@section('content')
<div class="card">
  <h1>マイページ</h1>

  @if(session('message'))
  <div class="alert alert-success" style="margin:8px 0 16px;color:#166534;">
    {{ session('message') }}
  </div>
  @endif

  <p>ようこそ、<strong>{{ $user->name }}</strong> さん。</p>
  <ul>
    <li>郵便番号：{{ $address->postal ?? 'ー' }}</li>
    <li>住所：
      @if ($address)
      {{ ($address->prefecture ?? '') . ($address->city ?? '') . ($address->line1 ?? '') }}
      @else
      ー
      @endif
    </li>
    <li>建物名：{{ $address->line2 ?? 'ー' }}</li>
  </ul>
  <div class="actions" style="display:flex;gap:12px;justify-content:center;margin-top:16px;">
    <a class="btn" href="{{ route('mypage.purchases') }}">購入履歴</a>
    <a class="btn" href="{{ route('mypage.sales') }}">出品一覧</a>
    <a class="btn" href="{{ route('profile.edit') }}">プロフィール編集</a>
  </div>
</div>
@endsection