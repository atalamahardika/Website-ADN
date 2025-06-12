@include('head')
@include('header')

{{-- Semua section di bawah ini dengan menggunakan include() --}}
@include('user.guest.carousel')
@include('user.guest.hero')
@include('user.guest.about')
{{-- @include('user.guest.count') --}}
@include('user.guest.portal-berita')
@include('user.guest.contact')

@include('footer')