# CLO 2 Non-Secure Comments

Aplikasi papan komentar publik berbasis PHP, MySQL, Apache, dan Docker untuk skenario non-secure.

Kontrol keamanan yang sengaja dimatikan:

- Password disimpan plaintext tanpa hash dan salt.
- Login memakai query SQL mentah.
- Token CSRF tidak divalidasi.
- Output komentar tidak di-escape.
- Komentar tidak dibatasi 500 karakter.
- Cookie session tidak memakai konfigurasi hardening.
- HTTP tidak diarahkan otomatis ke HTTPS.

Identitas instalasi:

- Nama: `Andrian Irmawan`
- NIM: `101032300219`
- Container web: `andrian_irmawan_101032300219_web`
- Container database: `andrian_irmawan_101032300219_db`
- IP web: `192.168.219.219`
- IP database: `192.168.219.220`

IP web memakai oktet terakhir `219`, sesuai 3 digit akhir NIM. Database memakai `.220` agar tidak konflik dengan container web.

## Menjalankan Aplikasi

```powershell
docker compose up --build -d
```

Jika muncul error `dockerDesktopLinuxEngine` atau Docker engine belum hidup, jalankan:

```powershell
.\scripts\start-site.ps1
```

URL lokal:

- HTTPS: https://localhost:8443
- HTTP: http://localhost:8080

Jika port 8080/8443 sedang dipakai, jalankan dengan port lain:

```powershell
$env:HTTP_PORT=8081; $env:HTTPS_PORT=8444; docker compose up --build -d
```

Browser akan menampilkan peringatan jika membuka HTTPS karena sertifikat SSL dibuat sendiri.

## Akun Demo

- Username: `admin`
- Password: `Admin@240!`
- User biasa dapat dibuat lewat `/signup.php`.

## Kondisi Non-Secure

- Admin panel menampilkan password plaintext.
- Payload SQL injection di login dapat melewati autentikasi.
- Payload XSS di komentar dieksekusi browser.
- Komentar panjang tersimpan karena tidak ada batas 500 karakter.
- Form POST tetap diterima walaupun tanpa token CSRF.
- Admin panel tidak lagi membatasi role admin.

## Skenario Uji

- Buka halaman utama tanpa login untuk membaca komentar.
- Login memakai payload username `' OR '1'='1` dengan password bebas; aplikasi masuk sebagai admin.
- Login sebagai `admin`, lalu buka `/admin.php` untuk melihat password plaintext.
- Kirim komentar `<script>alert(1)</script>`; browser mengeksekusi script.
- Kirim komentar lebih dari 500 karakter; aplikasi tetap menyimpan komentar.
- Buka `/admin.php` dari akun biasa; panel tetap terbuka karena role check dimatikan.

## Deliverable Pendukung

- Dokumentasi aplikasi: `docs/DOKUMENTASI_APLIKASI.md`
- Naskah video 3 menit: `docs/NASKAH_VIDEO_3_MENIT.md`
- File presentasi: `presentation/output/CLO2_Secure_Comments.pptx`
- Generator presentasi: `presentation/build-deck.mjs`
