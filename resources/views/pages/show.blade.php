@extends('layouts.app')

@section('content')
<div class="container" style="padding: 40px 0;">
    <div class="row">
        <div class="column">
            <h1 style="color: #9b4dca;">{{ $page->name }}</h1>            
            <hr>
            <div class="page-content">
                <p style="white-space: pre-line;">{{ $page->content }}</p>
            </div>            
            <br>
        </div>
    </div>
</div>
@endsection