@extends('layouts.admin')

@section('page_title', 'Statistik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Statistik Sistem</h2>
    <div class="btn-group">
        <button class="btn btn-outline-primary active" data-period="month">Bulanan</button>
        <button class="btn btn-outline-primary" data-period="quarter">Triwulan</button>
        <button class="btn btn-outline-primary" data-period="year">Tahunan</button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card stats-card border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-title">Total Diagnosa</div>
                        <div class="card-value text-primary">{{ $stats['total_diagnosa'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $stats['diagnosa_hari_ini'] }} hari ini
                        </div>
                    </div>
                    <div class="card-icon text-primary">
                        <i class="fas fa-stethoscope"></i>
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
                        <div class="card-title">Total User</div>
                        <div class="card-value text-success">{{ $stats['total_users'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $stats['users_baru_bulan_ini'] }} bulan ini
                        </div>
                    </div>
                    <div class="card-icon text-success">
                        <i class="fas fa-users"></i>
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
                        <div class="card-title">Penyakit Terdaftar</div>
                        <div class="card-value text-info">{{ $stats['total_penyakit'] }}</div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>
                            Aktif
                        </div>
                    </div>
                    <div class="card-icon text-info">
                        <i class="fas fa-disease"></i>
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
                            {{ $stats['unique_gejala_used'] }} unik
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

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-chart-pie me-2"></i>Distribusi Penyakit</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    @if(count($chartData['distribusi_penyakit']['data']) > 0)
                    <canvas id="pieChart"></canvas>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data untuk chart distribusi penyakit.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-chart-bar me-2"></i>Trend Diagnosa</h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    @if(count($chartData['diagnosa']['data']) > 0 && array_sum($chartData['diagnosa']['data']) > 0)
                    <canvas id="barChart"></canvas>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data untuk chart trend diagnosa.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card dashboard-card">
    <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary"><i class="fas fa-table me-2"></i>Data Statistik Detail</h5>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" onclick="exportToCSV()">
                <i class="fas fa-file-csv me-1"></i>CSV
            </button>
            <button class="btn btn-sm btn-outline-success" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-1"></i>Excel
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-1"></i>PDF
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(count($statistikDetail) > 0)
        <div class="table-responsive">
            <table id="statistikTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Penyakit</th>
                        <th>Jumlah Diagnosa</th>
                        <th>Persentase</th>
                        <th>Rata-rata Keyakinan</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistikDetail as $stat)
                    <tr>
                        <td>{{ $stat['nama_penyakit'] }}</td>
                        <td>{{ $stat['jumlah'] }}</td>
                        <td>{{ $stat['persentase'] }}%</td>
                        <td>{{ number_format($stat['rata_rata_cf'], 1) }}%</td>
                        <td>
                            <span class="badge bg-{{ $stat['trend'] > 0 ? 'success' : ($stat['trend'] < 0 ? 'danger' : 'secondary') }}">
                                <i class="fas fa-arrow-{{ $stat['trend'] > 0 ? 'up' : ($stat['trend'] < 0 ? 'down' : 'right') }} me-1"></i>
                                {{ number_format(abs($stat['trend']), 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>{{ $statistikDetail->sum('jumlah') }}</th>
                        <th>100%</th>
                        <th>{{ number_format($statistikDetail->avg('rata_rata_cf'), 1) }}%</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada data diagnosa yang tercatat.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
        @endif
    </div>
</div>

<!-- User Aktif dan Penyakit Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-trophy me-2"></i>User Paling Aktif</h5>
            </div>
            <div class="card-body">
                @if(count($usersAktif) > 0)
                <div class="list-group list-group-flush">
                    @foreach($usersAktif as $user)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <div>
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $user->diagnosas_count }} diagnosa</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Belum ada user yang melakukan diagnosa.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 text-primary"><i class="fas fa-disease me-2"></i>Penyakit Terbanyak</h5>
            </div>
            <div class="card-body">
                @if(count($penyakitTerbanyak) > 0)
                <div class="list-group list-group-flush">
                    @foreach($penyakitTerbanyak as $penyakit)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <span class="fw-medium">{{ $penyakit->penyakit_tertinggi }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $penyakit->total }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">Belum ada data penyakit.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    // Pie Chart - hanya render jika ada data
    @if(count($chartData['distribusi_penyakit']['data']) > 0)
    const pieCtx = document.getElementById('pieChart');
    if (pieCtx) {
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
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Bar Chart - hanya render jika ada data
    @if(count($chartData['diagnosa']['data']) > 0 && array_sum($chartData['diagnosa']['data']) > 0)
    const barCtx = document.getElementById('barChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['diagnosa']['labels']) !!},
                datasets: [{
                    label: 'Jumlah Diagnosa',
                    data: {!! json_encode($chartData['diagnosa']['data']) !!},
                    backgroundColor: 'rgba(44, 125, 160, 0.8)',
                    borderColor: 'rgba(44, 125, 160, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @endif

    // Initialize DataTable hanya jika ada data
    @if(count($statistikDetail) > 0)
    

    // Export functions
    function exportToCSV() {
        $('.buttons-csv').click();
    }

    function exportToExcel() {
        $('.buttons-excel').click();
    }

    function exportToPDF() {
        $('.buttons-pdf').click();
    }
    @endif

    // Period filter functionality
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            
            // Update active button
            document.querySelectorAll('[data-period]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Here you can add AJAX call to update data based on period
            console.log('Period changed to:', period);
            // You can implement AJAX call here to reload data based on selected period
        });
    });
</script>

<style>
.chart-container {
    position: relative;
}

.stats-card {
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.card-value {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.card-trend {
    font-size: 0.875rem;
}

.trend-up {
    color: #28a745;
}

.trend-down {
    color: #dc3545;
}

.card-icon {
    font-size: 2rem;
    opacity: 0.7;
}

.border-left-primary {
    border-left: 4px solid #007bff;
}

.border-left-success {
    border-left: 4px solid #28a745;
}

.border-left-info {
    border-left: 4px solid #17a2b8;
}

.border-left-warning {
    border-left: 4px solid #ffc107;
}
</style>
@endsection