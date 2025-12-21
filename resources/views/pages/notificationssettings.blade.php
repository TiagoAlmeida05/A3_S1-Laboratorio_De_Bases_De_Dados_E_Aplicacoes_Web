@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
<div class="container" style="max-width: 800px; margin-top: 40px;">
    <h2>Notification Settings</h2>
    
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('notifications.settings.update') }}" method="POST">
        @csrf
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Notification Type</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $type)
                        {{-- 1. Hide System Alerts (ID 1) --}}
                        @if($type->id == 1) @continue @endif

                        {{-- 2. Role-based Visibility Logic --}}
                        @php
                            $isJobSeeker = $user->isJobSeeker(); 
                            $isRecruiter = $user->recruiter()->exists();
                            $isManager = $isRecruiter && $user->recruiter->is_company_manager;

                            // Job Seekers don't see Recruiter/Manager stuff
                            if ($isJobSeeker && in_array($type->id, [6, 7])) continue;
                            
                            // Recruiters don't see Job Seeker specific stuff
                            if ($isRecruiter && in_array($type->id, [3, 5])) continue;

                            // Regular Recruiters don't see Manager Tasks
                            if ($isRecruiter && !$isManager && $type->id == 7) continue;

                            $isEnabled = $userSettings[$type->id] ?? $type->default_enabled;
                        @endphp

                        <tr>
                            <td>
                                <strong>
                                    @switch($type->id)
                                        @case(2) Direct Messages @break
                                        @case(3) Bookmark Reminders @break
                                        @case(4) 
                                            {{ $isRecruiter ? 'New Application Alerts' : 'Application Status Updates' }}
                                            @break
                                        @case(5) Job Recommendations @break
                                        @case(6) My Job Posting Updates @break
                                        @case(7) Manager Tasks (Approvals) @break
                                        @default {{ $type->name }}
                                    @endswitch
                                </strong>
                                <br>
                                <small class="text-muted">
                                    @switch($type->id)
                                        @case(2) Receive messages from other users. @break
                                        @case(3) Alerts when saved jobs are closing soon. @break
                                        @case(4) 
                                             {{ $isRecruiter ? 'Get notified when a candidate applies to your job.' : 'Know when you are accepted or rejected.' }}
                                             @break
                                        @case(5) Get alerts for new jobs matching your profile. @break
                                        @case(6) Updates when your manager approves/closes your job. @break
                                        @case(7) Notifications for jobs pending your approval. @break
                                    @endswitch
                                </small>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" 
                                       name="subscriptions[{{ $type->id }}]" 
                                       {{ $isEnabled ? 'checked' : '' }}
                                       style="width: 20px; height: 20px;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="button btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection