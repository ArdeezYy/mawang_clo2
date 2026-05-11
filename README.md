# CLO 2 Secure Comments

Aplikasi papan komentar publik berbasis PHP, MySQL, Apache, dan Docker untuk demo pengamanan aplikasi web.

## Menjalankan Aplikasi

```powershell
docker compose up --build -d
```

Jika muncul error `dockerDesktopLinuxEngine` atau Docker engine belum hidup, jalankan:

```powershell
.\scripts\start-site.ps1
```

URL demo:

- HTTPS: https://localhost:8443
- HTTP redirect: http://localhost:8080

Jika port 8080/8443 sedang dipakai, jalankan dengan port lain:

```powershell
$env:HTTP_PORT=8081; $env:HTTPS_PORT=8444; docker compose up --build -d
```

Browser akan menampilkan peringatan karena sertifikat SSL dibuat sendiri. Lanjutkan ke halaman untuk kebutuhan demo lokal.

Untuk menjalankan mode demo yang sengaja rentan SQL injection pada login:

```powershell
docker compose -f docker-compose.yml -f docker-compose.vulnerable.yml up --build -d
```

Kembalikan ke mode aman:

```powershell
docker compose up --build -d
```

## Akun Demo

- Username: `admin`
- Password: `Admin@240!`
- User biasa dapat dibuat lewat `/signup.php`.
- Username `admin` dan `root` tidak bisa didaftarkan dari sign up publik.

## Branch Demo

- `secure-login`: versi aman. Default `docker compose up --build -d` menjalankan `LOGIN_MODE=secure`, sehingga payload SQL injection di login gagal.
- `vulnerable-login`: versi rentan untuk demo. Default `docker compose up --build -d` menjalankan `LOGIN_MODE=vulnerable`, sehingga form login utama sengaja memakai query SQL mentah dan dapat dibobol dengan payload SQL injection.

File `docker-compose.vulnerable.yml` tetap disediakan sebagai override tambahan jika ingin menyalakan mode rentan dari branch `secure-login`, tetapi untuk skenario presentasi paling rapi adalah berpindah branch:

```powershell
git switch secure-login
docker compose up --build -d

git switch vulnerable-login
docker compose up --build -d
```

## Kontrol Keamanan

- HTTPS melalui Apache SSL dan self-signed certificate.
- Cookie session memakai `HttpOnly`, `Secure`, `SameSite=Strict`, dan strict session mode.
- Semua form POST memakai CSRF token.
- Password user disimpan dengan `password_hash()` yang otomatis memakai salt.
- Signup menerapkan password policy minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol.
- Halaman login dan signup memiliki tombol tampil/sembunyikan password.
- Halaman signup menampilkan checklist password secara langsung dan tombol daftar hanya aktif jika password memenuhi syarat.
- Branch `secure-login` memakai prepared statement untuk login dan input komentar.
- Output dari database di-escape dengan `htmlspecialchars()` untuk mitigasi XSS.
- Input dibatasi panjangnya di sisi server untuk mengurangi risiko overflow/abuse.
- Admin panel hanya bisa diakses akun dengan role admin.
- Security headers aktif: CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy.

## Skenario Uji

- Buka halaman utama tanpa login untuk membaca komentar.
- Buka `/comment.php` tanpa login; aplikasi harus meminta login.
- Buat akun baru lewat `/signup.php`; akun baru bisa menulis komentar tetapi tidak bisa membuka admin panel.
- Login sebagai `admin`, lalu buka `/admin.php` untuk melihat monitoring tabel `users` dan `comments`.
- Login dengan akun demo, tambah komentar, lalu cek komentar tampil di halaman utama.
- Coba SQL injection di form login: `' OR '1'='1`; login harus gagal.
- Coba XSS di komentar: `<script>alert(1)</script>`; teks harus tampil mentah dan tidak dieksekusi.
- Kirim komentar lebih dari 500 karakter; aplikasi harus menolak.
- Submit form POST tanpa CSRF token harus ditolak/redirect.
- Ulangi login gagal; respons memiliki delay sekitar 2 detik.
- Inspeksi sertifikat browser pada `https://localhost:8443`.

## Deliverable Pendukung

- Dokumentasi aplikasi: `docs/DOKUMENTASI_APLIKASI.md`
- Naskah video 3 menit: `docs/NASKAH_VIDEO_3_MENIT.md`
- File presentasi: `presentation/output/CLO2_Secure_Comments.pptx`
- Generator presentasi: `presentation/build-deck.mjs`
