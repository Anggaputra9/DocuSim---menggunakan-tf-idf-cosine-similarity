@extends('layouts.app')
@section('title', 'Tambah Dokumen Corpus')

@section('content')
    <div class="mb-8">
        <span class="nb-tag">// new corpus</span>
        <h1 class="nb-display text-4xl md:text-5xl mt-3">Tambah Dokumen.</h1>
        <p class="font-semibold text-black/70 mt-2">Tambahkan dokumen referensi baru ke corpus.</p>
    </div>

    <form action="{{ route('corpus.store') }}" method="POST" class="bg-white nb-border nb-shadow p-6 md:p-8">
        @include('corpus._form')
    </form>
@endsection
