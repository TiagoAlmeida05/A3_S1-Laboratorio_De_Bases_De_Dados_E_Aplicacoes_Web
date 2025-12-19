@extends('layouts.admin')

@section('content')
    <h1>Create New User</h1>

    {{-- Erros --}}
    @if($errors->any())
        <div>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        {{-- Campos Normais --}}
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required value="{{ old('name') }}">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required value="{{ old('email') }}">

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="birthday">Birthday</label>
        <input type="date" name="birthday" id="birthday" required value="{{ old('birthday') }}">

        <hr>

        {{-- Seleção de Role --}}
        <label for="userTypeSelector">User Role</label>
        <select name="user_type" id="userTypeSelector" onchange="toggleRecruiterFields()">
            <option value="job_seeker" {{ old('user_type') == 'job_seeker' ? 'selected' : '' }}>Job Seeker</option>
            <option value="recruiter" {{ old('user_type') == 'recruiter' ? 'selected' : '' }}>Recruiter</option>
        </select>

        {{-- Campos de Recruiter (Usa o atributo 'hidden' do HTML5) --}}
        <div id="recruiterFields" hidden>
            <h3>Recruiter Details</h3>
            
            <label for="department_id">Department</label>
            <select name="department_id" id="department_id">
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
        <div>
            <button type="submit" class="button">Create User</button>
            <a href="{{ route('admin.job_seekers') }}" class="button button-outline">Cancel</a>
        </div>
    </form>

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