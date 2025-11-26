@extends('layouts.admin')

@section('content')
    <h1>Info Pages Management</h1>

    <table>
        <thead>
            <tr>
                <th>Page Name</th>
                <th>Last edited by</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
                <tr>
                    <td style="font-weight: bold; color: #9b4dca;">{{ $page->name }}</td>
                    
                    <td>
                        {{ $page->editor->name ?? 'Sistema' }}
                    </td>
                    
                    <td>
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="button" style="background-color: #9b4dca, border-color: #9b4dca; color: white;">
                            Edit Page
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection