@extends('layouts.app')

@section('title', 'Log In - Absolute Essential Trading')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<style>
body {
    font-family: 'Arial', sans-serif;
    background: #FFD700;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0;
    padding: 0;
}

.container {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@section('content')
<div class="container">
    <button class="close-btn" onclick="window.location.href='{{ route('home') }}'">×</button>
    
    <div class="content">
        <h1 class="title">Log in</h1>
        
        @if ($errors->any())
            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <p style="margin: 0; color: #856404;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form class="login-form" method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            
            <div class="form-group">
                <div class="forgot-password">
                    <a href="#" class="forgot-link">Forgot your password?</a>
                </div>
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="login-btn">Log in</button>
        </form>
        
        <div class="signup-link">
            No account? <a href="{{ route('signup') }}" class="signup-link-text">Create account</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
    } else {
        passwordInput.type = 'password';
    }
}
</script>
@endpush
