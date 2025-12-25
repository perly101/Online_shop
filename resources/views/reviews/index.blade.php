@extends('layouts.customer')

@section('title', 'Review Service - Absolute Essential Trading')

@push('styles')
<style>
:root{--gold1:#ffd54a;--gold2:#f0ad06}
.page-header{background:linear-gradient(135deg,var(--gold1),var(--gold2));padding:30px 20px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-top:72px}
.store-icon{width:60px;height:60px;margin:0 auto 15px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center}
.store-icon i{font-size:30px;color:#111}
.page-title{font-size:28px;font-weight:700;margin:0;color:#111}
.content{padding:20px;max-width:1000px;margin:0 auto 80px}
.review-card{background:#fff;border-radius:12px;padding:25px;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,0.08)}
.service-info{display:flex;align-items:center;gap:20px;margin-bottom:20px;justify-content:center}
.service-icon{width:100px;height:100px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.1)}
.service-icon i{font-size:48px;color:var(--gold2)}
.service-details{flex:1;text-align:center}
.service-name{font-size:24px;font-weight:700;margin-bottom:5px}
.service-description{font-size:16px;color:#666;text-align:center;max-width:600px;margin:0 auto 20px}
.rating-section{margin:20px 0}
.rating-label{font-size:18px;font-weight:600;margin-bottom:10px}
.stars{display:flex;gap:10px;font-size:32px;justify-content:center}
.star{cursor:pointer;color:#ddd;transition:color 0.2s}
.star.active{color:#f6b40b}
.star:hover{color:#f6b40b}
.comment-section{margin:20px 0}
.comment-label{font-size:16px;font-weight:600;margin-bottom:8px;color:#666}
.comment-textarea{width:100%;min-height:100px;padding:15px;border:1px solid #ddd;border-radius:8px;font-size:16px;font-family:Georgia,serif;resize:vertical;background:rgba(245,245,245,0.5);box-sizing:border-box}
.comment-textarea:focus{outline:none;border-color:var(--gold2);background:#fff}
.submit-btn{background:linear-gradient(90deg,var(--gold1),var(--gold2));border:none;padding:12px 30px;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer;color:#111;box-shadow:0 4px 10px rgba(0,0,0,0.1);width:100%}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 6px 15px rgba(0,0,0,0.15)}
.submit-btn:disabled{opacity:0.5;cursor:not-allowed}
.alert{padding:15px;border-radius:8px;margin-bottom:20px;text-align:center}
.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
.alert-error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="store-icon">
        <i class="fa-solid fa-shop"></i>
    </div>
    <h1 class="page-title">Service Review</h1>
</div>

<div class="content">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    
    <div class="review-card">
        <div class="service-info">
            <div class="service-icon">
                <i class="fa-solid fa-store"></i>
            </div>
            <div>
                <div class="service-name">Absolute Essential Trading</div>
                <div class="service-description">Help us improve our service by sharing your experience with our store, staff, and overall shopping experience.</div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('reviews.store') }}" id="review-form">
            @csrf
            <div class="rating-section">
                <div class="rating-label">Rate Our Service</div>
                <div class="stars" id="service-stars">
                    <span class="star" data-rating="1">★</span>
                    <span class="star" data-rating="2">★</span>
                    <span class="star" data-rating="3">★</span>
                    <span class="star" data-rating="4">★</span>
                    <span class="star" data-rating="5">★</span>
                </div>
                <input type="hidden" name="rating" id="rating-input" required>
                @error('rating')
                    <div style="color:#e74c3c;font-size:14px;margin-top:5px;text-align:center">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="comment-section">
                <div class="comment-label">Share Your Experience</div>
                <textarea class="comment-textarea" name="comment" id="service-comment" placeholder="Tell us about your experience with our service, staff, store environment, or anything else..."></textarea>
                @error('comment')
                    <div style="color:#e74c3c;font-size:14px;margin-top:5px">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="submit-btn">Submit Review</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starsContainer = document.getElementById('service-stars');
    const stars = starsContainer.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating-input');
    let selectedRating = 0;
    
    stars.forEach(star => {
        star.addEventListener('click', function(){
            selectedRating = parseInt(this.dataset.rating);
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
        });
        
        star.addEventListener('mouseenter', function(){
            const rating = parseInt(this.dataset.rating);
            updateStars(rating);
        });
    });
    
    starsContainer.addEventListener('mouseleave', function(){
        updateStars(selectedRating);
    });
    
    function updateStars(rating) {
        stars.forEach((s, idx) => {
            if(idx < rating){
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    }
    
    // Form validation
    document.getElementById('review-form').addEventListener('submit', function(e){
        if(selectedRating === 0){
            e.preventDefault();
            alert('Please select a star rating');
        }
    });
});
</script>
@endpush
