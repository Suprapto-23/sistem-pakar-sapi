@extends('layouts.admin')

@section('page_title', 'Tambah Aturan Baru')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Aturan Baru
                    </h5>
                    <a href="{{ route('admin.kelola-aturan') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.simpan-aturan') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="penyakit_id" class="form-label">Penyakit <span class="text-danger">*</span></label>
                                <select class="form-select @error('penyakit_id') is-invalid @enderror" 
                                        id="penyakit_id" name="penyakit_id" required>
                                    <option value="">Pilih Penyakit</option>
                                    @foreach($penyakits as $penyakit)
                                        <option value="{{ $penyakit->id }}" 
                                            {{ old('penyakit_id') == $penyakit->id ? 'selected' : '' }}>
                                            {{ $penyakit->kode }} - {{ $penyakit->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('penyakit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gejala_id" class="form-label">Gejala <span class="text-danger">*</span></label>
                                <select class="form-select @error('gejala_id') is-invalid @enderror" 
                                        id="gejala_id" name="gejala_id" required>
                                    <option value="">Pilih Gejala</option>
                                    @foreach($gejalas as $gejala)
                                        <option value="{{ $gejala->id }}"
                                            {{ old('gejala_id') == $gejala->id ? 'selected' : '' }}>
                                            {{ $gejala->kode }} - {{ $gejala->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gejala_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cf_pakar" class="form-label">CF Pakar (Certainty Factor) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" min="0" max="1" 
                               class="form-control @error('cf_pakar') is-invalid @enderror" 
                               id="cf_pakar" name="cf_pakar" 
                               value="{{ old('cf_pakar', '0.5') }}" required>
                        @error('cf_pakar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Nilai Certainty Factor antara 0.0 (tidak yakin) sampai 1.0 (sangat yakin).
                            Nilai default: 0.5 (cukup yakin)
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Aturan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Penyakit:</strong>
                                    <div class="mt-1">
                                        <span id="preview-penyakit" class="text-muted">Pilih penyakit</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Gejala:</strong>
                                    <div class="mt-1">
                                        <span id="preview-gejala" class="text-muted">Pilih gejala</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>CF Pakar:</strong>
                                    <div class="mt-1">
                                        <span id="preview-cf" class="badge bg-warning">0.5</span>
                                        <div class="progress mt-2" style="height: 10px;">
                                            <div id="preview-cf-bar" class="progress-bar bg-warning" style="width: 50%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Card -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Aturan</h6>
                        <ul class="mb-0">
                            <li>Aturan menghubungkan penyakit dengan gejala yang terkait</li>
                            <li>CF Pakar menunjukkan tingkat keyakinan pakar terhadap hubungan tersebut</li>
                            <li>Pastikan aturan belum ada sebelumnya untuk kombinasi penyakit dan gejala yang sama</li>
                            <li>Nilai CF yang tinggi menunjukkan keyakinan kuat bahwa gejala tersebut terkait dengan penyakit</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.kelola-aturan') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Aturan
                        </button>
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
        const penyakitSelect = document.getElementById('penyakit_id');
        const gejalaSelect = document.getElementById('gejala_id');
        const cfInput = document.getElementById('cf_pakar');
        
        const previewPenyakit = document.getElementById('preview-penyakit');
        const previewGejala = document.getElementById('preview-gejala');
        const previewCf = document.getElementById('preview-cf');
        const previewCfBar = document.getElementById('preview-cf-bar');

        function updatePreview() {
            // Update penyakit preview
            const selectedPenyakit = penyakitSelect.options[penyakitSelect.selectedIndex];
            previewPenyakit.textContent = selectedPenyakit.value ? selectedPenyakit.text : 'Pilih penyakit';
            previewPenyakit.className = selectedPenyakit.value ? '' : 'text-muted';

            // Update gejala preview
            const selectedGejala = gejalaSelect.options[gejalaSelect.selectedIndex];
            previewGejala.textContent = selectedGejala.value ? selectedGejala.text : 'Pilih gejala';
            previewGejala.className = selectedGejala.value ? '' : 'text-muted';

            // Update CF preview
            const cfValue = parseFloat(cfInput.value) || 0;
            previewCf.textContent = cfValue.toFixed(1);
            
            // Update progress bar
            const percentage = cfValue * 100;
            previewCfBar.style.width = percentage + '%';
            
            // Update progress bar color based on CF value
            if (cfValue >= 0.8) {
                previewCfBar.className = 'progress-bar bg-success';
            } else if (cfValue >= 0.5) {
                previewCfBar.className = 'progress-bar bg-warning';
            } else {
                previewCfBar.className = 'progress-bar bg-danger';
            }
        }

        // Event listeners
        penyakitSelect.addEventListener('change', updatePreview);
        gejalaSelect.addEventListener('change', updatePreview);
        cfInput.addEventListener('input', updatePreview);

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const penyakitId = penyakitSelect.value;
            const gejalaId = gejalaSelect.value;
            const cfValue = cfInput.value;

            if (!penyakitId || !gejalaId || !cfValue) {
                e.preventDefault();
                alert('Semua field harus diisi!');
                return false;
            }

            if (cfValue < 0 || cfValue > 1) {
                e.preventDefault();
                alert('Nilai CF Pakar harus antara 0.0 dan 1.0!');
                cfInput.focus();
                return false;
            }
        });

        // Initialize preview
        updatePreview();
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

.alert ul {
    padding-left: 1.5rem;
    margin-bottom: 0;
}

.alert li {
    margin-bottom: 0.25rem;
}
</style>
@endsection