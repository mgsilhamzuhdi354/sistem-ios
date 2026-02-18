<!-- ERP INTEGRATION MODAL -->
<div class="p-modal" id="erpModal">
    <div class="p-modal-box" style="max-width:550px;">
        <div class="p-modal-header" style="background:linear-gradient(135deg, #6366f1, #4f46e5);display:flex;align-items:center;gap:12px;">
            <i class="fas fa-paper-plane" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:white;width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:12px;font-size:1.1rem;"></i>
            <div>
                <h3>Send to ERP System</h3>
                <p class="text-muted" id="erpApplicantNameText" style="font-size:0.85rem;margin:0;"></p>
            </div>
        </div>
        <div class="p-modal-body">
            <div style="background:#f0f9ff;border:1px solid#bae6fd;border-radius:12px;padding:12px;margin-bottom:20px;">
                <p style="margin:0;font-size:0.85rem;color:#0369a1;">
                    <i class="fas fa-info-circle me-1"></i>
                    This will create a crew record in the ERP system. Please fill in the required information below.
                </p>
            </div>
            
            <div style="margin-bottom:18px;">
                <label style="font-weight:600;font-size:0.85rem;margin-bottom:6px;display:block;color:#475569;">
                    <i class="fas fa-anchor me-1" style="color:#6366f1;"></i> Rank *
                </label>
                <select id="erpRank" style="width:100%;padding:10px;border-radius:10px;border:2px solid #e5e7eb;font-family:inherit;" required>
                    <option value="">-- Select Rank --</option>
                </select>
            </div>
            
            <div style="margin-bottom:18px;">
                <label style="font-weight:600;font-size:0.85rem;margin-bottom:6px;display:block;color:#475569;">
                    <i class="fas fa-calendar me-1" style="color:#6366f1;"></i> Join Date
                </label>
                <input type="date" id="erpJoinDate" style="width:100%;padding:10px;border-radius:10px;border:2px solid #e5e7eb;font-family:inherit;">
            </div>
            
            <div>
                <label style="font-weight:600;font-size:0.85rem;margin-bottom:6px;display:block;color:#475569;">
                    <i class="fas fa-sticky-note me-1" style="color:#6366f1;"></i> Notes (Optional)
                </label>
                <textarea id="erpNotes" style="width:100%;padding:10px;border-radius:10px;border:2px solid #e5e7eb;font-family:inherit;resize:vertical;" rows="2"
                    placeholder="Additional notes..."></textarea>
            </div>
        </div>
        <div class="p-modal-footer">
            <input type="hidden" id="erpAppId">
            <button class="btn-modal-cancel" onclick="closeModal('erpModal')">Cancel</button>
            <button class="btn-modal-submit" id="erpSubmitBtn" onclick="submitErpTransfer()">
                <i class="fas fa-paper-plane me-1"></i>Send to ERP
            </button>
        </div>
    </div>
</div>

<style>
.btn-erp {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-erp:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99,102,241,0.4);
}
</style>

<script>
// Load ranks when modal opens
function openErpModal(appId, applicantName) {
    document.getElementById('erpAppId').value = appId;
    document.getElementById('erpApplicantNameText').textContent = applicantName;
    document.getElementById('erpRank').innerHTML = '<option value="">Loading...</option>';
    document.getElementById('erpJoinDate').value = '';
    document.getElementById('erpNotes').value = '';
    
    // Load ranks from ERP
    fetch('<?= url('/crewing/erp/get-ranks') ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= csrf_token() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.ranks) {
            let options = '<option value="">-- Select Rank --</option>';
            let currentCategory = '';
            data.ranks.forEach(rank => {
                if (rank.category !== currentCategory) {
                    if (currentCategory !== '') options += '</optgroup>';
                    options += `<optgroup label="${rank.category}">`;
                    currentCategory = rank.category;
                }
                options += `<option value="${rank.id}">${rank.name}</option>`;
            });
            if (currentCategory !== '') options += '</optgroup>';
            document.getElementById('erpRank').innerHTML = options;
        } else {
            document.getElementById('erpRank').innerHTML = '<option value="">Error loading ranks</option>';
            showToast('Failed to load ranks', 'error');
        }
    })
    .catch(e => {
        document.getElementById('erpRank').innerHTML = '<option value="">Error loading ranks</option>';
        showToast('Error: ' + e.message, 'error');
    });
    
    openModal('erpModal');
}

function submitErpTransfer() {
    const appId = document.getElementById('erpAppId').value;
    const rankId = document.getElementById('erpRank').value;
    const joinDate = document.getElementById('erpJoinDate').value;
    const notes = document.getElementById('erpNotes').value;
    const btn = document.getElementById('erpSubmitBtn');
    
    if (!rankId) {
        showToast('Please select a rank', 'error');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
    
    fetch('<?= url('/crewing/erp/send') ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `csrf_token=<?= csrf_token() ?>&application_id=${appId}&rank_id=${rankId}&join_date=${joinDate}&notes=${encodeURIComponent(notes)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeModal('erpModal');
            // Reload page to show updated status
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(e => {
        showToast('Error: ' + e.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send to ERP';
    });
}
</script>
