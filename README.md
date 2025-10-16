## Microservices API — CRUD Pegawai (PHP + MySQL + Docker)

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)](https://docs.docker.com/compose/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

Sebuah layanan API sederhana untuk manajemen data pegawai dengan fitur autentikasi (register & login) dan CRUD pegawai. Proyek ini menggunakan PHP (Apache) dan MySQL dengan orkestrasi Docker Compose sehingga bisa dijalankan secara cepat dan konsisten di berbagai mesin.

---

### Fitur Utama

- Autentikasi: Register & Login (hash password + verifikasi)
- CRUD Pegawai: tambah, edit, hapus, daftar
- CORS + Preflight: mendukung request dari frontend SPA
- Keamanan: prepared statements untuk mencegah SQL Injection
- PhpMyAdmin: UI untuk melihat/kelola database

---

### Arsitektur Singkat

- `php` (container): Menjalankan PHP 8.2 + Apache, melayani endpoint API.
- `db` (container): MySQL 8.0 sebagai database utama.
- `phpmyadmin` (container): Antarmuka web untuk manajemen database.

Struktur folder ringkas:

```
src/
  index.php              # Router sederhana
  config/db.php          # Koneksi MySQL
  auth/
    register.php         # Registrasi user (prepared)
    login.php            # Login user (prepared)
  pegawai/
    list.php             # GET daftar pegawai
    add.php              # POST tambah pegawai (prepared)
    edit.php             # PUT/POST ubah pegawai (prepared)
    delete.php           # DELETE/POST hapus pegawai (prepared)
```

---

### Menjalankan Secara Cepat (Docker)

1. Pastikan Docker Desktop sudah aktif.

2. Jalankan perintah berikut di root proyek:

```bash
docker compose up -d --build
```

3. Akses layanan:

- API: `http://localhost:8080`
- PhpMyAdmin: `http://localhost:8081` (host: `db`, user: `userku`, pass: `passku`, database: `db_pegawai`)

4. Hentikan layanan saat selesai:

```bash
docker compose down
```

---

### Konfigurasi Database (Default)

- Host: `db`
- Port: `3306` (internal), dipetakan ke `3307` pada host
- Root: user `root`, pass `root123`
- App user: `userku` / `passku`
- Database: `db_pegawai`

Jika tabel belum ada, buat skema minimal berikut via PhpMyAdmin atau client SQL favorit Anda:

```sql
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pegawai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  alamat VARCHAR(255) NULL,
  tanggal_lahir DATE NULL,
  jenis_kelamin VARCHAR(10) NULL,
  jabatan VARCHAR(100) NOT NULL,
  departemen VARCHAR(100) NOT NULL,
  gaji INT NOT NULL,
  tanggal_masuk DATE NOT NULL,
  status_karyawan VARCHAR(30) NOT NULL,
  foto_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Jika Anda sudah memiliki tabel `pegawai` lama, gunakan ALTER sebagai berikut:

```sql
ALTER TABLE pegawai
  ADD COLUMN email VARCHAR(150) NOT NULL AFTER nama,
  ADD COLUMN phone VARCHAR(30) NOT NULL AFTER email,
  ADD COLUMN alamat VARCHAR(255) NULL AFTER phone,
  ADD COLUMN tanggal_lahir DATE NULL AFTER alamat,
  ADD COLUMN jenis_kelamin VARCHAR(10) NULL AFTER tanggal_lahir,
  ADD COLUMN departemen VARCHAR(100) NOT NULL AFTER jabatan,
  ADD COLUMN tanggal_masuk DATE NOT NULL AFTER gaji,
  ADD COLUMN status_karyawan VARCHAR(30) NOT NULL AFTER tanggal_masuk,
  ADD COLUMN foto_url VARCHAR(255) NULL AFTER status_karyawan;
```

---

### Endpoint API

Base URL: `http://localhost:8080`

Autentikasi:

- POST `/auth/register`

  - Body JSON: `{ "nama": string, "email": string, "password": string }`
  - Response sukses: `{ "status": "success", "message": "Registrasi berhasil" }`

- POST `/auth/login`
  - Body JSON: `{ "email": string, "password": string }`
  - Response sukses:
    ```json
    {
      "status": "success",
      "message": "Login berhasil",
      "data": { "id": 1, "nama": "User", "email": "user@mail.com" }
    }
    ```

Pegawai:

- GET `/pegawai/list`

  - Response:
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "nama": "A",
          "email": "a@mail.com",
          "phone": "+62...",
          "alamat": "Jl. ...",
          "tanggal_lahir": "1995-01-01",
          "jenis_kelamin": "L",
          "jabatan": "B",
          "departemen": "HR",
          "gaji": 1000000,
          "tanggal_masuk": "2024-01-01",
          "status_karyawan": "tetap",
          "foto_url": "https://.../foto.jpg"
        }
      ]
    }
    ```

- POST `/pegawai/add`

  - Body JSON:
    ```json
    {
      "nama": "Andi",
      "email": "andi@mail.com",
      "phone": "+62812...",
      "alamat": "Jl. Melati No. 1",
      "tanggal_lahir": "1998-03-21",
      "jenis_kelamin": "L",
      "jabatan": "Staff",
      "departemen": "Keuangan",
      "gaji": 5000000,
      "tanggal_masuk": "2024-09-01",
      "status_karyawan": "kontrak",
      "foto_url": "https://cdn.example.com/andi.jpg"
    }
    ```

- PUT atau POST `/pegawai/edit`

  - Body JSON (opsional selain `id`, kirim field yang ingin diubah saja):
    ```json
    {
      "id": 1,
      "email": "andi.baru@mail.com",
      "departemen": "Operasional",
      "gaji": 6000000,
      "foto_url": "https://cdn.example.com/andi-new.jpg"
    }
    ```

- DELETE atau POST `/pegawai/delete`
  - Body JSON: `{ "id": number }`

Catatan: Semua endpoint sudah memiliki header CORS dan menangani preflight `OPTIONS` untuk memudahkan integrasi dengan aplikasi frontend.

---

### Contoh cURL

Register:

```bash
curl -X POST http://localhost:8080/auth/register \
  -H "Content-Type: application/json" \
  -d '{"nama":"User Demo","email":"demo@mail.com","password":"rahasia"}'
```

Login:

```bash
curl -X POST http://localhost:8080/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@mail.com","password":"rahasia"}'
```

Tambah Pegawai:

```bash
curl -X POST http://localhost:8080/pegawai/add \
  -H "Content-Type: application/json" \
  -d '{"nama":"Andi","jabatan":"Staff","gaji":5000000}'
```

Daftar Pegawai:

```bash
curl -X GET http://localhost:8080/pegawai/list
```

Edit Pegawai:

```bash
curl -X PUT http://localhost:8080/pegawai/edit \
  -H "Content-Type: application/json" \
  -d '{"id":1,"nama":"Andi","jabatan":"Senior Staff","gaji":6000000}'
```

Hapus Pegawai:

```bash
curl -X DELETE http://localhost:8080/pegawai/delete \
  -H "Content-Type: application/json" \
  -d '{"id":1}'
```

---

### Catatan Teknis & Keamanan

- Password disimpan menggunakan `password_hash` dan diverifikasi dengan `password_verify`.
- Semua query tulis/baca sensitif menggunakan prepared statements (`$conn->prepare` + `bind_param`).
- Header CORS seragam dan preflight `OPTIONS` di-handle di setiap endpoint dan router.

---

### Troubleshooting

- Port bentrok: ubah mapping port pada `docker-compose.yml`.
- Gagal koneksi MySQL: pastikan service `db` sudah siap (cek `docker compose logs db`).
- Tabel belum ada: jalankan SQL skema pada bagian "Konfigurasi Database".

---

### Deploy ke GitHub

1. Buat repository baru di GitHub.
2. Inisialisasi git di folder proyek ini dan push:

```bash
git init
git add .
git commit -m "feat: initial microservices-api (CRUD Pegawai)"
git branch -M main
git remote add origin https://github.com/<username>/<repo>.git
git push -u origin main
```

3. GitHub akan merender file `README.md` ini sebagai halaman utama yang rapi.

---

### License

MIT License. Silakan gunakan dan kembangkan sesuai kebutuhan.
