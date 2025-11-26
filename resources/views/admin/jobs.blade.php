@extends('layouts.admin')

@section('content')
    <h1>Job Postings Management</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->title }}</td>
                    <td style="color: {{ $job->status == 'Active' ? 'green' : 'orange' }}">
                        {{ $job->status }}
                    </td>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            <form action="{{ route('admin.jobs.delete', $job->id) }}" method="POST" onsubmit="return confirm('Apagar?');">
                                @csrf @method('DELETE')
                                <button class="button button-outline" style="color:red; border-color:red;">Remove</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 20px;">
        {{ $jobs->links() }}
    </div>
@endsection