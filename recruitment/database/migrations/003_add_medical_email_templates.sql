-- Migration: Add medical email templates
-- Run this SQL against the recruitment database

-- Insert medical check-up scheduled email template
INSERT INTO email_templates (name, slug, subject, body, is_active, created_at)
VALUES (
    'Medical Checkup Scheduled',
    'medical_checkup_scheduled',
    'Medical Check-up Dijadwalkan - PT Indo Ocean Crew Services',
    '<h2>Halo {{name}},</h2>
<p>Medical check-up Anda telah dijadwalkan. Berikut detailnya:</p>
<table style="width:100%;border-collapse:collapse;margin:15px 0;">
<tr><td style="padding:8px;color:#6b7280;width:140px;">📅 Tanggal</td><td style="padding:8px;font-weight:600;">{{scheduled_date}}</td></tr>
<tr><td style="padding:8px;color:#6b7280;">⏰ Waktu</td><td style="padding:8px;font-weight:600;">{{scheduled_time}}</td></tr>
<tr><td style="padding:8px;color:#6b7280;">🏥 Rumah Sakit</td><td style="padding:8px;font-weight:600;">{{hospital_name}}</td></tr>
<tr><td style="padding:8px;color:#6b7280;">📍 Alamat</td><td style="padding:8px;font-weight:600;">{{hospital_address}}</td></tr>
</table>
<p><strong>Catatan Penting:</strong></p>
<ul>
<li>Hadir 15 menit sebelum jadwal</li>
<li>Bawa KTP dan Paspor</li>
<li>Puasa minimal 8 jam sebelum pemeriksaan</li>
</ul>
<p>Salam,<br>Tim Rekrutmen<br>PT Indo Ocean Crew Services</p>',
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE subject = VALUES(subject), body = VALUES(body);

-- Insert medical result email template
INSERT INTO email_templates (name, slug, subject, body, is_active, created_at)
VALUES (
    'Medical Result',
    'medical_result',
    'Hasil Medical Check-up - PT Indo Ocean Crew Services',
    '<h2>Halo {{name}},</h2>
<p>Hasil medical check-up Anda telah diproses:</p>
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;margin:15px 0;text-align:center;">
<p style="font-size:14px;color:#6b7280;margin:0 0 8px;">Hasil</p>
<p style="font-size:24px;font-weight:800;margin:0;color:#166534;">{{result}}</p>
</div>
<table style="width:100%;border-collapse:collapse;margin:15px 0;">
<tr><td style="padding:8px;color:#6b7280;width:140px;">📝 Catatan</td><td style="padding:8px;">{{result_notes}}</td></tr>
<tr><td style="padding:8px;color:#6b7280;">📅 Berlaku Sampai</td><td style="padding:8px;">{{valid_until}}</td></tr>
</table>
<p>Jika ada pertanyaan mengenai hasil, silakan hubungi kami.</p>
<p>Salam,<br>Tim Rekrutmen<br>PT Indo Ocean Crew Services</p>',
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE subject = VALUES(subject), body = VALUES(body);
