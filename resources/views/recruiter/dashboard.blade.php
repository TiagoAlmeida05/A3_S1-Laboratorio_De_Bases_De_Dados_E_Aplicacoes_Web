@extends('layouts.app')

@section('title', 'My dashboard' . ' | ' . config('app.name'))

@section('content')
<section id="recruiter-dashboard">
    <h1>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} Recruiter Dashboard</h1>
</section>

@endsection