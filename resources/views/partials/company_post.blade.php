<article class="company-posting mb-3" data-id="{{ $company->id }}">
    <div class="card border-light mb-3 container d-flex justify-content-center" style="max-width: 150rem;">
        <div class="card-header">
            <div class="d-flex align-items-center">
                {{-- Company Logo --}}
                @if(isset($company->logo) && $company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" 
                         alt="{{ $company->name }} Logo" 
                         class="rounded me-3"
                         style="width: 50px; height: 50px; object-fit: cover;">
                @else
                    <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center text-white fw-bold" 
                         style="width: 50px; height: 50px; font-size: 1.5rem;">
                        {{ substr($company->name, 0, 1) }}
                    </div>
                @endif
                
                <h3 class="mb-0">
                    {{-- Link to the detailed company page --}}
                    <a href="{{ route('companies.show', $company->id) }}">
                        {{ $company->name }}
                    </a>
                </h3>
            </div>
        </div>
        <div class="card-body">
            <p class="card-text">
                {{-- Location --}}
                @if(isset($company->city) && $company->city)
                    <p><strong>Location</strong>: 
                        {{ $company->city->name }}
                        @if(isset($company->city->country) && $company->city->country)
                            , {{ $company->city->country->name }}
                        @endif
                    </p>
                @endif
                
                {{-- Website --}}
                @if(isset($company->website) && $company->website)
                    <p><strong>Website</strong>: 
                        <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                            {{ $company->website }}
                        </a>
                    </p>
                @endif
                
                {{-- About Us --}}
                @if(isset($company->about_us) && $company->about_us)
                    <p><strong>About</strong>: {{ Str::limit($company->about_us, 200) }}</p>
                @endif
                
                {{-- Tags --}}
                @if(isset($company->tags) && $company->tags->count() > 0)
                    <p><strong>Skills & Technologies</strong>: 
                        @foreach($company->tags->take(5) as $tag)
                            <span class="badge me-1">{{ $tag->name }}</span>
                        @endforeach
                        @if($company->tags->count() > 5)
                            <span class="text-muted">+{{ $company->tags->count() - 5 }} more</span>
                        @endif
                    </p>
                @endif
                
                {{-- Job Postings Count --}}
                @if(isset($company->jobPostings) && $company->jobPostings->count() > 0)
                    <p><strong>Open Positions</strong>: {{ $company->jobPostings->count() }} {{ Str::plural('job', $company->jobPostings->count()) }}</p>
                @endif
            </p>
        </div>
    </div>
</article>