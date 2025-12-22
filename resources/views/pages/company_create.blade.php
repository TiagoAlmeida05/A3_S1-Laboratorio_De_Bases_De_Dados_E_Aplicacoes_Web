@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Register New Company</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- WARNING SECTION --}}
                        <div class="alert alert-warning d-flex align-items-start" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                            <div>
                                <strong>Important:</strong> 
                                Upon submission, you will be <u>logged out</u>. 
                                Your account will be set to <strong>Pending</strong>. You cannot log in until an Administrator approves your company request.
                            </div>
                        </div>

                        {{-- SECTION 1: Manager Info --}}
                        <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Manager Information</h6>
                        
                        <div class="mb-3">
                            <label for="manager_email" class="form-label">Manager Email <span class="text-danger">*</span></label>
                            {{-- Pre-fill with current user's email --}}
                            <input type="email" class="form-control @error('manager_email') is-invalid @enderror" 
                                   id="manager_email" name="manager_email" 
                                   value="{{ old('manager_email', Auth::user()->email) }}" required>
                            @error('manager_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">The user with this email will become the Company Manager.</div>
                        </div>

                        {{-- SECTION 2: Company Info --}}
                        <h6 class="fw-bold text-secondary mb-3 mt-4 border-bottom pb-2">Company Details</h6>

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Website --}}
                        <div class="mb-3">
                            <label for="website" class="form-label">Website URL</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- City --}}
                        <div class="mb-3">
                            <label for="city_id" class="form-label">Location (City)</label>
                            <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                                <option value="">Select a city...</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }} ({{ $city->country->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Logo Upload --}}
                        <div class="mb-3">
                            <label for="logo" class="form-label">Company Logo</label>
                            <input class="form-control @error('logo') is-invalid @enderror" type="file" id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- About Us --}}
                        <div class="mb-3">
                            <label for="about_us" class="form-label">About Us</label>
                            <textarea class="form-control @error('about_us') is-invalid @enderror" id="about_us" name="about_us" rows="5" placeholder="Tell us about your company...">{{ old('about_us') }}</textarea>
                            @error('about_us')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tags / Industry --}}
                        <div class="mb-4">
                            <label class="form-label d-block fw-bold">Industry / Tags</label>
                            <div class="card p-3 bg-light border-0">
                                <div class="row">
                                    @foreach($tags as $tag)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                                    {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                    {{ $tag->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('tags')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Create Company & Assign Manager</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection