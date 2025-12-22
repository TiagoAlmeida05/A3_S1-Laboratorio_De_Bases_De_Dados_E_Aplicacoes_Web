<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | HireUp!</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @stack('scripts')
</head>
<body>

    <aside id="admin-sidebar">
        <h3 style="padding: 1rem;">Admin dashboard</h3>
        
        <nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding: 1rem;">
            <a href="{{ route('admin.jobs') }}" class=button style="background-color: white; color: black;">Job Postings Management</a>
            <a href="{{ route('admin.user_reports') }}" class=button style="background-color: white; color: black;">User reports</a>
            <a href="{{ route('admin.pages') }}" class=button style="background-color: white; color: black;">Info Pages Management</a>
            <a href="{{ route('admin.job_seekers') }}" class=button style="background-color: white; color: black;">Job Seeker Management</a>
            <a href="{{ route('admin.companies') }}" class=button style="background-color: white; color: black;">Company Management</a>
            <a href="{{ route('admin.recruiters') }}" class=button style="background-color: white; color: black;">Recruiter Management</a>
            <a href="{{ route('admin.content') }}" class="button" style="background-color: white; color: black;">Add Content (Tags/Loc)</a>
            <a href="{{ route('admin.notifications.create') }}" class="button" style="background-color: white; color: black;">Send Notification</a>
            <a href="{{ route('admin.users.create') }}" class=button style="background-color: white; color: black;">Create New User</a>
            <a href="{{ route('admin.settings') }}" class=button style="background-color: white; color: black;">Settings</a>
        </nav>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn btn btn-danger">Logout</button>
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
    
