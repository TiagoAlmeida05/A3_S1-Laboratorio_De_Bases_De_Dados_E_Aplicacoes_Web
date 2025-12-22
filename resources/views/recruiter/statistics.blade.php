@extends('layouts.app')

@section('title', 'My statistics | ' . config('app.name'))

@section('content')
<section id="recruiter-statistics">
    <h2><h2>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} statistics</h2>
    
    <div style="margin: 2rem 0;">
        <h3>Overall statistics</h3>
        <p><strong>Total number of job postings</strong>: {{ $totalJobs }}</p>
        <p><strong>Active job postings</strong>: {{ $totalActiveJobs }}</p>
        <p><strong>Pending job postings</strong>: {{ $totalPendingJobs }}</p>
        <p><strong>Expired job postings</strong>: {{ $totalExpiredJobs }}</p>
        <p><strong>Closed job postings</strong>: {{ $totalClosedJobs }}</p>
        <p><strong>Total number of applications</strong>: {{ $totalApplications }}</p>
        <p><strong>Total number of acceptances</strong>: {{ $totalAcceptances }}</p>
        <p><strong>Acceptance rate</strong>: {{ $acceptanceRate }}%</p>
    </div>

    <div style="margin: 2rem 0;">
        <h3>Statistics of the past month (from {{ now()->setTimezone('Europe/Lisbon')->subMonth()->format('d/m/Y') }} to today)</h3>
        <p><strong>Total number of job postings created this month</strong>: {{ $totalJobsCreatedThisMonth }}</p>
        <p><strong>Active job postings</strong>: {{ $activeJobsCreatedThisPastMonth }}</p>
        <p><strong>Pending job postings</strong>: {{ $pendingJobsCreatedThisPastMonth }}</p>
        <p><strong>Expired job postings</strong>: {{ $expiredJobsCreatedThisPastMonth }}</p>
        <p><strong>Closed job postings</strong>: {{ $closedJobsCreatedThisPastMonth }}</p>
        <p><strong>Total number of applications</strong>: {{ $totalApplicationsSubmittedThisPastMonth }}</p>
        <p><strong>Total number of acceptances</strong>: {{ $acceptancesForApplicationsSubmittedThisMonth }}</p>
    </div>

    <div style="margin: 2rem 0;">
        <h3>Currently active job postings</h3>
        @if($currentlyActiveJobs->isEmpty())
            <p>You currently have no active job postings.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Job posting</th>
                        <th>Number of applications</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentlyActiveJobs as $job)
                        <tr>
                            <td><a href="{{ route('job_postings.show', $job->id) }}">{{ $job->title }}</a></td>
                            <td>{{ $job->applications_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div>
        <h3>Currently expired job postings</h3>
        @if($currentlyExpiredJobs->isEmpty())
            <p>You currently have no expired jobs.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Job posting</th>
                        <th>Number of applications</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentlyExpiredJobs as $job)
                        <tr>
                            <td><a href="{{ route('job_postings.show', $job->id) }}">{{ $job->title }}</a></td>
                            <td>{{ $job->applications_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div>
        <a href="{{ route('recruiter-dashboard.index') }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Return to my dashboard</a>
    </div>
</section>
@endsection