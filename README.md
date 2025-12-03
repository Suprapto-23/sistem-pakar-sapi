# Sistem Pakar Diagnosa Penyakit Sapi

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300">
</p>

## 🐄 Deskripsi Singkat
Aplikasi sistem pakar untuk diagnosa penyakit sapi berbasis Laravel.  
Menggunakan metode **forward chaining** untuk menganalisis gejala dan menghasilkan rekomendasi tindakan.  
Dilengkapi dashboard admin, manajemen basis pengetahuan, serta integrasi MySQL.  

## 🚀 Fitur Utama
- Manajemen gejala & penyakit  
- Basis pengetahuan rules forward chaining  
- Diagnosa otomatis  
- Dashboard admin interaktif  
- Riwayat diagnosa  
- CRUD lengkap  
- Otentikasi user  

## 🛠️ Teknologi
- Laravel 12  
- PHP 8.2  
- MySQL  
- Bootstrap / Tailwind

## 📦 Instalasi
git clone https://github.com/Suprapto-23/sistem-pakar-sapi
cd sistem-pakar-sapi
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
