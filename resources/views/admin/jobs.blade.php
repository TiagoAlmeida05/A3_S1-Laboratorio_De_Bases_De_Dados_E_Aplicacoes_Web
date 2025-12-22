@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-secondary">Job Postings Management</h1>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-4">ID</th> {{-- py-3 aumenta a altura do cabeçalho --}}
                            <th class="py-3">Title</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobs as $job)
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#{{ $job->id }}</td>
                                
                                <td class="fw-medium">
                                    {{ $job->title }}
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $job->status }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.jobs.delete', $job->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete?');" style="display: inline-block;">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $jobs->links() }}
        </div>
    </div>
@endsection