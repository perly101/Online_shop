@extends('layouts.app')

@section('title', 'Absolute Essential Trading - Staff Login')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@section('content')
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text">Absolute Essential Trading</div>
        </div>
        
        <div class="login-card">
            <h2>Staff Login</h2>
            
            @if ($errors->any())
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    @foreach ($errors->all() as $error)
                        <p style="margin: 0; color: #721c24;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Staff ID</label>
                    <input type="text" id="email" name="email" class="form-input" placeholder="Enter your staff ID (e.g., ADMIN001)" value="{{ old('email') }}" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="btn-login">Login</button>
            </form>
            
            <div class="login-footer">
                <p>© 2025 Absolute Essential Trading. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
@endsection
