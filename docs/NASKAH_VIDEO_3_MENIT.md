# Naskah Video Presentasi Maksimal 3 Menit

## 0:00 - 0:20 Pendahuluan

Assalamualaikum, saya mempresentasikan skenario non-secure dari aplikasi Secure Comments. Aplikasi ini adalah papan komentar publik berbasis PHP, Apache, MySQL, dan Docker. Tujuan skenario ini adalah menunjukkan kondisi aplikasi sebelum kontrol keamanan diterapkan.

## 0:20 - 0:45 Kondisi Rentan

Pada branch ini, beberapa keamanan sengaja dimatikan. Password disimpan plaintext tanpa hash dan salt, login memakai query SQL mentah, komentar tidak dibatasi 500 karakter, output komentar tidak di-escape, token CSRF tidak diperiksa, dan admin panel tidak membatasi role.

## 0:45 - 1:05 Blok Diagram

Aplikasi berjalan di Docker Compose dengan identitas Andrian Irmawan, NIM 101032300219. Container web bernama `andrian_irmawan_101032300219_web` dan memakai IP `192.168.219.219`. Container database bernama `andrian_irmawan_101032300219_db` dan memakai IP `192.168.219.220`.

## 1:05 - 2:25 Demo Aplikasi dan Uji

Pertama, saya buka `http://localhost:8080`. HTTP langsung membuka aplikasi dan tidak diarahkan otomatis ke HTTPS.

Kedua, saya mencoba login memakai payload username `' OR '1'='1` dengan password bebas. Login berhasil masuk sebagai admin karena query SQL disusun langsung dari input.

Ketiga, saya buka `/admin.php`. Panel tetap terbuka dan pada tabel users terlihat password admin tersimpan plaintext sebagai `Admin@240!`.

Keempat, saya mengirim komentar berisi `<script>alert(1)</script>`. Script dieksekusi browser karena output komentar tidak di-escape.

Kelima, saya mengirim komentar lebih dari 500 karakter. Komentar tetap tersimpan karena tidak ada pembatasan panjang input di server maupun client.

Keenam, saya kirim form POST tanpa token CSRF. Aksi tetap diterima karena pengecekan CSRF dimatikan.

## 2:25 - 2:50 Kesimpulan

Kesimpulannya, skenario non-secure ini menunjukkan risiko utama sebelum pengamanan: password plaintext, SQL injection, XSS, tidak ada CSRF protection, tidak ada batas input, dan admin panel tanpa pembatasan role.

## 2:50 - 3:00 Saran

Solusinya ada pada branch `secure-login`, yaitu mengaktifkan hash dan salt password, prepared statement, output escaping, CSRF token, pembatasan input, session hardening, dan role check admin.
