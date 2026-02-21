<?php
/**
 * Modern Settings View - Multi-language support
 * Clean white design with modern sidebar
 */
$currentPage = 'settings';

// Parse settings into groups
$generalSettings = [];
foreach ($settings['general'] ?? [] as $s) { $generalSettings[$s['setting_key']] = $s['setting_value']; }
$currencySettings = [];
foreach ($settings['currency'] ?? [] as $s) { $currencySettings[$s['setting_key']] = $s['setting_value']; }
$taxSettings = [];
foreach ($settings['tax'] ?? [] as $s) { $taxSettings[$s['setting_key']] = $s['setting_value']; }
$contractSettings = [];
foreach ($settings['contract'] ?? [] as $s) { $contractSettings[$s['setting_key']] = $s['setting_value']; }
$payrollSettings = [];
foreach ($settings['payroll'] ?? [] as $s) { $payrollSettings[$s['setting_key']] = $s['setting_value']; }
$notifSettings = [];
foreach ($settings['notification'] ?? [] as $s) { $notifSettings[$s['setting_key']] = $s['setting_value']; }

$currentLang = getLanguage();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('settings.title') ?> - IndoOcean ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in{animation:fadeInUp .4s ease-out forwards}
        .animate-d1{animation-delay:.05s}.animate-d2{animation-delay:.1s}.animate-d3{animation-delay:.15s}.animate-d4{animation-delay:.2s}
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
<div class="flex h-screen overflow-hidden">
    <?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-hidden ml-64">
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight"><?= __('settings.title') ?></h1>
                <p class="text-[11px] text-slate-400"><?= __('settings.subtitle') ?></p>
            </div>
            <a href="<?= BASE_URL ?>settings/init"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                <span class="material-icons text-sm">restart_alt</span> <?= __('common.reset_default') ?>
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium
                        <?= $type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' ?>
                        <?= $type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : '' ?>">
                        <?= $msg ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Language Switcher Card -->
            <div class="bg-white rounded-xl border-2 border-purple-200 shadow-sm overflow-hidden mb-6 opacity-0 animate-fade-in">
                <div class="px-5 py-4 border-b border-purple-100 flex items-center gap-2 bg-purple-50/50">
                    <div class="p-2 bg-purple-100 rounded-lg"><span class="material-icons text-purple-600">translate</span></div>
                    <div>
                        <h3 class="text-sm font-bold text-purple-800"><?= __('settings.language') ?></h3>
                        <p class="text-[11px] text-purple-500"><?= __('settings.language_subtitle') ?></p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <button onclick="switchLanguage('en')" 
                                class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 transition-all cursor-pointer
                                <?= $currentLang === 'en' ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-200' : 'border-slate-200 hover:border-purple-300 hover:bg-purple-50/30' ?>">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-lg font-bold text-blue-700">EN</div>
                            <div class="text-left">
                                <div class="text-sm font-bold text-slate-800">English</div>
                                <div class="text-[11px] text-slate-500">English language</div>
                            </div>
                            <?php if ($currentLang === 'en'): ?>
                                <span class="material-icons text-purple-600 ml-auto">check_circle</span>
                            <?php endif; ?>
                        </button>
                        <button onclick="switchLanguage('id')"
                                class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 transition-all cursor-pointer
                                <?= $currentLang === 'id' ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-200' : 'border-slate-200 hover:border-purple-300 hover:bg-purple-50/30' ?>">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-lg font-bold text-red-700">ID</div>
                            <div class="text-left">
                                <div class="text-sm font-bold text-slate-800">Bahasa Indonesia</div>
                                <div class="text-[11px] text-slate-500">Indonesian language</div>
                            </div>
                            <?php if ($currentLang === 'id'): ?>
                                <span class="material-icons text-purple-600 ml-auto">check_circle</span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>

            <form method="POST" action="<?= BASE_URL ?>settings/save">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                    <!-- Company Info -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                            <div class="p-2 bg-blue-50 rounded-lg"><span class="material-icons text-blue-600">business</span></div>
                            <h3 class="text-sm font-bold text-slate-800"><?= __('settings.company_info') ?></h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.company_name') ?></label>
                                <input type="text" name="settings[company_name]" value="<?= htmlspecialchars($generalSettings['company_name'] ?? 'PT Indo Ocean') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.company_email') ?></label>
                                <input type="email" name="settings[company_email]" value="<?= htmlspecialchars($generalSettings['company_email'] ?? '') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.company_phone') ?></label>
                                <input type="text" name="settings[company_phone]" value="<?= htmlspecialchars($generalSettings['company_phone'] ?? '') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.company_address') ?></label>
                                <textarea name="settings[company_address]" rows="2"
                                          class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 resize-none"><?= htmlspecialchars($generalSettings['company_address'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Currency & Tax -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d1">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                            <div class="p-2 bg-emerald-50 rounded-lg"><span class="material-icons text-emerald-600">paid</span></div>
                            <h3 class="text-sm font-bold text-slate-800"><?= __('settings.currency_tax') ?></h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.default_currency') ?></label>
                                <select name="settings[default_currency]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="USD" <?= ($currencySettings['default_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD - US Dollar</option>
                                    <option value="IDR" <?= ($currencySettings['default_currency'] ?? '') === 'IDR' ? 'selected' : '' ?>>IDR - Indonesian Rupiah</option>
                                    <option value="SGD" <?= ($currencySettings['default_currency'] ?? '') === 'SGD' ? 'selected' : '' ?>>SGD - Singapore Dollar</option>
                                    <option value="EUR" <?= ($currencySettings['default_currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.currency_position') ?></label>
                                <select name="settings[currency_position]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="before" <?= ($currencySettings['currency_position'] ?? '') === 'before' ? 'selected' : '' ?>><?= __('settings.before_amount') ?></option>
                                    <option value="after" <?= ($currencySettings['currency_position'] ?? '') === 'after' ? 'selected' : '' ?>><?= __('settings.after_amount') ?></option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.default_tax_rate') ?></label>
                                <input type="number" name="settings[default_tax_rate]" step="0.1"
                                       value="<?= htmlspecialchars($taxSettings['default_tax_rate'] ?? '5') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.tax_calculation') ?></label>
                                <select name="settings[tax_calculation]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="gross" <?= ($taxSettings['tax_calculation'] ?? '') === 'gross' ? 'selected' : '' ?>><?= __('settings.gross_salary') ?></option>
                                    <option value="net" <?= ($taxSettings['tax_calculation'] ?? '') === 'net' ? 'selected' : '' ?>><?= __('settings.net_salary') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Settings -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d2">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                            <div class="p-2 bg-indigo-50 rounded-lg"><span class="material-icons text-indigo-600">description</span></div>
                            <h3 class="text-sm font-bold text-slate-800"><?= __('settings.contract_settings') ?></h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.contract_prefix') ?></label>
                                <input type="text" name="settings[contract_prefix]" value="<?= htmlspecialchars($contractSettings['contract_prefix'] ?? 'CTR') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.default_duration') ?></label>
                                <input type="number" name="settings[default_duration]" value="<?= htmlspecialchars($contractSettings['default_duration'] ?? '6') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.expiry_alert_days') ?></label>
                                <input type="text" name="settings[expiry_alert_days]" value="<?= htmlspecialchars($contractSettings['expiry_alert_days'] ?? '30,14,7') ?>"
                                       placeholder="30,14,7"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                <p class="text-[11px] text-slate-400 mt-1"><?= __('settings.expiry_alert_help') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Settings -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                            <div class="p-2 bg-amber-50 rounded-lg"><span class="material-icons text-amber-600">account_balance_wallet</span></div>
                            <h3 class="text-sm font-bold text-slate-800"><?= __('settings.payroll_settings') ?></h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.payroll_processing_day') ?></label>
                                <input type="number" name="settings[payroll_day]" min="1" max="28"
                                       value="<?= htmlspecialchars($payrollSettings['payroll_day'] ?? '15') ?>"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                <p class="text-[11px] text-slate-400 mt-1">Tanggal proses gaji kru setiap bulan (default: tanggal 15)</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.auto_generate_payroll') ?></label>
                                <select name="settings[auto_generate_payroll]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="0" <?= ($payrollSettings['auto_generate_payroll'] ?? '0') === '0' ? 'selected' : '' ?>><?= __('common.disabled') ?></option>
                                    <option value="1" <?= ($payrollSettings['auto_generate_payroll'] ?? '0') === '1' ? 'selected' : '' ?>><?= __('common.enabled') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3 lg:col-span-2">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                            <div class="p-2 bg-red-50 rounded-lg"><span class="material-icons text-red-500">notifications_active</span></div>
                            <h3 class="text-sm font-bold text-slate-800"><?= __('settings.notification_settings') ?></h3>
                        </div>
                        <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.email_notifications') ?></label>
                                <select name="settings[email_notifications]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="1" <?= ($notifSettings['email_notifications'] ?? '1') === '1' ? 'selected' : '' ?>><?= __('common.enabled') ?></option>
                                    <option value="0" <?= ($notifSettings['email_notifications'] ?? '1') === '0' ? 'selected' : '' ?>><?= __('common.disabled') ?></option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.contract_expiry_notify') ?></label>
                                <select name="settings[contract_expiry_notify]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="1" <?= ($notifSettings['contract_expiry_notify'] ?? '1') === '1' ? 'selected' : '' ?>><?= __('common.enabled') ?></option>
                                    <option value="0" <?= ($notifSettings['contract_expiry_notify'] ?? '1') === '0' ? 'selected' : '' ?>><?= __('common.disabled') ?></option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5"><?= __('settings.payroll_complete_notify') ?></label>
                                <select name="settings[payroll_complete_notify]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                                    <option value="1" <?= ($notifSettings['payroll_complete_notify'] ?? '1') === '1' ? 'selected' : '' ?>><?= __('common.enabled') ?></option>
                                    <option value="0" <?= ($notifSettings['payroll_complete_notify'] ?? '1') === '0' ? 'selected' : '' ?>><?= __('common.disabled') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end mb-6">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm">
                        <span class="material-icons text-lg">save</span> <?= __('settings.save_settings') ?>
                    </button>
                </div>
            </form>

            <!-- Backup & Restore -->
            <div class="bg-white rounded-xl border-2 border-blue-200 shadow-sm overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-blue-100 flex items-center gap-2 bg-blue-50/50">
                    <div class="p-2 bg-blue-100 rounded-lg"><span class="material-icons text-blue-600">cloud_sync</span></div>
                    <h3 class="text-sm font-bold text-blue-800"><?= __('settings.backup_restore') ?></h3>
                </div>
                <div class="p-5">
                    <p class="text-xs text-slate-500 mb-4"><?= __('settings.backup_description') ?></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-icons text-emerald-600 text-lg">download</span>
                                <h4 class="text-sm font-semibold text-slate-800"><?= __('settings.export_data') ?></h4>
                            </div>
                            <p class="text-[11px] text-slate-400 mb-3"><?= __('settings.export_description') ?></p>
                            <a href="<?= BASE_URL ?>settings/export" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                <span class="material-icons text-sm">file_download</span> <?= __('settings.download_backup') ?>
                            </a>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-icons text-amber-600 text-lg">upload</span>
                                <h4 class="text-sm font-semibold text-slate-800"><?= __('settings.import_data') ?></h4>
                            </div>
                            <p class="text-[11px] text-slate-400 mb-3"><?= __('settings.import_description') ?></p>
                            <form method="POST" action="<?= BASE_URL ?>settings/import" enctype="multipart/form-data" id="importForm" class="flex gap-2">
                                <input type="file" name="import_file" id="importFile" accept=".json"
                                       class="flex-1 text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">
                                <button type="button" onclick="confirmImport()"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                    <span class="material-icons text-sm">upload_file</span> <?= __('common.import') ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl border-2 border-red-200 shadow-sm overflow-hidden mb-6" x-data="{ showDanger: false }">
                <div class="px-5 py-4 border-b border-red-100 flex items-center justify-between bg-red-50/50 cursor-pointer" @click="showDanger = !showDanger">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-red-100 rounded-lg"><span class="material-icons text-red-500">warning</span></div>
                        <h3 class="text-sm font-bold text-red-700"><?= __('settings.danger_zone') ?></h3>
                    </div>
                    <span class="material-icons text-red-400 transition-transform" :class="showDanger && 'rotate-180'">expand_more</span>
                </div>
                <div x-show="showDanger" x-transition class="p-5">
                    <p class="text-xs text-slate-500 mb-4"><?= __('settings.danger_description') ?></p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="showConfirmModal('payroll', '<?= __('settings.reset_payroll') ?>', '<?= __('settings.all_payroll_deleted') ?>')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 text-xs font-semibold rounded-lg transition-colors">
                            <span class="material-icons text-sm">payments</span> <?= __('settings.reset_payroll') ?>
                        </button>
                        <button type="button" onclick="showConfirmModal('contracts', '<?= __('settings.delete_contracts') ?>', '<?= __('settings.all_contracts_deleted') ?>')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 text-xs font-semibold rounded-lg transition-colors">
                            <span class="material-icons text-sm">description</span> <?= __('settings.delete_contracts') ?>
                        </button>
                        <button type="button" onclick="showConfirmModal('notifications', '<?= __('settings.delete_notifications') ?>', '<?= __('settings.all_notifications_deleted') ?>')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 text-xs font-semibold rounded-lg transition-colors">
                            <span class="material-icons text-sm">notifications</span> <?= __('settings.delete_notifications') ?>
                        </button>
                        <button type="button" onclick="showConfirmModal('all', '<?= __('settings.delete_all_data') ?>', '<?= __('settings.all_data_deleted') ?>')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors">
                            <span class="material-icons text-sm">delete_forever</span> <?= __('settings.delete_all_data') ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-center"><p class="text-xs text-slate-400">© <?= date('Y') ?> IndoOcean ERP System</p></div>
        </div>
    </main>
</div>

<!-- Confirm Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-[90%]">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 bg-red-100 rounded-lg"><span class="material-icons text-red-500">warning</span></div>
            <h3 id="modalTitle" class="text-sm font-bold text-red-700"></h3>
        </div>
        <p id="modalMessage" class="text-sm text-slate-600 mb-4"></p>
        <div class="bg-red-50 rounded-lg p-3 mb-4">
            <label class="block text-xs text-slate-500 mb-1.5"><?= __('settings.type_confirm', ['code' => '<strong class="text-red-600">HAPUS</strong>']) ?></label>
            <input type="text" id="confirmInput" autocomplete="off" placeholder="HAPUS"
                   class="w-full px-3 py-2 border border-red-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20">
        </div>
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg"><?= __('common.cancel') ?></button>
            <button type="button" id="confirmBtn" onclick="executeDelete()" disabled
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg disabled:opacity-40 disabled:cursor-not-allowed">
                <span class="material-icons text-sm align-middle mr-0.5">delete</span> <?= __('settings.delete_now') ?>
            </button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="<?= BASE_URL ?>settings/delete-data" class="hidden">
    <input type="hidden" name="delete_type" id="deleteType">
    <input type="hidden" name="confirm_code" id="confirmCode">
</form>

<script>
// Language switch
function switchLanguage(lang) {
    fetch('<?= BASE_URL ?>settings/change-language', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'language=' + lang
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(() => { location.href = '<?= BASE_URL ?>settings/change-language?language=' + lang; });
}

// Delete confirmation
let currentDeleteType = '';
function showConfirmModal(type, title, message) {
    currentDeleteType = type;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('confirmInput').value = '';
    document.getElementById('confirmBtn').disabled = true;
    const m = document.getElementById('confirmModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    document.getElementById('confirmInput').focus();
}
function closeConfirmModal() {
    const m = document.getElementById('confirmModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('confirmInput').addEventListener('input', function(e) {
    document.getElementById('confirmBtn').disabled = e.target.value.toUpperCase() !== 'HAPUS';
});
function executeDelete() {
    if (document.getElementById('confirmInput').value.toUpperCase() !== 'HAPUS') return;
    document.getElementById('deleteType').value = currentDeleteType;
    document.getElementById('confirmCode').value = 'HAPUS';
    document.getElementById('deleteForm').submit();
}
document.getElementById('confirmModal').addEventListener('click', function(e) { if (e.target === this) closeConfirmModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeConfirmModal(); });
function confirmImport() {
    const f = document.getElementById('importFile');
    if (!f.files || !f.files.length) { alert('<?= __('settings.select_file_first') ?>'); return; }
    if (!f.files[0].name.toLowerCase().endsWith('.json')) { alert('<?= __('settings.file_must_json') ?>'); return; }
    if (confirm('<?= __('settings.confirm_import') ?>')) document.getElementById('importForm').submit();
}
</script>
</body>
</html>
