@extends('layouts.app')
@section('title','購入履歴')
@section('header')
@include('partials.header', [
'showSearch' => true,
'showLogout' => true,
'showSell' => true,])
@endsection
@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">購入履歴</h2>

    @if ($orders->isEmpty())
        <p class="text-center text-muted">購入履歴はありません。</p>
    @else
        <div class="row justify-content-center">
            @foreach ($orders as $order)
                <div class="card mb-3" style="max-width: 600px;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{{ asset('storage/' . $order->item->image_path) }}" 
                                 class="img-fluid rounded-start" 
                                 alt="{{ $order->item->name }}">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">{{ $order->item->name }}</h5>
                                <p class="card-text">価格：¥{{ number_format($order->item->price) }}</p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        購入日：{{ $order->created_at->format('Y/m/d H:i') }}
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('items.index') }}" class="btn btn-secondary">商品一覧に戻る</a>
    </div>
</div>
@endsection
