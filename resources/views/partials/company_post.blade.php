<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            @if(isset($company->logo) && $company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" 
                     alt="{{ $company->name }} Logo" 
                     class="rounded-circle me-3"
                     style="width: 50px; height: 50px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" 
                     style="width: 50px; height: 50px;">
                    <span class="text-muted fw-bold fs-5">{{ substr($company->name, 0, 1) }}</span>
                </div>
            @endif
            
            <h3 class="h5 mb-0">
                <a href="{{ route('companies.show', $company->id) }}" class="text-decoration-none">{{ $company->name }}</a>
            </h3>
        </div>

        @if(isset($company->city) && $company->city)
            <p class="mb-1">
                <strong>Location</strong>: {{ $company->city->name }}@if(isset($company->city->country) && $company->city->country), {{ $company->city->country->name }}@endif
            </p>
        @endif
        
        @if(isset($company->website) && $company->website)
            <p class="mb-1">
                <strong>Website</strong>: <a href="{{ $company->website }}" target="_blank" class="text-primary">{{ $company->website }}</a>
            </p>
        @endif
        
        @if(isset($company->about_us) && $company->about_us)
            <p class="mb-1"><strong>About</strong>: {{ Str::limit($company->about_us, 200) }}</p>
        @endif
        
        @if(isset($company->tags) && $company->tags->count() > 0)
            <p class="mb-1">
                <strong>Skills & Technologies</strong>: 
                @foreach($company->tags->take(5) as $tag)
                    <span class="badge me-1">{{ $tag->name }}</span>
                @endforeach
                @if($company->tags->count() > 5)
                    <span class="text-muted">+{{ $company->tags->count() - 5 }} more</span>
                @endif
            </p>
        @endif
        
        @if(isset($company->jobPostings) && $company->jobPostings->count() > 0)
            <p class="mb-0"><strong>Open Positions</strong>: {{ $company->jobPostings->count() }} {{ Str::plural('job', $company->jobPostings->count()) }}</p>
        @endif
    </div>
</div>