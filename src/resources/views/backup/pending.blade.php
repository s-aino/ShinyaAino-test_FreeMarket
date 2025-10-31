@extends('layouts.app')
@section('title', 'コンビニ支払いまち')

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
  <h2>お支払い手続き中</h2>
  <p>コンビニでのお支払いが完了すると、商品が発送されます。</p>
  <p>お支払い番号は Stripe の画面に表示されています。</p>
  <p class="mt-4 text-muted">※支払い完了後に自動でSOLD表示に変わります。</p>

  {{-- ✅ ボタンエリア --}}
  <div class="mt-5 d-flex justify-content-center gap-3">

    {{-- 購入完了ページへ進む --}}
    <form action="{{ route('purchase.success', ['item' => $item->id]) }}" method="GET">
      <button type="submit" class="btn btn-success">購入完了ページへ進む</button>
    </form>

    {{-- 商品一覧に戻る --}}
    <a href="{{ route('items.index') }}" class="btn btn-primary">商品一覧に戻る</a>
  </div>
</div>
@endsection
