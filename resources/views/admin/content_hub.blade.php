@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Content Management</h1>

    {{-- NAVIGATION TABS --}}
    <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tags-tab" data-bs-toggle="tab" data-bs-target="#tags" type="button" role="tab">
                <i class="bi bi-tags-fill me-1"></i> Tags
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cities-tab" data-bs-toggle="tab" data-bs-target="#cities" type="button" role="tab">
                <i class="bi bi-geo-alt-fill me-1"></i> Cities
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="countries-tab" data-bs-toggle="tab" data-bs-target="#countries" type="button" role="tab">
                <i class="bi bi-globe me-1"></i> Countries
            </button>
        </li>
    </ul>

    {{-- TAB CONTENT AREA --}}
    <div class="tab-content" id="contentTabsContent">

        {{-- ==================== TAB 1: TAGS ==================== --}}
        <div class="tab-pane fade show active" id="tags" role="tabpanel">
            <div class="row">
                {{-- LEFT: Add Tag Form --}}
                <div class="col-md-4 mb-4">
                    <div class="card bg-light border-0 p-3">
                        <h5 class="mb-3">Add New Tag</h5>
                        <form action="{{ route('admin.tags.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Tag Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Remote" required>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="job_posting_exclusive" id="exclusive" value="1">
                                <label class="form-check-label" for="exclusive">
                                    Exclusive to Job Postings?
                                </label>
                                <div class="form-text small">If checked, Job Seekers cannot select this tag.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Tag</button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT: Tags List --}}
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Name</th>
                                        <th>Type</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tags as $tag)
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $tag->name }}</td>
                                            <td>
                                                @if($tag->job_posting_exclusive)
                                                    <span class="badge bg-info text-dark">Job Only</span>
                                                @else
                                                    <span class="badge bg-secondary">General</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <form action="{{ route('admin.tags.delete', $tag->id) }}" method="POST" onsubmit="return confirm('Delete tag: {{ $tag->name }}?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 2: CITIES ==================== --}}
        <div class="tab-pane fade" id="cities" role="tabpanel">
            <div class="row">
                {{-- LEFT: Add City Form --}}
                <div class="col-md-4 mb-4">
                    <div class="card bg-light border-0 p-3">
                        <h5 class="mb-3">Add New City</h5>
                        <form action="{{ route('admin.cities.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">City Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Porto" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select name="country_id" class="form-select" required>
                                    <option value="">Select Country...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add City</button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT: Cities List --}}
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">City</th>
                                        <th>Country</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cities as $city)
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $city->name }}</td>
                                            <td>{{ $city->country->name ?? 'N/A' }}</td>
                                            <td class="text-end pe-3">
                                                <form action="{{ route('admin.cities.delete', $city->id) }}" method="POST" onsubmit="return confirm('Delete city: {{ $city->name }}?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 3: COUNTRIES ==================== --}}
        <div class="tab-pane fade" id="countries" role="tabpanel">
            <div class="row">
                {{-- LEFT: Add Country Form --}}
                <div class="col-md-4 mb-4">
                    <div class="card bg-light border-0 p-3">
                        <h5 class="mb-3">Add New Country</h5>
                        <form action="{{ route('admin.countries.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Country Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Portugal" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Country</button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT: Countries List --}}
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Country Name</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countries as $country)
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $country->name }}</td>
                                            <td class="text-end pe-3">
                                                <form action="{{ route('admin.countries.delete', $country->id) }}" method="POST" onsubmit="return confirm('Delete country: {{ $country->name }}? This will fail if cities exist.');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- End Tab Content --}}
</div>

{{-- Ensure Icons are loaded --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection