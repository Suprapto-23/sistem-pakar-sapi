@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Tentang SapiSehat</h1>
                    <p class="lead text-center mb-5">
                        Sistem Pakar Diagnosa Penyakit Sapi Berbasis Web dengan Metode Certainty Factor
                    </p>
                    
                    <div class="content">
                        <h3>Visi</h3>
                        <p>Menjadi platform terdepan dalam membantu peternak sapi mendeteksi penyakit secara dini dan akurat.</p>
                        
                        <h3>Misi</h3>
                        <ul>
                            <li>Menyediakan sistem diagnosa yang mudah digunakan</li>
                            <li>Menggunakan metode Certainty Factor untuk hasil yang akurat</li>
                            <li>Memberikan solusi penanganan yang tepat</li>
                            <li>Meningkatkan kesadaran peternak akan kesehatan ternak</li>
                        </ul>
                        
                        <h3>Metode Certainty Factor</h3>
                        <p>
                            Certainty Factor (CF) adalah metode yang digunakan untuk menangani ketidakpastian 
                            dalam sistem pakar. Metode ini cocok untuk diagnosa penyakit karena dapat 
                            menangani ketidakpastian dari gejala yang diamati.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection