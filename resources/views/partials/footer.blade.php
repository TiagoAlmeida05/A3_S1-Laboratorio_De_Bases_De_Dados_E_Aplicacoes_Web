<footer style="background-color: #f4f5f6; border-top: 1px solid #e1e1e1; padding: 20px 0; margin-top: 50px; text-align: center;">
    <div class="container">
        <div style="margin-bottom: 10px;">
            <a href="{{ url('/') }}" style="margin: 0 10px;">Home</a>
            <a href="{{ route('page.about') }}" style="margin: 0 10px; color: #9b4dca;">About Us</a>
            <a href="{{ route('page.privacy') }}" style="margin: 0 10px; color: #9b4dca;">Privacy Policy</a>
            <a href="{{ route('page.terms') }}" style="margin: 0 10px; color: #9b4dca;">Terms of Service</a>
        </div>

        <p style="color: #606c76; font-size: 0.8em;">
            {{ date('Y') }} HireUp!
        </p>
    </div>
</footer>