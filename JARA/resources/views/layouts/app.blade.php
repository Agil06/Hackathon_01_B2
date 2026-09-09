<!DOCTYPE html>
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
    
    <style>
        :root {
            --canvas: #FAFAFA;
            --surface: #FFFFFF;
            --recessed: #F4F4F5;
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
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--canvas);
            color: var(--text-main);
            line-height: 1.5;
            font-size: 14px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

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

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            line-height: 1.25;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #FFFFFF;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--surface);
            color: var(--text-main);
            border-color: var(--divider);
        }

        .btn-secondary:hover {
            background-color: var(--recessed);
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
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 0.4rem;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
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
        }

        /* Footer */
        .footer {
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
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="padding-left: 1.25rem; margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} JARA. Dikembangkan oleh Tim Pengembang JARA.
    </footer>
    @stack('scripts')
</body>
</html>
