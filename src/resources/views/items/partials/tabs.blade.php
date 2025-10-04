@php
$tab = request('tab', 'recommend');
$isRecommend = $tab === 'recommend';
@endphp

<nav class="tabs">
    <a class="tab{{ $isRecommend ? ' is-active' : '' }}"
        href="{{ route($baseRoute, ['tab'=>'recommend']) }}">おすすめ</a>
    <a class="tab{{ !$isRecommend ? ' is-active' : '' }}"
        href="{{ route($baseRoute, ['tab'=>'mylist']) }}">マイリスト</a>
</nav>