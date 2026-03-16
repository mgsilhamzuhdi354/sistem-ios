<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
<div style="margin-left:256px;min-height:100vh;background:#f8fafc;">
  <!-- Header -->
  <div style="padding:24px 32px 0;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <div>
        <h1 style="font-size:24px;font-weight:700;color:#1e293b;margin:0;">
          <span class="material-icons" style="font-size:24px;vertical-align:middle;color:#2563eb;">preview</span>
          Preview Import — <?= htmlspecialchars($fileName ?? '') ?>
        </h1>
        <p style="color:#64748b;margin:4px 0 0;font-size:14px;"><?= $sheetCount ?? 0 ?> sheet ditemukan • Periksa data sebelum di-import</p>
      </div>
      <a href="<?= BASE_URL ?>SmartImport" style="padding:8px 16px;background:#f1f5f9;color:#475569;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
        <span class="material-icons" style="font-size:14px;vertical-align:middle;">arrow_back</span> Kembali
      </a>
    </div>
  </div>

  <div style="padding:16px 32px 32px;">
    <!-- Summary Cards -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:20px;">
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #e2e8f0;">
        <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">📄 Sheets</div>
        <div style="font-size:28px;font-weight:700;color:#1e293b;margin-top:4px;"><?= $sheetCount ?? 0 ?></div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #e2e8f0;">
        <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Total Baris</div>
        <div style="font-size:28px;font-weight:700;color:#1e293b;margin-top:4px;"><?= $totalRows ?></div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #dcfce7;">
        <div style="font-size:11px;color:#16a34a;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">✅ Valid (New)</div>
        <div style="font-size:28px;font-weight:700;color:#16a34a;margin-top:4px;"><?= $validRows ?></div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #fef3c7;">
        <div style="font-size:11px;color:#d97706;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">🔄 Update</div>
        <div style="font-size:28px;font-weight:700;color:#d97706;margin-top:4px;"><?= $warnRows ?></div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #fecaca;">
        <div style="font-size:11px;color:#dc2626;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">❌ Error</div>
        <div style="font-size:28px;font-weight:700;color:#dc2626;margin-top:4px;"><?= $errorRows ?></div>
      </div>
    </div>

    <!-- Import Button -->
    <?php if ($validRows > 0 || $warnRows > 0): ?>
    <form method="POST" action="<?= BASE_URL ?>SmartImport/process" style="margin:0 0 20px;">
      <button type="submit" id="btnImport" style="padding:12px 32px;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(22,163,74,0.3);"
        onclick="this.disabled=true;this.innerHTML='⏳ Mengimport semua sheet...';this.form.submit();">
        <span class="material-icons" style="font-size:18px;vertical-align:middle;margin-right:6px;">cloud_download</span>
        Import <?= $totalRows ?> Data dari <?= $sheetCount ?> Sheet
      </button>
    </form>
    <?php endif; ?>

    <!-- Sheet Tabs -->
    <div style="margin-bottom:16px;">
      <div style="display:flex;gap:4px;border-bottom:2px solid #e2e8f0;margin-bottom:0;" id="sheetTabs">
        <?php foreach ($allSheets as $si => $sheetData): ?>
        <button onclick="showSheet(<?= $si ?>)" id="tab-<?= $si ?>"
          style="padding:10px 18px;border:none;background:<?= $si === 0 ? '#fff' : 'transparent' ?>;color:<?= $si === 0 ? '#2563eb' : '#64748b' ?>;font-size:12px;font-weight:<?= $si === 0 ? '700' : '600' ?>;cursor:pointer;border-bottom:2px solid <?= $si === 0 ? '#2563eb' : 'transparent' ?>;margin-bottom:-2px;border-radius:8px 8px 0 0;white-space:nowrap;">
          <?= htmlspecialchars($sheetData['name']) ?>
          <span style="padding:1px 6px;background:<?= $sheetData['validRows'] > 0 ? '#dcfce7' : '#f1f5f9' ?>;color:<?= $sheetData['validRows'] > 0 ? '#16a34a' : '#94a3b8' ?>;border-radius:8px;font-size:10px;font-weight:700;margin-left:4px;"><?= $sheetData['totalRows'] ?></span>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Sheet Content Panels -->
    <?php foreach ($allSheets as $si => $sheetData): ?>
    <div id="sheet-<?= $si ?>" style="display:<?= $si === 0 ? 'block' : 'none' ?>;">

      <!-- Column Mapping for this sheet -->
      <div style="background:#fff;border-radius:12px;padding:14px 18px;margin-bottom:16px;border:1px solid #e2e8f0;">
        <h4 style="font-size:12px;font-weight:600;color:#94a3b8;margin:0 0 8px;text-transform:uppercase;letter-spacing:0.5px;">
          🔗 Column Mapping — <?= htmlspecialchars($sheetData['name']) ?> (<?= $sheetData['totalRows'] ?> rows)
        </h4>
        <div style="display:flex;flex-wrap:wrap;gap:4px;">
          <?php
          $fieldLabels = [
              'first_name'=>'Nama','last_name'=>'Nama2','certificate'=>'Rank','company'=>'Company',
              'vessel'=>'Vessel','imo_number'=>'IMO','flag'=>'Flag','port_of_registry'=>'Port',
              'status'=>'Status','joint_date'=>'Join','finish_contract'=>'Finish','birth_date'=>'Lahir',
              'address'=>'Alamat','phone'=>'Telp','emergency_phone'=>'Emg Phone','emergency_relation'=>'Emg Rel',
              'passport_number'=>'Passport','passport_exp'=>'Pasp Exp','seaman_number'=>'Seaman',
              'seaman_exp'=>'Seam Exp','mcu'=>'MCU','pic'=>'PIC','bank_account'=>'Bank Acc',
              'bank_holder'=>'Bank Name','bank_name'=>'Bank','email'=>'Email','currency'=>'Currency',
              'salary_payroll'=>'Gaji PR','salary_invoice'=>'Gaji INV','note'=>'Note'
          ];
          foreach ($fieldLabels as $field => $label):
              $mapped = isset($sheetData['mapping'][$field]);
          ?>
            <span style="padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600;
              background:<?= $mapped ? '#dcfce7' : '#fef3c7' ?>;
              color:<?= $mapped ? '#166534' : '#d97706' ?>;">
              <?= $mapped ? '✓' : '✗' ?> <?= $label ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Data Table -->
      <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:11px;">
            <thead>
              <tr style="background:#f8fafc;">
                <th style="padding:8px 10px;text-align:center;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">#</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Status</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Nama</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Rank</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Vessel</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Company</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Join</th>
                <th style="padding:8px 10px;text-align:center;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Curr</th>
                <th style="padding:8px 10px;text-align:right;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Salary</th>
                <th style="padding:8px 10px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;white-space:nowrap;font-size:10px;">Info</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $getVal = function($row, $field) use ($sheetData) {
                  $col = $sheetData['mapping'][$field] ?? null;
                  return $col ? trim($row[$col] ?? '') : '';
              };
              $displayed = 0;
              foreach ($sheetData['dataRows'] as $i => $row):
                if ($displayed >= 100) break;
                $displayed++;
                $rowV = $sheetData['validation']['rows'][$i] ?? ['status'=>'ok','errors'=>[],'warnings'=>[],'info'=>[]];
                $bgColor = $rowV['status'] === 'error' ? '#fef2f2' : ($rowV['status'] === 'warning' ? '#fffbeb' : '#fff');
                $firstName = $getVal($row, 'first_name');
                $lastName = $getVal($row, 'last_name');
                $fullName = trim($firstName . ' ' . $lastName);
              ?>
              <tr style="background:<?= $bgColor ?>;border-bottom:1px solid #f1f5f9;">
                <td style="padding:6px 10px;text-align:center;color:#94a3b8;font-size:10px;"><?= $i + 3 ?></td>
                <td style="padding:6px 10px;">
                  <?php if ($rowV['status'] === 'error'): ?>
                    <span style="padding:2px 6px;background:#fecaca;color:#dc2626;border-radius:4px;font-size:9px;font-weight:700;">ERR</span>
                  <?php elseif ($rowV['status'] === 'warning'): ?>
                    <span style="padding:2px 6px;background:#fef3c7;color:#d97706;border-radius:4px;font-size:9px;font-weight:700;">UPD</span>
                  <?php else: ?>
                    <span style="padding:2px 6px;background:#dcfce7;color:#16a34a;border-radius:4px;font-size:9px;font-weight:700;">NEW</span>
                  <?php endif; ?>
                </td>
                <td style="padding:6px 10px;font-weight:600;color:#1e293b;white-space:nowrap;"><?= htmlspecialchars($fullName) ?></td>
                <td style="padding:6px 10px;white-space:nowrap;"><?= htmlspecialchars($getVal($row, 'certificate')) ?></td>
                <td style="padding:6px 10px;white-space:nowrap;"><?= htmlspecialchars($getVal($row, 'vessel')) ?></td>
                <td style="padding:6px 10px;white-space:nowrap;"><?= htmlspecialchars($getVal($row, 'company')) ?></td>
                <td style="padding:6px 10px;white-space:nowrap;"><?= htmlspecialchars($getVal($row, 'joint_date')) ?></td>
                <td style="padding:6px 10px;text-align:center;"><?= htmlspecialchars($getVal($row, 'currency')) ?></td>
                <td style="padding:6px 10px;text-align:right;font-weight:600;"><?= htmlspecialchars($getVal($row, 'salary_payroll')) ?></td>
                <td style="padding:6px 10px;max-width:250px;font-size:10px;">
                  <?php if (!empty($rowV['errors'])): ?>
                    <span style="color:#dc2626;"><?= htmlspecialchars(implode(', ', $rowV['errors'])) ?></span>
                  <?php elseif (!empty($rowV['warnings'])): ?>
                    <span style="color:#d97706;"><?= htmlspecialchars(implode(', ', $rowV['warnings'])) ?></span>
                  <?php elseif (!empty($rowV['info'])): ?>
                    <span style="color:#2563eb;"><?= htmlspecialchars(implode(' · ', $rowV['info'])) ?></span>
                  <?php else: ?>
                    <span style="color:#16a34a;">✓ Siap</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if ($displayed === 0): ?>
              <tr><td colspan="10" style="padding:40px;text-align:center;color:#94a3b8;">Tidak ada data</td></tr>
              <?php endif; ?>
              <?php if (count($sheetData['dataRows']) > 100): ?>
              <tr><td colspan="10" style="padding:12px;text-align:center;color:#94a3b8;font-size:11px;">... dan <?= count($sheetData['dataRows']) - 100 ?> baris lainnya</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

  </div>
</div>

<script>
function showSheet(idx) {
  // Hide all sheets
  document.querySelectorAll('[id^="sheet-"]').forEach(el => el.style.display = 'none');
  // Reset all tabs
  document.querySelectorAll('[id^="tab-"]').forEach(el => {
    el.style.background = 'transparent';
    el.style.color = '#64748b';
    el.style.fontWeight = '600';
    el.style.borderBottomColor = 'transparent';
  });
  // Show selected
  document.getElementById('sheet-' + idx).style.display = 'block';
  var tab = document.getElementById('tab-' + idx);
  tab.style.background = '#fff';
  tab.style.color = '#2563eb';
  tab.style.fontWeight = '700';
  tab.style.borderBottomColor = '#2563eb';
}
</script>
