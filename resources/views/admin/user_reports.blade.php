@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <h1 class="h3 text-secondary mb-4">User Reports</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SECÇÃO: PENDING REPORTS --}}
    <h4 class="text-primary mb-3">Pending Reports</h4>

    @php
        $reportTypeOrder = ['JobSeeker', 'JobPosting', 'Company', 'Application', 'General'];
        $reportTypeLabels = [
            'JobSeeker' => 'Job Seeker Reports',
            'JobPosting' => 'Job Posting Reports',
            'Company' => 'Company Reports',
            'Application' => 'Application Reports',
            'General' => 'General Reports'
        ];
        $hasPending = false;
    @endphp

    @foreach($reportTypeOrder as $reportType)
        @if(isset($pendingReports[$reportType]) && $pendingReports[$reportType]->count() > 0)
            @php $hasPending = true; @endphp
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold">
                    {{ $reportTypeLabels[$reportType] }}
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Reporter</th>
                                <th>Reported Content</th>
                                <th>Description</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReports[$reportType] as $report)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $report->date->format('d/m/Y H:i') }}</td>
                                    
                                    <td>
                                        {{ $report->reporter->name ?? 'Anonymous' }} <br>
                                        <small class="text-muted">{{ $report->reporter->email ?? '' }}</small>
                                    </td>

                                    <td class="fw-medium">
                                        @if($reportType === 'JobSeeker' && $report->reportedJobSeeker)
                                            <a href="{{ route('jobseeker.profile', $report->reported_job_seeker_id) }}" class="text-decoration-none">{{ $report->reportedJobSeeker->registeredUser->name }}</a>
                                        @elseif($reportType === 'JobPosting' && $report->reportedJobPosting)
                                            <a href="{{ route('job_postings.show', $report->reported_job_posting_id) }}" class="text-decoration-none">{{ $report->reportedJobPosting->title ?? 'Deleted' }}</a>
                                        @elseif($reportType === 'Company' && $report->reportedCompany)
                                            <a href="{{ route('companies.show', $report->reported_company_id) }}" class="text-decoration-none">{{ $report->reportedCompany->name }}</a>
                                        @elseif($reportType === 'Application' && $report->reportedApplication)
                                            <a href="{{ route('admin.view-application', $report->reported_application_id) }}" class="text-decoration-none">Application #{{ $report->reported_application_id }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td>{{ $report->description }}</td>

                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.user_reports.solve', $report->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success">Mark Solved</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    @if(!$hasPending)
        <p class="text-muted mb-5">No pending reports.</p>
    @endif

</div>
@endsection