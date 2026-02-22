<?php
/**
 * Premium Payslip Email Template
 * PT Indo Ocean Crew Services
 * 
 * Variables available (from extract):
 *   $name   - Crew member name
 *   $period - Payroll period array (period_name, period_month, period_year)
 *   $item   - Payroll item array (all salary fields)
 */

// Prepare formatted values
$currency     = $item['currency_code'] ?? 'IDR';
$basicSalary  = number_format($item['basic_salary'] ?? 0, 0, ',', '.');
$overtime     = number_format($item['overtime'] ?? 0, 0, ',', '.');
$leavePay     = number_format($item['leave_pay'] ?? 0, 0, ',', '.');
$bonus        = number_format($item['bonus'] ?? 0, 0, ',', '.');
$otherAllow   = number_format($item['other_allowance'] ?? 0, 0, ',', '.');
$grossSalary  = number_format($item['gross_salary'] ?? 0, 0, ',', '.');
$totalDeduct  = number_format($item['total_deductions'] ?? 0, 0, ',', '.');
$taxAmount    = number_format($item['tax_amount'] ?? 0, 0, ',', '.');
$taxRate      = $item['tax_rate'] ?? 0;
$netSalary    = number_format($item['net_salary'] ?? 0, 0, ',', '.');
$rankName     = $item['rank_name'] ?? '-';
$vesselName   = $item['vessel_name'] ?? '-';

// Month names in Indonesian
$monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$periodMonth = $monthNames[$period['period_month'] ?? 1] ?? '';
$periodYear  = $period['period_year'] ?? date('Y');
$periodText  = $periodMonth . ' ' . $periodYear;

// Deduction breakdown
$insurance   = number_format($item['insurance'] ?? 0, 0, ',', '.');
$adminFee    = number_format($item['admin_bank_fee'] ?? 0, 0, ',', '.');
$otherDeduct = number_format($item['other_deductions'] ?? 0, 0, ',', '.');
$reimbursement = number_format($item['reimbursement'] ?? 0, 0, ',', '.');
$loans       = number_format($item['loans'] ?? 0, 0, ',', '.');

// Current year
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Slip Gaji - <?= htmlspecialchars($name) ?> - <?= $periodText ?></title>
</head>
<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-text-size-adjust:100%;">

<!-- Wrapper Table -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;">
<tr><td align="center" style="padding:24px 12px;">

