import { createRequire } from "node:module";
import { pathToFileURL } from "node:url";
import { mkdir } from "node:fs/promises";

const require = createRequire(import.meta.url);
const artifactToolPath = require.resolve("@oai/artifact-tool");
const artifact = await import(pathToFileURL(artifactToolPath).href);

const {
  Presentation,
  PresentationFile,
  column,
  row,
  grid,
  panel,
  text,
  rule,
  fill,
  hug,
  fixed,
  wrap,
  grow,
  fr,
  auto,
} = artifact;

const W = 1920;
const H = 1080;
const colors = {
  ink: "#16202A",
  muted: "#5E6B78",
  bg: "#F5F7FB",
  surface: "#FFFFFF",
  teal: "#0F766E",
  tealDark: "#115E59",
  line: "#D9E1EA",
  danger: "#B42318",
  amber: "#9A6700",
};

const titleStyle = { fontSize: 58, bold: true, color: colors.ink };
const bodyStyle = { fontSize: 28, color: colors.ink };
const mutedStyle = { fontSize: 22, color: colors.muted };
const smallStyle = { fontSize: 18, color: colors.muted };

const presentation = Presentation.create({ slideSize: { width: W, height: H } });

function addSlide(content) {
  const slide = presentation.slides.add();
  slide.compose(content, { frame: { left: 0, top: 0, width: W, height: H }, baseUnit: 8 });
}

function shell(title, subtitle, children) {
  return column(
    { name: "slide-root", width: fill, height: fill, padding: { x: 96, y: 72 }, gap: 32 },
    [
      column(
        { name: "title-stack", width: fill, height: hug, gap: 14 },
        [
          text(title, { name: "slide-title", width: fill, height: hug, style: titleStyle }),
          subtitle
            ? text(subtitle, { name: "slide-subtitle", width: wrap(1400), height: hug, style: mutedStyle })
            : rule({ name: "title-rule", width: fixed(220), stroke: colors.teal, weight: 6 }),
        ],
      ),
      ...children,
      text("CLO 2 Secure Comments | PHP, Apache, MySQL, Docker", {
        name: "footer",
        width: fill,
        height: hug,
        style: smallStyle,
      }),
    ],
  );
}

function bullets(items) {
  return column(
    { name: "bullet-list", width: fill, height: hug, gap: 18 },
    items.map((item, index) =>
      text(`${index + 1}. ${item}`, {
        name: `bullet-${index + 1}`,
        width: wrap(1450),
        height: hug,
        style: bodyStyle,
      }),
    ),
  );
}

function method(label, detail, color = colors.teal) {
  return panel(
    {
      name: `method-${label}`,
      width: fill,
      height: hug,
      padding: { x: 28, y: 22 },
      fill: colors.surface,
      stroke: colors.line,
      borderRadius: 8,
    },
    column(
      { width: fill, height: hug, gap: 10 },
      [
        text(label, { width: fill, height: hug, style: { fontSize: 28, bold: true, color } }),
        text(detail, { width: fill, height: hug, style: { fontSize: 21, color: colors.ink } }),
      ],
    ),
  );
}

addSlide(
  column(
    { name: "cover", width: fill, height: fill, padding: { x: 116, y: 96 }, gap: 42 },
    [
      text("Secure Comments", {
        name: "cover-title",
        width: wrap(1100),
        height: hug,
        style: { fontSize: 96, bold: true, color: colors.tealDark },
      }),
      rule({ name: "cover-rule", width: fixed(360), stroke: colors.teal, weight: 8 }),
      text("Pengamanan aplikasi web dengan HTTPS, hash password, prepared statement, validasi input, CSRF, dan mitigasi XSS.", {
        name: "cover-subtitle",
        width: wrap(1280),
        height: hug,
        style: { fontSize: 34, color: colors.ink },
      }),
      text("Projek CLO 2 Keamanan Sistem", {
        name: "cover-context",
        width: fill,
        height: hug,
        style: { fontSize: 24, color: colors.muted },
      }),
    ],
  ),
);

addSlide(
  shell("Pendahuluan", "Aplikasi demo komentar publik untuk menunjukkan kontrol keamanan web yang bisa diuji langsung.", [
    bullets([
      "Publik dapat membaca komentar tanpa login.",
      "User terdaftar dapat menulis komentar.",
      "Admin dapat melihat monitoring users dan comments.",
      "Demo berjalan lokal dengan Docker Compose.",
    ]),
  ]),
);

