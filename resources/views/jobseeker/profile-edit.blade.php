@extends('layouts.app')

@section('title', 'Edit Profile - ' . $jobSeeker->registeredUser->name . ' | ' . config('app.name'))

@section('content')
<section id="edit_job_seeker_profile">
    <h1>Edit Profile</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('jobseeker.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input name="profile_photo" type="file" class="form-control">

            @if ($jobSeeker->profile_photo)
                <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" class="mt-2 rounded shadow-sm" width="120">
            @endif
        </div>

        <div>
            <h2>About Me</h2>
            <div>
                <label>City</label>
                <select name="city_id">
                    <option value="">Select a City</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ $jobSeeker->city_id == $city->id ? 'selected' : '' }}>                                {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <textarea name="about_me" rows="4" 
                      placeholder="How do you describe yourself?">{{ old('about_me', $jobSeeker->about_me) }}</textarea>
        </div>

        <div>
            <h2>Contacts</h2>
            <div>
                <div>
                    <label>Email</label>
                    <input type="email" value="{{ $jobSeeker->registeredUser->email }}" disabled>
                </div>
                <div>
                    <label>Website</label>
                    <input type="url" name="website" 
                           value="{{ old('website', $jobSeeker->website) }}"
                           placeholder="https://exemplo.com">
                </div>

                <div>
                    <label class="form-label">CV (Curriculum Vitae)</label>
                    <input name="cv" type="file" class="form-control" accept=".pdf,.doc,.docx">
                    @if ($jobSeeker->cv)
                        <p>
                            Current CV:
                            <a href="{{ asset('storage/' . $jobSeeker->cv) }}" target="_blank">
                                View CV
                            </a>
                        </p>
                    @endif
                </div>

                <input type="hidden" name="show_cv" value="0">

                <div style="margin-top: 16px; display: flex; align-items: center;">
                    <input type="checkbox" name="show_cv" value="1"
                        {{ old('show_cv', $jobSeeker->show_cv) ? 'checked' : '' }}>
                    <label>Public CV</label>
                </div>
            </div>
        </div>

        <div>
            <h2>Experience</h2>
            <div id="experience-container">
                @foreach($jobSeeker->experienceEntries as $index => $exp)
                <div class="experience-item">
                    <div>
                        <div>
                            <label>Position</label>
                            <input type="text" name="experience[{{ $index }}][position_name]" 
                                value="{{ old('experience.' . $index . '.position_name', $exp->position_name) }}"
                                placeholder="Position name">
                        </div>
                        <div>
                            <label>Employer</label>
                            <input type="text" name="experience[{{ $index }}][employer]" 
                                value="{{ old('experience.' . $index . '.employer', $exp->employer) }}"
                                placeholder="Employer name">
                        </div>
                    </div>
                    <div>
                        <div>
                            <label>Start Date</label>
                            <input type="date" name="experience[{{ $index }}][start_date]" 
                                value="{{ old('experience.' . $index . '.start_date', $exp->start_date ? $exp->start_date->format('Y-m-d') : '') }}">
                        </div>
                        <div>
                            <label>End Date</label>
                            <input type="date" name="experience[{{ $index }}][end_date]" 
                                value="{{ old('experience.' . $index . '.end_date', $exp->end_date ? $exp->end_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <input type="hidden" name="experience[{{ $index }}][id]" value="{{ $exp->id }}">
                    <button type="button" 
                            class="remove-experience-btn">
                        Remove Experience
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" 
                    id="add-experience-btn">
                Add Experience
            </button>
        </div>

        <div>
            <h2>Education</h2>
            <div id="education-container">
                @foreach($jobSeeker->educationEntries as $index => $edu)
                <div class="education-item">
                    <div>
                        <div>
                            <label>Degree/Course</label>
                            <input type="text" name="education[{{ $index }}][name]" 
                                value="{{ old('education.' . $index . '.name', $edu->name) }}"
                                placeholder="Degree or course name">
                        </div>
                        <div>
                            <label>Institution</label>
                            <input type="text" name="education[{{ $index }}][issued_by]" 
                                value="{{ old('education.' . $index . '.issued_by', $edu->issued_by) }}"
                                placeholder="Institution name">
                        </div>
                    </div>
                    <div>
                        <div>
                            <label>Start Date</label>
                            <input type="date" name="education[{{ $index }}][start_date]" 
                                value="{{ old('education.' . $index . '.start_date', $edu->start_date ? $edu->start_date->format('Y-m-d') : '') }}">
                        </div>
                        <div>
                            <label>End Date</label>
                            <input type="date" name="education[{{ $index }}][end_date]" 
                                value="{{ old('education.' . $index . '.end_date', $edu->end_date ? $edu->end_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <input type="hidden" name="education[{{ $index }}][id]" value="{{ $edu->id }}">
                    <button type="button" 
                            class="remove-education-btn">
                        Remove Education
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" 
                    id="add-education-btn">
                Add Education
            </button>
        </div>

        <div>
            <h2>Certifications</h2>
            <div id="certifications-container">
                @foreach($jobSeeker->certifications as $index => $cert)
                <div class="certification-item">
                    <div>
                        <div>
                            <label>Certification Name</label>
                            <input type="text" name="certifications[{{ $index }}][name]" 
                                   value="{{ old('certifications.' . $index . '.name', $cert->name) }}"
                                   placeholder="Certification name">
                        </div>
                        <div>
                            <label>Issued By</label>
                            <input type="text" name="certifications[{{ $index }}][issued_by]" 
                                   value="{{ old('certifications.' . $index . '.issued_by', $cert->issued_by) }}"
                                   placeholder="Issuing organization">
                        </div>
                    </div>
                    <input type="hidden" name="certifications[{{ $index }}][id]" value="{{ $cert->id }}">
                    <button type="button" 
                            class="remove-certification-btn">
                        Remove Certification
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" 
                    id="add-certification-btn">
                Add Certification
            </button>
        </div>


        <div>
            <h2>Tags & Skills</h2>
            <div>
                @foreach($tags as $tag)
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                               {{ $jobSeeker->tags->contains($tag->id) ? 'checked' : '' }}>
                        <label>{{ $tag->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h2>Awards</h2>
            <div id="awards-container">
                @foreach($jobSeeker->awards as $index => $award)
                <div class="award-item">
                    <div>
                        <div>
                            <label>Award Name</label>
                            <input type="text" name="awards[{{ $index }}][name]" 
                                   value="{{ old('awards.' . $index . '.name', $award->name) }}"
                                   placeholder="Award name">
                            <input type="hidden" name="awards[{{ $index }}][id]" value="{{ $award->id }}">
                        </div>
                    </div>
                    <button type="button" 
                            class="remove-award-btn">
                        Remove Award
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" 
                    id="add-award-btn">
                Add Award
            </button>
        </div>

        <div>
            <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}">
                Cancel
            </a>
            <button type="submit">
                Save Changes
            </button>
        </div>
    </form>

    <hr>
    <div>
        <h2>Delete Account</h2>
        <p>Once you delete your account, your personal data will be deleted. This action is irreversible.</p>
        
        <form action="{{ route('jobseeker.profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')

            <div>
                <label for="password_delete">Confirm Password to delete:</label>
                <input type="password" id="password_delete" name="password" required>
                
                @error('password')
                    <div style="color: red;">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
            <button type="submit" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                Delete Account
            </button>
        </form>
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
    
    awardDiv.innerHTML = `
        <div>
            <div>
                <label>Award Name</label>
                <input type="text" name="awards[${awardIndex}][name]" 
                       placeholder="Award name">
            </div>
        </div>
        <button type="button" 
                class="remove-award-btn">
            Remove Award
        </button>
    `;
    
    container.appendChild(awardDiv);
    
    const removeBtn = awardDiv.querySelector('.remove-award-btn');
    removeBtn.addEventListener('click', function() {
        removeAward(this);
    });
    
    awardIndex++;
}

function removeAward(button) {
    const awardItem = button.closest('.award-item');
    if (awardItem) {
        awardItem.remove();
    }
}

function addExperience() {
    const container = document.getElementById('experience-container');
    const expDiv = document.createElement('div');
    
    expDiv.innerHTML = `
        <div>
            <div>
                <label>Position</label>
                <input type="text" name="experience[${experienceIndex}][position_name]"
                       placeholder="Position name">
            </div>
            <div>
                <label>Employer</label>
                <input type="text" name="experience[${experienceIndex}][employer]"
                       placeholder="Employer name">
            </div>
        </div>
        <div>
            <div>
                <label>Start Date</label>
                <input type="date" name="experience[${experienceIndex}][start_date]" required>
            </div>
            <div>
                <label>End Date</label>
                <input type="date" name="experience[${experienceIndex}][end_date]">
            </div>
        </div>
        <button type="button" 
                class="remove-experience-btn">
            Remove Experience
        </button>
    `;
    
    container.appendChild(expDiv);
    
    const removeBtn = expDiv.querySelector('.remove-experience-btn');
    removeBtn.addEventListener('click', function() {
        removeExperience(this);
    });
    
    experienceIndex++;
}

function removeExperience(button) {
    const expItem = button.closest('.experience-item');
    if (expItem) {
        expItem.remove();
    }
}

function addEducation() {
    const container = document.getElementById('education-container');
    const eduDiv = document.createElement('div');
    
    eduDiv.innerHTML = `
        <div>
            <div>
                <label">Degree/Course</label>
                <input type="text" name="education[${educationIndex}][name]"
                       placeholder="Degree or course name">
            </div>
            <div>
                <label>Institution</label>
                <input type="text" name="education[${educationIndex}][issued_by]"
                       placeholder="Institution name">
            </div>
        </div>
        <div>
            <div>
                <label>Start Date</label>
                <input type="date" name="education[${educationIndex}][start_date]" required>
            </div>
            <div>
                <label>End Date</label>
                <input type="date" name="education[${educationIndex}][end_date]">
            </div>
        </div>
        <button type="button" 
                class="remove-education-btn">
            Remove Education
        </button>
    `;
    
    container.appendChild(eduDiv);
    
    const removeBtn = eduDiv.querySelector('.remove-education-btn');
    removeBtn.addEventListener('click', function() {
        removeEducation(this);
    });
    
    educationIndex++;
}

function removeEducation(button) {
    const eduItem = button.closest('.education-item');
    if (eduItem) {
        eduItem.remove();
    }
}

function addCertification() {
    const container = document.getElementById('certifications-container');
    const certDiv = document.createElement('div');
    
    certDiv.innerHTML = `
        <div>
            <div>
                <label>Certification Name</label>
                <input type="text" name="certifications[${certificationIndex}][name]" 
                       placeholder="Certification name">
            </div>
            <div>
                <label>Issued By</label>
                <input type="text" name="certifications[${certificationIndex}][issued_by]"
                       placeholder="Issuing organization">
            </div>
        </div>
        <button type="button" 
                class="remove-certification-btn">
            Remove Certification
        </button>
    `;
    
    container.appendChild(certDiv);
    
    const removeBtn = certDiv.querySelector('.remove-certification-btn');
    removeBtn.addEventListener('click', function() {
        removeCertification(this);
    });
    
    certificationIndex++;
}

function removeCertification(button) {
    const certItem = button.closest('.certification-item');
    if (certItem) {
        certItem.remove();
    }
}

document.getElementById('add-award-btn').addEventListener('click', addAward);
document.getElementById('add-experience-btn').addEventListener('click', addExperience);
document.getElementById('add-education-btn').addEventListener('click', addEducation);
document.getElementById('add-certification-btn').addEventListener('click', addCertification);

document.querySelectorAll('.remove-award-btn').forEach(button => {
    button.addEventListener('click', function() {
        removeAward(this);
    });
});

document.querySelectorAll('.remove-experience-btn').forEach(button => {
    button.addEventListener('click', function() {
        removeExperience(this);
    });
});

document.querySelectorAll('.remove-education-btn').forEach(button => {
    button.addEventListener('click', function() {
        removeEducation(this);
    });
});

document.querySelectorAll('.remove-certification-btn').forEach(button => {
    button.addEventListener('click', function() {
        removeCertification(this);
    });
});
</script>
@endsection