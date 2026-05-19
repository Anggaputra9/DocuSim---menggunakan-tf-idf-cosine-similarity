@extends('layouts.app')
@section('title', 'Edit Dokumen Corpus')

@section('content')
    <div class="mb-8">
        <span class="nb-tag">// edit corpus</span>
        <h1 class="nb-display text-4xl md:text-5xl mt-3">Edit Dokumen.</h1>
        <p class="font-semibold text-black/70 mt-2">Perbarui informasi dokumen referensi.</p>
    </div>

    <form action="{{ route('corpus.update', $corpus) }}" method="POST" class="bg-white nb-border nb-shadow p-6 md:p-8">
        @method('PUT')
        @include('corpus._form')
    </form>
@endsection
