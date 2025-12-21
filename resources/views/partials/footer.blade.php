<footer style="background-color: #f4f5f6; border-top: 1px solid #e1e1e1; padding: 20px 0; margin-top: 50px; text-align: center;">
    <div class="container">
        <div>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('page.about') }}">About Us</a>
            <a href="{{ route('page.privacy') }}">Privacy Policy</a>
            <a href="{{ route('page.terms') }}">Terms of Service</a>
            <a href="{{ route('page.faq') }}">FAQs</a>
            <a href="{{ route('page.contacts') }}">Contact us!</a>
        </div>

        <p>
            {{ date('Y') }} HireUp!
        </p>
    </div>
</footer>