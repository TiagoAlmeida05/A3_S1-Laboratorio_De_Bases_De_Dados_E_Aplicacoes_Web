@extends('layouts.admin')

@section('content')
    <h1>Edit: {{ $page->name }}</h1>

    <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="content">Page content:</label>
            <textarea name="content" id="content" rows="15" style="width: 100%; height: 300px; font-family: monospace;">{{ $page->content }}</textarea>

            <div style="margin-top: 20px;">
                <button type="submit" class="button">Save</button>
                <a href="{{ route('admin.pages') }}" class="button button-outline" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
@endsection