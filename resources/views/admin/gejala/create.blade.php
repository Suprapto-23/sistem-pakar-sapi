@extends('layouts.admin')

@section('page_title', 'Tambah Gejala Baru')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Gejala Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.gejala.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Gejala <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" name="kode" value="{{ old('kode') }}" 
                                       placeholder="Contoh: G01" required
                                       pattern="^G\d+$" title="Format: G diikuti angka (contoh: G01, G02)">
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Format: G diikuti angka. Contoh: G01, G02, G03
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Gejala <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" name="nama" value="{{ old('nama') }}" 
                                       placeholder="Contoh: Demam tinggi" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Nama gejala yang jelas dan deskriptif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Gejala</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Jelaskan detail tentang gejala ini, ciri-ciri, dan karakteristiknya...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Deskripsi membantu dalam memahami gejala dengan lebih jelas
                        </div>
                    </div>

                    

                    <!-- Preview Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Gejala</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Kode Gejala:</strong>
                                    <div class="mt-1">
                                        <span id="preview-kode" class="badge bg-primary">GXX</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Nama Gejala:</strong>
                                    <div class="mt-1">
                                        <span id="preview-nama" class="text-muted">Nama gejala akan muncul di sini</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Deskripsi:</strong>
                                    <div class="mt-1">
                                        <p id="preview-deskripsi" class="text-muted mb-0">
                                            Deskripsi gejala akan muncul di sini...
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Status:</strong>
                                    <div class="mt-1">
                                        <span class="badge bg-success" id="preview-status">Aktif</span>
                                        <small class="text-muted ms-2">Gejala baru akan tersedia untuk sistem diagnosa</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Tips Membuat Gejala yang Baik:</h6>
                        <ul class="mb-0">
                            <li>Gunakan kode yang konsisten (G01, G02, G03, dst.)</li>
                            <li>Beri nama gejala yang spesifik dan mudah dipahami</li>
                            <li>Tambahkan deskripsi yang jelas untuk membantu diagnosis</li>
                           
                            <li>Setelah dibuat, gejala dapat dikaitkan dengan penyakit melalui menu Aturan</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <div class="btn-group">
                            <button type="reset" class="btn btn-outline-danger" id="reset-btn">
                                <i class="fas fa-undo me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save me-1"></i>Simpan Gejala
                            </button>
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
        const kodeInput = document.getElementById('kode');
        const namaInput = document.getElementById('nama');
        const deskripsiInput = document.getElementById('deskripsi');
       
        const resetBtn = document.getElementById('reset-btn');
        const submitBtn = document.getElementById('submit-btn');
        
        // Preview elements
        const previewKode = document.getElementById('preview-kode');
        const previewNama = document.getElementById('preview-nama');
        const previewDeskripsi = document.getElementById('preview-deskripsi');
        
        // Image preview elements
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const imagePreview = document.getElementById('image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');

        // Update preview in real-time
        function updatePreview() {
            // Kode preview
            const kodeValue = kodeInput.value.trim();
            previewKode.textContent = kodeValue || 'GXX';
            previewKode.className = kodeValue ? 'badge bg-primary' : 'badge bg-secondary';
            
            // Nama preview
            const namaValue = namaInput.value.trim();
            previewNama.textContent = namaValue || 'Nama gejala akan muncul di sini';
            previewNama.className = namaValue ? '' : 'text-muted';
            
            // Deskripsi preview
            const deskripsiValue = deskripsiInput.value.trim();
            previewDeskripsi.textContent = deskripsiValue || 'Deskripsi gejala akan muncul di sini...';
            previewDeskripsi.className = deskripsiValue ? '' : 'text-muted';
            
            // Validate form completeness
            const isFormValid = kodeValue && namaValue;
            submitBtn.disabled = !isFormValid;
        }

        // Image preview functionality
        function handleImagePreview(event) {
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imagePlaceholder.style.display = 'none';
                    imagePreviewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                imagePreviewContainer.style.display = 'none';
                imagePreview.style.display = 'none';
                imagePlaceholder.style.display = 'block';
            }
        }

        // Auto-format kode input
        function formatKodeInput() {
            let value = kodeInput.value.trim().toUpperCase();
            
            // Remove any existing "G" and non-digit characters, then add "G" prefix
            value = value.replace(/[^0-9]/g, '');
            
            if (value) {
                kodeInput.value = 'G' + value;
            }
        }

       

        // Form submission validation
        function validateForm() {
            const kodeValue = kodeInput.value.trim();
            const namaValue = namaInput.value.trim();
            
            if (!kodeValue) {
                alert('Kode gejala harus diisi!');
                kodeInput.focus();
                return false;
            }
            
            if (!namaValue) {
                alert('Nama gejala harus diisi!');
                namaInput.focus();
                return false;
            }
            
            // Validate kode format
            const kodePattern = /^G\d+$/i;
            if (!kodePattern.test(kodeValue)) {
                alert('Format kode tidak valid. Harus diawali dengan G dan diikuti angka (contoh: G01, G02)');
                kodeInput.focus();
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            
            return true;
        }

        // Event listeners
        kodeInput.addEventListener('input', function() {
            formatKodeInput();
            updatePreview();
        });
        
        kodeInput.addEventListener('blur', formatKodeInput);
        
        namaInput.addEventListener('input', updatePreview);
        deskripsiInput.addEventListener('input', updatePreview);
       
        
        resetBtn.addEventListener('click', function(e) {
            if (!confirmReset()) {
                e.preventDefault();
            }
        });
        
        // Form submission
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });

        // Initialize preview
        updatePreview();

        // Auto-focus on kode input
        kodeInput.focus();
    });
</script>

<style>
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

#image-preview-container {
    transition: all 0.3s ease;
}

#preview-kode, #preview-nama, #preview-deskripsi {
    transition: all 0.3s ease;
}

.alert ul {
    padding-left: 1.5rem;
    margin-bottom: 0;
}

.alert li {
    margin-bottom: 0.25rem;
}

.btn:disabled {
    cursor: not-allowed;
}

/* Custom file input styling */
.form-control[type="file"] {
    padding: 0.5rem;
}

.form-control[type="file"]::file-selector-button {
    padding: 0.375rem 0.75rem;
    margin: -0.5rem -0.75rem;
    margin-inline-end: 0.75rem;
    color: #fff;
    background-color: #0d6efd;
    border: 0;
    border-radius: 0.375rem;
    transition: background-color 0.15s ease-in-out;
}

.form-control[type="file"]::file-selector-button:hover {
    background-color: #0b5ed7;
}
</style>
@endsection