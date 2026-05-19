# Jawaban Diskusi Keamanan Web

## Identitas Proyek

Proyek yang digunakan adalah **CLO 2 Secure Comments**, yaitu aplikasi komentar publik berbasis PHP, Apache, MySQL, dan Docker Compose.

Identitas instalasi:

- Nama: `Andrian Irmawan`
- NIM: `101032300219`
- Container web: `andrian_irmawan_101032300219_web`
- Container database: `andrian_irmawan_101032300219_db`
- Subnet: `192.168.219.0/24`
- IP web: `192.168.219.219`
- IP database: `192.168.219.220`

Scope keamanan target 80:

- SSL/TLS pada web server.
- Hash dan salt pada password.
- Mitigasi input berlebihan / buffer overflow.
- Pencegahan SQL injection.

XSS scripting dan brute force belum menjadi scope karena berada di luar target 80 yang dipilih.

## 1. Konfigurasi SSL/TLS Pada Web Server

Proyek berjalan menggunakan Docker container. Web server memakai image `php:8.3-apache`, sedangkan database memakai `mysql:8.4`.

Port aplikasi:

```text
HTTP  : http://localhost:8080
HTTPS : https://localhost:8443
```

HTTP diarahkan ke HTTPS dengan redirect 301. Konfigurasi SSL/TLS berada di:

```text
docker/php-apache/secure-comments.conf
```

Bagian penting:

```apache
SSLEngine on
SSLCertificateFile /etc/apache2/ssl/clo2-selfsigned.crt
SSLCertificateKeyFile /etc/apache2/ssl/clo2-selfsigned.key
SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
SSLCipherSuite HIGH:!aNULL:!MD5
```

Bukti algoritma kunci publik:

```text
Public Key Algorithm: rsaEncryption
Public-Key: (2048 bit)
Signature Algorithm: sha256WithRSAEncryption
```

## 2. Hash dan Salt Pada Password

Password tidak disimpan sebagai plaintext. Saat signup atau seed admin, aplikasi memakai:

```php
password_hash($password, PASSWORD_DEFAULT)
```

Saat login, aplikasi memakai:

```php
password_verify($password, $user['password_hash'])
```

Pada PHP 8.3, `PASSWORD_DEFAULT` memakai bcrypt. Salt dibuat otomatis oleh `password_hash()`, sehingga password yang sama dapat menghasilkan hash yang berbeda.

Bukti di database untuk admin:

```text
$2y$10$...
```

Hash bcrypt tersebut panjangnya 60 karakter dan bukan password asli `Admin@240!`.

## 3. Menghindari Buffer Overflow / Input Berlebihan

Pada aplikasi web PHP, risiko yang relevan adalah input berlebihan. Komentar dibatasi maksimal 500 karakter di sisi server dan client.

Kode server:

```php
$length = strlen($body);

if ($length < 1 || $length > 500) {
    flash('error', 'Komentar wajib diisi dan maksimal 500 karakter.');
    redirect('/comment.php');
}
```

Kode form:

```html
<textarea id="body" name="body" maxlength="500" required></textarea>
```

Apache juga membatasi ukuran request:

```apache
LimitRequestBody 1048576
```

Bukti uji: komentar 501 karakter ditolak dan jumlah data `CHAR_LENGTH(body)>500` tidak bertambah.

## 4. Menghindari SQL Injection

SQL injection terjadi ketika input user digabung langsung ke query SQL. Proyek ini memakai prepared statement sehingga input diperlakukan sebagai data, bukan perintah SQL.

Contoh pada login:

```php
$stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
```

Contoh pada komentar:

```php
$stmt = $pdo->prepare('INSERT INTO comments (user_id, body) VALUES (?, ?)');
$stmt->execute([(int) $user['id'], $body]);
```

Bukti uji: payload username berikut gagal login pada branch `secure-login`:

```text
' OR '1'='1
```

## 5. XSS Scripting

XSS terjadi ketika input user berisi script lalu ditampilkan kembali sebagai HTML aktif.

Pada target 80 yang dipilih, XSS belum dijadikan kontrol penilaian utama. Fokus branch `secure-login` untuk target 80 adalah SSL/TLS, hash dan salt password, pembatasan input, dan SQL injection. Jika ingin mengejar poin lebih tinggi, output komentar dapat diamankan kembali dengan `htmlspecialchars()`.

## 6. Brute Force

Mekanisme brute force seperti rate limiting, captcha, atau account lockout belum menjadi scope implementasi. Pada rubrik PDF, brute force berada sebagai poin tambahan setelah target utama.

## 7. Ringkasan Bukti Target 80

| Kontrol | Bukti |
| --- | --- |
| SSL/TLS | HTTPS `https://localhost:8443` aktif, HTTP redirect ke HTTPS |
| Algoritma kunci publik | RSA 2048-bit pada sertifikat |
| Hash dan salt | Password admin berbentuk bcrypt `$2y$10$...` |
| Buffer/input abuse | Komentar 501 karakter ditolak |
| SQL injection | Payload `' OR '1'='1` gagal login |
| XSS scripting | Tidak diklaim pada target 80 |

## 8. Kesimpulan

Proyek Secure Comments pada branch `secure-login` sudah memenuhi target 80: SSL/TLS, hash dan salt password, pembatasan input untuk mencegah input berlebihan, dan SQL injection dengan prepared statement.
