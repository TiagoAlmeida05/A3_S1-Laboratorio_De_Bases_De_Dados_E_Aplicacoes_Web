@extends('layouts.admin')

@section('content')
    <h1>Edit: {{ $page->name }}</h1>

    <div>
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="content">Page content:</label>
            <textarea name="content" id="content">{{ $page->content }}</textarea>

            <div>
                <button type="submit" class="button">Save</button>
                <a href="{{ route('admin.pages') }}" class="button">Cancel</a>
            </div>
        </form>
    </div>
@endsection