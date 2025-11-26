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

    <style>
        body { display: flex; min-height: 100vh; margin: 0; font-family: 'Roboto', sans-serif; }
        
        #admin-sidebar {
            width: 250px;
            background-color: #9b4dca;
            color: white;
            padding: 20px;
            flex-shrink: 0;
        }
        #admin-sidebar h3 { color: white; border-bottom: 1px solid #ecf0f1; padding-bottom: 10px; }
        #admin-sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        #admin-sidebar a:hover { color: #3498db; padding-left: 5px; transition: 0.3s; }
        
        #admin-content { flex-grow: 1; padding: 40px; background-color: #f4f6f7; overflow-y: auto; }
        
        .logout-btn { margin-top: 20px; color: #ea4c3aff !important; cursor: pointer; background: none; border: none; text-align: left; padding: 0; }
    </style>
</head>
<body>

    <aside id="admin-sidebar">
        <h3>Admin</h3>
        
        <nav>
            <a href="{{ route('admin.jobs') }}">Job Postings Management</a>
            <a href="{{ route('admin.content') }}">Content Management</a>
            <a href="{{ route('admin.pages') }}">Info Pages Management</a>
        </nav>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </aside>

    <main id="admin-content">
        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
</body>
</html>