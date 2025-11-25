@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Perfil</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('jobseeker.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                   id="profile_photo" name="profile_photo" accept="image/*">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($jobSeeker->profile_photo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" 
                                         alt="Current profile photo" class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="about_me" class="form-label">Sobre Mim</label>
                            <textarea class="form-control @error('about_me') is-invalid @enderror" 
                                      id="about_me" name="about_me" rows="4" 
                                      placeholder="Fala um pouco sobre a tua experiência, skills e interesses...">{{ old('about_me', $jobSeeker->about_me) }}</textarea>
                            @error('about_me')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">Website Pessoal</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" 
                                   value="{{ old('website', $jobSeeker->website) }}" 
                                   placeholder="https://exemplo.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="city_id" class="form-label">Localização</label>
                            <select class="form-control @error('city_id') is-invalid @enderror" 
                                    id="city_id" name="city_id">
                                <option value="">Selecionar Cidade</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" 
                                        {{ old('city_id', $jobSeeker->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}, {{ $city->country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cv" class="form-label">Currículo (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control @error('cv') is-invalid @enderror" 
                                   id="cv" name="cv" accept=".pdf,.doc,.docx">
                            @error('cv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($jobSeeker->cv)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $jobSeeker->cv) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        Ver Currículo Atual
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="show_cv" name="show_cv" value="1"
                                   {{ old('show_cv', $jobSeeker->show_cv) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_cv">
                                Mostrar currículo publicamente
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection