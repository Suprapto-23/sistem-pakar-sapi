@extends('layouts.admin')

@section('page_title', 'Kelola Gejala')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-symptoms me-2"></i>Data Gejala
                    </h5>
                    <a href="{{ route('admin.gejala.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Gejala
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

                @if($gejalas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="gejalaTable">
                        <thead>
                            <tr>
                                <th width="10%">Kode</th>
                                <th width="25%">Nama Gejala</th>
                                <th width="30%">Deskripsi</th>
                                <th width="15%">Penyakit Terkait</th>
                                <th width="10%">Tanggal Dibuat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gejalas as $gejala)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $gejala->kode }}</span>
                                </td>
                                <td>
                                    <strong>{{ $gejala->nama }}</strong>
                                </td>
                                <td>
                                    @if($gejala->deskripsi)
                                        {{ Str::limit($gejala->deskripsi, 70) }}
                                        @if(strlen($gejala->deskripsi) > 70)
                                            <a href="#" data-bs-toggle="tooltip" title="{{ $gejala->deskripsi }}">
                                                <i class="fas fa-info-circle text-primary"></i>
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $penyakitCount = $gejala->aturans_count ?? $gejala->aturans->count();
                                    @endphp
                                    @if($penyakitCount > 0)
                                        <span class="badge bg-success">{{ $penyakitCount }} penyakit</span>
                                        <br>
                                        <small class="text-muted">
                                            @foreach($gejala->aturans->take(2) as $aturan)
                                                {{ $aturan->penyakit->kode }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($penyakitCount > 2)
                                                ...
                                            @endif
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">0 penyakit</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $gejala->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.gejala.show', $gejala->id) }}" 
                                           class="btn btn-sm btn-info" title="Detail" data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.gejala.edit', $gejala->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.gejala.destroy', $gejala->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Hapus" data-bs-toggle="tooltip"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus gejala {{ $gejala->nama }}?')"
                                                    {{ $penyakitCount > 0 ? 'disabled' : '' }}>
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
                    <i class="fas fa-symptoms fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada data gejala</h5>
                    <p class="text-muted">Mulai dengan menambahkan gejala pertama Anda.</p>
                    <a href="{{ route('admin.gejala.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Gejala Pertama
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
        $('#gejalaTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            order: [[4, 'desc']],
            columnDefs: [
                { orderable: false, targets: [5] } // Non-orderable untuk kolom aksi
            ]
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection