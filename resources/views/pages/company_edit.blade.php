@extends('layouts.app')

@section('title', 'Edit ' . $company->name . ' | ' . config('app.name'))

@section('content')
<section class="container py-5">
    <div class="card mx-auto company-edit-card">
        <div class="card-body">
            <h2 class="h4 mb-4">Edit company profile</h2>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
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

                <div class="mb-4">
                    <label class="form-label">Company logo</label>
                    <div class="mb-3">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" class="company-logo-preview">
                        @else
                            <div class="company-logo-placeholder">
                                {{ strtoupper(substr($company->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>

                <div class="mb-4">
                    <label class="form-label">Company name</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $company->name) }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control"
                           value="{{ old('website', $company->website) }}">
                </div>

                <div class="mb-4">
                    <label class="form-label">About us</label>
                    <textarea name="about_us" rows="5" class="form-control">{{ old('about_us', $company->about_us) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Location</label>
                    <select name="city_id" class="form-select">
                        <option value="">Select a city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $company->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}@if($city->country), {{ $city->country->name }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Skills & Tags</label>
                    <div class="border rounded p-3 tags-scroll">
                        @foreach($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', $company->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $tag->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('companies.show', $company->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
