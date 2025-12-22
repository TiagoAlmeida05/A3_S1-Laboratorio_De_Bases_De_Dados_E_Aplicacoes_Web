@extends('layouts.admin')

@section('content')
<h1>User Reports</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<h2>Pending Reports</h2>
@php
    $reportTypeOrder = ['JobSeeker', 'JobPosting', 'Company', 'Application', 'General'];
    $reportTypeLabels = [
        'JobSeeker' => 'Job seeker reports',
        'JobPosting' => 'Job posting reports',
        'Company' => 'Company reports',
        'Application' => 'Application reports',
        'General' => 'General reports'
    ];
@endphp

@forelse($reportTypeOrder as $reportType)
    @if(isset($pendingReports[$reportType]) && $pendingReports[$reportType]->count() > 0)
        <h3>{{ $reportTypeLabels[$reportType] }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reporter name</th>
                    <th>Reporter e-mail</th>
                    <th>Reported content</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingReports[$reportType] as $report)
                    <tr>
                        <td>{{ $report->date->format('d/m/Y H:i') }}</td>
                        <td>{{ $report->reporter->name ?? 'Anonymous user' }}</td>
                        <td>{{ $report->reporter->email ?? 'N/A' }}</td>
                        <td>
                            @if($reportType === 'JobSeeker' && $report->reportedJobSeeker)
                                <a href="{{ route('jobseeker.profile', $report->reported_job_seeker_id) }}">{{ $report->reportedJobSeeker->registeredUser->name }}</a>
                            @elseif($reportType === 'JobPosting' && $report->reportedJobPosting)
                                <a href="{{ route('job_postings.show', $report->reported_job_posting_id) }}">{{ $report->reportedJobPosting->title ?? 'Deleted job posting' }} </a>
                            @elseif($reportType === 'Company' && $report->reportedCompany)
                                <a href="{{ route('companies.show', $report->reported_company_id) }}">{{ $report->reportedCompany->name }}</a>
                            @elseif($reportType === 'Application' && $report->reportedApplication)
                                <a href="{{ route('admin.view-application', $report->reported_application_id) }}">Application #{{ $report->reported_application_id }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $report->description }}</td>
                        <td>
                            <form action="{{ route('admin.user_reports.solve', $report->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="button">Mark as solved</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@empty
@endforelse

@if($pendingReports->isEmpty())
    <p>No pending reports.</p>
@endif

<hr style="margin: 2rem 0;">

<h2>Solved Reports</h2>
@if($solvedReports->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reporter name</th>
                <th>Reporter e-mail</th>
                <th>Reported content</th>
                <th>Description</th>
                <th>Handled by</th>
            </tr>
        </thead>
        <tbody>
            @foreach($solvedReports as $report)
                @php 
                    $reportType = $report->getReportType(); 
                @endphp
                <tr>
                    <td>{{ $report->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $report->getReportTypeLabel() }}</td>
                    <td>{{ $report->reporter->name ?? 'Anonymous user' }}</td>
                    <td>{{ $report->reporter->email ?? 'N/A' }}</td>
                    <td>
                        @if($reportType === 'JobSeeker' && $report->reportedJobSeeker)
                            <a href="{{ route('jobseeker.profile', $report->reported_job_seeker_id) }}">{{ $report->reportedJobSeeker->registeredUser->name }}</a>
                        @elseif($reportType === 'JobPosting' && $report->reportedJobPosting)
                            <a href="{{ route('job_postings.show', $report->reported_job_posting_id) }}">{{ $report->reportedJobPosting->title ?? 'Deleted job posting' }} </a>
                        @elseif($reportType === 'Company' && $report->reportedCompany)
                            <a href="{{ route('companies.show', $report->reported_company_id) }}">{{ $report->reportedCompany->name }}</a>
                        @elseif($reportType === 'Application' && $report->reportedApplication)
                            <a href="{{ route('admin.view-application', $report->reported_application_id) }}">Application #{{ $report->reported_application_id }}</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $report->description }}</td>
                    <td>{{ $report->handler->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No solved reports.</p>
@endif
@endsection