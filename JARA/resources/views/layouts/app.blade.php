<!DOCTYPE html>
<<<<<<< HEAD
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JARA') — Manajemen Project & Akun</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    
=======
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'JARA') — Project Management</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">

>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
    <style>
        :root {
            --canvas: #FAFAFA;
            --surface: #FFFFFF;
            --recessed: #F4F4F5;
<<<<<<< HEAD
            --divider: #E2E8F0;
            --primary: #0F172A;
            --primary-hover: #1E293B;
            --indigo: #4F46E5;
            --indigo-light: #EEF2FF;
            --text-main: #09090B;
            --text-secondary: #71717A;
            --error: #DC2626;
            --error-light: #FEF2F2;
            --success: #16A34A;
            --success-light: #F0FDF4;
=======
            --recessed-dark: #E4E4E7;
            --border: #E2E8F0;
            --primary: #0F172A;
            --primary-hover: #1E293B;
            --interactive: #4F46E5;
            --text-main: #09090B;
            --text-muted: #71717A;
            --error: #ba1a1a;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --success: #047857;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
<<<<<<< HEAD
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--canvas);
            color: var(--text-main);
            line-height: 1.5;
            font-size: 14px;
=======
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--canvas);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.6;
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

<<<<<<< HEAD
        h1, h2, h3, h4 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.02em;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Top Navigation Header */
        .navbar {
            background-color: var(--surface);
            border-bottom: 1px solid var(--divider);
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
        }

        .navbar-brand-badge {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: var(--primary);
            color: #FFFFFF;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-secondary);
            transition: color 0.15s ease;
            font-size: 13px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary);
        }

        .navbar-user {
=======
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            color: var(--text-main);
            letter-spacing: -0.015em;
        }

        a {
            color: var(--interactive);
            text-decoration: none;
            transition: color 150ms ease;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Top Navbar */
        .navbar {
            background-color: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .brand-logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.03em;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 13px;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
            text-decoration: none;
        }

        .nav-user {
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            display: flex;
            align-items: center;
            gap: 1rem;
        }

<<<<<<< HEAD
        .user-tag {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            font-weight: 500;
        }

        .role-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 9999px;
            background-color: var(--recessed);
            color: var(--text-secondary);
            border: 1px solid var(--divider);
            text-transform: uppercase;
        }

        .role-badge.admin {
            background-color: #FEF3C7;
            color: #92400E;
            border-color: #FDE68A;
        }

        .role-badge.user {
            background-color: var(--indigo-light);
            color: var(--indigo);
            border-color: #C7D2FE;
        }

        /* Container */
        .main-container {
            max-width: 1140px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        /* Flash Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: var(--success-light);
            color: #15803D;
            border-color: #BBF7D0;
        }

        .alert-error {
            background-color: var(--error-light);
            color: #B91C1C;
            border-color: #FECACA;
        }

        /* Card Elements */
        .card {
            background-color: var(--surface);
            border: 1px solid var(--divider);
            border-radius: 12px;
            box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--divider);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

=======
        .user-name {
            font-weight: 500;
            color: var(--text-main);
            font-size: 13px;
        }

        .user-role-badge {
            background-color: var(--recessed);
            color: var(--text-muted);
            border: 1px solid var(--recessed-dark);
            padding: 2px 6px;
            font-size: 11px;
            border-radius: var(--radius-sm);
            text-transform: uppercase;
            font-weight: 600;
        }

>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
<<<<<<< HEAD
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            line-height: 1.25;
=======
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            padding: 0.5rem 0.875rem;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 150ms ease;
            text-decoration: none;
            line-height: 1;
        }

        .btn:hover {
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
<<<<<<< HEAD
            color: #FFFFFF;
=======
            color: #ffffff;
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
<<<<<<< HEAD
=======
            transform: translateY(-0.5px);
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        }

        .btn-secondary {
            background-color: var(--surface);
            color: var(--text-main);
<<<<<<< HEAD
            border-color: var(--divider);
=======
            border-color: var(--border);
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        }

        .btn-secondary:hover {
            background-color: var(--recessed);
<<<<<<< HEAD
            border-color: #CBD5E1;
        }

        .btn-danger {
            background-color: #EF4444;
            color: #FFFFFF;
            border-color: #DC2626;
        }

        .btn-danger:hover {
            background-color: #DC2626;
        }

        .btn-sm {
            padding: 0.35rem 0.65rem;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn:disabled, .btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .table th {
            background-color: var(--recessed);
            padding: 0.75rem 1.25rem;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--divider);
        }

        .table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #EEEEEE;
            font-size: 13px;
            color: var(--text-main);
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #F8FAFC;
        }

        /* Form Controls */
