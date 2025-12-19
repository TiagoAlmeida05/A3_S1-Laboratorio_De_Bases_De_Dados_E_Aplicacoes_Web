<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - HireUp!</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/milligram.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <aside id="admin-sidebar">
        <h3>Admin</h3>
        
        <nav>
            <a href="{{ route('admin.jobs') }}" class=button>Job Postings Management</a>
            <a href="{{ route('admin.content') }}" class=button>Content Management</a>
            <a href="{{ route('admin.pages') }}" class=button>Info Pages Management</a>
            <a href="{{ route('admin.job_seekers') }}" class=button>Job Seeker Management</a>
            <a href="{{ route('admin.companies') }}" class=button>Company Management</a>
            <a href="{{ route('admin.notifications.create') }}" class="button">Send Notification</a>
            <a href="{{ route('admin.settings') }}" class=button>Settings</a>
        </nav>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </aside>

    <div id="right-panel">
        
        <main id="admin-content">
            @if(session('success'))
                <div>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
