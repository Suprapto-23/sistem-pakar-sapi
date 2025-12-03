@extends('layouts.admin')

@section('page_title', 'Edit Penyakit - ' . $penyakit->nama)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-edit me-2"></i>Edit Penyakit
                    </h5>
                    <a href="{{ route('admin.penyakit.show', $penyakit->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.penyakit.update', $penyakit->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Penyakit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" name="kode" value="{{ old('kode', $penyakit->kode) }}" 
                                       placeholder="Contoh: P01" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Penyakit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" name="nama" value="{{ old('nama', $penyakit->nama) }}" 
                                       placeholder="Contoh: Penyakit Mulut dan Kuku" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                  id="deskripsi" name="deskripsi" rows="4" 
                                  placeholder="Deskripsi lengkap tentang penyakit..." required>{{ old('deskripsi', $penyakit->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="solusi" class="form-label">Solusi & Penanganan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('solusi') is-invalid @enderror" 
                                  id="solusi" name="solusi" rows="4" 
                                  placeholder="Langkah-langkah penanganan dan solusi..." required>{{ old('solusi', $penyakit->solusi) }}</textarea>
                        @error('solusi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    
                    <!-- Gejala Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">Gejala yang Terkait</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addGejala">
                                <i class="fas fa-plus me-1"></i>Tambah Gejala
                            </button>
                        </div>
                        
                        <div id="gejala-container">
                            <!-- Gejala akan ditambahkan dinamis di sini -->
                            @php
                                $existingGejalas = [];
                                foreach($penyakit->aturans as $aturan) {
                                    $existingGejalas[] = [
                                        'gejala_id' => $aturan->gejala_id,
                                        'cf_pakar' => $aturan->cf_pakar
                                    ];
                                }
                            @endphp
                            
                            @if(count($existingGejalas) > 0)
                                @foreach($existingGejalas as $index => $existing)
                                    <div class="gejala-row mb-3 p-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <label class="form-label">Pilih Gejala</label>
                                                <select name="gejala_ids[]" class="form-select gejala-select" required>
                                                    <option value="">Pilih Gejala</option>
                                                    @foreach($gejalas as $gejala)
                                                        <option value="{{ $gejala->id }}" 
                                                            {{ $existing['gejala_id'] == $gejala->id ? 'selected' : '' }}>
                                                            {{ $gejala->kode }} - {{ $gejala->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">CF Pakar (0-1)</label>
                                                <input type="number" name="cf_values[]" class="form-control cf-value" 
                                                       step="0.1" min="0" max="1" value="{{ $existing['cf_pakar'] }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm remove-gejala w-100">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Default empty row -->
                                <div class="gejala-row mb-3 p-3 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label">Pilih Gejala</label>
                                            <select name="gejala_ids[]" class="form-select gejala-select" required>
                                                <option value="">Pilih Gejala</option>
                                                @foreach($gejalas as $gejala)
                                                    <option value="{{ $gejala->id }}">
                                                        {{ $gejala->kode }} - {{ $gejala->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">CF Pakar (0-1)</label>
                                            <input type="number" name="cf_values[]" class="form-control cf-value" 
                                                   step="0.1" min="0" max="1" value="0.5" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-gejala w-100">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Statistik Info -->
                    @if($penyakit->diagnosas_count > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Penyakit ini telah digunakan dalam <strong>{{ $penyakit->diagnosas_count }} diagnosa</strong>. 
                        Perubahan yang dilakukan akan mempengaruhi data historis.
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.penyakit.show', $penyakit->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Perbarui Penyakit
                            </button>
                            <a href="{{ route('admin.penyakit.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-1"></i>Daftar Penyakit
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
        const gejalaContainer = document.getElementById('gejala-container');
        const addGejalaBtn = document.getElementById('addGejala');
        let gejalaCount = {{ count($existingGejalas) > 0 ? count($existingGejalas) : 1 }};

        // Add gejala row
        function addGejalaRow(gejalaId = '', cfValue = 0.5) {
            const row = document.createElement('div');
            row.className = 'gejala-row mb-3 p-3 border rounded';
            row.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label class="form-label">Pilih Gejala</label>
                        <select name="gejala_ids[]" class="form-select gejala-select" required>
                            <option value="">Pilih Gejala</option>
                            @foreach($gejalas as $gejala)
                                <option value="{{ $gejala->id }}" ${gejalaId == {{ $gejala->id }} ? 'selected' : ''}>
                                    {{ $gejala->kode }} - {{ $gejala->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">CF Pakar (0-1)</label>
                        <input type="number" name="cf_values[]" class="form-control cf-value" 
                               step="0.1" min="0" max="1" value="${cfValue}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-gejala w-100">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            gejalaContainer.appendChild(row);
            gejalaCount++;

            // Add remove event
            row.querySelector('.remove-gejala').addEventListener('click', function() {
                if (gejalaCount > 1) {
                    row.remove();
                    gejalaCount--;
                    updateGejalaValidation();
                } else {
                    alert('Minimal harus ada satu gejala!');
                }
            });

            updateGejalaValidation();
        }

        // Add more gejala
        addGejalaBtn.addEventListener('click', function() {
            addGejalaRow();
        });

        // Remove gejala event for existing rows
        document.querySelectorAll('.remove-gejala').forEach(button => {
            button.addEventListener('click', function() {
                if (gejalaCount > 1) {
                    this.closest('.gejala-row').remove();
                    gejalaCount--;
                    updateGejalaValidation();
                } else {
                    alert('Minimal harus ada satu gejala!');
                }
            });
        });

        // Prevent duplicate gejala selection
        function updateGejalaValidation() {
            const allSelects = document.querySelectorAll('.gejala-select');
            const selectedValues = Array.from(allSelects)
                .map(select => select.value)
                .filter(value => value !== '');

            allSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const currentValue = this.value;
                    const duplicates = selectedValues.filter((value, index) => 
                        value === currentValue && value !== ''
                    );

                    if (duplicates.length > 1) {
                        alert('Gejala tidak boleh dipilih lebih dari sekali!');
                        this.value = '';
                    }
                });
            });
        }

        

        // Initialize validation
        updateGejalaValidation();
    });
</script>

<style>
.gejala-row {
    background-color: #f8f9fa;
    transition: background-color 0.2s;
}

.gejala-row:hover {
    background-color: #e9ecef;
}

.remove-gejala {
    transition: all 0.2s;
}

.remove-gejala:hover {
    transform: scale(1.05);
}
</style>
@endsection