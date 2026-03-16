<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>
<div style="margin-left:256px;min-height:100vh;background:#f8fafc;">
  <div style="padding:24px 32px 0;">
    <div style="display:flex;align-items:center;justify-content:space-between;">
      <div>
        <h1 style="font-size:24px;font-weight:700;color:#1e293b;margin:0;">
          <span class="material-icons" style="font-size:24px;vertical-align:middle;color:#16a34a;">check_circle</span>
          Import Selesai
        </h1>
        <p style="color:#64748b;margin:4px 0 0;font-size:14px;">Data dari <?= $totalSheets ?? 0 ?> sheet berhasil diproses</p>
      </div>
      <a href="<?= BASE_URL ?>SmartImport" style="padding:8px 16px;background:#2563eb;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
        <span class="material-icons" style="font-size:14px;vertical-align:middle;">upload_file</span> Import Lagi
      </a>
    </div>
  </div>

  <div style="padding:16px 32px 32px;">
    <!-- Grand Total Summary -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px;">
      <div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:12px;padding:16px 20px;color:#fff;">
        <div style="font-size:11px;font-weight:600;opacity:0.8;">✅ BARU</div>
        <div style="font-size:32px;font-weight:700;margin-top:4px;"><?= $results['success'] ?? 0 ?></div>
        <div style="font-size:11px;opacity:0.7;">Crew baru dibuat</div>
      </div>
      <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;padding:16px 20px;color:#fff;">
        <div style="font-size:11px;font-weight:600;opacity:0.8;">🔄 UPDATE</div>
        <div style="font-size:32px;font-weight:700;margin-top:4px;"><?= $results['updated'] ?? 0 ?></div>
        <div style="font-size:11px;opacity:0.7;">Crew data diupdate</div>
      </div>
      <div style="background:linear-gradient(135deg,#94a3b8,#64748b);border-radius:12px;padding:16px 20px;color:#fff;">
        <div style="font-size:11px;font-weight:600;opacity:0.8;">⏭️ SKIP</div>
        <div style="font-size:32px;font-weight:700;margin-top:4px;"><?= $results['skipped'] ?? 0 ?></div>
        <div style="font-size:11px;opacity:0.7;">Data sama, dilewati</div>
      </div>
      <div style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px;padding:16px 20px;color:#fff;">
        <div style="font-size:11px;font-weight:600;opacity:0.8;">📋 KONTRAK</div>
        <div style="font-size:32px;font-weight:700;margin-top:4px;"><?= $results['created_contracts'] ?? 0 ?></div>
        <div style="font-size:11px;opacity:0.7;">Kontrak baru</div>
      </div>
      <div style="background:linear-gradient(135deg,#ec4899,#db2777);border-radius:12px;padding:16px 20px;color:#fff;">
        <div style="font-size:11px;font-weight:600;opacity:0.8;">❌ ERROR</div>
        <div style="font-size:32px;font-weight:700;margin-top:4px;"><?= count($results['errors'] ?? []) ?></div>
        <div style="font-size:11px;opacity:0.7;">Gagal diproses</div>
      </div>
    </div>

    <!-- Auto-Created Entities -->
    <?php if (!empty($results['created_vessels']) || !empty($results['created_clients'])): ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
      <?php if (!empty($results['created_vessels'])): ?>
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #e2e8f0;">
        <h3 style="font-size:13px;font-weight:600;color:#1e293b;margin:0 0 10px;">
          🚢 Vessel Baru Dibuat (<?= count($results['created_vessels']) ?>)
        </h3>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
          <?php foreach ($results['created_vessels'] as $v): ?>
          <a href="<?= BASE_URL ?>vessels" style="padding:4px 10px;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;">
            <?= htmlspecialchars($v) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($results['created_clients'])): ?>
      <div style="background:#fff;border-radius:12px;padding:16px 20px;border:1px solid #e2e8f0;">
        <h3 style="font-size:13px;font-weight:600;color:#1e293b;margin:0 0 10px;">
          🏢 Client Baru Dibuat (<?= count($results['created_clients']) ?>)
        </h3>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
          <?php foreach ($results['created_clients'] as $c): ?>
          <a href="<?= BASE_URL ?>clients" style="padding:4px 10px;background:#fef3c7;color:#d97706;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;">
            <?= htmlspecialchars($c) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Per-Sheet Results -->
    <?php if (!empty($sheetResults)): ?>
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;overflow:hidden;">
      <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
        <h3 style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">📊 Hasil Per Sheet</h3>
      </div>
      <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <thead>
          <tr style="background:#f8fafc;">
            <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;border-bottom:1px solid #e2e8f0;">Sheet</th>
            <th style="padding:10px 16px;text-align:center;color:#16a34a;font-weight:600;border-bottom:1px solid #e2e8f0;">Baru</th>
            <th style="padding:10px 16px;text-align:center;color:#2563eb;font-weight:600;border-bottom:1px solid #e2e8f0;">Update</th>
            <th style="padding:10px 16px;text-align:center;color:#94a3b8;font-weight:600;border-bottom:1px solid #e2e8f0;">Skip</th>
            <th style="padding:10px 16px;text-align:center;color:#dc2626;font-weight:600;border-bottom:1px solid #e2e8f0;">Error</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sheetResults as $sr): ?>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:10px 16px;font-weight:600;color:#1e293b;"><?= htmlspecialchars($sr['sheet']) ?></td>
            <td style="padding:10px 16px;text-align:center;">
              <?php if ($sr['success'] > 0): ?><span style="padding:2px 8px;background:#dcfce7;color:#16a34a;border-radius:6px;font-weight:700;"><?= $sr['success'] ?></span><?php else: ?>-<?php endif; ?>
            </td>
            <td style="padding:10px 16px;text-align:center;">
              <?php if ($sr['updated'] > 0): ?><span style="padding:2px 8px;background:#dbeafe;color:#2563eb;border-radius:6px;font-weight:700;"><?= $sr['updated'] ?></span><?php else: ?>-<?php endif; ?>
            </td>
            <td style="padding:10px 16px;text-align:center;">
              <?php if ($sr['skipped'] > 0): ?><span style="padding:2px 8px;background:#f1f5f9;color:#64748b;border-radius:6px;font-weight:700;"><?= $sr['skipped'] ?></span><?php else: ?>-<?php endif; ?>
            </td>
            <td style="padding:10px 16px;text-align:center;">
              <?php if (count($sr['errors']) > 0): ?><span style="padding:2px 8px;background:#fecaca;color:#dc2626;border-radius:6px;font-weight:700;"><?= count($sr['errors']) ?></span><?php else: ?>-<?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <!-- Created Crews List -->
    <?php if (!empty($results['created_crews'])): ?>
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px;overflow:hidden;">
      <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;">
        <h3 style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">👥 Crew Baru (<?= count($results['created_crews']) ?>)</h3>
      </div>
      <div style="padding:12px 20px;display:flex;flex-wrap:wrap;gap:8px;">
        <?php foreach (array_slice($results['created_crews'], 0, 50) as $c): ?>
        <a href="<?= BASE_URL ?>crews/<?= $c['id'] ?>" style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f0fdf4;color:#166534;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #dcfce7;">
          <span class="material-icons" style="font-size:14px;">person_add</span>
          <?= htmlspecialchars($c['name']) ?>
          <?php if (!empty($c['rank'])): ?><span style="font-weight:400;color:#64748b;font-size:10px;">(<?= htmlspecialchars($c['rank']) ?>)</span><?php endif; ?>
        </a>
        <?php endforeach; ?>
        <?php if (count($results['created_crews']) > 50): ?>
        <span style="padding:6px 12px;color:#94a3b8;font-size:12px;">+<?= count($results['created_crews']) - 50 ?> lainnya</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Errors -->
    <?php if (!empty($results['errors'])): ?>
    <div style="background:#fff;border-radius:12px;border:1px solid #fecaca;overflow:hidden;">
      <div style="padding:16px 20px;border-bottom:1px solid #fecaca;background:#fef2f2;">
        <h3 style="font-size:14px;font-weight:600;color:#dc2626;margin:0;">⚠️ Error Log (<?= count($results['errors']) ?>)</h3>
      </div>
      <div style="padding:12px 20px;max-height:300px;overflow-y:auto;">
        <?php foreach ($results['errors'] as $err): ?>
        <div style="padding:4px 0;font-size:12px;color:#991b1b;border-bottom:1px solid #fef2f2;"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Quick Links -->
    <div style="margin-top:24px;display:flex;gap:12px;">
      <a href="<?= BASE_URL ?>crews" style="padding:10px 20px;background:#2563eb;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
        <span class="material-icons" style="font-size:14px;vertical-align:middle;">people</span> Lihat Data Crew
      </a>
      <a href="<?= BASE_URL ?>contracts" style="padding:10px 20px;background:#16a34a;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
        <span class="material-icons" style="font-size:14px;vertical-align:middle;">description</span> Lihat Kontrak
      </a>
      <a href="<?= BASE_URL ?>vessels" style="padding:10px 20px;background:#f59e0b;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
        <span class="material-icons" style="font-size:14px;vertical-align:middle;">directions_boat</span> Lihat Vessel
      </a>
      <a href="<?= BASE_URL ?>clients" style="padding:10px 20px;background:#ec4899;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
        <span class="material-icons" style="font-size:14px;vertical-align:middle;">business</span> Lihat Client
      </a>
    </div>
  </div>
</div>
