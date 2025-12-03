@extends('layouts.admin')

@section('page_title', 'Kelola Penyakit')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-disease me-2"></i>Data Penyakit
                    </h5>
                    <a href="{{ route('admin.penyakit.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Penyakit
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

                @if($penyakits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="penyakitTable">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Penyakit</th>
                                <th>Jumlah Gejala</th>
                                <th>Jumlah Diagnosa</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penyakits as $penyakit)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $penyakit->kode }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($penyakit->gambar_url)
                                            <img src="{{ $penyakit->gambar_url }}" alt="{{ $penyakit->nama }}" 
                                                 class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-disease text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $penyakit->nama }}</h6>
                                            <small class="text-muted">{{ Str::limit($penyakit->deskripsi, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $penyakit->aturans_count }} gejala</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $penyakit->diagnosas_count }} diagnosa</span>
                                </td>
                                <td>{{ $penyakit->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.penyakit.show', $penyakit->id) }}" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.penyakit.edit', $penyakit->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.penyakit.destroy', $penyakit->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Hapus" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus penyakit ini?')"
                                                    {{ $penyakit->diagnosas_count > 0 ? 'disabled' : '' }}>
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
                @else
                <div class="text-center py-5">
                    <i class="fas fa-disease fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada data penyakit</h5>
                    <p class="text-muted">Mulai dengan menambahkan penyakit pertama Anda.</p>
                    <a href="{{ route('admin.penyakit.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Penyakit Pertama
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
        $('#penyakitTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            order: [[4, 'desc']]
        });
    });
</script>
@endsection