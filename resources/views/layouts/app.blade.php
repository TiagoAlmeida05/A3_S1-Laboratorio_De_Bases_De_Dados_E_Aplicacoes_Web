<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @auth
        <meta name="user-id" content="{{ Auth::id() }}">
        @endauth

        <title>@yield('title', config('app.name', 'Laravel'))</title>
        
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        @stack('scripts')
    </head>
    <body>
        <main>
            <header>
                <h1 id="header-website-name">
                    <a href="{{ url('/') }}">HireUp!</a>
                </h1>

                <div class="header-actions">
                    @auth
                        @php
                            $user = Auth::user();
                            $recruiter = $user->recruiter;
                            $isRecruiter = $recruiter !== null;
                            $isManager = $isRecruiter && $recruiter->is_company_manager;
                            $isJobSeeker = ($user->jobSeeker !== null) && !$isRecruiter;
                            $companyId = $isManager && $recruiter->department ? $recruiter->department->company_id : null;
                            $jobSeekerId = $isJobSeeker ? $user->jobSeeker->registered_user_id : null;
                        @endphp
                        
                        {{-- BELL ICON --}}
                        <div class="dropdown" style="position: relative;">
                            <a href="#" class="icon-btn" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                
                                <span id="notificationBadge" class="icon-badge" 
                                      style="display: {{ (isset($bellCount) && $bellCount > 0) ? 'block' : 'none' }};">
                                </span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationBell">
                                <li class="dropdown-header notification-header-text" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span>Recent Notifications</span>
                                    <a href="#" onclick="markAllNotificationsRead(event)" style="font-size: 0.7rem; text-decoration: none; cursor: pointer;">
                                        Mark all read
                                    </a>
                                </li>
                                <div id="notificationList">
                                    <li class="p-4 text-center text-muted">Loading...</li>
                                </div>
                                <li><a class="notification-footer-link" href="{{ route('notifications.index') }}">View All Notifications</a></li>
                            </ul>
                        </div>

                        {{-- MESSAGE ICON --}}
                        <a href="{{ route('messages.index') }}" class="icon-btn" title="Messages">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>

                            <span id="messageBadge" class="icon-badge"
                                  style="display: {{ (isset($letterCount) && $letterCount > 0) ? 'flex' : 'none' }};">
                                {{ $letterCount ?? 0 }}
                            </span>
                        </a>

                        {{-- USER MENU --}}
                        <div class="dropdown">
                            <a href="#" class="user-dropdown-toggle dropdown-toggle" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $user->name }}
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuButton">
                                @if($isJobSeeker)
                                    <li><a class="dropdown-item" href="{{ route('jobseeker.profile', $jobSeekerId) }}">View Profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('jobseeker.applications') }}">My Applications</a></li>
                                    <li><a class="dropdown-item" href="{{ route('jobseeker.bookmarks') }}">My Bookmarks</a></li>
                                @endif
                                @if($isManager)
                                    <li><a class="dropdown-item" href="{{ route('companies.edit', $companyId) }}">Edit Company Profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('recruiter-dashboard.index') }}">Dashboard</a></li>
                                @elseif($isRecruiter && !$isManager)
                                    <li><a class="dropdown-item" href="{{ route('recruiter-dashboard.index') }}">Dashboard</a></li>
                                @endif

                                {{-- ADDED: Notification Settings Link --}}
                                <li><a class="dropdown-item" href="{{ route('notifications.settings') }}">Notification Settings</a></li>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ url('/logout') }}">Log Out</a></li>
                            </ul>
                        </div>

                    @else
                        <a class="button button-outline" href="{{ url('/login') }}">Login</a>
                        <a class="button" href="{{ url('/register') }}">Register</a>
                    @endauth
                </div>
            </header>

            <section id="content">
                @yield('content')
            </section>

            <div id="notification-card" class="notification">
                <div class="notification-header">
                    <strong id="notification-title"></strong>
                </div>
                <div class="notification-body">
                    <p id="notification-message"></p>
                    <button>Dismiss</button>
                </div>
            </div>

        </main>
        @include('partials.footer')
    </body>
</html>