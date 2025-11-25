@extends('layouts.app')

@section('title', 'Edit Profile - ' . $jobSeeker->registeredUser->name . ' | ' . config('app.name'))

@section('content')
<section id="edit_job_seeker_profile">
    <h1>Edit Profile</h1>

    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('jobseeker.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <h2>Profile Picture</h2>
            <div>
                <div>
                    <img src="" alt="Profile Picture">
                </div>
                <div>
                    <input type="file" name="profile_photo">
                </div>
            </div>
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
                    <label>Website</label>
                    <input type="url" name="website" 
                           value="{{ old('website', $jobSeeker->website) }}"
                           placeholder="https://exemplo.com">
                </div>

                @if($jobSeeker->cv)
                    <div>
                        <a href="{{ asset('storage/'.$jobSeeker->cv) }}">
                            See current CV
                        </a>
                    </div>
                @endif

                <input type="file" name="cv">

                <div style="margin-top: 16px; display: flex; align-items: center;">
                    <input type="checkbox" name="show_cv" value="1" 
                        {{ $jobSeeker->show_cv ? 'checked' : '' }}>
                    <label>Public CV</label>
                </div>
            </div>
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
            <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}">
                Cancel
            </a>
            <button type="submit">
                Save Changes
            </button>
        </div>
    </form>
</section>
@endsection