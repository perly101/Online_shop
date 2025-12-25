@extends('layouts.customer')

@push('styles')
<style>
    .profile-header{background:linear-gradient(90deg,#ffd54a,#f0ad06);padding:30px 24px;display:flex;align-items:center;gap:15px;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
    .profile-icon{width:70px;height:70px;background:rgba(0,0,0,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;color:#111;flex-shrink:0}
    .profile-info h1{margin:0 0 5px;font-size:26px;color:#111;font-weight:700;}
    .profile-info p{margin:0;font-size:15px;color:#333}
    
    .content-area{padding:24px;max-width:800px;margin:0 auto}
    .info-card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:20px;margin-bottom:20px}
    .info-card h2{margin:0 0 15px;font-size:20px;font-weight:600;color:#111;padding-bottom:10px;border-bottom:2px solid #ffd54a}
    .info-item{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #f0f0f0}
    .info-item:last-child{border-bottom:none}
    .info-label{font-size:15px;color:#666;font-weight:500}
    .info-value{font-size:15px;color:#111;font-weight:600;display:flex;align-items:center;gap:10px}
    .edit-link{color:#f0ad06;text-decoration:none;font-size:14px;padding:4px 10px;border:1px solid #f0ad06;border-radius:4px;transition:all 0.2s;cursor:pointer}
    .edit-link:hover{background:#ffd54a;border-color:#ffd54a}
    
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.30);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center;z-index:10000}
    .modal-content{background:#fff;border-radius:12px;box-shadow:0 30px 60px rgba(0,0,0,0.25);width:90%;max-width:500px;position:relative;border:2px solid rgba(240,173,6,0.3)}
    .modal-header{background:linear-gradient(90deg,#ffd54a,#f0ad06);padding:20px;border-radius:10px 10px 0 0;display:flex;align-items:center;justify-content:space-between}
    .modal-header h3{margin:0;font-size:20px;font-weight:700;color:#111}
    .modal-close{background:rgba(0,0,0,0.15);border:none;font-size:20px;cursor:pointer;width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:4px;color:#111}
    .modal-body{padding:20px}
    .form-group{margin-bottom:20px}
    .form-group label{display:block;margin-bottom:8px;font-weight:600;color:#333}
    .form-control{width:100%;padding:12px;border:1px solid #ddd;border-radius:6px;font-size:15px;box-sizing:border-box}
    .form-control:focus{outline:none;border-color:#f0ad06}
    .btn-submit{background:linear-gradient(90deg,#ffd54a,#f0ad06);border:none;padding:12px 24px;border-radius:6px;font-weight:600;cursor:pointer;width:100%;font-size:16px;color:#111}
    .btn-submit:hover{opacity:0.9}
    .error-message{color:#e74c3c;font-size:14px;margin-top:5px;display:none}
</style>
@endpush

@section('content')
<div class="profile-header">
    <div class="profile-icon"><i class="fas fa-user"></i></div>
    <div class="profile-info">
        <h1>Profile Information</h1>
        <p>Manage your personal details</p>
    </div>
</div>

<div class="content-area">
    <div class="info-card">
        <h2>Personal Information</h2>
        <div class="info-item">
            <span class="info-label">Name</span>
            <span class="info-value">
                {{ auth()->user()->name }}
                <a onclick="openEditNameModal()" class="edit-link">Edit</a>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Mobile Number</span>
            <span class="info-value">
                {{ auth()->user()->phone ?? 'Not set' }}
                <a onclick="openEditPhoneModal()" class="edit-link">Edit</a>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value">
                {{ auth()->user()->email }}
                <a onclick="openEditEmailModal()" class="edit-link">{{ auth()->user()->email ? 'Edit' : 'Add' }}</a>
            </span>
        </div>
    </div>
</div>

<!-- Edit Name Modal -->
<div id="editNameModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Name</h3>
            <button class="modal-close" onclick="closeEditNameModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editNameForm" onsubmit="submitNameForm(event)">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                    <div class="error-message" id="nameError"></div>
                </div>
                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Phone Modal -->
<div id="editPhoneModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Mobile Number</h3>
            <button class="modal-close" onclick="closeEditPhoneModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editPhoneForm" onsubmit="submitPhoneForm(event)">
                @csrf
                <div class="form-group">
                    <label for="phone">Mobile Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="{{ auth()->user()->phone }}" required>
                    <div class="error-message" id="phoneError"></div>
                </div>
                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Email Modal -->
<div id="editEmailModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>{{ auth()->user()->email ? 'Edit Email' : 'Add Email' }}</h3>
            <button class="modal-close" onclick="closeEditEmailModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editEmailForm" onsubmit="submitEmailForm(event)">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                    <div class="error-message" id="emailError"></div>
                </div>
                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditNameModal() {
    document.getElementById('editNameModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditNameModal() {
    document.getElementById('editNameModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openEditPhoneModal() {
    document.getElementById('editPhoneModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditPhoneModal() {
    document.getElementById('editPhoneModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openEditEmailModal() {
    document.getElementById('editEmailModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditEmailModal() {
    document.getElementById('editEmailModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

async function submitNameForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const errorEl = document.getElementById('nameError');
    errorEl.style.display = 'none';
    
    try {
        const response = await fetch('{{ route("profile.update-name") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            errorEl.textContent = data.message || 'Failed to update name';
            errorEl.style.display = 'block';
        }
    } catch (error) {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.style.display = 'block';
    }
}

async function submitPhoneForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const errorEl = document.getElementById('phoneError');
    errorEl.style.display = 'none';
    
    try {
        const response = await fetch('{{ route("profile.update-phone") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            errorEl.textContent = data.message || 'Failed to update phone';
            errorEl.style.display = 'block';
        }
    } catch (error) {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.style.display = 'block';
    }
}

async function submitEmailForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const errorEl = document.getElementById('emailError');
    errorEl.style.display = 'none';
    
    try {
        const response = await fetch('{{ route("profile.add-email") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            errorEl.textContent = data.message || 'Failed to update email';
            errorEl.style.display = 'block';
        }
    } catch (error) {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.style.display = 'block';
    }
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});
</script>
@endpush
