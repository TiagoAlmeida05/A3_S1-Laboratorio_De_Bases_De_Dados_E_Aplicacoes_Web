@extends('layouts.admin')

@section('content')
    <h1>Company Management</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Website</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->id }}</td>
                    
                    <td>
                        {{ $company->name }}
                    </td>
                    
                    <td>
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank">Link</a>
                        @else
                            -
                        @endif
                    </td>
                    
                    <td>
                        <a href="{{ route('admin.companies.edit', $company->id) }}" class="button button-outline">
                            Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        {{ $companies->links() }}
    </div>
@endsection