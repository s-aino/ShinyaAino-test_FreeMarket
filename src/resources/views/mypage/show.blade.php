@extends('layouts.app')
@section('title','マイページ')
@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showMypage' => true,
'showSell' => true,
])
@endsection
@section('content')
<div class="mypage-container">

  {{-- ▼ プロフィールブロック --}}
  <div class="profile-block">
    @if(!empty($user->profile_image_path))
    <img src="{{ asset('storage/' . $user->profile_image_path) }}" alt="プロフィール画像" class="profile-image">
    @else
    {{-- プレースホルダ --}}
    <div class="profile-placeholder"></div>
    @endif

    <div class="profile-info">
      <div class="profile-text">
        <h2 class="user-name">{{ $user->name }}</h2>
      </div>
      <a href="{{ route('profile.edit') }}" class="btn-profile-edit">プロフィールを編集</a>
    </div>
  </div>

  {{-- ▼ タブ --}}
  <div class="mypage-tabs">
    <button class="tab active" data-tab="selling">出品した商品</button>
    <button class="tab" data-tab="purchased">購入した商品</button>
  </div>

  {{-- ▼ 出品した商品 --}}
  <div class="tab-content active" id="selling">
    @if($sellingItems->isEmpty())
    <p>出品はまだありません。</p>
    @else
    <div class="items-grid">
      @foreach ($sellingItems as $item)
      <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}">
          <div class="item-image-wrapper">
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">
            @if ($item->status === 'sold')
            <span class="sold-badge">SOLD</span>
            @endif
          </div>
          <p class="item-name">{{ $item->title }}</p>
        </a>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- ▼ 購入した商品 --}}
  <div class="tab-content" id="purchased">
    @if($purchasedItems->isEmpty())
    <p>購入はまだありません。</p>
    @else
    <div class="items-grid">
      @foreach ($purchasedItems as $item)
      <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}">
          <div class="item-image-wrapper">
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">
            @if ($item->status === 'sold')
            <span class="sold-badge">SOLD</span>
            @endif
          </div>
          <p class="item-name">{{ $item->title }}</p>
        </a>
      </div>
      @endforeach
    </div>
    @endif
  </div>

</div>

{{-- ▼ JS --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // すべてリセット
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));

        // 選択中のものだけ表示
        tab.classList.add('active');
        const targetId = tab.dataset.tab;
        document.getElementById(targetId).classList.add('active');
      });
    });
  });
</script>

@push('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush
@endsection