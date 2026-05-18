@extends('layouts.app')
@section('title', 'Tambah Dokumen Corpus')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Tambah Dokumen Corpus</h1>
        <p class="text-slate-500 mt-1">Tambahkan dokumen referensi baru ke corpus.</p>
    </div>

    <form action="{{ route('corpus.store') }}" method="POST" class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-100">
        @include('corpus._form')
    </form>
@endsection
