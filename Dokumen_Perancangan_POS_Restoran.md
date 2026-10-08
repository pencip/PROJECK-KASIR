---
title: "DOKUMEN PERANCANGAN PERANGKAT LUNAK (RPL)"
subtitle: "SISTEM INFORMASI POINT OF SALE (POS) RESTORAN"
author: "Tim Rekayasa Perangkat Lunak POS Restoran"
date: "2026"
geometry: "margin=2.5cm"
output: pdf_document
---

# BAB 1. USE CASE MODELING & ROLE-BASED ACCESS CONTROL

Sistem Point of Sale (POS) Restoran dirancang menggunakan pendekatan **Role-Based Access Control (RBAC)** yang membagi hak akses pengguna ke dalam 3 peran (*role*):

1. **Kasir (Front-Office):** Bertugas melayani pemesanan menu makanan/minuman, mengkalkulasi total belanja dan kembalian, menerima pembayaran (Tunai atau QRIS), serta mencetak struk transaksi.
2. **Manajer (Middle-Office / Pengawas Keuangan):** Memantau dashboard performa penjualan secara *real-time* (penjualan harian, penjualan bulanan, jumlah volume transaksi), serta melihat dan memfilter riwayat transaksi kasir.
3. **Administrator (Back-Office / Pengelola Sistem):** Mengelola master data menu makanan & minuman (nama, harga, kategori, ketersediaan stok) serta mengelola akun pengguna staf beserta hak aksesnya.

## 1.1 Diagram Use Case Sistem

Berikut adalah rancangan Diagram Use Case UML lengkap yang memodelkan interaksi ketiga aktor dengan batas sistem (*system boundary*):

![Diagram Use Case UML Sistem POS Restoran](/opt/lampp/htdocs/pos_resto/use_case_diagram.png)

*Catatan: File mentahan dapat diedit sewaktu-waktu di draw.io menggunakan file `use_case_diagram.drawio`.*

---

## 1.2 Dokumen Use Case Description: Transaksi Penjualan Kasir (UC-POS-01)

| Komponen | Penjelasan Skenario |
| :--- | :--- |
| **Nama Use Case** | **Memproses Transaksi Penjualan Kasir** |
| **Kode Use Case** | UC-POS-01 |
| **Aktor** | Kasir *(dapat diakses juga oleh Admin & Manajer)* |
| **Deskripsi** | Kasir memilih pesanan menu makanan/minuman pelanggan, menghitung grand total, menerima pembayaran (Tunai atau QRIS), memvalidasi stok, serta menyelesaikan transaksi hingga struk tercetak. |
| **Pre-Condition** | 1. Kasir telah berhasil login dan terautentikasi ke sistem.<br>2. Kasir berada pada antarmuka POS (`/pos`) dengan daftar menu aktif dan ketersediaan stok termuat. |
| **Post-Condition** | 1. Transaksi tersimpan ke basis data MySQL dengan status `completed` dan nomor invoice unik.<br>2. Stok produk makanan/minuman berkurang secara otomatis.<br>3. Data akumulasi kas dan riwayat transaksi pada dashboard manajer terbarui secara *real-time*. |
| **Normal Course (Alur Utama)** | 1. Kasir memilih menu makanan atau minuman dari katalog yang tampil di layar kasir POS.<br>2. Setiap item yang dipilih otomatis masuk ke panel rincian pesanan dan total harga diakumulasikan.<br>3. Kasir memilih metode pembayaran (Tunai atau QRIS).<br>4. Untuk pembayaran Tunai, kasir memasukkan nominal uang yang diterima dari pelanggan.<br>5. Sistem secara otomatis menghitung nilai kembalian transaksi.<br>6. Kasir menekan tombol **"Proses & Cetak Struk"**.<br>7. Sistem menjalankan transaksi basis data (*database transaction*): memvalidasi kecukupan stok, mengurangi kuantitas stok menu, menyimpan data transaksi dan rincian belanja, menerbitkan nomor invoice unik, serta mencetak struk transaksi. |
| **Alternate Course (Alur Alternatif)** | **A.1. Nominal Uang Tunai Kurang:**<br>Jika nominal pembayaran tunai kurang dari total belanja, tombol proses transaksi terkunci (*disabled*) dan sistem menolak proses sampai nominal mencukupi.<br>**A.2. Stok Menu Tidak Cukup:**<br>Jika pesanan melebihi stok yang tersisa saat diproses, sistem membatalkan transaksi (*rollback*) dan memunculkan notifikasi ketersediaan stok.<br>**A.3. Pembatalan/Kosongkan Pesanan:**<br>Kasir dapat menekan tombol "Kosongkan" untuk mereset seluruh keranjang belanja.<br>**A.4. Transaksi QRIS:**<br>Kasir memvalidasi pembayaran QRIS berhasil pada terminal/aplikasi pembayaran digital sebelum menekan tombol konfirmasi. |
| **Asumsi** | Perangkat kasir terhubung secara lokal ke server aplikasi Laravel dan basis data MySQL. |

