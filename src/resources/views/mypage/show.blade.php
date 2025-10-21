@extends('layouts.app')
@section('title','マイページ')
@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showMypage' => true,
'showSell' => true,])
@endsection
@section('content')
<div class="mypage-container">

  {{-- プロフィールブロック --}}
  <div class="profile-block">
    @if (!empty($user->profile_image_path))
    {{-- プロフィール画像あり --}}
    <img src="{{ asset('storage/' . $user->profile_image_path) }}"
      alt="プロフィール画像"
      class="profile-image">
    @else
    {{-- プロフィール画像なし：グレーのプレースホルダSVG --}}
    @php
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">
      <defs>
        <clipPath id="r">
          <circle cx="48" cy="48" r="48" />
        </clipPath>
      </defs>
      <g clip-path="url(#r)">
        <rect width="96" height="96" fill="#e6e7e8" />
        <circle cx="48" cy="36" r="16" fill="#cfd1d4" />
        <rect x="0" y="56" width="96" height="40" fill="#cfd1d4" />
      </g>
    </svg>';
    @endphp
    @endif

    <div class="profile-info">
      <h2 class="user-name">{{ $user->name }}</h2>
      <a href="{{ route('profile.edit') }}" class="btn--profile-edit">プロフィールを編集</a>
    </div>

    {{-- タブ --}}
    <div class="mypage-tabs">
      <button class="tab active" data-tab="selling">出品した商品</button>
      <button class="tab" data-tab="purchased">購入した商品</button>
    </div>

    {{-- 出品一覧 --}}
    <div class="tab-content active" id="selling">
      @if ($sellingItems->isEmpty())
      <p>出品はまだありません。</p>
      @else
      <div class="items-grid">
        @foreach ($sellingItems as $item)
        <div class="item-card">
          <a href="{{ route('items.show', $item->id) }}">
            <div class="item-image-wrapper">
              <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
            </div>
            <p class="item-name">{{ $item->title }}</p>
            @if ($item->status === 'sold')
            <span class="sold-badge">SOLD</span>
            @endif
          </a>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- 購入一覧 --}}
    <div class="tab-content" id="purchased">
      @if ($purchasedItems->isEmpty())
      <p>購入はまだありません。</p>
      @else
      <div class="items-grid">
        @foreach ($purchasedItems as $item)
        <div class="item-card">
          <a href="{{ route('items.show', $item->id) }}">
            <div class="item-image-wrapper">
              <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
            </div>
            <p class="item-name">{{ $item->title }}</p>
          </a>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>

  {{-- タブ切替JS --}}
  <script>
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
      });
    });
  </script>
  @endsection