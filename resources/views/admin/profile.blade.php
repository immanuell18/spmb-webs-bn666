@extends('layouts.admin')

@section('title', 'Profile Admin - SPMB Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ asset('assets/admin_css/images/profile/user1.jpg') }}" alt="Admin" class="rounded-circle mb-3" width="120" height="120">
                    <h5 class="card-title">Administrator</h5>
                    <p class="text-muted">SPMB SMK Bakti Nusantara 666</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-success">Online</span>
                        <span class="badge bg-primary">Admin</span>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Statistik Admin</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Login</span>
                        <strong>127 kali</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Last Login</span>
                        <strong>{{ date('d M Y, H:i') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Member Since</span>
                        <strong>Jan 2024</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Profile</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" value="Administrator SPMB" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="admin" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="admin@smkbaktinusantara.sch.id" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" value="+62 812-3456-7890" readonly>
                            </div>
                        </div>
                        

                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="Super Administrator" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="Aktif" readonly>
                            </div>
                        </div>
                        

                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Aktivitas Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-steps aos-init aos-animate" data-aos="fade-up">
                        <div class="timeline-step">
                            <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="And here's some amazing content. It's very engaging. Right?" data-original-title="2003">
                                <div class="inner-circle"></div>
                                <p class="h6 mt-3 mb-1">{{ date('H:i') }}</p>
                                <p class="h6 text-muted mb-0 mb-lg-0">Login ke sistem admin</p>
                            </div>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="And here's some amazing content. It's very engaging. Right?" data-original-title="2004">
                                <div class="inner-circle"></div>
                                <p class="h6 mt-3 mb-1">{{ date('H:i', strtotime('-30 minutes')) }}</p>
                                <p class="h6 text-muted mb-0 mb-lg-0">Mengakses Master Data</p>
                            </div>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="And here's some amazing content. It's very engaging. Right?" data-original-title="2005">
                                <div class="inner-circle"></div>
                                <p class="h6 mt-3 mb-1">{{ date('H:i', strtotime('-1 hour')) }}</p>
                                <p class="h6 text-muted mb-0 mb-lg-0">Verifikasi berkas pendaftar</p>
                            </div>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-content" data-toggle="popover" data-trigger="hover" data-placement="top" title="" data-content="And here's some amazing content. It's very engaging. Right?" data-original-title="2010">
                                <div class="inner-circle"></div>
                                <p class="h6 mt-3 mb-1">{{ date('H:i', strtotime('-2 hours')) }}</p>
                                <p class="h6 text-muted mb-0 mb-lg-0">Export data pendaftar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.timeline-steps {
    display: flex;
    justify-content: center;
    flex-wrap: wrap
}

.timeline-steps .timeline-step {
    align-items: center;
    display: flex;
    flex-direction: column;
    position: relative;
    margin: 1rem
}

.timeline-steps .timeline-step .timeline-content {
    text-align: center;
    max-width: 200px
}

.timeline-steps .timeline-step .inner-circle {
    position: absolute;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #007bff;
    left: 50%;
    transform: translateX(-50%);
    top: 0;
    z-index: 1
}

.timeline-steps .timeline-step .inner-circle::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 2px;
    background-color: #007bff;
    left: 30px;
    top: 50%;
    transform: translateY(-50%)
}

.timeline-steps .timeline-step:last-child .inner-circle::before {
    display: none
}
</style>
@endpush