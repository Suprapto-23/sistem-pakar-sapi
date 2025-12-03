@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card stats-card border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-title">Total Penyakit</div>
                        <div class="card-value text-primary">{{ $stats['total_penyakit'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            Baru
                        </div>
                    </div>
                    <div class="card-icon text-primary">
                        <i class="fas fa-disease"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card stats-card border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-title">Total Gejala</div>
                        <div class="card-value text-success">{{ $stats['total_gejala'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            Terbaru
                        </div>
                    </div>
                    <div class="card-icon text-success">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card stats-card border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-title">Total Diagnosa</div>
                        <div class="card-value text-info">{{ $stats['total_diagnosa'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $stats['diagnosa_hari_ini'] }} hari ini
                        </div>
                    </div>
                    <div class="card-icon text-info">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card stats-card border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-title">Gejala Digunakan</div>
                        <div class="card-value text-warning">{{ $stats['total_gejala_used'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            Total penggunaan
                        </div>
                    </div>
                    <div class="card-icon text-warning">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-card quick-action-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.penyakit.create') }}" class="action-btn" style="background-color: var(--primary);">
                            <i class="fas fa-plus-circle"></i>
                            <span>Tambah Penyakit</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.gejala.create') }}" class="action-btn" style="background-color: var(--secondary);">
                            <i class="fas fa-plus-circle"></i>
                            <span>Tambah Gejala</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.laporan.index') }}" class="action-btn" style="background-color: var(--info);">
                            <i class="fas fa-chart-bar"></i>
                            <span>Lihat Laporan</span>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.statistik') }}" class="action-btn" style="background-color: var(--accent);">
                            <i class="fas fa-chart-pie"></i>
                            <span>Statistik Lengkap</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Recent Activity -->
<div class="row">
    <!-- Chart Section -->
    <div class="col-lg-8 mb-4">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-chart-line me-2"></i>Statistik Diagnosa 6 Bulan Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="diagnosaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="col-lg-4 mb-4">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-history me-2"></i>Diagnosa Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($diagnosaTerbaru as $diagnosa)
                    <div class="list-group-item d-flex align-items-center px-0 border-0">
                        <div class="flex-shrink-0">
                            <div class="bg-success rounded-circle p-2 text-white">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ $diagnosa->user->name }}</h6>
                            <p class="mb-0 text-muted small">{{ $diagnosa->penyakit_tertinggi }}</p>
                            <small class="text-muted">{{ $diagnosa->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">{{ number_format($diagnosa->cf_tertinggi * 100, 0) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Penyakit -->
        <div class="card dashboard-card mt-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-trophy me-2"></i>Penyakit Terbanyak</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($penyakitTerbanyak as $penyakit)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <span class="fw-medium">{{ $penyakit->penyakit_tertinggi }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $penyakit->total }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-info-circle me-2"></i>Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-muted small">Diagnosa Bulan Ini</div>
                        <div class="h5 text-primary">{{ $stats['diagnosa_bulan_ini'] }}</div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-muted small">Diagnosa Minggu Ini</div>
                        <div class="h5 text-success">{{ $stats['diagnosa_minggu_ini'] }}</div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-muted small">Total Aturan</div>
                        <div class="h5 text-info">{{ $stats['total_aturan'] }}</div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-muted small">User Baru Bulan Ini</div>
                        <div class="h5 text-warning">{{ $stats['users_baru_bulan_ini'] }}</div>
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
</script>
@endsection