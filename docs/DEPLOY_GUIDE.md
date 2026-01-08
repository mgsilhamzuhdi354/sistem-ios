# 🚀 Panduan Auto-Deploy ke Domainesia via GitHub Actions

Panduan lengkap untuk setup auto-deploy website PT Indoocean ke hosting Domainesia.

---

## 📋 Daftar Isi

1. [Prasyarat](#-prasyarat)
2. [Langkah 1: Inisialisasi Git Repository](#-langkah-1-inisialisasi-git-repository)
3. [Langkah 2: Buat GitHub Repository](#-langkah-2-buat-github-repository)
4. [Langkah 3: Dapatkan Credential FTP dari Domainesia](#-langkah-3-dapatkan-credential-ftp-dari-domainesia)
5. [Langkah 4: Setup GitHub Secrets](#-langkah-4-setup-github-secrets)
6. [Langkah 5: Push dan Deploy](#-langkah-5-push-dan-deploy)
7. [Verifikasi Deployment](#-verifikasi-deployment)
8. [Troubleshooting](#-troubleshooting)

---

## 📌 Prasyarat

- [x] Akun GitHub (gratis di [github.com](https://github.com))
- [x] Git terinstall di komputer ([Download Git](https://git-scm.com/downloads))
- [x] Akun hosting Domainesia dengan akses FTP
- [x] VS Code atau terminal

---

## 📂 Langkah 1: Inisialisasi Git Repository

Buka terminal/PowerShell di folder project:

```powershell
cd c:\xampp\htdocs\PT_indoocean

# Inisialisasi Git repository
git init

# Tambahkan semua file
git add .

# Commit pertama
git commit -m "Initial commit - PT Indoocean Website"
```

---

## 🐙 Langkah 2: Buat GitHub Repository

### Option A: Via GitHub Website

1. Buka [github.com/new](https://github.com/new)
2. Isi nama repository: `PT_indoocean` atau nama lain
3. Pilih **Private** (disarankan untuk keamanan)
4. Klik **Create repository**
5. Ikuti instruksi "push an existing repository":

```powershell
git remote add origin https://github.com/USERNAME/PT_indoocean.git
git branch -M main
git push -u origin main
```

### Option B: Via GitHub CLI

```powershell
# Install GitHub CLI jika belum
# Download di: https://cli.github.com/

gh repo create PT_indoocean --private --source=. --push
```

---

## 🔑 Langkah 3: Dapatkan Credential FTP dari Domainesia

### 3.1 Login ke cPanel Domainesia

1. Buka [https://my.domainesia.com](https://my.domainesia.com)
2. Login dengan akun Anda
3. Pilih hosting yang akan digunakan
4. Klik **Kelola Hosting** → **cPanel**

### 3.2 Buat/Cek FTP Account

1. Di cPanel, cari **FTP Accounts**
2. Buat FTP account baru atau gunakan yang sudah ada
3. Catat informasi berikut:

| Info | Contoh | Keterangan |
|------|--------|------------|
| **FTP Server** | `ftp.domainanda.com` atau `domainanda.com` | Host/Server FTP |
| **FTP Username** | `deploy@domainanda.com` | Username FTP lengkap |
| **FTP Password** | `********` | Password FTP |
| **FTP Port** | `21` | Port standar FTP |
| **Directory** | `/public_html/` | Folder tujuan deploy |

### 3.3 Struktur Folder Umum Domainesia

```
/home/username/
├── public_html/              ← Main website (FTP_PATH: /public_html/)
│   ├── recruitment/          ← Recruitment system (FTP_PATH_RECRUITMENT)
│   └── erp/                  ← ERP system (FTP_PATH_ERP)
└── ...
```

---

## 🔐 Langkah 4: Setup GitHub Secrets

### 4.1 Buka Repository Settings

1. Buka repository GitHub Anda
2. Klik tab **Settings**
3. Di sidebar kiri, klik **Secrets and variables** → **Actions**
4. Klik **New repository secret**

### 4.2 Tambahkan Secrets Berikut

Tambahkan satu per satu:

| Secret Name | Value | Contoh |
|-------------|-------|--------|
| `FTP_SERVER` | Hostname FTP | `ftp.domainanda.com` |
| `FTP_USERNAME` | Username FTP | `deploy@domainanda.com` |
| `FTP_PASSWORD` | Password FTP | `YourSecurePassword123` |
| `FTP_PATH` | Path folder tujuan | `/public_html/` |

**Opsional** (jika deploy terpisah):

| Secret Name | Value |
|-------------|-------|
| `FTP_PATH_RECRUITMENT` | `/public_html/recruitment/` |
| `FTP_PATH_ERP` | `/public_html/erp/` |

### 4.3 Screenshot Panduan

```
GitHub → Settings → Secrets → Actions
┌─────────────────────────────────────────────────────┐
│  Repository secrets                                 │
├─────────────────────────────────────────────────────┤
│  🔒 FTP_SERVER        ···········                   │
│  🔒 FTP_USERNAME      ···········                   │
│  🔒 FTP_PASSWORD      ···········                   │
│  🔒 FTP_PATH          ···········                   │
│                                                     │
│  [+ New repository secret]                          │
└─────────────────────────────────────────────────────┘
```

---

## 🚀 Langkah 5: Push dan Deploy

Setelah setup secrets, setiap kali Anda push ke branch `main`:

```powershell
# Buat perubahan pada file
# ...

# Add dan commit
git add .
git commit -m "Update: deskripsi perubahan"

# Push ke GitHub (ini akan trigger auto-deploy!)
git push origin main
```

### Workflow yang Tersedia

| Workflow | File | Trigger |
|----------|------|---------|
| **Deploy All** | `.github/workflows/deploy.yml` | Push ke main/master |
| **Deploy Recruitment** | `.github/workflows/deploy-recruitment.yml` | Push dengan perubahan di `recruitment/` |
| **Deploy ERP** | `.github/workflows/deploy-erp.yml` | Push dengan perubahan di `erp sistem/` |

### Manual Trigger

Anda juga bisa trigger deploy manual:

1. Buka tab **Actions** di GitHub repo
2. Pilih workflow yang diinginkan
3. Klik **Run workflow**
4. Pilih branch dan klik **Run workflow**

---

## ✅ Verifikasi Deployment

### Cek Status di GitHub Actions

1. Buka repository → tab **Actions**
2. Lihat status workflow terbaru:
   - ✅ **Hijau**: Deploy berhasil
   - ❌ **Merah**: Ada error (klik untuk lihat detail)
   - 🟡 **Kuning**: Sedang berjalan

### Cek Website

Buka website Anda di browser untuk verifikasi perubahan sudah live.

---

## 🔧 Troubleshooting

### Error: "Login authentication failed"

**Penyebab**: Credential FTP salah

**Solusi**:
1. Cek ulang FTP_USERNAME (harus lengkap dengan @domain)
2. Cek ulang FTP_PASSWORD
3. Coba login manual via FileZilla untuk verifikasi

### Error: "Connection timed out"

**Penyebab**: Server tidak bisa diakses atau firewall blocking

**Solusi**:
1. Cek FTP_SERVER sudah benar
2. Pastikan FTP service aktif di hosting
3. Coba gunakan `domainanda.com` tanpa `ftp.` prefix

### Error: "Permission denied"

**Penyebab**: FTP user tidak punya akses ke folder tujuan

**Solusi**:
1. Cek FTP_PATH sudah benar (harus diawali dan diakhiri `/`)
2. Pastikan FTP user punya akses ke folder tersebut
3. Buat folder terlebih dahulu jika belum ada

### Deploy Lambat / Timeout

**Penyebab**: Terlalu banyak file

**Solusi**:
1. Tambahkan file/folder ke exclude list di workflow
2. Gunakan `.gitignore` untuk file yang tidak perlu di-track

---

## 📝 Tips Tambahan

### 1. Gunakan Branch untuk Development

```powershell
# Buat branch development
git checkout -b development

# Kerja di development branch
# ...

# Merge ke main saat siap deploy
git checkout main
git merge development
git push origin main  # Auto-deploy!
```

### 2. Deploy ke Staging Dulu

Buat workflow tambahan untuk deploy ke subdomain staging sebelum production.

### 3. Backup Sebelum Deploy

Domainesia menyediakan fitur backup di cPanel. Aktifkan backup otomatis atau lakukan backup manual sebelum deploy besar.

---

## 📞 Butuh Bantuan?

- **Domainesia Support**: [domainesia.com/kontak](https://www.domainesia.com/kontak)
- **GitHub Actions Docs**: [docs.github.com/actions](https://docs.github.com/en/actions)

---

*Terakhir diupdate: Januari 2026*