---

## 1.3 Dokumen Use Case Description: Dashboard Analitik Manajer (UC-MGR-01)

| Komponen | Penjelasan Skenario |
| :--- | :--- |
| **Nama Use Case** | **Melihat Dashboard Analitik & Riwayat Transaksi** |
| **Kode Use Case** | UC-MGR-01 |
| **Aktor** | Manajer, Administrator |
| **Deskripsi** | Menampilkan ringkasan eksekutif performa resto: omzet harian, akumulasi omzet bulanan, jumlah volume transaksi, dan daftar riwayat transaksi kasir beserta filter tanggal dan metode bayar. |
| **Pre-Condition** | Pengguna telah login dengan akun ber-role `manajer` atau `admin`. |
| **Post-Condition** | Ringkasan finansial dan data transaksi kasir tersaji untuk audit dan monitoring operasional harian resto. |
| **Normal Course** | 1. Manajer membuka halaman Dashboard Analitik (`/manager/dashboard`).<br>2. Sistem memuat kartu metrik: Penjualan Hari Ini, Penjualan Bulan Ini, Total Transaksi Hari Ini, dan Transaksi Bulan Ini.<br>3. Sistem menampilkan 10 transaksi kasir terkini.<br>4. Manajer dapat membuka halaman riwayat transaksi (`/manager/transactions`) untuk memfilter berdasarkan rentang tanggal atau metode bayar. |
| **Alternate Course** | Jika belum terdapat transaksi pada periode berjalan, indikator metrik bernilai `Rp 0` dan tabel menampilkan keterangan data kosong. |

---

## 1.4 Dokumen Use Case Description: Modul Administrator (UC-ADM-01 & UC-ADM-02)

### A. Mengelola Data Menu (UC-ADM-01)
* **Aktor:** Administrator
* **Deskripsi:** Admin melakukan penambahan menu baru, pengubahan harga jual, pembaruan jumlah stok, serta penonaktifan menu makanan/minuman yang habis atau tidak dijual lagi melalui rute `/admin/menus`.
* **Alur:** Admin membuka katalog menu -> memilih Tambah Menu / Edit Menu -> menginput nama, kategori, harga, dan stok -> sistem menyimpan ke tabel `menus` di database MySQL.

### B. Mengelola Pengguna & Hak Akses (UC-ADM-02)
* **Aktor:** Administrator
* **Deskripsi:** Admin mendaftarkan akun staf baru (Kasir, Manajer, Admin), memperbarui profil/password staf, serta menonaktifkan akun staf yang sudah berhenti bekerja melalui rute `/admin/users`.
* **Alur:** Admin membuka daftar staf -> menambah atau mengedit data staf -> memilih peran (*role*) -> sistem menyimpan sandi dengan enkripsi Hash Bcrypt.

---

# BAB 2. ACTIVITY DIAGRAM: ALUR TRANSAKSI CHECKOUT KASIR

Diagram aktivitas berikut menggambarkan alur proses transaksi dari interaksi Kasir hingga eksekusi basis data:

1. **Kasir:** Memilih menu pesanan pelanggan, memasukkan jumlah kuantitas, memilih metode pembayaran (Tunai atau QRIS).
2. **Sistem Antarmuka (POS Frontend):**
   * Menghitung subtotal per item dan total akumulasi pesanan.
   * Pada metode Tunai: menghitung uang kembalian secara otomatis.
   * Memvalidasi apakah uang tunai mencukupi. Jika kurang, tombol checkout dinonaktifkan.
   * Mengirim request transaksi POST `/pos/checkout`.
3. **Basis Data MySQL (Backend Laravel):**
   * Mengunci baris menu pesanan dengan `lockForUpdate` untuk mencegah *race condition*.
   * Memvalidasi ketersediaan stok fisik di database. Jika kurang, transaksi di-*rollback*.
   * Mengurangi stok menu secara otomatis (`decrement`).
   * Menghasilkan nomor invoice unik (`INV-YYYYMMDD-XXXXX`).
   * Menyimpan master transaksi ke tabel `transactions` dan rincian ke tabel `transaction_details`.
   * Mengembalikan respons sukses ke antarmuka untuk memicu pencetakan struk belanja.

---

# BAB 3. SEQUENCE DIAGRAM: MEMPROSES TRANSAKSI PENJUALAN

Alur pertukaran pesan (*message passing*) antar komponen sistem saat transaksi kasir berlangsung:

