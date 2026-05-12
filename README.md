# CLO 2 Secure Comments

Aplikasi papan komentar publik berbasis PHP, MySQL, Apache, dan Docker untuk demo pengamanan aplikasi web target 80 poin.

Scope yang ditunjukkan:

- Konfigurasi HTTPS/SSL pada web server.
- Password disimpan dengan hash dan salt.
- Input komentar dibatasi untuk mitigasi buffer overflow/input berlebihan.

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

URL demo:

- HTTPS: https://localhost:8443
- HTTP redirect: http://localhost:8080

Jika port 8080/8443 sedang dipakai, jalankan dengan port lain:

```powershell
$env:HTTP_PORT=8081; $env:HTTPS_PORT=8444; docker compose up --build -d
```

Browser akan menampilkan peringatan karena sertifikat SSL dibuat sendiri. Lanjutkan ke halaman untuk kebutuhan demo lokal.

## Akun Demo

- Username: `admin`
- Password: `Admin@240!`
- User biasa dapat dibuat lewat `/signup.php`.
- Username `admin` dan `root` tidak bisa didaftarkan dari sign up publik.

## Kontrol Keamanan

- HTTPS melalui Apache SSL dan self-signed certificate.
- Cookie session memakai `HttpOnly`, `Secure`, `SameSite=Strict`, dan strict session mode.
- Password user disimpan dengan `password_hash()` yang otomatis memakai salt.
- Signup menerapkan password policy minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol.
- Halaman login dan signup memiliki tombol tampil/sembunyikan password.
- Halaman signup menampilkan checklist password secara langsung dan tombol daftar hanya aktif jika password memenuhi syarat.
- Input komentar dibatasi maksimal 500 karakter di sisi server.
- Apache memakai `LimitRequestBody` untuk membatasi ukuran request.
- Admin panel hanya bisa diakses akun dengan role admin.
- Admin panel menampilkan hash password sebagai bukti bahwa plaintext password tidak disimpan.

## Skenario Uji

- Buka halaman utama tanpa login untuk membaca komentar.
- Buka `/comment.php` tanpa login; aplikasi harus meminta login.
- Buat akun baru lewat `/signup.php`; akun baru bisa menulis komentar tetapi tidak bisa membuka admin panel.
- Login sebagai `admin`, lalu buka `/admin.php` untuk melihat monitoring tabel `users`, `comments`, dan hash password.
- Inspeksi sertifikat browser pada `https://localhost:8443`; algoritma public key adalah RSA 2048-bit.
- Cek hash password admin di admin panel; format bcrypt diawali `$2y$10$` dan salt ada pada 22 karakter setelah prefix tersebut.
- Kirim komentar lebih dari 500 karakter; aplikasi harus menolak.

## Deliverable Pendukung

- Dokumentasi aplikasi: `docs/DOKUMENTASI_APLIKASI.md`
- Naskah video 3 menit: `docs/NASKAH_VIDEO_3_MENIT.md`
- File presentasi: `presentation/output/CLO2_Secure_Comments.pptx`
- Generator presentasi: `presentation/build-deck.mjs`
