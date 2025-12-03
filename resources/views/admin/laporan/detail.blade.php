@extends('layouts.admin')

@section('page_title', 'Detail Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Detail Laporan Diagnosa</h2>
    <div>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <a href="{{ route('admin.laporan.export-excel') }}" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="card dashboard-card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 text-primary"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.laporan.detail') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="penyakit" class="form-label">Penyakit</label>
                    <input type="text" class="form-control" id="penyakit" name="penyakit" 
                           value="{{ request('penyakit') }}" placeholder="Cari penyakit...">
                </div>
                <div class="col-md-3">
                    <label for="confidence_level" class="form-label">Tingkat Keyakinan</label>
                    <select class="form-select" id="confidence_level" name="confidence_level">
                        <option value="">Semua</option>
                        <option value="high" {{ request('confidence_level') == 'high' ? 'selected' : '' }}>Tinggi (≥80%)</option>
                        <option value="medium" {{ request('confidence_level') == 'medium' ? 'selected' : '' }}>Sedang (50-79%)</option>
                        <option value="low" {{ request('confidence_level') == 'low' ? 'selected' : '' }}>Rendah (<50%)</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.laporan.detail') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card dashboard-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Penyakit</th>
                        <th>Keyakinan</th>
                        <th>Jml Gejala</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($diagnosas as $diagnosa)
                    <tr>
                        <td>D{{ str_pad($diagnosa->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $diagnosa->user->name }}</td>
                        <td>{{ $diagnosa->penyakit_tertinggi }}</td>
                        <td>
                            <span class="badge bg-{{ $diagnosa->cf_tertinggi >= 0.8 ? 'success' : ($diagnosa->cf_tertinggi >= 0.5 ? 'warning' : 'danger') }}">
                                {{ number_format($diagnosa->cf_tertinggi * 100, 1) }}%
                            </span>
                        </td>
                        <td>{{ count(json_decode($diagnosa->gejala_terpilih, true) ?? []) }}</td>
                        <td>{{ $diagnosa->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.diagnosa.show', $diagnosa->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $diagnosas->firstItem() }} - {{ $diagnosas->lastItem() }} dari {{ $diagnosas->total() }} data
            </div>
            <div>
                {{ $diagnosas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection