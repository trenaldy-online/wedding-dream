<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'WeddingPlanner' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <header class="app-header">
        <div class="app-container header-inner">
            <a href="{{ route('dashboard') }}" class="brand">
                <div class="brand-logo">✦</div>
                <span>WeddingPlanner</span>
            </a>

            <nav class="main-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                
                <a href="{{ route('wedding-events.index') }}" class="{{ request()->routeIs('wedding-events.*') ? 'active' : '' }}">
                    Acara
                </a>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    Undangan
                </a>

                <a href="{{ route('budget-items.index') }}" class="{{ request()->routeIs('budget-items.*') ? 'active' : '' }}">
                    Budget
                </a>

                <a href="{{ route('guests.index') }}" class="{{ request()->routeIs('guests.*') ? 'active' : '' }}">
                    Tamu
                </a>
                <a href="{{ route('checklists.index') }}" class="{{ request()->routeIs('checklists.*') ? 'active' : '' }}">
                    Checklist
                </a>

                <a href="{{ route('sync.index') }}" class="{{ request()->routeIs('sync.*') ? 'active' : '' }}">
                    Sinkronisasi
                </a>
            </nav>

            <div class="header-user">
                @auth
                    <span>{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="app-main">
        <div class="app-container">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ada input yang perlu diperbaiki:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>