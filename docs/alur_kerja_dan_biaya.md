# Alur Kerja & Struktur Biaya
## PT Indo Ocean Crew Services

---

## 🔄 Alur Kerja Sistem

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           ALUR KERJA PENUH                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  RECRUITMENT          KONTRAK              OPERASIONAL         PAYROLL      │
│  ──────────           ──────               ───────────         ───────      │
│                                                                              │
│  Job Posting    →    Import Crew     →    Crew Onboard   →   Hitung Gaji   │
│       ↓                  ↓                     ↓                  ↓         │
│  Applicant Apply →  Buat Kontrak    →    Monitoring      →   Potong Pajak  │
│       ↓                  ↓                     ↓                  ↓         │
│  Screening      →    Assign Kapal   →    Sign Off/Renew  →   Pembayaran    │
│       ↓                  ↓                                       ↓         │
│  Interview      →    Sign On                              →   Laporan      │
│       ↓                                                                     │
│  Medical Check                                                              │
│       ↓                                                                     │
│  ✅ APPROVED                                                                 │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 💰 Sumber Pendapatan (Revenue)

| No | Sumber Pendapatan | Keterangan | Estimasi |
|----|-------------------|------------|----------|
| 1 | **Management Fee** | Fee bulanan dari Principal/Client per crew | $50-150/crew/bulan |
| 2 | **Recruitment Fee** | Fee sekali bayar per penempatan crew | $200-500/penempatan |
| 3 | **Training Fee** | Biaya pelatihan & sertifikasi (opsional) | Vary |

---

## 📊 Struktur Biaya (Cost)

| No | Komponen Biaya | Persentase | Keterangan |
|----|----------------|------------|------------|
| 1 | **Gaji Crew** | 70-80% | Gaji pokok + tunjangan (dibayar ke crew) |
| 2 | **Pajak PPh21** | 5-6% | Dipotong dari gaji, disetorkan ke negara |
| 3 | **Asuransi** | 2-3% | Asuransi kesehatan & kecelakaan kerja |
| 4 | **Medical Check** | 1-2% | Biaya pemeriksaan kesehatan |
| 5 | **Operasional** | 5-10% | Gaji staff, kantor, sistem IT, dll |

---

## 💵 Flow Uang

```
┌─────────────┐         ┌─────────────────────┐         ┌─────────────┐
│   CLIENT/   │ ──────→ │   PT INDO OCEAN     │ ──────→ │    CREW     │
│  PRINCIPAL  │  Bayar  │   CREW SERVICES     │  Gaji   │  (Pelaut)   │
└─────────────┘         └─────────────────────┘         └─────────────┘
                                  │
                                  │ Bayar
                                  ↓
                        ┌─────────────────────┐
                        │ • Pajak (Pemerintah)│
                        │ • Asuransi          │
                        │ • Vendor/Supplier   │
                        │ • Operasional       │
                        └─────────────────────┘
```

**Penjelasan:**
1. **Client/Principal** (perusahaan kapal) membayar PT Indo Ocean
2. **PT Indo Ocean** mengelola crew, mengurus kontrak, dan membayarkan gaji
3. **Crew** menerima gaji bersih (sudah dipotong pajak & potongan lain)

---

## 📈 Contoh Perhitungan

### Untuk 1 Crew Captain dengan Gaji $2,000/bulan:

| Item | Jumlah | Keterangan |
|------|--------|------------|
| Revenue dari Client | $2,200 | Gaji + Management Fee ($200) |
| (-) Gaji Crew | $2,000 | Dibayarkan ke crew |
| (-) Pajak PPh21 (5%) | $100 | Disetorkan ke pemerintah |
| (-) Asuransi (2%) | $40 | Premi asuransi |
| **Gross Profit** | **$60** | Margin per crew/bulan |

*Note: Angka ilustratif, aktual tergantung kontrak dengan client*

---

## ❓ FAQ untuk Bos

**Q: Dari mana PT Indo Ocean dapat untung?**
> Dari **Management Fee** yang dibayar client di atas gaji crew, dan dari **efisiensi operasional** dalam mengelola banyak crew sekaligus.

**Q: Siapa yang bayar gaji crew?**
> Client membayar ke PT Indo Ocean, lalu PT Indo Ocean yang membayarkan ke crew setelah dipotong pajak.

**Q: Kenapa perlu sistem ERP?**
> Untuk mengelola ratusan crew, kontrak, gaji, dan dokumen secara efisien. Tanpa sistem, rentan salah hitung dan tidak tertib administrasi.
