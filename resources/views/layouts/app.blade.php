<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/milligram.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @stack('styles')

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        @stack('scripts')
    </head>
    <body>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <main>
            <header>
                <h1>
                    <a href="{{ url('/') }}">HireUp!</a></h1>

                @auth
                    @php
                        $recruiter = Auth::user()->recruiter;
                        $isManager = $recruiter && $recruiter->is_company_manager;
                        $companyId = $isManager ? $recruiter->department->company_id : null;

                        $user = Auth::user();
                        $isJobSeeker = $user->jobSeeker !== null;
                        $isRecruiter = $user->recruiter !== null;
                        
                        if ($isJobSeeker) {
                            $profileUrl = route('jobseeker.profile', $user->jobSeeker->registered_user_id);
                        } elseif ($isRecruiter) {
                            $profileUrl = route('recruiter-dashboard.index');
                        }
                    @endphp
                    
                    @if($isManager && $companyId)
                        <a class="button" href="{{ route('companies.edit', $companyId) }}">Edit Company</a>
                    @endif

                    <a class="button" href="{{ url('/logout') }}"> Logout </a> 
                    <a href="{{ $profileUrl }}" class="user-profile-link">{{ Auth::user()->name }}</a>
                @else
                    <a class="button" href="{{ url('/login') }}">Login</a>
                @endauth
            </header>

            <section id="content">
                @yield('content')
            </section>
        </main>
        @include('partials.footer')
    </body>
</html>