@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    {{-- Container centrado para o formulário não ficar demasiado largo --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <h1 class="h3 text-secondary mb-4">Create New User</h1>

            {{-- Erros --}}
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
                        </div>

                        <div class="row">
                            {{-- Password --}}
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            {{-- Birthday --}}
                            <div class="col-md-6 mb-3">
                                <label for="birthday" class="form-label fw-bold">Birthday</label>
                                <input type="date" name="birthday" id="birthday" class="form-control" required value="{{ old('birthday') }}">
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        {{-- Seleção de Role --}}
                        <div class="mb-3">
                            <label for="userTypeSelector" class="form-label fw-bold">User Role</label>
                            <select name="user_type" id="userTypeSelector" class="form-select" onchange="toggleRecruiterFields()">
                                <option value="job_seeker" {{ old('user_type') == 'job_seeker' ? 'selected' : '' }}>Job Seeker</option>
                                <option value="recruiter" {{ old('user_type') == 'recruiter' ? 'selected' : '' }}>Recruiter</option>
                                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </div>

                        {{-- Campos de Recruiter (Escondidos por defeito) --}}
                        <div id="recruiterFields" class="bg-light p-3 rounded mb-3 border" hidden>
                            <h5 class="h6 text-primary mb-3">Recruiter Details</h5>
                            
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">Select a department...</option>
                                @foreach($companies as $company)
                                    <optgroup label="{{ $company->name }}">
                                        @foreach($company->departments as $dept)
                                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botões --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.job_seekers') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Create User
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function toggleRecruiterFields() {
        const type = document.getElementById('userTypeSelector').value;
        const fields = document.getElementById('recruiterFields');
        
        if (type === 'recruiter') {
            fields.removeAttribute('hidden');
        } else {
            fields.setAttribute('hidden', true);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleRecruiterFields();
    });
</script>
@endsection