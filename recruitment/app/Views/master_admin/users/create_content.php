<!-- Create User Form -->
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Create New User</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('/master-admin/users/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required onchange="toggleLeaderFields(this.value)">
                            <option value="">Select Role...</option>
                            <option value="11">Master Admin (Full System Access)</option>
                            <option value="1">Admin (Vacancy Management)</option>
                            <option value="4">Leader (Team & Pipeline Management)</option>
                            <option value="5">Crewing Staff (Application Handler)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    
                    <!-- Leader-specific fields -->
                    <div id="leaderFields" style="display: none;">
                        <hr>
                        <h6 class="text-warning">Leader Details</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="<?= url('/master-admin/users') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-danger flex-grow-1">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLeaderFields(roleId) {
    // Show additional fields for Leader (4) and Crewing Staff (5)
    document.getElementById('leaderFields').style.display = (roleId == 4 || roleId == 5) ? 'block' : 'none';
}
</script>
