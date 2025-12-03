@extends('layouts.admin')

@section('page_title', 'Edit Aturan - ' . $aturan->id)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-edit me-2"></i>Edit Aturan
                    </h5>
                    <a href="{{ route('admin.kelola-aturan') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.update-aturan', $aturan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Penyakit</label>
                                <input type="text" class="form-control" 
                                       value="{{ $aturan->penyakit->kode }} - {{ $aturan->penyakit->nama }}" 
                                       readonly>
                                <div class="form-text">
                                    Penyakit tidak dapat diubah. Buat aturan baru jika perlu mengubah penyakit.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gejala</label>
                                <input type="text" class="form-control" 
                                       value="{{ $aturan->gejala->kode }} - {{ $aturan->gejala->nama }}" 
                                       readonly>
                                <div class="form-text">
                                    Gejala tidak dapat diubah. Buat aturan baru jika perlu mengubah gejala.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="cf_pakar" class="form-label">CF Pakar (Certainty Factor) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" min="0" max="1" 
                               class="form-control @error('cf_pakar') is-invalid @enderror" 
                               id="cf_pakar" name="cf_pakar" 
                               value="{{ old('cf_pakar', $aturan->cf_pakar) }}" required>
                        @error('cf_pakar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Nilai Certainty Factor antara 0.0 (tidak yakin) sampai 1.0 (sangat yakin)
                        </div>
                    </div>

                    <!-- Current Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Aturan Saat Ini</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Penyakit:</strong>
                                    <div class="mt-1">
                                        <span class="badge bg-primary">{{ $aturan->penyakit->kode }}</span>
                                        <span class="ms-2">{{ $aturan->penyakit->nama }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Gejala:</strong>
                                    <div class="mt-1">
                                        <span class="badge bg-info">{{ $aturan->gejala->kode }}</span>
                                        <span class="ms-2">{{ $aturan->gejala->nama }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <strong>CF Saat Ini:</strong>
                                    <div class="mt-1">
                                        <span class="badge bg-warning">{{ number_format($aturan->cf_pakar, 2) }}</span>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $aturan->cf_pakar >= 0.8 ? 'success' : ($aturan->cf_pakar >= 0.5 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $aturan->cf_pakar * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Changes -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Perubahan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>CF Baru:</strong>
                                    <div class="mt-1">
                                        <span id="preview-cf" class="badge bg-warning">{{ number_format($aturan->cf_pakar, 2) }}</span>
                                        <div class="progress mt-2" style="height: 10px;">
                                            <div id="preview-cf-bar" class="progress-bar bg-warning" 
                                                 style="width: {{ $aturan->cf_pakar * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Perubahan:</strong>
                                    <div class="mt-1">
                                        <span id="preview-change" class="badge bg-secondary">Tidak ada perubahan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($aturan->created_at)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Perubahan CF Pakar akan mempengaruhi hasil diagnosa sistem.
                        Aturan ini dibuat pada {{ $aturan->created_at->format('d M Y H:i') }}.
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.kelola-aturan') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Perbarui Aturan
                            </button>
                            <a href="{{ route('admin.kelola-aturan') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-1"></i>Daftar Aturan
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cfInput = document.getElementById('cf_pakar');
        const previewCf = document.getElementById('preview-cf');
        const previewCfBar = document.getElementById('preview-cf-bar');
        const previewChange = document.getElementById('preview-change');

        const originalCf = parseFloat('{{ $aturan->cf_pakar }}');

        function updatePreview() {
            const newCf = parseFloat(cfInput.value) || 0;
            
            // Update CF preview
            previewCf.textContent = newCf.toFixed(2);
            
            // Update progress bar
            const percentage = newCf * 100;
            previewCfBar.style.width = percentage + '%';
            
            // Update progress bar color based on CF value
            if (newCf >= 0.8) {
                previewCfBar.className = 'progress-bar bg-success';
            } else if (newCf >= 0.5) {
                previewCfBar.className = 'progress-bar bg-warning';
            } else {
                previewCfBar.className = 'progress-bar bg-danger';
            }

            // Update change indicator
            const change = newCf - originalCf;
            if (change === 0) {
                previewChange.textContent = 'Tidak ada perubahan';
                previewChange.className = 'badge bg-secondary';
            } else if (change > 0) {
                previewChange.textContent = `+${change.toFixed(2)} (Meningkat)`;
                previewChange.className = 'badge bg-success';
            } else {
                previewChange.textContent = `${change.toFixed(2)} (Menurun)`;
                previewChange.className = 'badge bg-danger';
            }
        }

        // Event listener
        cfInput.addEventListener('input', updatePreview);

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const newCf = parseFloat(cfInput.value);

            if (isNaN(newCf) || newCf < 0 || newCf > 1) {
                e.preventDefault();
                alert('Nilai CF Pakar harus antara 0.0 dan 1.0!');
                cfInput.focus();
                return false;
            }

            if (newCf === originalCf) {
                if (!confirm('Tidak ada perubahan yang dilakukan. Tetap lanjutkan?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Initialize preview
        updatePreview();

        // Auto-focus on CF input
        cfInput.focus();
        cfInput.select();
    });
</script>

<style>
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.progress-bar {
    border-radius: 0.375rem;
    transition: width 0.3s ease;
}

.card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection