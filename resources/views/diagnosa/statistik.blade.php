@extends('layouts.app')

@section('title', 'Statistik Diagnosa - SapiSehat')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Diagnosa
                    </h4>
                </div>
                <div class="card-body">
                    
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <!-- Statistik Admin -->
                            <div class="row mb-5">
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['total_diagnosa'] ?? 0 }}</h4>
                                                    <p class="mb-0">Total Diagnosa</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-stethoscope fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h4>
                                                    <p class="mb-0">Total Users</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-users fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['total_penyakit'] ?? 0 }}</h4>
                                                    <p class="mb-0">Total Penyakit</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-disease fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-warning text-dark">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['total_gejala'] ?? 0 }}</h4>
                                                    <p class="mb-0">Total Gejala</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart Diagnosa Bulanan -->
                            <div class="row mb-5">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Diagnosa Per Bulan ({{ date('Y') }})</h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="diagnosaChart" height="100"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Penyakit Terbanyak -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">10 Penyakit Terbanyak</h5>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($stats['penyakit_terbanyak']))
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Penyakit</th>
                                                                <th>Jumlah Diagnosa</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($stats['penyakit_terbanyak'] as $penyakit => $jumlah)
                                                                <tr>
                                                                    <td>{{ $penyakit }}</td>
                                                                    <td>{{ $jumlah }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Belum ada data diagnosa.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @else
                            <!-- Statistik User Biasa -->
                            <div class="row mb-5">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['total_diagnosa'] ?? 0 }}</h4>
                                                    <p class="mb-0">Total Diagnosa</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-stethoscope fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['diagnosa_bulan_ini'] ?? 0 }}</h4>
                                                    <p class="mb-0">Bulan Ini</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0">{{ $stats['diagnosa_minggu_ini'] ?? 0 }}</h4>
                                                    <p class="mb-0">Minggu Ini</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-calendar-week fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Penyakit Terbanyak User -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Penyakit yang Paling Sering Didiagnosa</h5>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($stats['penyakit_terbanyak']))
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Penyakit</th>
                                                                <th>Jumlah Diagnosa</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($stats['penyakit_terbanyak'] as $penyakit => $jumlah)
                                                                <tr>
                                                                    <td>{{ $penyakit }}</td>
                                                                    <td>{{ $jumlah }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Belum ada data diagnosa.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Silakan login untuk melihat statistik.
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@auth
    @if(auth()->user()->role === 'admin' && !empty($stats['diagnosa_chart']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('diagnosaChart').getContext('2d');
            const chartData = @json($stats['diagnosa_chart'] ?? []);
            
            // Siapkan data untuk 12 bulan
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const data = Array(12).fill(0);
            
            // Isi data dari chartData
            Object.keys(chartData).forEach(month => {
                // Pastikan month adalah angka valid antara 1-12
                const monthIndex = parseInt(month) - 1;
                if (monthIndex >= 0 && monthIndex < 12) {
                    data[monthIndex] = chartData[month];
                }
            });
            
            // Buat chart
            const diagnosaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Jumlah Diagnosa',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
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
        });
    </script>
    @endif
@endauth
@endsection