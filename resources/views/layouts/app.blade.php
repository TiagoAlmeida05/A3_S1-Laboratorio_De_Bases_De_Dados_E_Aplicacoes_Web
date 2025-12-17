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
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

        <link rel="stylesheet" href="{{ asset('css/milligram.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @stack('styles')

        @vite(['resources/js/app.js'])
        @stack('scripts')

        <style>
            /* =========================================
               1. HEADER CONTAINER (Flexbox Fix)
               ========================================= */
            header {
                display: flex !important;              /* Force Flexbox */
                justify-content: space-between !important; /* Logo Left, Icons Right */
                align-items: center !important;        /* Vertically Center */
                
                background-color: white;
                padding: 0 2rem;
                height: 80px;                          /* Fixed height prevents squashing */
                border-bottom: 1px solid #e1e1e1;
                position: relative;
                z-index: 100;
            }

            /* =========================================
               2. ACTION GROUP (Right Side Icons)
               ========================================= */
            .header-actions {
                display: flex !important;              /* Icons sit side-by-side */
                flex-direction: row !important;
                align-items: center !important;
                gap: 20px !important;                  /* Space between Bell, Msg, Name */
                width: auto !important;
            }

            /* =========================================
               3. ICON BUTTONS (Bell & Message)
               ========================================= */
            .icon-btn {
                position: relative !important;         /* Anchor for Red Badge */
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                text-decoration: none !important;
                border: none !important;
                box-shadow: none !important;
                background: transparent;
                transition: color 0.2s;
            }

            .icon-btn:hover {
                color: #9b4dca; 
                background-color: #f8f9fa;
                border-radius: 50%;
            }

            .icon-btn svg {
                width: 24px;
                height: 24px;
                color: #606c76;
            }

            /* =========================================
               4. RED NOTIFICATION BADGE
               ========================================= */
            #notificationBadge {
                display: none; /* JS turns this on */
                position: absolute;
                top: 5px;   
                right: 5px; 
                width: 10px;
                height: 10px;
                background-color: #ff4444;
                border: 2px solid white;
                border-radius: 50%;
                z-index: 10;
                pointer-events: none; /* Let clicks pass through to the bell */
            }

            /* =========================================
               5. USER NAME & DROPDOWN
               ========================================= */
            .user-dropdown-toggle {
                cursor: pointer;
                font-weight: 700;
                color: #9b4dca;
                text-decoration: none !important;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                white-space: nowrap; /* Prevents name from breaking lines */
            }

            .user-dropdown-toggle:hover {
                color: #606c76;
            }

            /* =========================================
               6. DROPDOWN MENU STYLING
               ========================================= */
            .dropdown-menu.notification-menu {
                width: 360px;
                max-height: 480px;
                overflow-y: auto;
                padding: 0;
                border: none;
                box-shadow: 0 10px 40px rgba(0,0,0,0.12);
                border-radius: 12px;
                margin-top: 10px;
            }

            .dropdown-header.notification-header-text {
                background-color: #ffffff;
                padding: 15px 20px;
                font-size: 0.85rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #333;
                border-bottom: 1px solid #f0f0f0;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            /* Notification Items */
            .notification-item {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .notification-date {
                font-size: 0.75rem;
                color: #adb5bd;
                font-weight: 500;
            }

            .notification-content {
                font-size: 0.95rem;
                color: #343a40;
                line-height: 1.4;
            }

            /* Unread Notification: Bright, Bold, White Background */
            .notification-item.unread {
                background-color: #ffffff;
                font-weight: 700; /* Bold text */
                border-left: 4px solid #9b4dca; /* Purple accent on left */
            }

            /* Read Notification: Darker/Gray, Normal weight */
            .notification-item.read {
                background-color: #f8f9fa; /* Light Gray */
                color: #6c757d; /* Muted text */
                border-left: 4px solid transparent;
            }

            .notification-item.read .notification-content {
                color: #6c757d; /* Force text to look "old" */
            }

            /* View All Link */
            .notification-footer-link {
                display: block;
                text-align: center;
                padding: 15px;
                background: #fff;
                color: #9b4dca;
                font-weight: 600;
                font-size: 0.9rem;
                text-decoration: none;
                border-top: 1px solid #f0f0f0;
            }
            .notification-footer-link:hover {
                background: #f1f1f1;
                color: #8a3cb0;
            }

            /* --- Popup Card (Bottom Right) --- */
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                width: 350px;
                background: white;
                border-left: 6px solid #ff4444; 
                padding: 15px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                border-radius: 4px;
                z-index: 1000;
                transform: translateX(150%);
                transition: transform 0.3s ease-out;
            }
            .notification.show {
                transform: translateX(0);
            }
            .notification-header {
                font-weight: bold;
                margin-bottom: 5px;
            }
            .notification-body button {
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        
        <main>
            <header>
                <h1 style="margin: 0; font-size: 2.4rem;">
                    <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">HireUp!</a>
                </h1>

                <div class="header-actions">
                    @auth
                        @php
                            $user = Auth::user();
                            
                            $hasUnread = \App\Models\Notification::where('registered_user_id', $user->id)
                                            ->whereNull('read_date')
                                            ->exists();
                            $recruiter = $user->recruiter;
                            $isRecruiter = $recruiter !== null;
                            $isManager = $isRecruiter && $recruiter->is_company_manager;
                            $isJobSeeker = $user->jobSeeker !== null;
                            $companyId = $isManager && $recruiter->department ? $recruiter->department->company_id : null;
                            $jobSeekerId = $isJobSeeker ? $user->jobSeeker->registered_user_id : null;
                        @endphp
                        
                        <div class="dropdown" style="position: relative;">
                            <a href="#" class="icon-btn" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                
                                <span id="notificationBadge" style="display: {{ $hasUnread ? 'block' : 'none' }};"></span>
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
                        <a href="#" class="icon-btn" title="Messages">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
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