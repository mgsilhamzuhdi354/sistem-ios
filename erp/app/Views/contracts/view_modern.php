<?php
/**
 * Modern Contract Detail View
 * PT Indo Ocean - ERP System
 */
$currentPage = $currentPage ?? 'contracts';
$contract = $contract ?? [];
$statusColors = [
    'draft' => ['bg-gray-100 text-gray-700', 'bg-gray-400'],
    'pending_approval' => ['bg-amber-100 text-amber-700', 'bg-amber-400'],
    'active' => ['bg-green-100 text-green-700', 'bg-green-400'],
    'onboard' => ['bg-blue-100 text-blue-700', 'bg-blue-400'],
    'completed' => ['bg-indigo-100 text-indigo-700', 'bg-indigo-400'],
    'terminated' => ['bg-red-100 text-red-700', 'bg-red-400'],
    'cancelled' => ['bg-gray-100 text-gray-500', 'bg-gray-400'],
];
$sColor = $statusColors[$contract['status'] ?? 'draft'] ?? $statusColors['draft'];
$daysRemaining = $daysRemaining ?? null;
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($contract['contract_no'] ?? '') ?> | <?= __('contracts.contract_detail') ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#6366F1', secondary: '#D4AF37' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-d1 { animation: fadeInUp 0.4s ease-out 0.1s forwards; opacity: 0; }
        .animate-d2 { animation: fadeInUp 0.4s ease-out 0.2s forwards; opacity: 0; }
    </style>
</head>

