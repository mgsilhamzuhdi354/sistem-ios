<?php
/**
 * Shared PDF Footer Partial
 * Closes the pdf_header.php container
 */
?>
    <!-- Footer -->
    <div class="report-footer">
        <span class="confidential">🔒 CONFIDENTIAL — PT. Indo Ocean Crew Services</span>
        <span>Printed: <?= date('d/m/Y H:i') ?> | Page 1</span>
    </div>
</div><!-- /.pdf-container -->

<!-- Print Button (screen only) -->
<div class="no-print" style="text-align:center; margin: 20px auto; max-width: 800px;">
    <button onclick="window.print()" style="padding:12px 40px; background:linear-gradient(135deg,#1e3a5f,#2c5282); color:#fff; border:none; border-radius:10px; font-weight:700; cursor:pointer; font-size:14px; font-family:'Inter',sans-serif; letter-spacing:0.5px; box-shadow:0 4px 15px rgba(30,58,95,0.3); transition:all 0.3s;">
        🖨️ Print / Save as PDF
    </button>
    <button onclick="window.history.back()" style="padding:12px 40px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:10px; font-weight:600; cursor:pointer; font-size:14px; font-family:'Inter',sans-serif; margin-left:10px; transition:all 0.3s;">
        ← Back
    </button>
</div>

</body>
</html>
