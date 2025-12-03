@extends('layouts.admin')

@section('page_title', 'Kelola Aturan Sistem')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-project-diagram me-2"></i>Kelola Aturan Sistem
                    </h5>
                    <a href="{{ route('admin.aturan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Aturan
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="card-title">Total Aturan</div>
                                        <div class="card-value text-primary">{{ $aturans->count() }}</div>
                                    </div>
                                    <div class="card-icon text-primary">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="card-title">Total Penyakit</div>
                                        <div class="card-value text-success">{{ $penyakits->count() }}</div>
                                    </div>
                                    <div class="card-icon text-success">
                                        <i class="fas fa-disease"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="card-title">Total Gejala</div>
                                        <div class="card-value text-info">{{ $gejalas->count() }}</div>
                                    </div>
                                    <div class="card-icon text-info">
                                        <i class="fas fa-symptoms"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="card-title">CF Rata-rata</div>
                                        <div class="card-value text-warning">
                                            @php
                                                $averageCF = $aturans->avg('cf_pakar');
                                                echo $averageCF ? number_format($averageCF, 2) : '0.00';
                                            @endphp
                                        </div>
                                    </div>
                                    <div class="card-icon text-warning">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($aturans->count() > 0)
                    <!-- Grouped by Penyakit -->
                    @php
                        $groupedAturans = $aturans->groupBy('penyakit_id');
                    @endphp
                    
                    @foreach($groupedAturans as $penyakitId => $aturanGroup)
                        @php
                            $penyakit = $aturanGroup->first()->penyakit;
                            $averageCF = $aturanGroup->avg('cf_pakar');
                        @endphp
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fas fa-disease me-2 text-primary"></i>
                                            {{ $penyakit->kode }} - {{ $penyakit->nama }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $aturanGroup->count() }} gejala terkait | 
                                            CF Rata-rata: <span class="badge bg-warning">{{ number_format($averageCF, 2) }}</span>
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.penyakit.show', $penyakit->id) }}" 
                                           class="btn btn-sm btn-info" title="Lihat Penyakit">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="15%">Kode Gejala</th>
                                                <th width="40%">Nama Gejala</th>
                                                <th width="20%">CF Pakar</th>
                                                <th width="20%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($aturanGroup as $index => $aturan)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $aturan->gejala->kode }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $aturan->gejala->nama }}</span>
                                                        @if($aturan->gejala->deskripsi)
                                                            <i class="fas fa-info-circle text-primary ms-2" 
                                                               data-bs-toggle="tooltip" 
                                                               title="{{ $aturan->gejala->deskripsi }}"></i>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-warning me-2">{{ number_format($aturan->cf_pakar, 2) }}</span>
                                                        <div class="progress flex-grow-1" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $aturan->cf_pakar >= 0.8 ? 'success' : ($aturan->cf_pakar >= 0.5 ? 'warning' : 'danger') }}" 
                                                                 style="width: {{ $aturan->cf_pakar * 100 }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.aturan.edit', $aturan->id) }}" 
                                                           class="btn btn-sm btn-warning" title="Edit CF">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.hapus-aturan', $aturan->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    title="Hapus"
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus aturan ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                <div class="text-center py-5">
                    <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada aturan yang dibuat</h5>
                    <p class="text-muted">Mulai dengan membuat aturan pertama untuk menghubungkan penyakit dengan gejala.</p>
                    <a href="{{ route('admin.aturan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Aturan Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Progress bar animation
        $('.progress-bar').each(function() {
            const width = $(this).attr('style').split('width:')[1].split('%')[0];
            $(this).css('width', '0%').animate({
                width: width + '%'
            }, 1000);
        });
    });
</script>

<style>
.stats-card {
    border-left: 4px solid !important;
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.card-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.card-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.card-icon {
    font-size: 2rem;
    opacity: 0.7;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.progress-bar {
    border-radius: 0.375rem;
    transition: width 1s ease-in-out;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endsection