addSlide(
  shell("Teori keamanan yang dipakai", "Kontrol dipilih sesuai risiko umum pada aplikasi web sederhana.", [
    grid(
      { name: "method-grid", width: fill, height: fill, columns: [fr(1), fr(1)], rows: [auto, auto, auto], gap: 18 },
      [
        method("HTTPS/TLS", "Mengenkripsi trafik browser ke Apache dengan self-signed certificate RSA 2048-bit."),
        method("Hash + salt", "Password disimpan memakai password_hash() dan diverifikasi dengan password_verify()."),
        method("SQL injection", "Query login, signup, komentar, dan admin memakai PDO prepared statement."),
        method("XSS", "Output database dirender dengan htmlspecialchars() sebelum masuk ke HTML."),
        method("CSRF", "Setiap form POST wajib membawa token acak yang divalidasi server."),
        method("Brute force", "Login gagal diberi delay sekitar 2 detik sebagai mitigasi dasar."),
      ],
    ),
  ]),
);

addSlide(
  shell("Blok diagram aplikasi", "Alur utama: browser masuk lewat HTTPS, PHP memproses request, MySQL menyimpan data.", [
    row(
      { name: "diagram", width: fill, height: hug, gap: 18 },
      [
        method("Browser", "Membuka localhost:8443 dan menerima cookie session aman.", colors.ink),
        text("->", { width: fixed(60), height: hug, style: { fontSize: 44, bold: true, color: colors.teal } }),
        method("Apache + PHP", "Redirect HTTP ke HTTPS, validasi input, CSRF, dan session hardening.", colors.tealDark),
        text("->", { width: fixed(60), height: hug, style: { fontSize: 44, bold: true, color: colors.teal } }),
        method("MySQL", "Menyimpan tabel users dan comments di network Docker kelas C.", colors.ink),
      ],
    ),
    text("Docker network: 192.168.240.0/24 | web: 192.168.240.10 | db: 192.168.240.11", {
      name: "network-note",
      width: fill,
      height: hug,
      style: { fontSize: 24, color: colors.muted },
    }),
  ]),
);

addSlide(
  shell("Metode mitigasi kerentanan", "Setiap risiko punya kontrol teknis yang bisa ditunjukkan saat demo.", [
    bullets([
      "SQL injection: prepared statement memisahkan query dan data.",
      "XSS: komentar di-escape sehingga script tampil sebagai teks.",
      "Buffer overflow/input abuse: panjang input dibatasi di server.",
      "Akses admin: role dicek sebelum menampilkan admin panel.",
      "Session: cookie HttpOnly, Secure, SameSite Strict, dan regenerate ID.",
    ]),
  ]),
);

addSlide(
  shell("Demo uji serangan", "Urutan demo disiapkan agar sesuai rubrik penilaian.", [
    bullets([
      "Buka HTTPS dan inspeksi sertifikat self-signed.",
      "Akses /comment.php tanpa login, lalu login sebagai admin.",
      "Coba payload SQL injection: ' OR '1'='1 pada mode secure.",
      "Kirim komentar <script>alert(1)</script> dan pastikan tidak dieksekusi.",
      "Kirim komentar lebih dari 500 karakter dan POST tanpa CSRF token.",
    ]),
  ]),
);

addSlide(
  shell("Kesimpulan dan saran", "Aplikasi memenuhi kontrol minimum proyek dan masih bisa dikembangkan lebih jauh.", [
    bullets([
      "Kontrol utama sudah aktif: HTTPS, hash+salt, SQLi, XSS, CSRF, input limit, brute force delay.",
      "Dokumentasi mencatat cara menjalankan, arsitektur, metode keamanan, dan skenario uji.",
      "Pengembangan berikutnya: rate limiter berbasis IP, audit log, reset password aman, dan sertifikat CA resmi.",
    ]),
  ]),
);

await mkdir("presentation/output", { recursive: true });
const pptxBlob = await PresentationFile.exportPptx(presentation);
await pptxBlob.save("presentation/output/CLO2_Secure_Comments.pptx");
console.log("Wrote presentation/output/CLO2_Secure_Comments.pptx");
