@extends('layouts.admin')

@section('content')
    <h1>Edit Job Seeker: {{ $jobSeeker->user->name }}</h1>

    <div>
        <form action="{{ route('admin.job_seekers.update', $jobSeeker->registered_user_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- 1. Dados Básicos --}}
            <fieldset>
                <legend>Basic Info</legend>
                
                <div>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $jobSeeker->user->name) }}" required>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $jobSeeker->user->email) }}" required>
                </div>
            </fieldset>

            {{-- 2. Perfil --}}
            <fieldset>
                <legend>Profile Details</legend>

                <div>
                    <label for="city_id">City</label>
                    <select name="city_id" id="city_id">
                        <option value="">Select a City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $jobSeeker->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="about_me">About Me</label>
                    <textarea name="about_me" id="about_me" rows="4">{{ old('about_me', $jobSeeker->about_me) }}</textarea>
                </div>

                <div>
                    <label for="website">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $jobSeeker->website) }}">
                </div>
            </fieldset>

            {{-- 3. Ficheiros (Corrigido com DIVs para separar) --}}
            <fieldset>
                <legend>Files</legend>

                <div>
                    <label for="profile_photo">Profile Picture</label>
                    @if($jobSeeker->profile_photo)
                        <div>
                            <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" alt="Current Photo" width="100">
                        </div>
                    @endif
                    <input type="file" name="profile_photo" id="profile_photo">
                </div>

                <div>
                    <label for="cv">CV</label>
                    @if($jobSeeker->cv)
                        <div>
                            <a href="{{ asset('storage/' . $jobSeeker->cv) }}" target="_blank">View Current CV</a>
                        </div>
                    @endif
                    <input type="file" name="cv" id="cv">
                </div>
                
                <div>
                    <input type="checkbox" name="show_cv" id="show_cv" value="1" {{ $jobSeeker->show_cv ? 'checked' : '' }}>
                    <label for="show_cv" style="display: inline;">Public CV?</label>
                </div>
            </fieldset>

            {{-- Botões --}}
            <div>
                <button type="submit" class="button">Save Changes</button>
                <a href="{{ route('admin.job_seekers') }}" class="button button-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection