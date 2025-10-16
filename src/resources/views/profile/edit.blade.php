@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('header')
@include('partials.header', ['showSearch' => true, 'showMypage' => true, 'showSell' => true])
@endsection

@section('content')
@php
/** @var \App\Models\User $user */
/** @var \App\Models\Address|null $address */
$addr = $address ?? null;

// プレビュー用アバター（画像未設定なら丸いグレーのプレースホルダSVG）
$avatarSrc = $user->profile_image_path
? asset('storage/'.$user->profile_image_path)
: 'data:image/svg+xml;utf8,' . rawurlencode(
'<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">
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
</svg>'
);
@endphp

<div class="card">
  <h1 class="title">プロフィール設定</h1>

  <form method="POST"
    action="{{ route('profile.update') }}"
    enctype="multipart/form-data"
    class="profile-form"
    novalidate
    autocomplete="off">
    @csrf

    {{-- 画像 --}}
    <div class="form-row" style="display:flex;gap:16px;align-items:center;justify-content:center;margin:12px 0 18px;">
      <img id="avatarPreview" class="avatar"
        src="{{ $avatarSrc }}"
        alt="avatar"
        style="width:96px;height:96px;border-radius:9999px;object-fit:cover;display:block;">
      <label for="profile_image"
        class="btn btn--outline btn--sm"
        style="margin:0;cursor:pointer">
        画像を選択する
      </label>
      <input id="profile_image"
        class="file"
        type="file"
        name="profile_image"
        accept="image/png,image/jpeg"
        style="display:none">
    </div>
    @error('profile_image')
    <div class="error" style="text-align:center;">{{ $message }}</div>
    @enderror

    {{-- ユーザー名 --}}
    <div class="form-row">
      <label class="label">ユーザー名</label>
      <input class="input" type="text" name="name"
        value="{{ old('name', $user->name) }}">
      @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>

    {{-- 郵便番号 --}}
    <div class="form-row">
      <label class="label">郵便番号</label>
      <input class="input" type="text" name="postal"
        value="{{ old('postal', optional($addr)->postal) }}">
      @error('postal') <div class="error">{{ $message }}</div> @enderror
    </div>

    {{-- 住所（addresses.line1 を使用） --}}
    <div class="form-row">
      <label class="label">住所</label>
      <input class="input" type="text" name="address"
        value="{{ old('address', optional($addr)->line1) }}">
      @error('address') <div class="error">{{ $message }}</div> @enderror
    </div>

    {{-- 建物名（addresses.line2 を使用） --}}
    <div class="form-row">
      <label class="label">建物名</label>
      <input class="input" type="text" name="building"
        value="{{ old('building', optional($addr)->line2) }}">
      @error('building') <div class="error">{{ $message }}</div> @enderror
    </div>

    {{-- 送信ボタン（入力と同幅に） --}}
    <div class="form-actions">
      <button type="submit" class="btn btn--primary">更新する</button>
    </div>
  </form>
</div>

{{-- 画像プレビュー用の超軽量JS --}}
<script>
  document.getElementById('profile_image')?.addEventListener('change', function(e) {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
      document.getElementById('avatarPreview').src = ev.target.result;
    };
    reader.readAsDataURL(file);
  });
</script>

@endsection