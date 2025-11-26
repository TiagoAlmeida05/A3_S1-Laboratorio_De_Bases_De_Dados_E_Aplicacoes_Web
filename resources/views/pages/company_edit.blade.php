@extends('layouts.app')

@section('title', 'Edit ' . $company->name . ' | ' . config('app.name'))

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Edit Company Profile</h1>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('companies.update', $company->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Company Logo --}}
        <div class="mb-4">
            <label class="form-label font-semibold">Company Logo</label>
            <div class="flex items-center gap-4 mb-2">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }} Logo" class="w-24 h-24 rounded-full">
                @else
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center">
                        <span class="text-gray-500 text-3xl font-bold">{{ substr($company->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <input type="file" name="logo" class="form-control" accept="image/*">
            <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
        </div>

        {{-- Company Name --}}
        <div class="mb-4">
            <label for="name" class="form-label font-semibold">Company Name *</label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $company->name) }}" 
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Website --}}
        <div class="mb-4">
            <label for="website" class="form-label font-semibold">Website</label>
            <input type="url" 
                   name="website" 
                   id="website" 
                   class="form-control @error('website') is-invalid @enderror" 
                   value="{{ old('website', $company->website) }}" 
                   placeholder="https://example.com">
            @error('website')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- About Us --}}
        <div class="mb-4">
            <label for="about_us" class="form-label font-semibold">About Us</label>
            <textarea name="about_us" 
                      id="about_us" 
                      class="form-control @error('about_us') is-invalid @enderror" 
                      rows="5">{{ old('about_us', $company->about_us) }}</textarea>
            @error('about_us')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Location --}}
        <div class="mb-4">
            <label for="city_id" class="form-label font-semibold">Location</label>
            <select name="city_id" 
                    id="city_id" 
                    class="form-select @error('city_id') is-invalid @enderror">
                <option value="">Select a city</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" 
                            {{ old('city_id', $company->city_id) == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}@if($city->country), {{ $city->country->name }}@endif
                    </option>
                @endforeach
            </select>
            @error('city_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Skills & Tags --}}
        <div class="mb-4">
            <label class="form-label font-semibold">Skills & Tags</label>
            <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                @foreach($tags as $tag)
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="tags[]" 
                               value="{{ $tag->id }}" 
                               id="tag_{{ $tag->id }}"
                               {{ in_array($tag->id, old('tags', $company->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tag_{{ $tag->id }}">
                            {{ $tag->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('companies.show', $company->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection