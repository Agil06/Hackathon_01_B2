<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'JARA') — Project Management</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans    :wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --canvas: #FAFAFA;
            --surface: #FFFFFF;
            --recessed: #F4F4F5;
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
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--canvas);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

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

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-0.5px);
        }

        .btn-secondary {
            background-color: var(--surface);
            color: var(--text-main);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background-color: var(--recessed);
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
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.375rem;
            font-weight: 500;
            font-size: 13px;
            color: var(--text-main);
        }

        .form-control,
        .form-input,
        .form-select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-main);
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: border-color 150ms ease, box-shadow 150ms ease;
            box-sizing: border-box;
        }

        .form-control:focus,
        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--interactive);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .form-control.is-invalid,
        .form-input.is-invalid,
        .form-select.is-invalid {
            border-color: var(--error);
        }

        .form-error {
            color: var(--error);
            font-size: 12px;
            margin-top: 0.25rem;
            display: block;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .required {
            color: var(--error);
        }

        .btn-full {
            width: 100%;
        }

        /* Auth Container & Cards */
        .auth-container {
            max-width: 440px;
            margin: 2rem auto;
            width: 100%;
        }

        .auth-card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.375rem;
        }

        .auth-subtitle {
            color: var(--text-muted);
            font-size: 13px;
            margin-bottom: 1.5rem;
        }

        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .auth-link {
            color: var(--interactive);
            font-weight: 500;
        }

        /* Badges */
        .badge,
        .role-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            line-height: 1.4;
            letter-spacing: 0.02em;
        }

        .badge-progress-not-started,
        .badge-status-not_done,
        .badge-not_done,
        .badge-priority-low,
        .badge-low {
            background-color: #F4F4F5;
            color: #71717A;
            border: 1px solid #E4E4E7;
        }

        .badge-progress-in-progress,
        .badge-status-in_progress,
        .badge-in_progress {
            background-color: #EEF2FF;
            color: #4F46E5;
            border: 1px solid #E0E7FF;
        }

        .badge-progress-completed,
        .badge-status-done,
        .badge-done {
            background-color: #ECFDF5;
            color: #047857;
            border: 1px solid #A7F3D0;
        }

        .badge-priority-medium,
        .badge-medium {
            background-color: #FEF3C7;
            color: #92400E;
            border: 1px solid #FDE68A;
        }

        .badge-priority-high,
        .badge-high {
            background-color: #FEE2E2;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }

        .role-badge.admin {
            background-color: var(--primary);
            color: #ffffff;
        }

        .role-badge.user {
            background-color: var(--recessed);
            color: var(--text-muted);
            border: 1px solid var(--recessed-dark);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table,
        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th,
        .table th {
            padding: 0.75rem 1rem;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .data-table td,
        .table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            vertical-align: middle;
        }

        .data-table tbody tr:hover,
        .table tbody tr:hover {
            background-color: #F8FAFC;
        }

        .task-link {
            font-weight: 500;
            color: var(--text-main);
        }

        .task-link:hover {
            color: var(--interactive);
        }

        /* Footer */
        .footer {
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
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} JARA — Clarity & Precision Project Management
    </footer>
</body>
</html>
