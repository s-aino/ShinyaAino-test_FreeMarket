@extends('layouts.app')
@section('title', $item->title ?? '')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogin' => true,
'showMypage' => true,
'showSell' => true,
])
@endsection

@section('content')
<div class="address-edit">
    <h1>住所の変更</h1>

    <form action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="POST">
        @csrf

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label for="postal">郵便番号</label>
            <input
                type="text"
                id="postal"
                name="postal"
                value="{{ old('postal', $address->postal ?? '') }}">
            @error('postal')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所（addresses.line1 を使用） --}}
        <div class="form-group">
            <label for="address">住所</label>
            <input
                type="text"
                id="address"
                name="address"
                value="{{ old('address', $address->line1 ?? '') }}">
            @error('address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名（addresses.line2 を使用） --}}
        <div class="form-group">
            <label for="building">建物名</label>
            <input
                type="text"
                id="building"
                name="building"
                value="{{ old('building', $address->line2 ?? '') }}">
            @error('building')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 更新ボタン --}}
        <div class="btn-area">
            <button type="submit" class="btn btn-red">更新する</button>
        </div>
    </form>
</div>

@push('css')
<link rel="stylesheet" href="{{ asset('css/shipping-edit.css') }}">
@endpush
@endsection