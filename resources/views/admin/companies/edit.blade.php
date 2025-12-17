@extends('layouts.admin')

@section('content')
    <h1>Edit Company: {{ $company->name }}</h1>

    <div>
        <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Company Name --}}
            <label for="name">Company Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror

            {{-- Website --}}
            <label for="website">Website</label>
            <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
            @error('website')
                <span class="error">{{ $message }}</span>
            @enderror

            {{-- About Us --}}
            <label for="about_us">About Us</label>
            <textarea name="about_us" id="about_us" rows="5">{{ old('about_us', $company->about_us) }}</textarea>
            @error('about_us')
                <span class="error">{{ $message }}</span>
            @enderror

            {{-- Location --}}
            <label for="city_id">Location</label>
            <select name="city_id" id="city_id">
                <option value="">Select a city</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $company->city_id) == $city->id ? 'selected' : '' }}>
                        {{ $city->name }} @if($city->country) ({{ $city->country->name }}) @endif
                    </option>
                @endforeach
            </select>
            @error('city_id')
                <span class="error">{{ $message }}</span>
            @enderror

            {{-- Logo --}}
            <label>Current Logo</label>
            <div>
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" width="150">
                @else
                    <p>No logo uploaded.</p>
                @endif
            </div>

            <label for="logo">Change Logo</label>
            <input type="file" name="logo" id="logo" accept="image/*">
            @error('logo')
                <span class="error">{{ $message }}</span>
            @enderror

            {{-- Actions --}}
            <div>
                <button type="submit" class="button">Save Changes</button>
                <a href="{{ route('admin.companies') }}" class="button button-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection