<?php
/**
 * Invoice PDF - Professional print-ready invoice
 */
$reportTitle = 'INVOICE';
$reportSubtitle = 'Invoice No: ' . ($invoice['invoice_no'] ?? '-');
$reportDate = date('d F Y');

$subtotal = (float)($invoice['subtotal'] ?? 0);
$taxRate = (float)($invoice['tax_rate'] ?? 0);
$taxAmount = (float)($invoice['tax_amount'] ?? $subtotal * $taxRate / 100);
$total = (float)($invoice['total'] ?? $subtotal + $taxAmount);
$currency = $invoice['currency'] ?? 'USD';
$currSymbol = $currency === 'IDR' ? 'Rp' : ($currency === 'USD' ? '$' : $currency . ' ');

if (!function_exists('fmtInv')) {
    function fmtInv($val, $curr = '$') { 
        if ($curr === 'Rp') return 'Rp ' . number_format($val, 0, ',', '.');
        return $curr . ' ' . number_format($val, 2, '.', ','); 
    }
}

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Invoice Details -->
    <div style="display:flex; gap:20px; margin-bottom:20px;">
        <!-- Bill To -->
        <div style="flex:1; padding:15px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
            <div style="font-size:7pt; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:6px; letter-spacing:1px;">Bill To</div>
            <div style="font-size:10pt; font-weight:800; color:#1e3a5f; margin-bottom:3px;"><?= htmlspecialchars($invoice['client_name'] ?? '-') ?></div>
            <div style="font-size:8pt; color:#475569; line-height:1.6;">
                <?= nl2br(htmlspecialchars($invoice['client_address'] ?? '-')) ?>
            </div>
        </div>
        <!-- Invoice Info -->
        <div style="flex:1; padding:15px; background:#f0f4f8; border:1px solid #d1d9e6; border-radius:8px;">
            <table style="width:100%; font-size:8.5pt;">
                <tr>
                    <td style="font-weight:700; color:#475569; padding:4px 0; width:120px;">Invoice Number</td>
                    <td style="font-weight:800; color:#1e3a5f; padding:4px 0;"><?= htmlspecialchars($invoice['invoice_no'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td style="font-weight:700; color:#475569; padding:4px 0;">Invoice Date</td>
                    <td style="padding:4px 0;"><?= !empty($invoice['invoice_date']) ? date('d F Y', strtotime($invoice['invoice_date'])) : '-' ?></td>
                </tr>
                <tr>
                    <td style="font-weight:700; color:#475569; padding:4px 0;">Due Date</td>
                    <td style="padding:4px 0; color:#dc2626; font-weight:600;"><?= !empty($invoice['due_date']) ? date('d F Y', strtotime($invoice['due_date'])) : '-' ?></td>
                </tr>
                <tr>
                    <td style="font-weight:700; color:#475569; padding:4px 0;">Status</td>
                    <td style="padding:4px 0;">
                        <?php 
                        $st = strtolower($invoice['status'] ?? 'draft');
                        $bc = $st === 'paid' ? 'badge-green' : ($st === 'overdue' ? 'badge-red' : ($st === 'sent' ? 'badge-blue' : 'badge-gray'));
                        ?>
                        <span class="badge <?= $bc ?>"><?= strtoupper($st) ?></span>
                    </td>
                </tr>
                <?php if (!empty($invoice['vessel_name'])): ?>
                <tr>
                    <td style="font-weight:700; color:#475569; padding:4px 0;">Vessel</td>
                    <td style="padding:4px 0;"><?= htmlspecialchars($invoice['vessel_name']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Items Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th style="width:45%">Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($invoiceItems)): ?>
                <?php foreach ($invoiceItems as $i => $item): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td>
                        <div class="font-bold" style="font-size:9pt"><?= htmlspecialchars($item['description'] ?? '-') ?></div>
                        <?php if (!empty($item['notes'])): ?>
                        <div style="font-size:7pt; color:#94a3b8; margin-top:2px;"><?= htmlspecialchars($item['notes']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= number_format($item['quantity'] ?? 1, 0) ?></td>
                    <td class="text-right"><?= fmtInv($item['unit_price'] ?? 0, $currSymbol) ?></td>
                    <td class="text-right font-bold"><?= fmtInv($item['amount'] ?? 0, $currSymbol) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:20px; color:#999;">No items</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
        <div style="width:280px;">
            <table style="width:100%; font-size:9pt;">
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:6px 10px; font-weight:600; color:#475569;">Subtotal</td>
                    <td style="padding:6px 10px; text-align:right;"><?= fmtInv($subtotal, $currSymbol) ?></td>
                </tr>
                <?php if ($taxRate > 0): ?>
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:6px 10px; font-weight:600; color:#475569;">Tax (<?= $taxRate ?>%)</td>
                    <td style="padding:6px 10px; text-align:right;"><?= fmtInv($taxAmount, $currSymbol) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($invoice['discount'])): ?>
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:6px 10px; font-weight:600; color:#475569;">Discount</td>
                    <td style="padding:6px 10px; text-align:right; color:#dc2626;">- <?= fmtInv($invoice['discount'], $currSymbol) ?></td>
                </tr>
                <?php endif; ?>
                <tr style="background:linear-gradient(135deg,#1e3a5f,#2c5282); color:#fff;">
                    <td style="padding:10px; font-weight:800; font-size:10pt; border-radius:0 0 0 6px;">TOTAL</td>
                    <td style="padding:10px; text-align:right; font-weight:800; font-size:12pt; border-radius:0 0 6px 0;"><?= fmtInv($total, $currSymbol) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Payment Terms -->
    <?php if (!empty($invoice['notes'])): ?>
    <div style="padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; font-size:8pt; color:#475569; margin-bottom:15px;">
        <strong>Notes / Terms:</strong><br>
        <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
    </div>
    <?php endif; ?>

    <!-- Bank Details -->
    <div style="padding:12px; background:#f0f4f8; border:1px solid #d1d9e6; border-radius:6px; font-size:8pt; margin-bottom:15px;">
        <div style="font-weight:700; color:#1e3a5f; margin-bottom:6px;">💳 Payment Details</div>
        <table style="font-size:8pt; color:#475569;">
            <tr><td style="width:120px; padding:2px 0; font-weight:600;">Bank Name</td><td>: BCA (Bank Central Asia)</td></tr>
            <tr><td style="padding:2px 0; font-weight:600;">Account Name</td><td>: PT. Indo Ocean Crew Services</td></tr>
            <tr><td style="padding:2px 0; font-weight:600;">Account Number</td><td>: <?= $invoice['payment_account'] ?? '—' ?></td></tr>
            <tr><td style="padding:2px 0; font-weight:600;">Swift Code</td><td>: CENAIDJA</td></tr>
        </table>
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
