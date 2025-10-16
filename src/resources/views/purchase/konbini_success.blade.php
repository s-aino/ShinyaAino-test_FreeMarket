@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>コンビニ支払い手続き完了</h2>
    <p class="mt-3">ご利用ありがとうございました。</p>
    <p>商品「{{ $item->name }}」は購入済み（SOLD）となりました。</p>
    <a href="{{ url('/') }}" class="btn btn-secondary mt-3">トップに戻る</a>
</div>
@endsection
