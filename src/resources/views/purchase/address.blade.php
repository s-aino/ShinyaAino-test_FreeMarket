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
        @method('PUT')

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code"
                value="{{ old('postal_code', $address->postal ?? '') }}">
            @error('postal_code')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="line1">住所</label>
            <input type="text" id="line1" name="line1"
                value="{{ old('line1', $address->line1 ?? '') }}">
            @error('line1')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="line2">建物名</label>
            <input type="text" id="line2" name="line2"
                value="{{ old('line2', $address->line2 ?? '') }}">
            @error('line2')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="btn-area">
            <button type="submit" class="btn-red">更新する</button>
        </div>
    </form>
</div>
@endsection