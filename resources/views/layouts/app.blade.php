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

                <div class="header-options">
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
                        
                        {{-- Notifications: bell icon --}}
                        <div class="dropdown">
                            <a href="#" class="icon-btn" id="notification-bell" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1A4175" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                
                                <span id="notification-badge" class="icon-badge"
                                      style="display: {{ (isset($bellCount) && $bellCount > 0) ? 'block' : 'none' }};">
                                </span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end notification-menu shadow-sm bg-white rounded" aria-labelledby="notification-bell">
                                <li class="dropdown-header notification-header-text">
                                    <span>Recent notifications</span>
                                    <a href="#" class="notif-mark-all-read" onclick="markAllNotificationsRead(event)">
                                        Mark all read
                                    </a>
                                </li>
                                <div id="notification-list">
                                    <li class="p-4 text-center text-muted">Loading...</li>
                                </div>
                                <li><a class="notification-footer-link" href="{{ route('notifications.index') }}">View all notifications</a></li>
                            </ul>
                        </div>

                        {{-- Messages: envelope icon --}}
                        <a href="{{ route('messages.index') }}" class="icon-btn" title="Messages">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1A4175" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>

                            <span id="message-badge" class="icon-badge"
                                  style="display: {{ (isset($letterCount) && $letterCount > 0) ? 'flex' : 'none' }};">
                                {{ $letterCount ?? 0 }}
                            </span>
                        </a>

                        {{-- User menu --}}
                        <div class="dropdown">
                            <a href="#" class="user-dropdown-toggle dropdown-toggle" id="user-menu-button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $user->name }}
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm p-3 mb-5 bg-white rounded" aria-labelledby="user-menu-button">
                                @if($isJobSeeker)
                                    <li><a class="dropdown-item" href="{{ route('jobseeker.profile', $jobSeekerId) }}">View profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('jobseeker.applications') }}">My applications</a></li>
                                    <li><a class="dropdown-item" href="{{ route('jobseeker.bookmarks') }}">My bookmarks</a></li>
                                @endif
                                @if($isManager)
                                    <li><a class="dropdown-item" href="{{ route('companies.edit', $companyId) }}">Edit company profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('recruiter-dashboard.index') }}">My dashboard</a></li>
                                @elseif($isRecruiter && !$isManager)
                                    <li><a class="dropdown-item" href="{{ route('recruiter-dashboard.index') }}">My dashboard</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('notifications.settings') }}">Notification settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center log-out" href="{{ url('/logout') }}">Log out</a></li>
                            </ul>
                        </div>

                    @else
                        <a class="button btn btn-primary" href="{{ url('/login') }}">Login</a>
                        <a class="button btn btn-primary" href="{{ url('/register') }}">Register</a>
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