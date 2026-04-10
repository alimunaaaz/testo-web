# Aplikasi Pengaduan Sarana Sekolah

## Deskripsi
Sistem informasi berbasis web untuk mengelola pengaduan sarana dan prasarana sekolah.
Siswa dapat membuat pengaduan, dan admin dapat menindaklanjuti dengan memberikan umpan balik.

## Fitur Utama
- **Login System** (Role: Admin & Siswa)
- **Dashboard** dengan statistik real-time
- **CRUD Aspirasi** dengan upload foto
- **Filter & Search** data aspirasi
- **Umpan Balik** dari admin ke siswa
- **Timeline** riwayat tanggapan

## Tech Stack
- **Backend:** PHP 8.1/8.3
- **Database:** MySQL
- **Frontend:** Bootstrap 5
- **Server:** Laragon

## Instalasi

### 1. Requirements
- Laragon (sudah include PHP 8+ dan MySQL)
- Browser modern (Chrome/Firefox/Edge)

### 2. Langkah Instalasi
1. Clone/Download project ke folder `C:\laragon\www\`
2. Buka phpMyAdmin (`localhost/phpmyadmin`)
3. Buat database baru: `pengaduan_sekolah`
4. Import file `database.sql` (jika ada) atau jalankan query CREATE TABLE manual
5. Akses aplikasi: `localhost/pengaduan_sekolah`

### 3. Default Login
**Admin:**
- Username: `admin`
- Password: `admin123`

**Siswa:**
- Username: `siswa`
- Password: `siswa123`

## Struktur Database

### Tabel users
- id_user (PK)
- nama
- username
- password (MD5)
- role (admin/siswa)
- kelas
- created_at

### Tabel kategori
- id_kategori (PK)
- nama_kategori
- deskripsi

### Tabel aspirasi
- id_aspirasi (PK)
- id_user (FK)
- id_kategori (FK)
- judul
- deskripsi
- lokasi
- status (pending/proses/selesai/ditolak)
- prioritas (rendah/sedang/tinggi)
- tanggal_pengaduan
- foto
- created_at
- updated_at

### Tabel umpan_balik
- id_umpan_balik (PK)
- id_aspirasi (FK)
- id_admin (FK)
- isi_umpan_balik
- progres_perbaikan
- estimasi_selesai
- tanggal_umpan_balik

## Troubleshooting

### Error "Cannot modify header"
**Solusi:** Hapus spasi/output sebelum `