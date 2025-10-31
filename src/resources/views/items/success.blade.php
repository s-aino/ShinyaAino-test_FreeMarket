@extends('layouts.app')

@section('title', '出品完了')

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
    <h2 class="success-title">商品の出品が完了しました！</h2>
    <p class="success-message">{{ $message ?? 'ご出品ありがとうございました。' }}</p>

    <div class="success-card">
        <a href="{{ route('items.index') }}" class="btn btn-back">商品一覧へ戻る</a>
    </div>
</div>

@push('css')
<link rel="stylesheet" href="{{ asset('css/success-edit.css') }}">
@endpush
@endsection
