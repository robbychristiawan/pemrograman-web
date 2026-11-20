# Sistem Informasi Manajemen Rumah Sakit (SIMRS) - Panel Admin Pluto

Proyek website admin ini dibuat menggunakan template HTML Bootstrap **Pluto 1.0.0** dan terintegrasi dengan kode backend PHP MySQL dari **modul3-CRUD**.

---

## 🚀 Cara Menjalankan Website

1. **Pastikan XAMPP Aktif:**
   - Buka **XAMPP Control Panel**.
   - Pastikan modul **Apache** dan **MySQL** dalam status **Running**.
   - *Catatan: Apache Anda berjalan pada port **8080**.*

2. **Buka di Browser:**
   - Buka browser (Chrome, Edge, Firefox, dll).
   - Masukkan URL berikut:
     ```
     http://localhost:8080/pemro_web/admin_web/
     ```

3. **Akun Login Default:**
   - **Username:** `admin`
   - **Password:** `admin123`

---

## 💻 Cara Membuka Proyek di Visual Studio Code (VS Code)

1. Buka aplikasi **Visual Studio Code**.
2. Klik menu **File** > **Open Folder...** (atau tekan `Ctrl + K, Ctrl + O`).
3. Arahkan dan pilih folder:
   ```
   C:\xampp\htdocs\pemro_web\admin_web
   ```
4. Klik **Select Folder**. Anda sekarang dapat melihat dan memodifikasi semua file proyek langsung di VS Code!

---

## 📁 Struktur File & Direktori Proyek

```
admin_web/
├── config.php             # Konfigurasi koneksi database 'simrs' & session check
├── login.php              # Halaman login dengan desain Pluto & validasi akun
├── logout.php             # Script proses keluar & destroy session
├── index.php              # Dashboard statistik (kartu counter, grafik Chart.js, 5 data terbaru)
│
├── alat.php               # CRUD: Tampilan tabel data alat/produk + fitur pencarian
├── tambah_alat.php        # CRUD: Form tambah data alat baru
├── edit_alat.php          # CRUD: Form edit data alat
├── hapus_alat.php         # CRUD: Aksi hapus data alat
├── produk.php             # Alias file untuk akses ke data alat/produk
│
├── pengguna.php           # CRUD Pengguna: Tampilan tabel akun user
├── tambah_pengguna.php    # CRUD Pengguna: Form tambah akun baru (password hashing)
├── edit_pengguna.php      # CRUD Pengguna: Form edit akun user
├── hapus_pengguna.php     # CRUD Pengguna: Aksi hapus akun
│
├── laporan.php            # Laporan rekap data alat dengan filter ruangan & tahun
├── cetak_laporan.php      # Halaman cetak / export PDF resmi dengan format kop surat
│
├── includes/              # Komponen modular agar kode rapi & mudah dirawat
│   ├── header.php         # Head tag, CSS template Pluto, custom style
│   ├── sidebar.php        # Navigasi sidebar menu dinamis (active state)
│   ├── topbar.php         # Navigasi atas (toggle sidebar, profil, logout)
│   └── footer.php         # Penutup HTML & library JavaScript (Bootstrap, jQuery, etc.)
│
├── css/                   # Asset CSS Pluto template
├── js/                    # Asset JavaScript & Chart.js
├── images/                # Asset logo dan gambar
└── fonts/                 # Asset icon font-awesome & font template
```

---

## 🌟 Fitur Utama yang Disediakan

1. **Autentikasi & Keamanan:**
   - Halaman login bergaya modern.
   - Proteksi sesi (`cek_login()`) di setiap halaman agar tidak bisa diakses tanpa login.
   - Password terenkripsi dengan aman menggunakan `password_hash()` (Bcrypt).
2. **Dashboard Statistik Interaktif:**
   - 4 Kartu Counter: Total Alat, Total Pengguna, Merek Terdaftar, Jumlah Ruangan.
   - Grafik Batang (Chart.js) interaktif menampilkan distribusi alat per ruangan.
   - Tabel ringkasan 5 data alat medis terbaru.
3. **Manajemen Produk / Alat Elektromedis (CRUD):**
   - Menggunakan dan menyempurnakan struktur dari `modul3-CRUD`.
   - **Create:** Tambah alat baru dengan form validasi.
   - **Read:** Tabel responsif dengan pencarian kata kunci (nama, merek, lokasi, tahun).
   - **Update:** Edit data alat yang sudah ada.
   - **Delete:** Hapus data dengan konfirmasi popup.
4. **Manajemen Pengguna (User Management CRUD):**
   - Tambah petugas atau admin baru.
   - Edit profil dan reset password.
   - Hapus user dengan proteksi (tidak bisa menghapus akun yang sedang aktif).
5. **Laporan & Cetak PDF:**
   - Filter data berdasarkan Ruangan/Lokasi dan Tahun Pengadaan.
   - Tampilan cetak resmi lengkap dengan Kop Rumah Sakit dan kolom tanda tangan pimpinan/kepala instalasi.
