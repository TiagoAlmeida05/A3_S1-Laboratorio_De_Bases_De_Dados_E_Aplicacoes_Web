<footer>
    <div class="d-flex flex-column align-items-start">
        <div class="d-flex flex-column ms-3">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('page.about') }}">About us</a>
            <a href="{{ route('page.privacy') }}">Privacy policy</a>
            <a href="{{ route('page.terms') }}">Terms of Service</a>
            <a href="{{ route('page.faq') }}">FAQs</a>
            <a href="{{ route('page.contacts') }}">Contact us!</a>
            @auth
                @if(!Auth::user()->isAdmin())
                    <a href="{{ route('reports.create') }}">Report an issue</a>
                @endif
            @endauth
        </div>

        <p class="align-self-center mt-3">
            {{ date('Y') }} HireUp!
        </p>
    </div>
</footer>