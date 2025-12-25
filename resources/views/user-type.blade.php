@extends('layouts.app')

@section('title', 'User Type Selection')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-type.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="content">
        <h1 class="question">Are you an?</h1>
        
        <div class="button-container">
            <a href="{{ route('admin.login') }}" class="user-type-btn staff-btn">
                Staff/Admin
            </a>
            <a href="{{ route('login') }}" class="user-type-btn customer-btn">
                Customer
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/user-type.js') }}"></script>
@endpush
