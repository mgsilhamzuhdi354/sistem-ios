<div class="page-header">
    <h1><i class="fas fa-briefcase"></i> Job Vacancies</h1>
    <a href="<?= url('/admin/vacancies/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Vacancy
    </a>
</div>

<!-- Filters -->
<div class="card mb-20">
    <div class="card-body">
        <form action="<?= url('/admin/vacancies') ?>" method="GET" class="filters-form">
            <div class="filter-row">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Search vacancies..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <select name="department" class="form-control">
                        <option value="">All Departments</option>
                        <?php foreach ($departments ?? [] as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= ($_GET['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($_GET['status'] ?? '') === 'published' ? 'selected' : '' ?>
                            >Published</option>
                        <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Vacancies Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($vacancies)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>No Vacancies Found</h3>
                <p>Start by creating a new job vacancy.</p>
                <a href="<?= url('/admin/vacancies/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Vacancy
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Salary Range</th>
                            <th>Applications</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vacancies as $vacancy): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($vacancy['title']) ?>
                                    </strong>
                                    <?php if (!empty($vacancy['is_featured'])): ?>
                                        <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($vacancy['department_name'] ?? '-') ?>
                                </td>
                                <td>
                                    <?php if ($vacancy['salary_min'] && $vacancy['salary_max']): ?>
                                        $
                                        <?= number_format($vacancy['salary_min']) ?> - $
                                        <?= number_format($vacancy['salary_max']) ?>
                                    <?php else: ?>
                                        Negotiable
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-info">
                                        <?= $vacancy['applications_count'] ?? 0 ?>
                                    </span></td>
                                <td>
                                    <?php
                                    $statusClass = $vacancy['status'] === 'published' ? 'success' : ($vacancy['status'] === 'draft' ? 'secondary' : 'danger');
                                    ?>
                                    <span class="badge badge-<?= $statusClass ?>">
                                        <?= ucfirst($vacancy['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($vacancy['created_at'])) ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?= url('/jobs/' . $vacancy['id']) ?>" class="action-btn view" title="View"
                                            target="_blank"><i class="fas fa-eye"></i></a>
                                        <a href="<?= url('/admin/vacancies/edit/' . $vacancy['id']) ?>" class="action-btn edit"
                                            title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="<?= url('/admin/vacancies/delete/' . $vacancy['id']) ?>" method="POST"
                                            class="delete-form" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="action-btn delete" title="Delete"
                                                onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>