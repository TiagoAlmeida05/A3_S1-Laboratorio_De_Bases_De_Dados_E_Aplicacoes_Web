@extends('layouts.app')

@section('title', 'Edit job posting' . ' | ' . config('app.name'))

@section('content')
<section id="edit-job-posting">
    <h1>Edit job posting</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class='form-container'>
        <form style="padding: 3rem; width: 40rem; border-color: #4856e7ff;" method="POST" action="{{ route('job_postings.update', $job_posting->id) }}">
            @csrf
            @method('PUT')

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-1">
                <label for="title">Title</label>
                <input class="form-text" id="title" name="title" type="text" placeholder="Insert title." value="{{ old('title', $job_posting->title) }}" required>
            </div>

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-2">
                <label for="description">Description</label>
                <textarea class="form-textarea" id="description" name="description" placeholder="Insert description." required>{{ old('description', $job_posting->description) }}</textarea>
            </div>

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-3">
                <label for="days_to_deadline">Deadline (in days)</label>
                <input class="form-number" id="days_to_deadline" name="days_to_deadline" type="number" placeholder="Days" value="{{ old('days_to_deadline', ceil(\Carbon\Carbon::now()->floatDiffInHours($job_posting->deadline) / 24)) }}" required>
            </div>

            <input type="hidden" id="deadline" name="deadline">

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-4">
                <label for="min_wage">Minimum wage</label>
                <input class="form-number" id="min_wage" name="min_wage" type="number" placeholder="Insert minimum wage (optional)." value="{{ old('min_wage', $job_posting->min_wage) }}">
            </div>

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-5">
                <label for="max_wage">Maximum wage</label>
                <input class="form-number" id="max_wage" name="max_wage" type="number" placeholder="Insert maximum wage (optional)." value="{{ old('max_wage', $job_posting->max_wage) }}">
            </div>

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-6">
                <label for="requirements">Job requirements</label>
                <textarea class="form-textarea" id="requirements" name="requirements" placeholder="Insert job requirements (optional).">{{ old('requirements', $job_posting->requirements) }}</textarea>
            </div>

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-7"> <!-- DO LATER: add Country support -->
                <label for="city_id">City</label>
                <select class="form-dropdown" id="city_id" name="city_id">
                    <option value="" disabled>Select city</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" 
                            {{ old('city_id', $job_posting->city_id) == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-element d-flex flex-column" style="margin: 0.5rem 0rem;" id="fe-8">
                <label for="status">Status</label>
                <select class="form-dropdown" id="status" name="status" required>
                    <option value="Active" 
                        {{ old('status', $job_posting->status) === 'Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="Closed" 
                        {{ old('status', $job_posting->status) === 'Closed' ? 'selected' : '' }}>
                        Closed
                    </option>
                </select>
            </div>

            <button class="submit-button" type="submit">Update job posting</button>
            <a href="{{ route('recruiter-dashboard.index') }}" class="cancel-button">Cancel</a>
        </form>
    </div>
</section>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@endsection

<!-- IMPORTANT: deadline is retrieved from the recruiter as the number of days to elapse FROM THE CURRENT DATE;
 so we needed to convert this number into an actual date to be inserted into the database -- JavaScript! -->

<script id="js-deadline-converter">
    document.addEventListener('DOMContentLoaded', function() {
        const daysToDeadline = document.getElementById('days_to_deadline');
        let deadlineInput = document.getElementById('deadline');
        deadlineInput.value = "{{ $job_posting->deadline->format('Y-m-d') }}";
        let deadlineDate;

        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        daysToDeadline.addEventListener('input', function() {
            const days = parseInt(daysToDeadline.value);

            if (isNaN(days) || days < 3) {
                deadlineInput.value = '';
                return;
            }

            let today = new Date();
            deadlineDate = new Date(today);
            deadlineDate.setDate(today.getDate() + days);
            const formattedDeadline = formatDate(deadlineDate);
            
            deadlineInput.value = formattedDeadline;
        });
    });
 </script>