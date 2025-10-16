@extends('layouts.app')
@section('title', '購入手続き')

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
    <h2>コンビニ払いの手続き</h2>

    <div class="card mx-auto mt-4 p-4" style="max-width: 500px;">
        <img src="{{ asset('storage/' . $item->image_path) }}" class="img-fluid mb-3" alt="商品画像">
        <h4>{{ $item->name }}</h4>
        <p class="mb-1">価格：¥{{ number_format($item->price) }}</p>
        <hr>

        <p>このあと表示されるコンビニ支払い番号を使ってお支払いください。</p>
        <p>支払い完了後に商品が発送されます。</p>

        <hr>

        <p>お支払い番号：</p>
        <h3 class="text-primary">{{ rand(100000000000, 999999999999) }}</h3>

        <form action="{{ route('purchase.konbini.success', $item->id) }}" method="GET" class="mt-4">
            <button type="submit" class="btn btn-primary">
                支払いを完了したとみなす（テスト）
            </button>
        </form>

        <a href="{{ route('purchase.show', $item->id) }}" class="btn btn-outline-secondary mt-3">
            商品ページに戻る
        </a>
    </div>
</div>
@endsection
