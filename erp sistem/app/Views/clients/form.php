<?php
/**
 * Client Form View (Create/Edit)
 */
$currentPage = 'clients';
$isEdit = !empty($client);
ob_start();
?>

<div class="page-header">
    <h1><?= $isEdit ? 'Edit Client' : 'Add New Client' ?></h1>
    <p><?= $isEdit ? 'Update client details' : 'Enter client/principal information below' ?></p>
</div>

<form method="POST" action="<?= BASE_URL ?>clients/<?= $isEdit ? 'update/' . $client['id'] : 'store' ?>">
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-building" style="color: var(--accent-gold);"></i> Company Information</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Company Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="form-control" 
                       value="<?= htmlspecialchars($client['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Short Name / Code</label>
                <input type="text" name="short_name" class="form-control" 
                       value="<?= htmlspecialchars($client['short_name'] ?? '') ?>" placeholder="e.g. PSC">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-control" 
                       value="<?= htmlspecialchars($client['country'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" 
                       value="<?= htmlspecialchars($client['city'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($client['address'] ?? '') ?></textarea>
        </div>
    </div>
    
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-phone" style="color: var(--accent-gold);"></i> Contact Details</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" 
                       value="<?= htmlspecialchars($client['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" 
                       value="<?= htmlspecialchars($client['phone'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Website</label>
            <input type="url" name="website" class="form-control" 
                   value="<?= htmlspecialchars($client['website'] ?? '') ?>" placeholder="https://">
        </div>
    </div>
    
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-user" style="color: var(--accent-gold);"></i> Contact Person</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="contact_person" class="form-control" 
                       value="<?= htmlspecialchars($client['contact_person'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="contact_email" class="form-control" 
                       value="<?= htmlspecialchars($client['contact_email'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="contact_phone" class="form-control" 
                   value="<?= htmlspecialchars($client['contact_phone'] ?? '') ?>">
        </div>
    </div>
    
    <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <a href="<?= BASE_URL ?>clients" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $isEdit ? 'Update Client' : 'Add Client' ?>
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
