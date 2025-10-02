@extends('layouts.app')
@section('title','プロフィール設定')
@section('header')
@include('partials.header', [      
  'showSearch' => true,
  'showLogout' => true,
  'showMypage' => true,
  'showSell'   => true,
      ])
@endsection
@section('content')
<div class="card">
  <h1>プロフィール設定</h1>

  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate autocomplete="off">
    @csrf

    {{-- 画像 --}}
    <div class="form-row" style="display:flex;gap:16px;align-items:center;justify-content:center;margin-top:6px;margin-bottom:18px">
      <img class="avatar" src="{{ $user->profile_image_path ? asset('storage/'.$user->profile_image_path) : 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'><rect width=\'100%\' height=\'100%\' fill=\'%23e5e7eb\'/></svg>' }}" alt="">
      <label class="btn btn--outline-red btn--sm" style="margin:0;cursor:pointer">
        画像を選択する
        <input class="file" type="file" name="profile_image" accept="image/png,image/jpeg" style="display:none">
      </label>
    </div>
    @error('profile_image') <div class="error" style="text-align:center">{{ $message }}</div> @enderror

    <div class="form-row">
      <label class="label">ユーザー名</label>
      <input class="input" type="text" name="name" value="{{ old('name', $user->name) }}">
      @error('name') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="form-row">
      <label class="label">郵便番号</label>
      <input class="input" type="text" name="postal" value="{{ old('postal', $user->postal) }}" placeholder="123-4567">
      @error('postal') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="form-row">
      <label class="label">住所</label>
      <input class="input" type="text" name="address" value="{{ old('address', $user->address) }}">
      @error('address') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="form-row">
      <label class="label">建物名（任意）</label>
      <input class="input" type="text" name="building" value="{{ old('building', $user->building) }}">
      @error('building') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="actions">
      <button type="submit" class="btn">更新する</button>
    </div>
  </form>
</div>
@endsection
