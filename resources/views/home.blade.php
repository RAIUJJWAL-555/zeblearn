@extends('layouts.app')

@section('content')
<main class="w-full pt-8 sm:pt-10 bg-canvas-base min-h-screen">
    <div class="flex flex-col w-full">
        @include('home.hero')
        @include('home.bento')
        @include('home.courses')
        @include('home.power-user')
        @include('home.why')
        @include('home.stories')
        @include('home.placement')
        @include('home.blog')
        @include('home.faq-cta')
        @include('components.layouts.footer')
    </div>
</main>
@endsection