=======
            border-color: #cbd5e1;
        }

        .btn-danger {
            background-color: var(--error);
            color: #ffffff;
            border-color: var(--error);
        }

        .btn-danger:hover {
            background-color: #991b1b;
        }

        .btn-sm {
            padding: 0.35rem 0.6rem;
            font-size: 12px;
        }

        /* Container & Layout */
        .main-container {
            max-width: 1100px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        /* Alerts */
        .alert {
            padding: 0.875rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 13px;
            line-height: 1.5;
        }

        .alert-success {
            background-color: var(--success-bg);
            color: var(--success);
            border: 1px solid var(--success-border);
        }

        .alert-danger {
            background-color: var(--error-bg);
            color: var(--error);
            border: 1px solid var(--error-border);
        }

        .alert ul {
            margin-left: 1.25rem;
            margin-top: 0.25rem;
        }

        /* Card */
        .card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Form elements */
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
<<<<<<< HEAD
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 0.4rem;
=======
            margin-bottom: 0.375rem;
            font-weight: 500;
            font-size: 13px;
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
<<<<<<< HEAD
            padding: 0.625rem 0.875rem;
            font-size: 14px;
            color: var(--text-main);
            background-color: var(--surface);
            border: 1px solid var(--divider);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .form-control.is-invalid {
            border-color: var(--error);
        }

        .invalid-feedback {
            color: var(--error);
            font-size: 12px;
            margin-top: 0.35rem;
            font-weight: 500;
        }

        .form-hint {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 0.35rem;
=======
            padding: 0.5rem 0.75rem;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-main);
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: border-color 150ms ease, box-shadow 150ms ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--interactive);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .form-error {
            color: var(--error);
            font-size: 12px;
            margin-top: 0.25rem;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            line-height: 1.4;
            letter-spacing: 0.02em;
        }

        .badge-progress-not-started {
            background-color: var(--recessed);
            color: var(--text-muted);
            border: 1px solid var(--recessed-dark);
        }

        .badge-progress-in-progress {
            background-color: #EEF2FF;
            color: var(--interactive);
            border: 1px solid #E0E7FF;
        }

        .badge-progress-completed {
            background-color: var(--success-bg);
            color: var(--success);
            border: 1px solid var(--success-border);
        }

        .badge-priority-low {
            background-color: #F4F4F5;
            color: #71717A;
            border: 1px solid #E4E4E7;
        }

        .badge-priority-medium {
            background-color: #FEF3C7;
            color: #92400E;
            border: 1px solid #FDE68A;
        }

        .badge-priority-high {
            background-color: #FEE2E2;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }

        .badge-status-not_done {
            background-color: #F4F4F5;
            color: #71717A;
            border: 1px solid #E4E4E7;
        }

        .badge-status-in_progress {
            background-color: #EEF2FF;
            color: #4F46E5;
            border: 1px solid #E0E7FF;
        }

        .badge-status-done {
            background-color: #ECFDF5;
            color: #047857;
            border: 1px solid #A7F3D0;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            padding: 0.75rem 1rem;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .data-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background-color: #F8FAFC;
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        }

        /* Footer */
        .footer {
<<<<<<< HEAD
            border-top: 1px solid var(--divider);
            padding: 1.25rem 2rem;
            font-size: 12px;
            color: var(--text-secondary);
            text-align: center;
            background-color: var(--surface);
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="navbar">
        <a href="{{ url('/') }}" class="navbar-brand">
            <span>JARA</span>
            <span class="navbar-brand-badge">App</span>
        </a>

        <ul class="navbar-nav">
            @if(auth()->check())
                <li>
                    <a href="{{ Route::has('projects.index') ? route('projects.index') : url('/projects') }}" class="nav-link">
                        Projects
                    </a>
                </li>
                @if(auth()->user()->role === 'admin')
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            Admin Users
                        </a>
                    </li>
                @endif
            @endif
        </ul>

        <div class="navbar-user">
            @if(auth()->check())
                <div class="user-tag">
                    <span>{{ auth()->user()->name }}</span>
                    <span class="role-badge {{ auth()->user()->role }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <form action="{{ Route::has('logout') ? route('logout') : url('/logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">Logout</button>
                </form>
            @else
                <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="btn btn-secondary btn-sm">Login</a>
                <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="btn btn-primary btn-sm">Register</a>
            @endif
=======
            border-top: 1px solid var(--border);
            padding: 1rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">
            <a href="{{ route('home') }}" class="brand-logo">JARA</a>
            @auth
                <ul class="nav-links">
                    <li><a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'active' : '' }}">Projects</a></li>
                    @if(auth()->user()->role === 'admin' && Route::has('admin.users.index'))
                        <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin Users</a></li>
                    @endif
                </ul>
            @endauth
        </div>

        <div class="nav-user">
            @auth
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role-badge">{{ auth()->user()->role }}</span>
                @if(Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Logout</button>
                    </form>
                @endif
            @else
                @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Login</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endif
            @endauth
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
<<<<<<< HEAD
                <span>{{ session('success') }}</span>
=======
                {{ session('success') }}
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            </div>
        @endif

        @if(session('error'))
<<<<<<< HEAD
            <div class="alert alert-error">
                <span>{{ session('error') }}</span>
=======
            <div class="alert alert-danger">
                {{ session('error') }}
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
            </div>
        @endif

        @if($errors->any())
<<<<<<< HEAD
            <div class="alert alert-error">
                <ul style="padding-left: 1.25rem; margin: 0;">
=======
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul>
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
<<<<<<< HEAD
        &copy; {{ date('Y') }} JARA. Dikembangkan oleh Tim Pengembang JARA.
    </footer>
    @stack('scripts')
=======
        &copy; {{ date('Y') }} JARA — Clarity & Precision Project Management
    </footer>
>>>>>>> 1cf801ca2ad77ee6a28d99aff40ae5881d421592
</body>
</html>
