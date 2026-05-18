<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dokumen Similarity')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-hover { transition: all 0.25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 35px -10px rgba(0,0,0,.15); }
        .score-bar {
            background: linear-gradient(90deg, #34d399, #fbbf24, #ef4444);
        }
        .glass {
            background: rgba(255,255,255,.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

    <!-- Sidebar + content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden md:flex flex-col w-64 gradient-bg text-white p-6 fixed h-full">
            <div class="flex items-center gap-3 mb-10">
                <div class="bg-white/20 rounded-xl p-2">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-lg leading-tight">DocuSim</p>
                    <p class="text-xs text-white/70">Abstract Similarity</p>
                </div>
            </div>

            @php $route = request()->route()?->getName(); @endphp
            <nav class="space-y-1 flex-1">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg {{ $route === 'dashboard' ? 'bg-white/20' : 'hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('similarity.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg {{ str_starts_with($route ?? '', 'similarity.create') ? 'bg-white/20' : 'hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cek Similarity
                </a>
                <a href="{{ route('similarity.history') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg {{ str_starts_with($route ?? '', 'similarity.history') ? 'bg-white/20' : 'hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat
                </a>
                <a href="{{ route('corpus.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg {{ str_starts_with($route ?? '', 'corpus.') ? 'bg-white/20' : 'hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 10h16M10 4v16"/></svg>
                    Corpus
                </a>
            </nav>

            <div class="bg-white/10 rounded-lg p-4 text-xs text-white/80 mt-auto">
                <p class="font-semibold mb-1">TF-IDF + Cosine</p>
                <p>Powered by Python scikit-learn, dijembatani Laravel via Symfony Process.</p>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 md:ml-64 p-6 md:p-10">
            <!-- mobile top bar -->
            <div class="md:hidden mb-6 flex items-center justify-between">
                <p class="font-bold text-lg gradient-text">DocuSim</p>
                <a href="{{ route('similarity.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">+ Cek</a>
            </div>

            @if (session('ok'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('ok') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
