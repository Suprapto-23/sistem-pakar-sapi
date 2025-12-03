@extends('layouts.admin')

@section('page_title', 'Tambah Penyakit Baru')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Penyakit Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.penyakit.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kode" class="form-label">Kode Penyakit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kode') is-invalid @enderror" 
                                       id="kode" name="kode" value="{{ old('kode') }}" 
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
                                       id="nama" name="nama" value="{{ old('nama') }}" 
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
                                  placeholder="Deskripsi lengkap tentang penyakit..." required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="solusi" class="form-label">Solusi & Penanganan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('solusi') is-invalid @enderror" 
                                  id="solusi" name="solusi" rows="4" 
                                  placeholder="Langkah-langkah penanganan dan solusi..." required>{{ old('solusi') }}</textarea>
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
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.penyakit.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Penyakit
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
        const gejalaContainer = document.getElementById('gejala-container');
        const addGejalaBtn = document.getElementById('addGejala');
        let gejalaCount = 0;

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
                row.remove();
                gejalaCount--;
            });
        }

        // Add first gejala row
        addGejalaRow();

        // Add more gejala
        addGejalaBtn.addEventListener('click', function() {
            addGejalaRow();
        });

        // Prevent duplicate gejala selection
        gejalaContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('gejala-select')) {
                const selectedValues = Array.from(document.querySelectorAll('.gejala-select'))
                    .map(select => select.value)
                    .filter(value => value !== '');
                
                const duplicates = selectedValues.filter((value, index) => 
                    selectedValues.indexOf(value) !== index
                );

                if (duplicates.length > 0) {
                    alert('Gejala tidak boleh dipilih lebih dari sekali!');
                    e.target.value = '';
                }
            }
        });
    });
</script>
@endsection