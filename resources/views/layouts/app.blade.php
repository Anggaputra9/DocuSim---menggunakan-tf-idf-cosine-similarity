<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DocuSim')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet">

    <style>
        :root {
            --nb-bg: #FFF6E0;
            --nb-ink: #000000;
            --nb-yellow: #FACC15;
            --nb-pink: #F472B6;
            --nb-lime: #A3E635;
            --nb-sky: #60A5FA;
            --nb-orange: #FB923C;
            --nb-red: #EF4444;
        }
        html, body { font-family: 'Space Grotesk', sans-serif; }
        body { background: var(--nb-bg); color: var(--nb-ink); }
        .nb-display { font-family: 'Archivo Black', sans-serif; letter-spacing: -0.5px; }

        /* === Brutalist primitives === */
        .nb-border { border: 3px solid #000; }
        .nb-border-2 { border: 2px solid #000; }
        .nb-border-4 { border: 4px solid #000; }
        .nb-shadow { box-shadow: 6px 6px 0 0 #000; }
        .nb-shadow-sm { box-shadow: 4px 4px 0 0 #000; }
        .nb-shadow-lg { box-shadow: 8px 8px 0 0 #000; }
        .nb-shadow-xl { box-shadow: 10px 10px 0 0 #000; }

        /* Hover lift effect */
        .nb-btn { transition: transform .1s ease, box-shadow .1s ease; }
        .nb-btn:hover { transform: translate(-2px, -2px); box-shadow: 8px 8px 0 0 #000; }
        .nb-btn:active { transform: translate(3px, 3px); box-shadow: 1px 1px 0 0 #000; }
        .nb-card-hover { transition: transform .15s ease, box-shadow .15s ease; }
        .nb-card-hover:hover { transform: translate(-3px, -3px); box-shadow: 9px 9px 0 0 #000; }

        /* Background colors */
        .nb-bg-yellow { background: var(--nb-yellow); }
        .nb-bg-pink   { background: var(--nb-pink); }
        .nb-bg-lime   { background: var(--nb-lime); }
        .nb-bg-sky    { background: var(--nb-sky); }
        .nb-bg-orange { background: var(--nb-orange); }
        .nb-bg-cream  { background: var(--nb-bg); }
        .nb-bg-ink    { background: var(--nb-ink); color: #fff; }

        /* Decorative dotted background */
        .nb-dots {
            background-image: radial-gradient(#000 1px, transparent 1px);
            background-size: 16px 16px;
        }

        /* Inputs */
        .nb-input {
            background: #fff;
            border: 3px solid #000;
            border-radius: 6px;
            padding: 12px 14px;
            font-weight: 500;
            transition: box-shadow .1s ease, transform .1s ease;
            box-shadow: 4px 4px 0 0 #000;
            outline: none;
            width: 100%;
        }
        .nb-input:focus {
            box-shadow: 6px 6px 0 0 #000;
            transform: translate(-1px, -1px);
        }

        /* Tables */
        .nb-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .nb-table thead th {
            background: #000; color: #fff; text-transform: uppercase; font-weight: 700;
            letter-spacing: .05em; font-size: 11px; padding: 14px 20px; text-align: left;
        }
        .nb-table tbody tr { border-bottom: 2px solid #000; }
        .nb-table tbody tr:last-child { border-bottom: 0; }
        .nb-table tbody td { padding: 16px 20px; border-bottom: 2px solid #000; }
        .nb-table tbody tr:last-child td { border-bottom: 0; }
        .nb-table tbody tr:hover { background: #FFF6E0; }

        /* Score bar */
        .nb-score-track { background: #fff; border: 2px solid #000; height: 14px; border-radius: 0; overflow: hidden; }
        .nb-score-fill  { height: 100%; }

        /* Badges */
        .nb-badge {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: 4px 10px; border: 2px solid #000; font-weight: 700;
            font-size: 11px; text-transform: uppercase; letter-spacing: .03em;
            background: #fff;
        }

        /* Sidebar nav active marker */
        .nb-nav-link {
            display: flex; align-items: center; gap: .75rem;
            padding: 12px 14px; font-weight: 700;
            border: 3px solid transparent;
            transition: all .1s ease;
        }
        .nb-nav-link:hover { background: #fff; border-color: #000; box-shadow: 4px 4px 0 0 #000; transform: translate(-1px,-1px); }
        .nb-nav-link.active { background: #000; color: var(--nb-yellow); border-color: #000; box-shadow: 4px 4px 0 0 #fff; }

        /* Decorative tag */
        .nb-tag {
            display: inline-block; padding: 4px 10px; background: #000; color: var(--nb-yellow);
            font-family: 'Archivo Black', sans-serif; font-size: 11px; letter-spacing: .1em; text-transform: uppercase;
        }

        /* Scrollbar (subtle brutalist) */
        ::-webkit-scrollbar { width: 12px; height: 12px; }
        ::-webkit-scrollbar-track { background: var(--nb-bg); border-left: 2px solid #000; }
        ::-webkit-scrollbar-thumb { background: #000; border: 2px solid var(--nb-bg); }
    </style>
    @stack('head')
</head>
<body class="min-h-screen">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden md:flex flex-col w-72 nb-bg-yellow p-6 fixed h-full" style="border-right: 4px solid #000;">
            <div class="flex items-center gap-3 mb-8 nb-border bg-white p-3 nb-shadow-sm">
                <div class="nb-bg-ink p-2 nb-border-2">
                    <svg class="w-6 h-6 text-[var(--nb-yellow)]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="nb-display text-xl leading-none">DocuSim</p>
                    <p class="text-[11px] font-semibold uppercase tracking-wider mt-1">Abstract Similarity</p>
                </div>
            </div>

            @php $route = request()->route()?->getName(); @endphp
            <nav class="space-y-2 flex-1">
                <a href="{{ route('dashboard') }}"
                   class="nb-nav-link {{ $route === 'dashboard' ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    DASHBOARD
                </a>
                <a href="{{ route('similarity.create') }}"
                   class="nb-nav-link {{ str_starts_with($route ?? '', 'similarity.create') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    CEK SIMILARITY
                </a>
                <a href="{{ route('similarity.history') }}"
                   class="nb-nav-link {{ str_starts_with($route ?? '', 'similarity.history') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    RIWAYAT
                </a>
                <a href="{{ route('corpus.index') }}"
                   class="nb-nav-link {{ str_starts_with($route ?? '', 'corpus.') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 10h16M10 4v16"/></svg>
                    CORPUS
                </a>
            </nav>

            <div class="nb-bg-ink text-white nb-border p-4 nb-shadow-sm mt-auto">
                <p class="nb-display text-sm mb-1" style="color: var(--nb-yellow)">TF-IDF × COSINE</p>
                <p class="text-xs leading-relaxed">Powered by Python scikit-learn, dijembatani Laravel via Symfony Process.</p>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 md:ml-72 p-6 md:p-10 relative">
            <!-- mobile top bar -->
            <div class="md:hidden mb-6 flex items-center justify-between nb-border bg-white p-3 nb-shadow-sm">
                <p class="nb-display text-lg">DocuSim</p>
                <a href="{{ route('similarity.create') }}" class="nb-bg-yellow nb-border-2 px-3 py-1.5 text-sm font-bold nb-btn">+ CEK</a>
            </div>

            @if (session('ok'))
                <div class="mb-6 nb-bg-lime nb-border p-4 font-bold nb-shadow flex items-center gap-3">
                    <div class="nb-bg-ink text-[var(--nb-lime)] p-1.5 nb-border-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    {{ session('ok') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 nb-bg-pink nb-border p-4 nb-shadow">
                    <p class="nb-display text-sm mb-2 uppercase">⚠ Ada masalah</p>
                    <ul class="list-disc pl-5 text-sm font-semibold space-y-1">
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