1. `Kasir` menekan tombol **"Proses & Cetak Struk"** pada layar browser POS.
2. `View POS` mengirim request Ajax POST ke `PosController::store` dengan membawa payload (daftar item menu, ID, qty, metode bayar, dan nominal uang diterima).
3. `PosController` membuka transaksi database terisolasi: `DB::beginTransaction()`.
4. `PosController` memanggil model `Menu::where('id', $id)->lockForUpdate()`.
5. Model `Menu` mengembalikan data stok saat ini. Jika stok < qty pesanan, controller memicu `DB::rollBack()` dan mengirim respons error HTTP 422.
6. Jika stok mencukupi, controller mengeksekusi `Menu::decrement('stock', $qty)`.
7. `PosController` memanggil `Transaction::create()` dengan rincian invoice baru, user_id kasir, total belanja, nominal bayar, dan uang kembalian.
8. `PosController` memanggil `TransactionDetail::insert()` untuk menyimpan setiap baris pesanan ke dalam tabel relasi.
9. `PosController` memanggil `DB::commit()` untuk menyimpan transaksi secara permanen.
10. `PosController` mengembalikan respons JSON HTTP 200 (Success) beserta objek transaksi.
11. `View POS` memicu dialog print struk belanja kasir (*Thermal Receipt Printer*) dan mereset keranjang belanja.

---

# BAB 4. PERANCANGAN BASIS DATA & ENTITY RELATIONSHIP DIAGRAM (ERD)

Struktur tabel basis data restoran dirancang menggunakan MySQL dengan relasi kardinalitas ternormalisasi:

### 4.1 Spesifikasi Tabel

#### 1. Tabel `users` (Pengguna & Hak Akses)
* `id` (BigInt, Primary Key, Auto Increment)
* `name` (VarChar 255, Nama lengkap staf)
* `email` (VarChar 255, Email login unik)
* `password` (VarChar 255, Hash Bcrypt sandi pengguna)
* `role` (Enum: `'admin'`, `'manajer'`, `'kasir'`)
* `created_at` & `updated_at` (Timestamp)

#### 2. Tabel `menus` (Katalog Makanan & Minuman)
* `id` (BigInt, Primary Key, Auto Increment)
* `name` (VarChar 255, Nama menu)
* `category` (VarChar 100, Kategori makanan/minuman)
* `price` (Decimal 12,2, Harga satuan menu)
* `stock` (Integer, Jumlah stok fisik tersedia)
* `image` (VarChar 255, Path foto menu, opsional)
* `is_active` (Boolean, Status menu aktif/nonaktif)
* `created_at` & `updated_at` (Timestamp)

#### 3. Tabel `transactions` (Master Transaksi Kasir)
* `id` (BigInt, Primary Key, Auto Increment)
* `invoice_number` (VarChar 50, Nomor unik invoice transaksi)
* `user_id` (BigInt, Foreign Key mengarah ke `users.id`)
* `total_amount` (Decimal 12,2, Grand total belanja)
* `payment_method` (Enum: `'cash'`, `'qris'`)
* `paid_amount` (Decimal 12,2, Nominal uang yang dibayarkan)
* `change_amount` (Decimal 12,2, Nilai uang kembalian)
* `status` (Enum: `'completed'`, `'cancelled'`)
* `created_at` & `updated_at` (Timestamp waktu transaksi)

#### 4. Tabel `transaction_details` (Rincian Item Transaksi)
* `id` (BigInt, Primary Key, Auto Increment)
* `transaction_id` (BigInt, Foreign Key mengarah ke `transactions.id`, On Delete Cascade)
* `menu_id` (BigInt, Foreign Key mengarah ke `menus.id`)
* `menu_name` (VarChar 255, Snapshot nama menu saat transaksi)
* `price` (Decimal 12,2, Snapshot harga satuan saat transaksi)
* `quantity` (Integer, Jumlah porsi/item dipesan)
* `subtotal` (Decimal 12,2, Kalkulasi `price * quantity`)
* `created_at` & `updated_at` (Timestamp)

### 4.2 Aturan Relasi Kardinalitas (Kamus Data)
* **Users ke Transactions (1 to N):** Satu staf kasir dapat memproses banyak transaksi penjualan.
* **Transactions ke Transaction Details (1 to N):** Satu transaksi penjualan menampung banyak item menu yang dibeli pelanggan.
* **Menus ke Transaction Details (1 to N):** Satu jenis menu makanan/minuman dapat terjual pada banyak transaksi yang berbeda.

---

# BAB 5. KESIMPULAN & PENJELASAN TEKNIS

1. **Integritas Stok & Transaksi:** Pemrosesan transaksi kasir menggunakan `DB::transaction` dan mekanisme `lockForUpdate` pada MySQL menjamin stok tidak akan pernah minus meskipun kasir melayani transaksi cepat (*concurrency protection*).
2. **Sinkronisasi Real-Time:** Begitu transaksi disimpan oleh kasir, metrik dashboard manajer (penjualan hari ini, akumulasi kas bulanan, dan log 10 transaksi terakhir) langsung terbarui seketika saat halaman dimuat.
3. **Audit Trail & Keamanan:** Pembagian peran Kasir, Manajer, dan Admin menggunakan middleware Laravel menjamin prinsip *least privilege*, di mana staf hanya dapat mengakses fitur yang menjadi wewenang tugas operasionalnya.
