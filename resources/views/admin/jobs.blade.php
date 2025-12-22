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
                    <td>
                        {{ $job->status }}
                    </td>
                    <td>
                        <div>
                            <form action="{{ route('admin.jobs.delete', $job->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete?');">
                                @csrf @method('DELETE')
                                <button class="button">Remove</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div">
        {{ $jobs->links() }}
    </div>
@endsection