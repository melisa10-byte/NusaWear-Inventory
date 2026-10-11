# NusaWear — Inventory Management System

Mini Project 3 berdasarkan PPT Pertemuan 4: Relational Database + Advanced PHP.

## Teknologi
- PHP 8+ (PDO)
- MySQL/MariaDB (XAMPP)
- HTML/CSS responsif

## Fitur sesuai PPT
- Login, logout, password hashing, session
- Role `admin` dan `staff`
- Master kategori, produk, supplier
- Relasi many-to-many produk-supplier
- Barang masuk/keluar dan histori transaksi
- Search, filter kategori, sorting, pagination
- Dashboard statistik dan laporan inventory
- Prepared statement, validasi server, escaping output
- Transaksi database untuk menjaga stok dan histori konsisten

## Cara menjalankan di XAMPP
1. Ekstrak folder `NusaWear_Inventory` ke `C:\xampp\htdocs\`.
2. Buka XAMPP, nyalakan **Apache** dan **MySQL**.
3. Buka `http://localhost/phpmyadmin`.
4. Import file `database/schema.sql`.
5. Buka `config/database.php`; sesuaikan username/password MySQL jika berbeda.
6. Buka `http://localhost/NusaWear_Inventory/public/seed_admin.php` satu kali untuk membuat akun demo.
7. Login melalui `http://localhost/NusaWear_Inventory/public/login.php`.
8. Setelah berhasil membuat akun demo, hapus atau ubah nama `public/seed_admin.php` agar tidak dapat diakses kembali.

## Akun demo
- Admin: `admin` / `Admin123!`
- Staff: `staff` / `Staff123!`

> Seed demo menyimpan password dengan `password_hash()`. Jangan gunakan password demo untuk aplikasi online.

## Struktur folder
- `config/database.php`: koneksi PDO
- `helpers/`: autentikasi, validasi, dan escaping
- `public/`: halaman aplikasi
- `database/schema.sql`: skema dan data awal
- `views/`: header/footer

## Catatan
Aplikasi ini merupakan proyek pembelajaran lokal. Untuk penggunaan nyata, tambahkan CSRF token, pembatasan percobaan login, pengaturan cookie HTTPS, dan pengelolaan akun yang lebih lengkap.
