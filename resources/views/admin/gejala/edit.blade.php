@extends('layouts.admin')

@section('page_title', 'Edit Gejala - ' . $gejala->nama)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-edit me-2"></i>Edit Gejala
                    </h5>
                    <a href="{{ route('admin.gejala.show', $gejala->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.gejala.update', $gejala->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Gejala <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" name="kode" value="{{ old('kode', $gejala->kode) }}" 
                                       placeholder="Contoh: G01" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Gejala <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" name="nama" value="{{ old('nama', $gejala->nama) }}" 
                                       placeholder="Contoh: Demam tinggi" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Deskripsi lengkap tentang gejala...">{{ old('deskripsi', $gejala->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informasi Penggunaan -->
                    @if($gejala->aturans_count > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Gejala ini terkait dengan <strong>{{ $gejala->aturans_count }} penyakit</strong>. 
                        Perubahan yang dilakukan akan mempengaruhi sistem diagnosa.
                    </div>
                    @endif

                    <!-- Preview Updated Data -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Perubahan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Kode:</strong> <span id="preview-kode" class="badge bg-primary">{{ $gejala->kode }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Nama:</strong> <span id="preview-nama">{{ $gejala->nama }}</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <strong>Deskripsi:</strong> 
                                    <p id="preview-deskripsi" class="mb-0">
                                        {{ $gejala->deskripsi ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.gejala.show', $gejala->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Perbarui Gejala
                            </button>
                            <a href="{{ route('admin.gejala.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-1"></i>Daftar Gejala
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
        // Real-time preview
        const kodeInput = document.getElementById('kode');
        const namaInput = document.getElementById('nama');
        const deskripsiInput = document.getElementById('deskripsi');
        
        const previewKode = document.getElementById('preview-kode');
        const previewNama = document.getElementById('preview-nama');
        const previewDeskripsi = document.getElementById('preview-deskripsi');

        function updatePreview() {
            previewKode.textContent = kodeInput.value || '{{ $gejala->kode }}';
            previewNama.textContent = namaInput.value || '{{ $gejala->nama }}';
            previewDeskripsi.textContent = deskripsiInput.value || '{{ $gejala->deskripsi ?: "-" }}';
        }

        kodeInput.addEventListener('input', updatePreview);
        namaInput.addEventListener('input', updatePreview);
        deskripsiInput.addEventListener('input', updatePreview);

        

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const kodeValue = kodeInput.value.trim();
            const namaValue = namaInput.value.trim();

            if (!kodeValue || !namaValue) {
                e.preventDefault();
                alert('Kode dan Nama Gejala harus diisi!');
                return false;
            }

            // Validate kode format (G followed by numbers)
            const kodePattern = /^G\d+$/i;
            if (!kodePattern.test(kodeValue)) {
                e.preventDefault();
                alert('Format kode tidak valid. Harus diawali dengan G dan diikuti angka (contoh: G01, G02)');
                kodeInput.focus();
                return false;
            }
        });
    });
</script>

<style>
#preview-kode, #preview-nama, #preview-deskripsi {
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endsection