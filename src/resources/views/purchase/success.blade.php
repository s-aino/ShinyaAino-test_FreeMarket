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
<div class="container text-center mt-5">
    <h2>ご購入ありがとうございました！</h2>

    <p>{{ $message ?? '決済が完了しました。' }}</p>

    <div class="card mx-auto mt-4 p-4" style="max-width: 500px;">
        <img src="{{ asset('storage/' . $item->image_path) }}" class="img-fluid mb-3" alt="商品画像">
        <h4>{{ $item->name }}</h4>
        <p>¥{{ number_format($item->price) }}</p>

        <span class="badge bg-secondary fs-6">SOLD</span>

        <a href="{{ route('items.index') }}" class="btn btn-primary mt-3">
            商品一覧に戻る
        </a>
    </div>
</div>
@endsection