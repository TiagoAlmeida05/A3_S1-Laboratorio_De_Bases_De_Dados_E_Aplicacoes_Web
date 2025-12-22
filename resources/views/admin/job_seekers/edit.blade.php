@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="mb-4">
                <h1 class="h3 text-secondary">Edit Job Seeker</h1>
                <p class="text-muted small">Editing details for: <strong>{{ $jobSeeker->user->name }}</strong></p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    
                    <form action="{{ route('admin.job_seekers.update', $jobSeeker->registered_user_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- 1. SECÇÃO: BASIC INFO --}}
                        <h5 class="text-primary mb-3">Basic Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $jobSeeker->user->name) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $jobSeeker->user->email) }}" required>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        {{-- 2. SECÇÃO: PROFILE DETAILS --}}
                        <h5 class="text-primary mb-3">Profile Details</h5>

                        <div class="mb-3">
                            <label for="city_id" class="form-label fw-bold">City</label>
                            <select name="city_id" id="city_id" class="form-select">
                                <option value="">Select a City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $jobSeeker->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="about_me" class="form-label fw-bold">About Me</label>
                            <textarea name="about_me" id="about_me" class="form-control" rows="4">{{ old('about_me', $jobSeeker->about_me) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label fw-bold">Website</label>
                            <input type="url" name="website" id="website" class="form-control" value="{{ old('website', $jobSeeker->website) }}" placeholder="https://example.com">
                        </div>

                        <hr class="my-4 text-muted">

                        {{-- 3. SECÇÃO: FILES & MEDIA --}}
                        <h5 class="text-primary mb-3">Files & Media</h5>

                        {{-- Foto de Perfil --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <div class="d-flex align-items-center gap-4">
                                <div class="bg-light border rounded p-1 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    @if($jobSeeker->profile_photo)
                                        <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" alt="Profile" class="img-fluid rounded" style="max-height: 100%;">
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        {{-- CV --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Curriculum Vitae (CV)</label>
                            
                            @if($jobSeeker->cv)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $jobSeeker->cv) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                            <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.8.8 0 0 1-.58.029m1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.545-.094.145-.096.25-.04.361.01.022.02.036.026.044a.27.27 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.06 11.1c.189-.545.404-1.115.541-1.516.292.203.626.39.926.54-.531.637-.96 1.252-1.258 1.628-.192-.22-.394-.435-.609-.652zM8 9.5a1.5 1.5 0 0 1 1.5-1.5 3 3 0 0 1 1.5 1.5"/>
                                        </svg>
                                        View Current CV
                                    </a>
                                </div>
                            @endif
                            
                            <input type="file" name="cv" id="cv" class="form-control">
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="show_cv" id="show_cv" value="1" {{ $jobSeeker->show_cv ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_cv">
                                Make CV Public?
                            </label>
                        </div>

                        {{-- Botões --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.job_seekers') }}" class="btn btn-outline-secondary">
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