<!-- Main Card -->
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);">

    <!-- Header Banner -->
    <tr>
        <td style="background: linear-gradient(135deg, #0A2463 0%, #1B3A8C 50%, #0A2463 100%); padding:0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:32px 40px 12px; text-align:center;">
                        <!-- Ocean wave decoration -->
                        <div style="font-size:28px; margin-bottom:6px;">🌊</div>
                        <h1 style="margin:0; font-size:22px; font-weight:700; color:#D4AF37; letter-spacing:1px;">PT INDO OCEAN</h1>
                        <p style="margin:4px 0 0; font-size:12px; color:rgba(212,175,55,0.7); letter-spacing:3px; text-transform:uppercase;">Crew Services</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 40px 28px; text-align:center;">
                        <div style="display:inline-block; background:rgba(255,255,255,0.1); border-radius:12px; padding:12px 28px; border:1px solid rgba(212,175,55,0.3);">
                            <p style="margin:0; font-size:11px; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:2px;">Slip Gaji / Payslip</p>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:700; color:#ffffff;"><?= $periodText ?></p>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Greeting -->
    <tr>
        <td style="padding:28px 40px 0;">
            <p style="margin:0; font-size:15px; color:#64748b;">Yth.</p>
            <p style="margin:4px 0 16px; font-size:20px; font-weight:700; color:#0f172a;"><?= htmlspecialchars($name) ?></p>
            
            <!-- Crew info badges -->
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding-right:8px;">
                        <span style="display:inline-block; background:#EEF2FF; color:#4338CA; font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px;">
                            ⚓ <?= htmlspecialchars($rankName) ?>
                        </span>
                    </td>
                    <td>
                        <span style="display:inline-block; background:#F0FDF4; color:#15803D; font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px;">
                            🚢 <?= htmlspecialchars($vesselName) ?>
                        </span>
                    </td>
                </tr>
            </table>

            <p style="margin:20px 0 0; font-size:14px; color:#64748b; line-height:1.6;">
                Berikut adalah rincian slip gaji Anda untuk periode <strong style="color:#0f172a;"><?= $periodText ?></strong>.
            </p>
        </td>
    </tr>

    <!-- Divider -->
    <tr>
        <td style="padding:20px 40px;">
            <div style="height:1px; background:linear-gradient(to right, transparent, #e2e8f0, transparent);"></div>
        </td>
    </tr>

    <!-- NET SALARY HIGHLIGHT -->
    <tr>
        <td style="padding:0 40px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg, #059669 0%, #047857 100%); border-radius:12px; overflow:hidden;">
                <tr>
                    <td style="padding:20px 24px; text-align:center;">
                        <p style="margin:0; font-size:11px; color:rgba(255,255,255,0.8); text-transform:uppercase; letter-spacing:2px;">Take-Home Pay</p>
                        <p style="margin:8px 0 0; font-size:28px; font-weight:800; color:#ffffff; letter-spacing:0.5px;">
                            Rp <?= $netSalary ?>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- INCOME SECTION -->
    <tr>
        <td style="padding:28px 40px 0;">
            <p style="margin:0 0 12px; font-size:13px; font-weight:700; color:#059669; text-transform:uppercase; letter-spacing:1px;">
                💰 Pendapatan
            </p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                <!-- Gaji Pokok -->
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Gaji Pokok</td>
                    <td style="padding:12px 16px; font-size:14px; color:#0f172a; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">Rp <?= $basicSalary ?></td>
                </tr>
                <?php if (($item['overtime'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Lembur</td>
                    <td style="padding:12px 16px; font-size:14px; color:#0f172a; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">Rp <?= $overtime ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['leave_pay'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Cuti</td>
                    <td style="padding:12px 16px; font-size:14px; color:#0f172a; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">Rp <?= $leavePay ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['bonus'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Bonus</td>
                    <td style="padding:12px 16px; font-size:14px; color:#0f172a; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">Rp <?= $bonus ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['other_allowance'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Tunjangan Lain</td>
                    <td style="padding:12px 16px; font-size:14px; color:#0f172a; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">Rp <?= $otherAllow ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['reimbursement'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Reimbursement</td>
                    <td style="padding:12px 16px; font-size:14px; color:#0f172a; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">Rp <?= $reimbursement ?></td>
                </tr>
                <?php endif; ?>
                <!-- Gross Total -->
                <tr style="background:#f0fdf4;">
                    <td style="padding:14px 16px; font-size:14px; color:#059669; font-weight:700;">Total Pendapatan (Bruto)</td>
                    <td style="padding:14px 16px; font-size:15px; color:#059669; text-align:right; font-weight:800;">Rp <?= $grossSalary ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- DEDUCTIONS SECTION -->
    <tr>
        <td style="padding:24px 40px 0;">
            <p style="margin:0 0 12px; font-size:13px; font-weight:700; color:#DC2626; text-transform:uppercase; letter-spacing:1px;">
                📋 Potongan
            </p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                <?php if (($item['insurance'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Asuransi</td>
                    <td style="padding:12px 16px; font-size:14px; color:#DC2626; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">- Rp <?= $insurance ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['admin_bank_fee'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Admin Bank</td>
                    <td style="padding:12px 16px; font-size:14px; color:#DC2626; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">- Rp <?= $adminFee ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['loans'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Pinjaman</td>
                    <td style="padding:12px 16px; font-size:14px; color:#DC2626; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">- Rp <?= $loans ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($item['other_deductions'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">Potongan Lain</td>
                    <td style="padding:12px 16px; font-size:14px; color:#DC2626; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">- Rp <?= $otherDeduct ?></td>
                </tr>
                <?php endif; ?>
                <!-- PPH Tax -->
                <?php if (($item['tax_amount'] ?? 0) > 0): ?>
                <tr>
                    <td style="padding:12px 16px; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9;">PPH 21 (<?= $taxRate ?>%)</td>
                    <td style="padding:12px 16px; font-size:14px; color:#DC2626; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;">- Rp <?= $taxAmount ?></td>
                </tr>
                <?php endif; ?>
                <!-- Deductions Total -->
                <tr style="background:#FEF2F2;">
                    <td style="padding:14px 16px; font-size:14px; color:#DC2626; font-weight:700;">Total Potongan</td>
                    <td style="padding:14px 16px; font-size:15px; color:#DC2626; text-align:right; font-weight:800;">- Rp <?= $totalDeduct ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- SUMMARY BOX -->
    <tr>
        <td style="padding:24px 40px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:2px solid #0A2463; border-radius:10px; overflow:hidden;">
                <tr style="background:#0A2463;">
                    <td style="padding:16px 20px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size:14px; color:rgba(255,255,255,0.8); font-weight:600;">Gaji Bruto</td>
                                <td style="font-size:14px; color:#ffffff; text-align:right; font-weight:600;">Rp <?= $grossSalary ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="background:#1B3A8C;">
                    <td style="padding:16px 20px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size:14px; color:rgba(255,255,255,0.8); font-weight:600;">Total Potongan + Pajak</td>
                                <td style="font-size:14px; color:#FCA5A5; text-align:right; font-weight:600;">- Rp <?= number_format(($item['total_deductions'] ?? 0) + ($item['tax_amount'] ?? 0), 0, ',', '.') ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="background:linear-gradient(135deg, #D4AF37 0%, #C49B2F 100%);">
                    <td style="padding:20px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size:16px; color:#0A2463; font-weight:800;">NET TAKE-HOME PAY</td>
                                <td style="font-size:22px; color:#0A2463; text-align:right; font-weight:900;">Rp <?= $netSalary ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Thank you message -->
    <tr>
        <td style="padding:28px 40px 0;">
            <div style="background:#FFFBEB; border-radius:10px; padding:20px; border-left:4px solid #D4AF37;">
                <p style="margin:0; font-size:14px; color:#92400E; line-height:1.6;">
                    💛 <strong>Terima kasih atas kerja keras dan dedikasi Anda.</strong><br>
                    <span style="font-size:13px; color:#A16207;">Semoga selalu sehat dan semangat dalam menjalankan tugas!</span>
                </p>
            </div>
        </td>
    </tr>

    <!-- Info notice -->
    <tr>
        <td style="padding:20px 40px;">
            <p style="margin:0; font-size:12px; color:#94a3b8; text-align:center; line-height:1.5;">
                📩 Dokumen ini dihasilkan secara otomatis oleh sistem ERP PT Indo Ocean.<br>
                Jika ada pertanyaan mengenai slip gaji, silakan hubungi bagian HRD.
            </p>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f8fafc; padding:24px 40px; border-top:1px solid #e2e8f0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="text-align:center;">
                        <p style="margin:0 0 4px; font-size:13px; font-weight:700; color:#0A2463;">PT Indo Ocean Crew Services</p>
                        <p style="margin:0 0 8px; font-size:11px; color:#94a3b8;">Maritime Crew Management</p>
                        <div style="height:1px; background:#e2e8f0; margin:8px auto; max-width:100px;"></div>
                        <p style="margin:8px 0 0; font-size:11px; color:#cbd5e1;">
                            &copy; <?= $currentYear ?> PT Indo Ocean Crew Services. All rights reserved.<br>
                            Email ini bersifat rahasia dan hanya ditujukan untuk penerima.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
<!-- End Main Card -->

</td></tr>
</table>
<!-- End Wrapper -->

</body>
</html>
