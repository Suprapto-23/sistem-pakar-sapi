@extends('layouts.admin')

@section('page_title', 'Laporan')

@section('content')
<!-- Di resources/views/admin/laporan/index.blade.php, perbaiki link: -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Laporan Sistem</h2>
    <div>
        <a href="{{ route('admin.laporan.cetak') }}" class="btn btn-success me-2" target="_blank">
            <i class="fas fa-file-pdf me-2"></i>Export PDF
        </a>
        <a href="{{ route('admin.laporan.detail') }}" class="btn btn-primary me-2">
            <i class="fas fa-search me-2"></i>Detail Laporan
        </a>
        <a href="{{ route('admin.laporan.performa') }}" class="btn btn-info">
            <i class="fas fa-chart-line me-2"></i>Statistik Performa
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Diagnosa</h5>
                <h2 class="text-primary">{{ $totalDiagnosa }}</h2>
                <p class="text-muted">Semua Waktu</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h5 class="card-title text-success">Diagnosa Akurat</h5>
                <h2 class="text-success">{{ $diagnosaAkurat }}</h2>
                <p class="text-muted">{{ $persentaseAkurat }}% Akurasi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h5 class="card-title text-warning">Penyakit Terbanyak</h5>
                <h2 class="text-warning">{{ $penyakitTerbanyak->count ?? 0 }}</h2>
                <p class="text-muted">{{ $penyakitTerbanyak->penyakit_tertinggi ?? 'Tidak ada data' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h5 class="card-title text-info">Total User</h5>
                <h2 class="text-info">{{ $userAktif }}</h2>
                <p class="text-muted">User Terdaftar</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-chart-line me-2"></i>Trend Diagnosa 6 Bulan Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="diagnosaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-chart-pie me-2"></i>Distribusi Penyakit</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card dashboard-card">
    <div class="card-header bg-white border-bottom-0 py-3">
        <h5 class="mb-0 text-primary"><i class="fas fa-table me-2"></i>Statistik Diagnosa Per Penyakit</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Penyakit</th>
                        <th>Jumlah Diagnosa</th>
                        <th>Persentase</th>
                        <th>Rata-rata Keyakinan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistikPenyakit as $stat)
                    <tr>
                        <td>{{ $stat->nama_penyakit }}</td>
                        <td>{{ $stat->jumlah }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" 
                                         style="width: {{ number_format(($stat->jumlah / $totalDiagnosa) * 100, 1) }}%">
                                    </div>
                                </div>
                                <span>{{ number_format(($stat->jumlah / $totalDiagnosa) * 100, 1) }}%</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $stat->rata_rata_cf >= 0.7 ? 'success' : ($stat->rata_rata_cf >= 0.5 ? 'warning' : 'danger') }}">
                                {{ number_format($stat->rata_rata_cf * 100, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-info-circle me-2"></i>Ringkasan Sistem</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Diagnosa Hari Ini</div>
                        <div class="h5 text-primary">{{ \App\Models\Diagnosa::whereDate('created_at', today())->count() }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Diagnosa Minggu Ini</div>
                        <div class="h5 text-success">{{ \App\Models\Diagnosa::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Rata-rata Keyakinan</div>
                        <div class="h5 text-info">{{ number_format(\App\Models\Diagnosa::avg('cf_tertinggi') * 100, 1) }}%</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted small">User Baru Bulan Ini</div>
                        <div class="h5 text-warning">{{ \App\Models\User::where('role', 'user')->whereMonth('created_at', now()->month)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-trophy me-2"></i>Top Performers</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <span>Penyakit Paling Sering</span>
                        <span class="badge bg-primary">{{ $penyakitTerbanyak->penyakit_tertinggi ?? 'N/A' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <span>Akurasi Tertinggi</span>
                        <span class="badge bg-success">{{ $persentaseAkurat }}%</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <span>Total Gejala Digunakan</span>
                        <span class="badge bg-info">{{ \App\Models\Gejala::count() }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <span>Aturan Sistem</span>
                        <span class="badge bg-warning">{{ \App\Models\Aturan::count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Diagnosa Chart
    const diagnosaCtx = document.getElementById('diagnosaChart').getContext('2d');
    new Chart(diagnosaCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['diagnosa']['labels']) !!},
            datasets: [{
                label: 'Jumlah Diagnosa',
                data: {!! json_encode($chartData['diagnosa']['data']) !!},
                borderColor: '#2c7da0',
                backgroundColor: 'rgba(44, 125, 160, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($chartData['distribusi_penyakit']['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['distribusi_penyakit']['data']) !!},
                backgroundColor: [
                    'rgba(44, 125, 160, 0.8)',
                    'rgba(164, 198, 57, 0.8)',
                    'rgba(255, 158, 68, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 125, 0.8)',
                    'rgba(23, 162, 184, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
</script>
@endsection