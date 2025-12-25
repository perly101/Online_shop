@extends('layouts.customer')

@section('title', 'Contact Us - Absolute Essential Trading')

@push('styles')
<style>
:root{--gold1:#ffd54a;--gold2:#f0ad06}
.content{padding:40px 20px 100px;max-width:1200px;margin:80px auto 0}
.contact-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:30px;margin-top:40px}
.contact-card{background:linear-gradient(to bottom, #FFD000, #6E6E6E);border-radius:12px;padding:40px 30px;text-align:center;box-shadow:0 8px 20px rgba(0,0,0,0.1)}
.contact-icon{width:80px;height:80px;margin:0 auto 20px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center}
.contact-icon i{font-size:36px;color:#111}
.contact-title{font-size:18px;font-weight:700;margin-bottom:15px;color:#111}
.contact-text{font-size:16px;color:#333;line-height:1.6}
.contact-link{color:#111;text-decoration:none;font-weight:600}
.contact-link:hover{text-decoration:underline}
.map-container{margin-top:20px;width:100%;height:250px;border-radius:5px;overflow:hidden;border:2px solid rgba(0,0,0,0.1)}
.map-container iframe{width:100%;height:100%;border:0}

@media (max-width:900px){
  .contact-grid{grid-template-columns:1fr;gap:20px}
}
</style>
@endpush

@section('content')
<div class="content">
    <div class="contact-grid">
        <div class="contact-card">
            <div class="contact-icon">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="contact-title">Address</div>
            <div class="contact-text">
                Absolute Essentials Traders Incorporated<br>
                9FQV+GWB, Alesandra Building, Rizal Corner Amat Streets, Surigao City, Surigao
            </div>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d987.9567!2d125.4926555!3d9.789401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x330136bbb9023d57%3A0x70696460b4be6c2d!2sAbsolute%20Essentials%20Trading!5e0!3m2!1sen!2sph!4v1699123456789!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <div class="contact-card">
            <div class="contact-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="contact-title">Hours</div>
            <div class="contact-text">
                Today: 8:00 AM - 6:30 PM<br><br>
                <a href="https://www.guko.com/Phi-biz/anseluta-essentials-trading" class="contact-link" target="_blank">
                    Visit our page
                </a>
            </div>
        </div>

        <div class="contact-card">
            <div class="contact-icon">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div class="contact-title">Phone</div>
            <div class="contact-text">
                <a href="tel:09486854195" class="contact-link">09486854195</a>
            </div>
        </div>
    </div>
</div>
@endsection
