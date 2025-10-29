@extends('layouts.app')
@section('title', '支払い')

@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showMypage' => true,
'showSell' => true,
])
@endsection

@section('content')
<div class="success-container text-center">
    <h2 class="success-title">ご購入ありがとうございました！</h2>
    <p class="success-message">{{ $message ?? '決済が完了しました。' }}</p>

    <div class="success-card">
        <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">

        <h4 class="success-name">{{ $item->name }}</h4>
        <p class="success-price">¥{{ number_format($item->price) }}</p>


        <div class="back-link">
            <a href="{{ route('items.index') }}" class="btn btn-back">商品一覧に戻る</a>
        </div>
    </div>
</div>
@push('css')
<link rel="stylesheet" href="{{ asset('css/success-edit.css') }}">
@endpush
@endsection