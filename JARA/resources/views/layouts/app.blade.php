<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'JARA') — Project Management</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --canvas: #f6f7ff;
            --surface: #FFFFFF;
            --recessed: #f1f2ff;
            --recessed-dark: #e1e3ff;
            --border: #e7e8f5;
            --primary: #5b4bdb;
            --primary-hover: #4839bf;
            --interactive: #6958e8;
            --text-main: #202038;
            --text-muted: #73738b;
            --text-secondary: #73738b;
            --indigo: #6958e8;
            --divider: #e7e8f5;
            --error: #d94b62;
            --error-bg: #fff0f3;
            --error-border: #ffcbd4;
            --success: #157a60;
            --success-bg: #e8fbf4;
            --success-border: #b8eedc;
            --radius-sm: 7px;
            --radius-md: 11px;
            --radius-lg: 18px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 8% -10%, #d9d4ff 0, transparent 30rem), radial-gradient(circle at 95% 8%, #c9f6ed 0, transparent 26rem), var(--canvas);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
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
            background: rgba(255,255,255,.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(231,232,245,.8);
            padding: .8rem clamp(1.25rem, 4vw, 4rem);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 2.1rem;
        }

        .brand-logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.06em;
            color: var(--text-main);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: .35rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 13px;
            padding: .5rem .75rem;
            border-radius: 8px;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
            background: #eeedff;
            text-decoration: none;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .user-name {
            font-weight: 500;
            color: var(--text-main);
            font-size: 13px;
        }

        .user-role-badge {
            background: #eeedff;
            color: var(--primary);
            border: 1px solid #dedbff;
            padding: 3px 8px;
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
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            padding: 0.5rem 0.875rem;
            border-radius: 10px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
            text-decoration: none;
            line-height: 1;
        }

        .btn:hover {
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6858e8, #8b5cf6);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(91,75,219,.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5645d0, #7947e2);
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(91,75,219,.28);
        }

        .btn-secondary {
            background-color: var(--surface);
            color: var(--text-main);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background-color: #f4f3ff;
            border-color: #d8d5ff;
        }

        .btn-danger {
            background-color: var(--error);
            color: #ffffff;
            border-color: var(--error);
        }

        .btn-danger:hover {
            background-color: #c93d54;
        }

        .btn-sm {
            padding: 0.35rem 0.6rem;
            font-size: 12px;
        }

        /* Container & Layout */
        .main-container {
            max-width: 1180px;
            width: 100%;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
            flex: 1;
        }

        /* Alerts */
        .alert {
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
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
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(231,232,245,.95);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(73,70,122,.06);
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
            padding: .65rem .8rem;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-main);
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            transition: border-color 150ms ease, box-shadow 150ms ease;
            box-sizing: border-box;
        }

        .form-control:focus,
        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--interactive);
            box-shadow: 0 0 0 4px rgba(105, 88, 232, 0.12);
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
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(231,232,245,.95);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: 0 18px 50px rgba(73,70,122,.12);
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
            background-color: #f1f2f8;
            color: #71717A;
            border: 1px solid #E4E4E7;
        }

        .badge-progress-in-progress,
        .badge-status-in_progress,
        .badge-in_progress {
            background-color: #eeedff;
            color: #5847d8;
            border: 1px solid #ddd9ff;
        }

        .badge-progress-completed,
        .badge-status-done,
        .badge-done {
            background-color: #e8fbf4;
            color: #157a60;
            border: 1px solid #b8eedc;
        }

        .badge-priority-medium,
        .badge-medium {
            background-color: #fff4d8;
            color: #a46109;
            border: 1px solid #ffe39e;
        }

        .badge-priority-high,
        .badge-high {
            background-color: #fff0f3;
            color: #c83c55;
            border: 1px solid #ffcbd4;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #6858e8, #8b5cf6);
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
            background-color: #f8f8ff;
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
            padding: 1.25rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 12px;
            margin-top: auto;
        }

        .brand-logo::before { content: 'J'; display: inline-grid; place-items: center; width: 30px; height: 30px; margin-right: .5rem; color: #fff; font-size: .9rem; border-radius: 9px; background: linear-gradient(135deg, #6858e8, #42c7b2); vertical-align: middle; box-shadow: 0 5px 12px rgba(91,75,219,.25); }
        .page-header { margin-bottom: 1.5rem; }
        .page-title { font-size: clamp(1.45rem, 3vw, 1.9rem); }
        .page-subtitle { color: var(--text-muted); margin-top: .35rem; }
        .card-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border); }
        .card-title { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; }
        .card-body { min-width: 0; }
        .d-inline { display: inline; }
        .text-right { text-align: right; }
        .form-hint, .invalid-feedback { display: block; color: var(--text-muted); font-size: 12px; margin-top: .4rem; }
        .invalid-feedback { color: var(--error); }
        .empty-state { text-align: center; padding: 3rem 1.5rem; background: rgba(255,255,255,.68); border: 1px dashed #d8d5ff; border-radius: var(--radius-lg); }
        .empty-state-text { color: var(--text-muted); margin-bottom: 1rem; }
        .task-list { background: rgba(255,255,255,.92); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 10px 30px rgba(73,70,122,.06); }
        .task-list-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); }
        .task-list-title { font-size: 1rem; }
        .projects-hero { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; padding: clamp(1.5rem, 4vw, 2.5rem); margin-bottom: 1.75rem; color: #fff; background: linear-gradient(120deg, #5745cf, #7863eb 56%, #3bbca9); border-radius: 22px; box-shadow: 0 18px 38px rgba(91,75,219,.22); }
        .projects-hero::after { content: ''; position: absolute; right: -3rem; bottom: -5rem; width: 15rem; height: 15rem; border: 28px solid rgba(255,255,255,.13); border-radius: 50%; }
        .projects-hero > * { position: relative; z-index: 1; }
        .projects-hero h1 { color: #fff; font-size: clamp(1.75rem, 4vw, 2.3rem); margin: .3rem 0; }
        .projects-hero p { color: rgba(255,255,255,.82); }
        .projects-hero .btn-primary { color: var(--primary); background: #fff; box-shadow: none; }
        .projects-hero .btn-primary:hover { background: #f4f3ff; }
        .eyebrow { display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: .12em; color: #d7fff7; }
        .empty-projects { text-align: center; padding: 4rem 2rem; }
        .project-table-card { padding: 0; overflow: hidden; }
        .project-name { font-weight: 700; color: var(--text-main); font-size: 14px; }
        .project-name:hover { color: var(--primary); text-decoration: none; }
        @media (max-width: 700px) { .navbar { align-items: flex-start; gap: .75rem; } .nav-brand { gap: .75rem; flex-wrap: wrap; } .nav-user { gap: .45rem; } .user-name, .user-role-badge { display: none; } .main-container { margin: 1.5rem auto; } .footer { margin-top: 1rem; } }
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
