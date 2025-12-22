@extends('layouts.app')
@section('title', 'Edit profile - ' . $jobSeeker->registeredUser->name . ' | ' . config('app.name'))

@section('content')

<section class="container py-5">

    <div class="card mx-auto shadow-sm" style="max-width: 800px;">
        <div class="card-body">
            <h1 class="h4 mb-4">Edit profile</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('jobseeker.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label">Profile picture</label>
                    <input name="profile_photo" type="file" class="form-control">
                    @if ($jobSeeker->profile_photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" class="rounded shadow-sm" width="100" height="100" style="object-fit: cover;">
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">About me</h2>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">City</label>
                            <select name="city_id" class="form-select">
                                <option value="">Select a city</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ $jobSeeker->city_id == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">About me</label>
                            <textarea name="about_me" rows="4" class="form-control"
                                      placeholder="How do you describe yourself?">{{ old('about_me', $jobSeeker->about_me) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">Contacts</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $jobSeeker->registeredUser->email }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control"
                                   value="{{ old('website', $jobSeeker->website) }}"
                                   placeholder="https://example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label">CV (Curriculum Vitae)</label>
                            <input name="cv" type="file" class="form-control" accept=".pdf,.doc,.docx">
                            @if ($jobSeeker->cv)
                                <div class="mt-2">
                                    <span class="text-muted small">Current CV:</span>
                                    <a href="{{ asset('storage/' . $jobSeeker->cv) }}" target="_blank" class="text-decoration-none ms-1">
                                        <i class="bi bi-file-earmark-text"></i> View CV
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="show_cv" value="0">
                            <div class="form-check">
                                <input type="checkbox" name="show_cv" value="1" class="form-check-input" id="show_cv_check"
                                    {{ old('show_cv', $jobSeeker->show_cv) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_cv_check">Public CV (Visible to recruiters)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">Experience</h2>
                    <div id="experience-container">
                        @foreach($jobSeeker->experienceEntries as $index => $exp)
                        <div class="experience-item card mb-3 bg-light border-0">
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Position</label>
                                        <input type="text" name="experience[{{ $index }}][position_name]" class="form-control"
                                            value="{{ old('experience.' . $index . '.position_name', $exp->position_name) }}"
                                            placeholder="Position name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Employer</label>
                                        <input type="text" name="experience[{{ $index }}][employer]" class="form-control"
                                            value="{{ old('experience.' . $index . '.employer', $exp->employer) }}"
                                            placeholder="Employer name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Start date</label>
                                        <input type="date" name="experience[{{ $index }}][start_date]" class="form-control"
                                            value="{{ old('experience.' . $index . '.start_date', $exp->start_date ? $exp->start_date->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">End date</label>
                                        <input type="date" name="experience[{{ $index }}][end_date]" class="form-control"
                                            value="{{ old('experience.' . $index . '.end_date', $exp->end_date ? $exp->end_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                                <input type="hidden" name="experience[{{ $index }}][id]" value="{{ $exp->id }}">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-experience-btn">
                                        Remove Experience
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-experience-btn" class="btn btn-primary">Add experience</button>
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">Education</h2>
                    <div id="education-container">
                        @foreach($jobSeeker->educationEntries as $index => $edu)
                        <div class="education-item card mb-3 bg-light border-0">
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Degree/Course</label>
                                        <input type="text" name="education[{{ $index }}][name]" class="form-control"
                                            value="{{ old('education.' . $index . '.name', $edu->name) }}"
                                            placeholder="Degree or course name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Institution</label>
                                        <input type="text" name="education[{{ $index }}][issued_by]" class="form-control"
                                            value="{{ old('education.' . $index . '.issued_by', $edu->issued_by) }}"
                                            placeholder="Institution name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Start date</label>
                                        <input type="date" name="education[{{ $index }}][start_date]" class="form-control"
                                            value="{{ old('education.' . $index . '.start_date', $edu->start_date ? $edu->start_date->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">End date</label>
                                        <input type="date" name="education[{{ $index }}][end_date]" class="form-control"
                                            value="{{ old('education.' . $index . '.end_date', $edu->end_date ? $edu->end_date->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                                <input type="hidden" name="education[{{ $index }}][id]" value="{{ $edu->id }}">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-education-btn">
                                        Remove education
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-education-btn" class="btn btn-primary">Add education</button>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">Certifications</h2>
                    <div id="certifications-container">
                        @foreach($jobSeeker->certifications as $index => $cert)
                        <div class="certification-item card mb-3 bg-light border-0">
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Certification name</label>
                                        <input type="text" name="certifications[{{ $index }}][name]" class="form-control"
                                               value="{{ old('certifications.' . $index . '.name', $cert->name) }}"
                                               placeholder="Certification name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Issued by</label>
                                        <input type="text" name="certifications[{{ $index }}][issued_by]" class="form-control"
                                               value="{{ old('certifications.' . $index . '.issued_by', $cert->issued_by) }}"
                                               placeholder="Issuing organization">
                                    </div>
                                </div>
                                <input type="hidden" name="certifications[{{ $index }}][id]" value="{{ $cert->id }}">
                                <div class="text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-certification-btn">
                                        Remove certification
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-certification-btn" class="btn btn-primary">Add certification</button>
                    
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">Tags & Skills</h2>
                    <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                        @foreach($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                       {{ $jobSeeker->tags->contains($tag->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tag_{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3 border-bottom pb-2">Awards</h2>
                    <div id="awards-container">
                        @foreach($jobSeeker->awards as $index => $award)
                        <div class="award-item card mb-3 bg-light border-0">
                            <div class="card-body d-flex align-items-end gap-3">
                                <div class="flex-grow-1">
                                    <label class="form-label">Award Name</label>
                                    <input type="text" name="awards[{{ $index }}][name]" class="form-control"
                                           value="{{ old('awards.' . $index . '.name', $award->name) }}"
                                           placeholder="Award name">
                                    <input type="hidden" name="awards[{{ $index }}][id]" value="{{ $award->id }}">
                                </div>
                                <button type="button" class="btn btn-outline-danger remove-award-btn">
                                    Remove
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-award-btn" class="btn btn-primary">Add award</button>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mx-auto mt-5 border-danger shadow-sm w-100" style="max-width: 800px;">
        <div class="card-body">
            <h2 class="h5 text-danger mb-3">Delete account</h2>
            <p class="text-muted">Please be aware that this action is irreversible!</p>

            <form action="{{ route('jobseeker.profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')

                @if(is_null($jobSeeker->registeredUser->google_id))
                    <div class="mb-3">
                        <label for="password-delete" class="form-label">Confirm password to delete:</label>
                        <input type="password" id="password-delete" name="password" class="form-control" required>

                        @error('password')
                            <div class="text-danger mt-1 small">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                @else
                    <div class="alert alert-info mb-3">
                        <strong>Note:</strong> Since you logged in via Google, you don't need to enter a password to delete your account.
                    </div>
                @endif

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>

<script>
let awardIndex = {{ $jobSeeker->awards->count() }};
let experienceIndex = {{ $jobSeeker->experienceEntries->count() }};
let educationIndex = {{ $jobSeeker->educationEntries->count() }};
let certificationIndex = {{ $jobSeeker->certifications->count() }};

function addAward() {
    const container = document.getElementById('awards-container');
    const awardDiv = document.createElement('div');
    awardDiv.className = 'award-item card mb-3 bg-light border-0';
    awardDiv.innerHTML = `
        <div class="card-body d-flex align-items-end gap-3">
            <div class="flex-grow-1">
                <label class="form-label">Award Name</label>
                <input type="text" name="awards[${awardIndex}][name]" class="form-control" placeholder="Award name">
            </div>
            <button type="button" class="btn btn-outline-danger remove-award-btn">Remove</button>
        </div>
    `;
    container.appendChild(awardDiv);
    awardDiv.querySelector('.remove-award-btn').addEventListener('click', function() {
        removeAward(this);
    });
    awardIndex++;
}

function removeAward(button) {
    const awardItem = button.closest('.award-item');
    if (awardItem) awardItem.remove();
}

function addExperience() {
    const container = document.getElementById('experience-container');
    const expDiv = document.createElement('div');
    expDiv.className = 'experience-item card mb-3 bg-light border-0';
    expDiv.innerHTML = `
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Position</label>
                    <input type="text" name="experience[${experienceIndex}][position_name]" class="form-control" placeholder="Position name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Employer</label>
                    <input type="text" name="experience[${experienceIndex}][employer]" class="form-control" placeholder="Employer name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start date</label>
                    <input type="date" name="experience[${experienceIndex}][start_date]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End date</label>
                    <input type="date" name="experience[${experienceIndex}][end_date]" class="form-control">
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-experience-btn">Remove Experience</button>
            </div>
        </div>
    `;
    container.appendChild(expDiv);
    expDiv.querySelector('.remove-experience-btn').addEventListener('click', function() {
        removeExperience(this);
    });
    experienceIndex++;
}

function removeExperience(button) {
    const expItem = button.closest('.experience-item');
    if (expItem) expItem.remove();
}

function addEducation() {
    const container = document.getElementById('education-container');
    const eduDiv = document.createElement('div');
    eduDiv.className = 'education-item card mb-3 bg-light border-0';
    eduDiv.innerHTML = `
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Degree/Course</label>
                    <input type="text" name="education[${educationIndex}][name]" class="form-control" placeholder="Degree or course name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Institution</label>
                    <input type="text" name="education[${educationIndex}][issued_by]" class="form-control" placeholder="Institution name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start date</label>
                    <input type="date" name="education[${educationIndex}][start_date]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End date</label>
                    <input type="date" name="education[${educationIndex}][end_date]" class="form-control">
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-education-btn">Remove Education</button>
            </div>
        </div>
    `;
    container.appendChild(eduDiv);
    eduDiv.querySelector('.remove-education-btn').addEventListener('click', function() {
        removeEducation(this);
    });
    educationIndex++;
}

function removeEducation(button) {
    const eduItem = button.closest('.education-item');
    if (eduItem) eduItem.remove();
}

function addCertification() {
    const container = document.getElementById('certifications-container');
    const certDiv = document.createElement('div');
    certDiv.className = 'certification-item card mb-3 bg-light border-0';
    certDiv.innerHTML = `
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Certification Name</label>
                    <input type="text" name="certifications[${certificationIndex}][name]" class="form-control" placeholder="Certification name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Issued By</label>
                    <input type="text" name="certifications[${certificationIndex}][issued_by]" class="form-control" placeholder="Issuing organization">
                </div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-certification-btn">Remove Certification</button>
            </div>
        </div>
    `;
    container.appendChild(certDiv);
    certDiv.querySelector('.remove-certification-btn').addEventListener('click', function() {
        removeCertification(this);
    });
    certificationIndex++;
}

function removeCertification(button) {
    const certItem = button.closest('.certification-item');
    if (certItem) certItem.remove();
}

document.getElementById('add-award-btn').addEventListener('click', addAward);
document.getElementById('add-experience-btn').addEventListener('click', addExperience);
document.getElementById('add-education-btn').addEventListener('click', addEducation);
document.getElementById('add-certification-btn').addEventListener('click', addCertification);

document.querySelectorAll('.remove-award-btn').forEach(button => button.addEventListener('click', () => removeAward(button)));
document.querySelectorAll('.remove-experience-btn').forEach(button => button.addEventListener('click', () => removeExperience(button)));
document.querySelectorAll('.remove-education-btn').forEach(button => button.addEventListener('click', () => removeEducation(button)));
document.querySelectorAll('.remove-certification-btn').forEach(button => button.addEventListener('click', () => removeCertification(button)));
</script>

@endsection
