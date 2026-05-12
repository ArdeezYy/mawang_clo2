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
      text("CLO 2 Non-Secure Comments | PHP, Apache, MySQL, Docker", {
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
      text("Skenario aplikasi sebelum pengamanan: password plaintext, SQL injection, XSS, tanpa CSRF, dan tanpa batas input.", {
        name: "cover-subtitle",
        width: wrap(1280),
        height: hug,
        style: { fontSize: 34, color: colors.ink },
      }),
      text("Skenario non-secure untuk pembanding branch secure-login", {
        name: "cover-context",
        width: fill,
        height: hug,
        style: { fontSize: 24, color: colors.muted },
      }),
    ],
  ),
);

addSlide(
  shell("Pendahuluan", "Aplikasi komentar publik untuk menunjukkan kondisi sebelum kontrol keamanan diterapkan.", [
    bullets([
      "Publik dapat membaca komentar tanpa login.",
      "User terdaftar dapat menulis komentar.",
      "Admin dapat melihat monitoring users dan comments.",
      "Berjalan lokal dengan Docker Compose dan identitas Nama+NIM.",
    ]),
  ]),
);

addSlide(
  shell("Kontrol yang dimatikan", "Branch ini sengaja dibuat rentan untuk menunjukkan risiko aplikasi web.", [
    grid(
      { name: "method-grid", width: fill, height: fill, columns: [fr(1), fr(1)], rows: [auto, auto, auto], gap: 18 },
      [
        method("Password plaintext", "Password admin dan user disimpan apa adanya tanpa hash dan salt."),
        method("Raw SQL", "Login menyusun query dari input user tanpa prepared statement."),
        method("No CSRF", "Form POST tetap diterima meskipun tidak membawa token CSRF."),
        method("Raw output", "Komentar dirender langsung tanpa htmlspecialchars()."),
        method("No input limit", "Komentar tidak dibatasi 500 karakter di server maupun client."),
        method("No role check", "Admin panel tidak membatasi akses berdasarkan role."),
      ],
    ),
  ]),
);

addSlide(
  shell("Blok diagram aplikasi", "Alur utama: browser masuk ke Apache/PHP lalu MySQL menyimpan data.", [
    row(
      { name: "diagram", width: fill, height: hug, gap: 18 },
      [
        method("Browser", "Membuka localhost:8080 atau localhost:8443.", colors.ink),
        text("->", { width: fixed(60), height: hug, style: { fontSize: 44, bold: true, color: colors.teal } }),
        method("Apache + PHP", "Tidak redirect HTTP, login raw SQL, output raw, dan tanpa batas komentar.", colors.tealDark),
        text("->", { width: fixed(60), height: hug, style: { fontSize: 44, bold: true, color: colors.teal } }),
        method("MySQL", "Menyimpan tabel users dan comments di network Docker kelas C.", colors.ink),
      ],
    ),
    text("Docker network: 192.168.219.0/24 | web: 192.168.219.219 | db: 192.168.219.220", {
      name: "network-note",
      width: fill,
      height: hug,
      style: { fontSize: 24, color: colors.muted },
    }),
  ]),
);

addSlide(
  shell("Bukti non-secure", "Setiap demo menunjukkan kontrol yang belum aktif.", [
    bullets([
      "Password admin tampil plaintext sebagai Admin@240! di admin panel.",
      "Payload SQL injection di username berhasil melewati login.",
      "Payload XSS pada komentar dieksekusi browser.",
      "Komentar lebih dari 500 karakter tetap tersimpan.",
    ]),
  ]),
);

addSlide(
  shell("Demo uji rentan", "Urutan demo difokuskan pada kontrol yang dimatikan.", [
    bullets([
      "Buka HTTP dan tunjukkan tidak ada redirect otomatis.",
      "Login dengan payload username ' OR '1'='1.",
      "Buka /admin.php dan tunjukkan password plaintext.",
      "Kirim komentar XSS dan komentar lebih dari 500 karakter.",
      "Bandingkan hasilnya dengan branch secure-login.",
    ]),
  ]),
);

addSlide(
  shell("Kesimpulan dan saran", "Branch ini menunjukkan kondisi sebelum aplikasi diamankan.", [
    bullets([
      "Risiko utama terlihat: plaintext password, SQL injection, XSS, CSRF, input berlebih, dan role bypass.",
      "Branch secure-login menjadi pembanding untuk versi yang sudah diamankan.",
      "Perbaikan utama: hash+salt, prepared statement, escaping, CSRF token, input limit, dan role check.",
    ]),
  ]),
);

await mkdir("presentation/output", { recursive: true });
const pptxBlob = await PresentationFile.exportPptx(presentation);
await pptxBlob.save("presentation/output/CLO2_Secure_Comments.pptx");
console.log("Wrote presentation/output/CLO2_Secure_Comments.pptx");
