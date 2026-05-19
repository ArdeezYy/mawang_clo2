# Naskah Video Presentasi Maksimal 3 Menit

## 0:00 - 0:20 Pendahuluan

Assalamualaikum, saya mempresentasikan proyek CLO 2 Keamanan Sistem, yaitu aplikasi Secure Comments. Aplikasi ini adalah papan komentar publik berbasis PHP, Apache, MySQL, dan Docker. Target scope proyek ini adalah 80 poin, yaitu HTTPS, hash dan salt password, mitigasi input berlebihan atau buffer overflow, dan SQL injection.

## 0:20 - 0:45 Teori Singkat

Masalah utama yang ditunjukkan adalah koneksi tidak terenkripsi, password plaintext, input terlalu panjang, dan SQL injection. Solusi yang dipakai adalah HTTPS/TLS, password hash dengan salt, password policy, pembatasan panjang input, dan prepared statement.

## 0:45 - 1:05 Blok Diagram

Aplikasi berjalan di Docker Compose. Browser mengakses Apache/PHP melalui HTTPS port 8443. Apache juga menerima HTTP port 8080 lalu redirect ke HTTPS. PHP terhubung ke MySQL untuk menyimpan users dan comments. Session memakai cookie aman, input komentar dibatasi maksimal 500 karakter, dan query memakai prepared statement.

## 1:05 - 2:25 Demo Aplikasi dan Uji

Pertama, saya buka `https://localhost:8443`. Browser memberi peringatan karena sertifikat self-signed, lalu saya lanjutkan untuk demo lokal. Di halaman utama, komentar bisa dibaca tanpa login.

Kedua, saya inspeksi sertifikat pada browser. Sertifikat memakai public key RSA 2048-bit. HTTP port 8080 juga otomatis diarahkan ke HTTPS.

Ketiga, saya login memakai akun admin `admin` dengan password `Admin@240!`, lalu membuka `/admin.php`. Admin panel menampilkan monitoring tabel users dan comments.

Keempat, pada tabel users terlihat kolom password hash. Password plaintext tidak disimpan. Hash memakai format bcrypt, diawali `$2y$10$`, dan salt bcrypt berada pada 22 karakter setelah prefix tersebut.

Kelima, saya mencoba payload SQL injection `' OR '1'='1` pada login. Login gagal karena input diproses sebagai data melalui prepared statement.

Keenam, saya mencoba komentar lebih dari 500 karakter. Server menolak input tersebut, sehingga input terlalu panjang tidak masuk ke database.

## 2:25 - 2:50 Kesimpulan

Kesimpulannya, aplikasi ini berhasil menerapkan kontrol target 80 poin: HTTPS/TLS pada web server, password hash dengan salt, pembatasan input, dan prepared statement untuk SQL injection.

## 2:50 - 3:00 Saran

Untuk pengembangan selanjutnya, aplikasi bisa ditambah audit log, reset password aman, dan sertifikat CA resmi.
