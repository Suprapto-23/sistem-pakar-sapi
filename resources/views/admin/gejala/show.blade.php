@extends('layouts.admin')

@section('page_title', 'Detail Gejala - ' . $gejala->nama)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-symptoms me-2"></i>Detail Gejala
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.gejala.edit', $gejala->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informasi Utama -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Gejala</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Kode Gejala:</div>
                                    <div class="col-sm-9">
                                        <span class="badge bg-primary fs-6">{{ $gejala->kode }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Nama Gejala:</div>
                                    <div class="col-sm-9">
                                        <h5 class="text-primary mb-0">{{ $gejala->nama }}</h5>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Deskripsi:</div>
                                    <div class="col-sm-9">
                                        @if($gejala->deskripsi)
                                            <p class="mb-0">{{ $gejala->deskripsi }}</p>
                                        @else
                                            <p class="text-muted mb-0">-</p>
                                        @endif
                                    </div>
                                </div>
                               
                            </div>
                        </div>

                        <!-- Penyakit Terkait -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-disease me-2"></i>Penyakit Terkait</h6>
                                    <span class="badge bg-primary">{{ $gejala->aturans_count ?? $relatedAturan->count() }} Penyakit</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(($gejala->aturans_count ?? $relatedAturan->count()) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Kode</th>
                                                    <th width="45%">Nama Penyakit</th>
                                                    <th width="20%">CF Pakar</th>
                                                    <th width="20%">Tingkat Keyakinan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($relatedAturan as $aturan)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-info">{{ $aturan->penyakit->kode ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                        @if(isset($aturan->penyakit))
                                                        <a href="{{ route('admin.penyakit.show', $aturan->penyakit->id) }}" 
                                                           class="text-decoration-none">
                                                            {{ $aturan->penyakit->nama }}
                                                        </a>
                                                        @else
                                                        <span class="text-muted">Penyakit tidak ditemukan</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ number_format($aturan->cf_pakar, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $percentage = $aturan->cf_pakar * 100;
                                                            $color = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                                                        @endphp
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                                <div class="progress-bar bg-{{ $color }}" 
                                                                     style="width: {{ $percentage }}%"
                                                                     title="{{ $percentage }}%">
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">{{ $percentage }}%</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-exclamation-triangle fa-2x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Gejala ini belum terkait dengan penyakit apapun.</p>
                                        <a href="{{ route('admin.aturan.index') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-link me-1"></i>Tautkan ke Penyakit
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Informasi -->
                    <div class="col-md-4">
                        <!-- Statistik -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Penyakit Terkait:</span>
                                    <span class="badge bg-primary fs-6">{{ $gejala->aturans_count ?? $relatedAturan->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Digunakan Dalam:</span>
                                    <span class="badge bg-info fs-6">{{ $usageStats['diagnosa_count'] ?? 0 }} diagnosa</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Tanggal Dibuat:</span>
                                    <small class="text-muted">{{ $gejala->created_at->format('d M Y') }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span>Terakhir Diupdate:</span>
                                    <small class="text-muted">{{ $gejala->updated_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- CF Pakar Rata-rata -->
                        @if(($gejala->aturans_count ?? $relatedAturan->count()) > 0)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-percentage me-2"></i>CF Pakar Rata-rata</h6>
                            </div>
                            <div class="card-body text-center">
                                @php
                                    $averageCF = $relatedAturan->avg('cf_pakar');
                                    $averagePercentage = $averageCF * 100;
                                    $color = $averagePercentage >= 80 ? 'success' : ($averagePercentage >= 60 ? 'warning' : 'danger');
                                @endphp
                                <h2 class="text-{{ $color }}">{{ number_format($averageCF, 2) }}</h2>
                                <div class="progress mb-2" style="height: 12px;">
                                    <div class="progress-bar bg-{{ $color }}" 
                                         style="width: {{ $averagePercentage }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($averagePercentage, 1) }}%</small>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Aksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.gejala.edit', $gejala->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-1"></i>Edit Gejala
                                    </a>
                                    <a href="{{ route('admin.aturan.index') }}" class="btn btn-info">
                                        <i class="fas fa-link me-1"></i>Kelola Aturan
                                    </a>
                                    @if(($gejala->aturans_count ?? $relatedAturan->count()) == 0)
                                    <form action="{{ route('admin.gejala.destroy', $gejala->id) }}" method="POST" class="d-grid">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus gejala {{ $gejala->nama }}?')">
                                            <i class="fas fa-trash me-1"></i>Hapus Gejala
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-danger" disabled 
                                            title="Tidak dapat dihapus karena sudah digunakan dalam aturan penyakit">
                                        <i class="fas fa-trash me-1"></i>Hapus Gejala
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simple Back Button -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-start">
                            <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Gejala
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection