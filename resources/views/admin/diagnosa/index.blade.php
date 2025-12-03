@extends('layouts.admin')

@section('page_title', 'Data Diagnosa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Data Diagnosa</h2>
    <div>
        <button class="btn btn-success me-2">
            <i class="fas fa-file-export me-2"></i>Export
        </button>
        <button class="btn btn-info">
            <i class="fas fa-filter me-2"></i>Filter
        </button>
    </div>
</div>

<div class="card dashboard-card">
    <div class="card-body">
        <table id="diagnosaTable" class="table table-striped table-bordered data-table" style="width:100%">
            <thead>
                <tr>
                    <th>ID Diagnosa</th>
                    <th>Nama Peternak</th>
                    <th>Penyakit Terdiagnosa</th>
                    <th>Tingkat Keyakinan</th>
                    <th>Gejala Dipilih</th>
                    <th>Tanggal Diagnosa</th>
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
                        <span class="badge bg-{{ $diagnosa->cf_tertinggi >= 0.8 ? 'success' : ($diagnosa->cf_tertinggi >= 0.6 ? 'warning' : 'danger') }}">
                            {{ number_format($diagnosa->cf_tertinggi * 100, 0) }}%
                        </span>
                    </td>
                    <td>
                        @php
                            $gejalaCount = count(json_decode($diagnosa->gejala_terpilih, true) ?? []);
                        @endphp
                        {{ $gejalaCount }} gejala
                    </td>
                    <td>{{ $diagnosa->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.diagnosa.show', $diagnosa->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete('diagnosa', {{ $diagnosa->id }}, 'Diagnosa {{ $diagnosa->user->name }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <form id="delete-form-{{ $diagnosa->id }}" action="{{ route('admin.diagnosa.destroy', $diagnosa->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection