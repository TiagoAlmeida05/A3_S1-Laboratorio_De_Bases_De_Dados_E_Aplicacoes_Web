@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="mb-4">
                <h1 class="h3 text-secondary">Edit Company</h1>
                <p class="text-muted small">Editing details for: <strong>{{ $company->name }}</strong></p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    
                    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Linha 1: Nome e Website --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
                                
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label fw-bold">Website</label>
                                <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                                
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Localização --}}
                        <div class="mb-3">
                            <label for="city_id" class="form-label fw-bold">Location</label>
                            <select name="city_id" id="city_id" class="form-select @error('city_id') is-invalid @enderror">
                                <option value="">Select a city</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $company->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }} @if($city->country) ({{ $city->country->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            
                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Sobre --}}
                        <div class="mb-4">
                            <label for="about_us" class="form-label fw-bold">About Us</label>
                            <textarea name="about_us" id="about_us" class="form-control @error('about_us') is-invalid @enderror" rows="5">{{ old('about_us', $company->about_us) }}</textarea>
                            
                            @error('about_us')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="text-muted my-4">

                        {{-- Secção do Logo --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Company Logo</label>
                            
                            <div class="d-flex align-items-center gap-4">
                                {{-- Visualização do Logo Atual --}}
                                <div class="bg-light border rounded p-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="img-fluid" style="max-height: 100%;">
                                    @else
                                        <span class="text-muted small">No Logo</span>
                                    @endif
                                </div>

                                {{-- Input para novo logo --}}
                                <div class="flex-grow-1">
                                    <label for="logo" class="form-label small text-secondary">Upload new logo (Optional)</label>
                                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                    
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.companies') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection