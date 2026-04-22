{{--
    Flash Message Component — tampilkan session flash otomatis
    Usage: <x-flash-message />
    Otomatis mendeteksi session: success, error, info, warning
--}}
@if(session()->hasAny(['success', 'error', 'info', 'warning']))
    <div class="mb-4 animate-spmb-fade-in" role="alert" aria-live="polite">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm border-0" role="alert">
            <i class="ti ti-check fs-4 me-2"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm border-0" role="alert">
            <i class="ti ti-alert-circle fs-4 me-2"></i>
            <div>
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center shadow-sm border-0" role="alert">
            <i class="ti ti-info-circle fs-4 me-2"></i>
            <div>
                {{ session('info') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm border-0" role="alert">
            <i class="ti ti-alert-triangle fs-4 me-2"></i>
            <div>
                {{ session('warning') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

    </div>
@endif