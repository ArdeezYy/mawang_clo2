# Naskah Video Presentasi Maksimal 3 Menit

## 0:00 - 0:20 Pendahuluan

Assalamualaikum, saya mempresentasikan proyek CLO 2 Keamanan Sistem, yaitu aplikasi Secure Comments. Aplikasi ini adalah papan komentar publik berbasis PHP, Apache, MySQL, dan Docker. Tujuannya adalah menunjukkan beberapa kontrol keamanan pada aplikasi web dan server.

## 0:20 - 0:45 Teori Singkat

Masalah utama yang diuji adalah koneksi tidak terenkripsi, password bocor, SQL injection, XSS, input terlalu panjang, CSRF, dan brute force. Solusi yang dipakai adalah HTTPS/TLS, password hash dengan salt, prepared statement, escaping output, validasi panjang input, CSRF token, session hardening, dan delay pada login gagal.

## 0:45 - 1:05 Blok Diagram

Aplikasi berjalan di Docker Compose. Browser mengakses Apache/PHP melalui HTTPS port 8443. Apache juga menerima HTTP port 8080 lalu redirect ke HTTPS. PHP terhubung ke MySQL menggunakan PDO prepared statement. Session disimpan di cookie aman dengan HttpOnly, Secure, dan SameSite Strict.

## 1:05 - 2:25 Demo Aplikasi dan Uji Serangan

Pertama, saya buka `https://localhost:8443`. Browser memberi peringatan karena sertifikat self-signed, lalu saya lanjutkan untuk demo lokal. Di halaman utama, komentar bisa dibaca tanpa login.

Kedua, saya buka `/comment.php` tanpa login. Aplikasi meminta login, artinya penulisan komentar dilindungi autentikasi.

Ketiga, saya login memakai akun admin `admin` dengan password `Admin@240!`, lalu membuka `/admin.php`. Admin panel menampilkan monitoring tabel users dan comments.

Keempat, saya uji SQL injection pada login dengan payload `' OR '1'='1`. Pada mode secure, login gagal karena query memakai prepared statement.

Kelima, saya membuat komentar berisi `<script>alert(1)</script>`. Komentar tampil sebagai teks biasa dan script tidak berjalan karena output memakai `htmlspecialchars`.

Keenam, saya mencoba komentar lebih dari 500 karakter. Server menolak input tersebut sebagai mitigasi input abuse atau buffer overflow.

Ketujuh, jika form POST dikirim tanpa CSRF token, server menolak aksi. Jika login gagal, respons diberi delay sekitar 2 detik untuk memperlambat brute force.

## 2:25 - 2:50 Kesimpulan

Kesimpulannya, aplikasi ini berhasil menerapkan pengamanan utama: HTTPS, hash dan salt password, prepared statement untuk SQL injection, escaping untuk XSS, validasi input, CSRF token, session hardening, admin authorization, security headers, dan mitigasi brute force sederhana.

## 2:50 - 3:00 Saran

Untuk pengembangan selanjutnya, aplikasi bisa ditambah rate limiter berbasis IP, audit log, reset password aman, dan deployment dengan sertifikat CA resmi.
