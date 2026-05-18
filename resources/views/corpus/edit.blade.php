@extends('layouts.app')
@section('title', 'Edit Dokumen Corpus')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Edit Dokumen Corpus</h1>
        <p class="text-slate-500 mt-1">Perbarui informasi dokumen referensi.</p>
    </div>

    <form action="{{ route('corpus.update', $corpus) }}" method="POST" class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-100">
        @method('PUT')
        @include('corpus._form')
    </form>
@endsection