<body class="bg-gray-100 text-slate-800 antialiased">
    <div class="flex h-screen overflow-hidden">
        <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

        <main class="ml-64 flex-1 overflow-y-auto bg-gray-100 custom-scrollbar">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-8 py-6 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="<?= BASE_URL ?>contracts" class="rounded-lg bg-gray-100 p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-colors">
                            <span class="material-icons-outlined text-xl">arrow_back</span>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900"><?= __('contracts.contract_detail') ?> - <?= htmlspecialchars($contract['contract_no'] ?? '') ?></h1>
                            <p class="text-sm text-gray-500 mt-0.5"><?= htmlspecialchars($contract['crew_name'] ?? '') ?> — <?= htmlspecialchars($contract['rank_name'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $sColor[0] ?>">
                            <?= ucfirst(str_replace('_', ' ', $contract['status'] ?? 'draft')) ?>
                        </span>
                        <?php if (in_array($contract['status'] ?? '', ['active', 'onboard'])): ?>
                            <a href="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>" class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700 transition-colors">
                                <span class="material-icons-outlined text-sm">autorenew</span> <?= __('contracts.renew') ?>
                            </a>
                            <a href="<?= BASE_URL ?>contracts/terminate/<?= $contract['id'] ?>" class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700 transition-colors">
                                <span class="material-icons-outlined text-sm">close</span> <?= __('contracts.terminate') ?>
                            </a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>contracts/export-pdf/<?= $contract['id'] ?>" target="_blank" class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">
                            <span class="material-icons-outlined text-sm">picture_as_pdf</span> PDF
                        </a>
                        <a href="<?= BASE_URL ?>contracts/edit/<?= $contract['id'] ?>" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <span class="material-icons-outlined text-sm">edit</span> <?= __('common.edit') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-6">
                <?php if (!empty($flash['success'])): ?>
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-sm text-green-700 animate-fade-in">
                        <span class="material-icons-outlined">check_circle</span>
                        <?= htmlspecialchars($flash['success']) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($flash['error'])): ?>
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700 animate-fade-in">
                        <span class="material-icons-outlined">error</span>
                        <?= htmlspecialchars($flash['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <!-- Left Column: 3/5 -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Contract Info -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">description</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.contract_info') ?></h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <?php
                                $infoRows = [
                                    ['Contract No', $contract['contract_no'] ?? '-'],
                                    ['Type', defined('CONTRACT_TYPES') ? (CONTRACT_TYPES[$contract['contract_type'] ?? ''] ?? $contract['contract_type'] ?? '-') : ($contract['contract_type'] ?? '-')],
                                    ['Vessel', $contract['vessel_name'] ?? '-'],
                                    ['Client', $contract['client_name'] ?? '-'],
                                    ['Rank', $contract['rank_name'] ?? '-'],
                                ];
                                foreach ($infoRows as $row): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500"><?= $row[0] ?></span>
                                    <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row[1]) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Contract Period -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-d1">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">event</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.contract_period') ?></h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Sign On</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $contract['sign_on_date'] ? date('d M Y', strtotime($contract['sign_on_date'])) : '-' ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Sign Off</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500"><?= __('contracts.duration') ?></span>
                                    <span class="text-sm font-medium text-gray-900"><?= $contract['duration_months'] ?? '-' ?> <?= __('contracts.months') ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500"><?= __('contracts.days_remaining') ?></span>
                                    <?php if ($daysRemaining !== null && in_array($contract['status'], ['active', 'onboard'])): ?>
                                        <?php $dc = $daysRemaining <= 7 ? 'bg-red-100 text-red-700' : ($daysRemaining <= 30 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'); ?>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $dc ?>"><?= $daysRemaining ?> <?= __('documents.days') ?></span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Structure -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-d2">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">payments</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.salary_structure') ?></h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <?php $cs = $contract['currency_symbol'] ?? '$'; ?>
                                <?php if (!empty($contract['exchange_rate']) && $contract['exchange_rate'] > 0): ?>
                                <div class="flex justify-between items-center bg-amber-50 rounded-lg px-4 py-2.5 -mx-2">
                                    <span class="text-sm text-amber-700 flex items-center gap-1">
                                        <span class="material-icons-outlined text-sm">currency_exchange</span> Exchange Rate
                                    </span>
                                    <span class="text-sm font-bold text-amber-700">1 USD = <?= $cs ?><?= number_format($contract['exchange_rate'], 0) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Basic Salary</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $cs ?><?= number_format($contract['basic_salary'] ?? 0, 2) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Overtime</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $cs ?><?= number_format($contract['overtime_allowance'] ?? 0, 2) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Leave Pay</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $cs ?><?= number_format($contract['leave_pay'] ?? 0, 2) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Bonus</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $cs ?><?= number_format($contract['bonus'] ?? 0, 2) ?></span>
                                </div>
                                <?php if (!empty($deductions)): ?>
                                    <?php foreach ($deductions as $ded): ?>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500"><?= defined('DEDUCTION_TYPES') ? (DEDUCTION_TYPES[$ded['deduction_type']] ?? $ded['deduction_type']) : $ded['deduction_type'] ?></span>
                                        <span class="text-sm font-medium text-red-500">-<?= $cs ?><?= number_format($ded['amount'], 2) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="border-t border-gray-200 pt-3 mt-1">
                                    <div class="flex justify-between items-center">
                                        <span class="text-base font-semibold text-gray-900">Total Monthly</span>
                                        <span class="text-lg font-bold text-indigo-600"><?= $cs ?><?= number_format($contract['total_monthly'] ?? 0, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: 2/5 -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Tax Info -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">receipt_long</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.tax_info') ?></h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Tax Type</span>
                                    <span class="text-sm font-medium text-gray-900"><?= defined('TAX_TYPES') ? (TAX_TYPES[$contract['tax_type'] ?? ''] ?? $contract['tax_type'] ?? '-') : ($contract['tax_type'] ?? '-') ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">NPWP</span>
                                    <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($contract['npwp_number'] ?? '-') ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Tax Rate</span>
                                    <span class="text-sm font-medium text-gray-900"><?= $contract['tax_rate'] ?? 5 ?>%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Workflow -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-d1">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">verified</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.approval_workflow') ?></h3>
                            </div>
                            <div class="p-6">
                                <?php if (empty($approvals)): ?>
                                    <p class="text-sm text-gray-400 text-center py-4"><?= __('contracts.no_approvals') ?></p>
                                <?php else: ?>
                                    <div class="space-y-3">
                                        <?php foreach ($approvals as $approval): ?>
                                            <?php
                                            $apprStyles = [
                                                'approved' => ['bg-green-50 border-green-200', 'text-green-600', 'check_circle'],
                                                'rejected' => ['bg-red-50 border-red-200', 'text-red-600', 'cancel'],
                                                'pending'  => ['bg-amber-50 border-amber-200', 'text-amber-600', 'pending'],
                                            ];
                                            $as = $apprStyles[$approval['status']] ?? $apprStyles['pending'];
                                            ?>
                                            <div class="flex items-center gap-3 rounded-lg border p-3 <?= $as[0] ?>">
                                                <span class="material-icons-outlined <?= $as[1] ?>"><?= $as[2] ?></span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-semibold text-gray-900"><?= defined('APPROVAL_LEVELS') ? (APPROVAL_LEVELS[$approval['approval_level'] ?? ''] ?? $approval['approval_level']) : ($approval['approval_level'] ?? '-') ?></span>
                                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase <?= $as[1] ?> <?= $as[0] ?>"><?= $approval['status'] ?></span>
                                                    </div>
                                                    <?php if (!empty($approval['approver_name'])): ?>
                                                        <p class="text-xs text-gray-400 mt-0.5">by <?= htmlspecialchars($approval['approver_name']) ?> · <?= date('d M Y H:i', strtotime($approval['approved_at'])) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($approval['status'] === 'pending'): ?>
                                                    <div class="flex gap-1">
                                                        <form method="POST" action="<?= BASE_URL ?>contracts/approve/<?= $contract['id'] ?>">
                                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-green-700 transition-colors">
                                                                <span class="material-icons-outlined text-sm">check</span> <?= __('contracts.approve') ?>
                                                            </button>
                                                        </form>
                                                        <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-red-700 transition-colors">
                                                            <span class="material-icons-outlined text-sm">close</span> <?= __('contracts.reject') ?>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-d2">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">folder_open</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.documents_section') ?></h3>
                            </div>
                            <div class="p-6">
                                <!-- Upload Form -->
                                <form method="POST" action="<?= BASE_URL ?>contracts/upload-doc/<?= $contract['id'] ?>" enctype="multipart/form-data" class="mb-4 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4">
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Document Type</label>
                                            <select name="document_type" class="w-full rounded-lg border-gray-300 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="contract">Contract</option>
                                                <option value="amendment">Amendment</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Language</label>
                                            <select name="language" class="w-full rounded-lg border-gray-300 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="id">Indonesia</option>
                                                <option value="en">English</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <input type="file" name="document" required class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                                        </div>
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors shadow-sm">
                                            <span class="material-icons-outlined text-sm">upload</span> <?= __('common.upload') ?>
                                        </button>
                                    </div>
                                </form>

                                <!-- Document List -->
                                <?php if (empty($documents)): ?>
                                    <div class="text-center py-6">
                                        <span class="material-icons-outlined text-4xl text-gray-300 block mb-2">description</span>
                                        <p class="text-sm text-gray-400"><?= __('contracts.no_documents') ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="space-y-2">
                                        <?php foreach ($documents as $doc): ?>
                                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 hover:bg-gray-50 transition-colors">
                                                <span class="material-icons-outlined text-xl text-indigo-400"><?= ($doc['document_type'] ?? '') === 'contract' ? 'description' : 'insert_drive_file' ?></span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($doc['file_name']) ?></p>
                                                    <p class="text-xs text-gray-400"><?= ucfirst($doc['document_type'] ?? '') ?> · <?= strtoupper($doc['language'] ?? '') ?> · <?= round(($doc['file_size'] ?? 0) / 1024, 1) ?> KB
                                                        <?php if (!empty($doc['is_signed'])): ?>
                                                            <span class="inline-flex items-center rounded bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 ml-1">✓ Signed</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <a href="<?= BASE_URL ?>contracts/download-doc/<?= $doc['id'] ?>" class="rounded p-1 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Download">
                                                    <span class="material-icons-outlined text-lg">download</span>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Activity Log -->
                        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden animate-d2">
                            <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <span class="material-icons-outlined text-indigo-500">history</span>
                                <h3 class="font-semibold text-gray-900"><?= __('contracts.activity_log') ?></h3>
                            </div>
                            <div class="p-6">
                                <?php if (empty($logs)): ?>
                                    <p class="text-sm text-gray-400 text-center py-4"><?= __('contracts.no_activity') ?></p>
                                <?php else: ?>
                                    <div class="max-h-64 overflow-y-auto custom-scrollbar divide-y divide-gray-100">
                                        <?php foreach ($logs as $log): ?>
                                            <div class="flex justify-between items-center py-3">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900"><?= ucfirst(str_replace('_', ' ', $log['action'])) ?></p>
                                                    <p class="text-xs text-gray-400">by <?= htmlspecialchars($log['user_name'] ?? 'System') ?></p>
                                                </div>
                                                <span class="text-xs text-gray-400 whitespace-nowrap"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-8"></div>
            </div>
        </main>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden" onclick="event.stopPropagation()">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <h3 class="text-white font-semibold flex items-center gap-2">
                    <span class="material-icons-outlined">gavel</span> <?= __('contracts.reject_contract') ?>
                </h3>
            </div>
            <form method="POST" action="<?= BASE_URL ?>contracts/reject/<?= $contract['id'] ?>">
                <div class="px-6 py-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?= __('contracts.rejection_reason') ?></label>
                    <textarea name="reason" rows="3" required placeholder="<?= __('contracts.enter_reason') ?>" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></textarea>
                </div>
                <div class="flex justify-end gap-2 px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"><?= __('common.cancel') ?></button>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors"><?= __('contracts.confirm_reject') ?></button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
