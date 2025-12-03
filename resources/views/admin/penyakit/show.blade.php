@extends('layouts.admin')

@section('page_title', 'Detail Penyakit - ' . $penyakit->nama)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-disease me-2"></i>Detail Penyakit
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.penyakit.edit', $penyakit->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.penyakit.index') }}" class="btn btn-secondary btn-sm">
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
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Penyakit</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Kode Penyakit:</div>
                                    <div class="col-sm-9">
                                        <span class="badge bg-primary fs-6">{{ $penyakit->kode }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Nama Penyakit:</div>
                                    <div class="col-sm-9">
                                        <h5 class="text-primary mb-0">{{ $penyakit->nama }}</h5>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3 fw-bold">Deskripsi:</div>
                                    <div class="col-sm-9">
                                        <p class="mb-0">{{ $penyakit->deskripsi }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3 fw-bold">Solusi & Penanganan:</div>
                                    <div class="col-sm-9">
                                        <div class="bg-light p-3 rounded">
                                            {!! nl2br(e($penyakit->solusi)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gejala Terkait -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-symptoms me-2"></i>Gejala Terkait</h6>
                                    <span class="badge bg-primary">{{ $penyakit->gejala_count }} Gejala</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($penyakit->gejala_count > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="10%">Kode</th>
                                                    <th width="60%">Nama Gejala</th>
                                                    <th width="20%">CF Pakar</th>
                                                    <th width="10%">Bobot</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($penyakit->aturans as $aturan)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-info">{{ $aturan->gejala->kode }}</span>
                                                    </td>
                                                    <td>{{ $aturan->gejala->nama }}</td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ number_format($aturan->cf_pakar, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $percentage = $aturan->cf_pakar * 100;
                                                            $color = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                                                        @endphp
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $color }}" 
                                                                 style="width: {{ $percentage }}%"
                                                                 title="{{ $percentage }}%">
                                                            </div>
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
                                        <p class="text-muted mb-0">Belum ada gejala yang terkait dengan penyakit ini.</p>
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
                                    <span>Total Diagnosa:</span>
                                    <span class="badge bg-primary fs-6">{{ $penyakit->diagnosas_count }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Gejala Terkait:</span>
                                    <span class="badge bg-info fs-6">{{ $penyakit->gejala_count }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Tanggal Dibuat:</span>
                                    <small class="text-muted">{{ $penyakit->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Riwayat Diagnosa Terbaru -->
                        @if($penyakit->diagnosas_count > 0)
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Diagnosa Terbaru</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @foreach($penyakit->diagnosas->take(5) as $diagnosa)
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $diagnosa->user->name ?? 'User' }}</h6>
                                                <small class="text-muted">
                                                    {{ $diagnosa->created_at->format('d M Y H:i') }}
                                                </small>
                                            </div>
                                            <span class="badge bg-{{ $diagnosa->confidence_color }}">
                                                {{ number_format($diagnosa->cf_tertinggi * 100, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($penyakit->diagnosas_count > 5)
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            dan {{ $penyakit->diagnosas_count - 5 }} diagnosa lainnya...
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.penyakit.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                                </a>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.penyakit.edit', $penyakit->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>Edit Penyakit
                                </a>
                                @if($penyakit->diagnosas_count == 0)
                                <form action="{{ route('admin.penyakit.destroy', $penyakit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus penyakit ini?')">
                                        <i class="fas fa-trash me-1"></i>Hapus
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-danger" disabled title="Tidak dapat dihapus karena sudah digunakan dalam diagnosa">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection