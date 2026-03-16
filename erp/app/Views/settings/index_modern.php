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
$emailSettings = [];
foreach ($settings['email'] ?? [] as $s) { $emailSettings[$s['setting_key']] = $s['setting_value']; }
$waSettings = [];
foreach ($settings['whatsapp'] ?? [] as $s) { $waSettings[$s['setting_key']] = $s['setting_value']; }

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

                    <!-- Email SMTP Settings -->
                    <div class="bg-white rounded-xl border-2 border-orange-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3 lg:col-span-2">
                        <div class="px-5 py-4 border-b border-orange-100 flex items-center justify-between bg-orange-50/50">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-orange-100 rounded-lg"><span class="material-icons text-orange-600">email</span></div>
                                <div>
                                    <h3 class="text-sm font-bold text-orange-800">Email SMTP Settings</h3>
                                    <p class="text-[11px] text-orange-500">Konfigurasi pengiriman email payslip & notifikasi</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">SMTP Host</label>
                                    <input type="text" name="settings[smtp_host]" value="<?= htmlspecialchars($emailSettings['smtp_host'] ?? 'mail.indooceancrew.co.id') ?>"
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                           placeholder="mail.indooceancrew.co.id">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">SMTP Port</label>
                                    <input type="number" name="settings[smtp_port]" value="<?= htmlspecialchars($emailSettings['smtp_port'] ?? '465') ?>"
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Security</label>
                                    <select name="settings[smtp_secure]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                        <option value="ssl" <?= ($emailSettings['smtp_secure'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                        <option value="tls" <?= ($emailSettings['smtp_secure'] ?? 'ssl') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Username / Email</label>
                                    <input type="text" name="settings[smtp_user]" value="<?= htmlspecialchars($emailSettings['smtp_user'] ?? 'ios@indooceancrew.co.id') ?>"
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label>
                                    <div class="relative">
                                        <input type="password" name="settings[smtp_pass]" id="smtpPass" value="<?= htmlspecialchars($emailSettings['smtp_pass'] ?? '') ?>"
                                               class="w-full px-3 py-2 pr-10 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                        <button type="button" onclick="toggleSmtpPass()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                            <span class="material-icons text-lg" id="smtpPassIcon">visibility</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">From Email</label>
                                    <input type="email" name="settings[smtp_from_email]" value="<?= htmlspecialchars($emailSettings['smtp_from_email'] ?? 'ios@indooceancrew.co.id') ?>"
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">From Name</label>
                                    <input type="text" name="settings[smtp_from_name]" value="<?= htmlspecialchars($emailSettings['smtp_from_name'] ?? 'PT Indo Ocean ERP') ?>"
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                </div>
                            </div>
                            <div class="border-t border-slate-100 pt-4 flex items-center gap-3">
                                <div class="flex-1 flex items-center gap-2">
                                    <input type="email" id="testEmailAddr" placeholder="Masukkan email untuk test..."
                                           class="flex-1 max-w-xs px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400">
                                    <button type="button" onclick="sendTestEmail()" id="testEmailBtn"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <span class="material-icons text-sm">send</span> Kirim Test Email
                                    </button>
                                </div>
                                <div id="testEmailResult" class="text-xs font-medium"></div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Notification Settings -->
                    <div class="bg-white rounded-xl border-2 border-green-200 shadow-sm overflow-hidden opacity-0 animate-fade-in animate-d3 lg:col-span-2">
                        <div class="px-5 py-4 border-b border-green-100 flex items-center justify-between bg-green-50/50">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-green-800">WhatsApp Notification</h3>
                                    <p class="text-[11px] text-green-500">Kirim notifikasi otomatis ke WhatsApp via Fonnte API</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= ($waSettings['wa_enabled'] ?? '0') === '1' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>">
                                <?= ($waSettings['wa_enabled'] ?? '0') === '1' ? '● ACTIVE' : '○ INACTIVE' ?>
                            </span>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">WhatsApp Notification</label>
                                    <select name="settings[wa_enabled]" id="waEnabled" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400">
                                        <option value="1" <?= ($waSettings['wa_enabled'] ?? '0') === '1' ? 'selected' : '' ?>>✅ Aktif</option>
                                        <option value="0" <?= ($waSettings['wa_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>⛔ Nonaktif</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">API Token Fonnte</label>
                                    <div class="relative">
                                        <input type="password" name="settings[wa_api_token]" id="waApiToken" value="<?= htmlspecialchars($waSettings['wa_api_token'] ?? '') ?>"
                                               class="w-full px-3 py-2 pr-10 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400"
                                               placeholder="Masukkan token dari dashboard Fonnte">
                                        <button type="button" onclick="toggleWaToken()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                            <span class="material-icons text-lg" id="waTokenIcon">visibility</span>
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Dapatkan token di <a href="https://fonnte.com" target="_blank" class="text-green-600 hover:underline">fonnte.com</a> → Dashboard → Device</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nomor HP Tujuan</label>
                                    <input type="text" name="settings[wa_target_phone]" id="waTargetPhone" value="<?= htmlspecialchars($waSettings['wa_target_phone'] ?? '') ?>"
                                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400"
                                           placeholder="08xxxxxxxxxx atau 628xxxxxxxxxx">
                                    <p class="text-[11px] text-slate-400 mt-1">Untuk banyak nomor, pisahkan dengan koma</p>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kontrak</label>
                                        <select name="settings[wa_notify_contract]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400">
                                            <option value="1" <?= ($waSettings['wa_notify_contract'] ?? '1') === '1' ? 'selected' : '' ?>>✅ On</option>
                                            <option value="0" <?= ($waSettings['wa_notify_contract'] ?? '1') === '0' ? 'selected' : '' ?>>Off</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Payroll</label>
                                        <select name="settings[wa_notify_payroll]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400">
                                            <option value="1" <?= ($waSettings['wa_notify_payroll'] ?? '1') === '1' ? 'selected' : '' ?>>✅ On</option>
                                            <option value="0" <?= ($waSettings['wa_notify_payroll'] ?? '1') === '0' ? 'selected' : '' ?>>Off</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sistem</label>
                                        <select name="settings[wa_notify_system]" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400">
                                            <option value="1" <?= ($waSettings['wa_notify_system'] ?? '1') === '1' ? 'selected' : '' ?>>✅ On</option>
                                            <option value="0" <?= ($waSettings['wa_notify_system'] ?? '1') === '0' ? 'selected' : '' ?>>Off</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 pt-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <input type="text" id="testWaPhone" placeholder="08xx, 08xx (bisa banyak nomor)"
                                           class="flex-1 max-w-sm px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400">
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" onclick="sendTestWA()" id="testWaBtn"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        Kirim Test WA
                                    </button>
                                    <button type="button" onclick="sendTestAllWA()" id="testAllWaBtn"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                                        <span class="material-icons text-sm">rocket_launch</span>
                                        Test Semua Notifikasi (8 tipe)
                                    </button>
                                    <div id="testWaResult" class="text-xs font-medium ml-2"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-2">💡 "Test Semua" akan mengirim 8 contoh pesan notifikasi lengkap ke nomor di atas</p>
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

// SMTP Password toggle
function toggleSmtpPass() {
    const inp = document.getElementById('smtpPass');
    const ico = document.getElementById('smtpPassIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.textContent = 'visibility_off'; }
    else { inp.type = 'password'; ico.textContent = 'visibility'; }
}

// WA Token toggle
function toggleWaToken() {
    const inp = document.getElementById('waApiToken');
    const ico = document.getElementById('waTokenIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.textContent = 'visibility_off'; }
    else { inp.type = 'password'; ico.textContent = 'visibility'; }
}

// Test WhatsApp - saves WA settings first, then tests
function sendTestWA() {
    const phone = document.getElementById('testWaPhone').value.trim();
    if (!phone) { alert('Masukkan nomor HP tujuan test'); return; }
    const btn = document.getElementById('testWaBtn');
    const res = document.getElementById('testWaResult');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-sm animate-spin">sync</span> Menyimpan & Mengirim...';
    res.textContent = '';
    
    // Collect current WA form values
    const waData = new URLSearchParams();
    waData.append('wa_enabled', document.querySelector('[name="settings[wa_enabled]"]').value);
    waData.append('wa_api_token', document.getElementById('waApiToken').value);
    waData.append('wa_target_phone', document.getElementById('waTargetPhone').value);
    waData.append('wa_notify_contract', document.querySelector('[name="settings[wa_notify_contract]"]').value);
    waData.append('wa_notify_payroll', document.querySelector('[name="settings[wa_notify_payroll]"]').value);
    waData.append('wa_notify_system', document.querySelector('[name="settings[wa_notify_system]"]').value);
    waData.append('test_phone', phone);
    
    // Step 1: Save WA settings to DB first
    fetch('<?= BASE_URL ?>settings/save-whatsapp', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: waData.toString()
    })
    .then(r => r.json())
    .then(saveResult => {
        // Step 2: Now send test WA
        return fetch('<?= BASE_URL ?>settings/test-whatsapp', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: waData.toString()
        });
    })
    .then(r => r.json())
    .then(data => {
        res.textContent = data.message;
        res.className = 'text-xs font-medium ' + (data.success ? 'text-emerald-600' : 'text-red-600');
    })
    .catch(e => { res.textContent = 'Error: ' + e.message; res.className = 'text-xs font-medium text-red-600'; })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> Kirim Test WA';
    });
}

// Test ALL WhatsApp notification types (8 samples)
function sendTestAllWA() {
    const phone = document.getElementById('testWaPhone').value.trim();
    if (!phone) { alert('Masukkan nomor HP tujuan test'); return; }
    const btn = document.getElementById('testAllWaBtn');
    const res = document.getElementById('testWaResult');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-sm animate-spin">sync</span> Mengirim 8 notifikasi...';
    res.innerHTML = '<span class="text-slate-500">⏳ Mengirim 8 pesan test, mohon tunggu ~10 detik...</span>';
    
    const waData = new URLSearchParams();
    waData.append('wa_enabled', document.querySelector('[name="settings[wa_enabled]"]').value);
    waData.append('wa_api_token', document.getElementById('waApiToken').value);
    waData.append('wa_target_phone', document.getElementById('waTargetPhone').value);
    waData.append('wa_notify_contract', document.querySelector('[name="settings[wa_notify_contract]"]').value);
    waData.append('wa_notify_payroll', document.querySelector('[name="settings[wa_notify_payroll]"]').value);
    waData.append('wa_notify_system', document.querySelector('[name="settings[wa_notify_system]"]').value);
    waData.append('test_phone', phone);
    
    fetch('<?= BASE_URL ?>settings/test-all-whatsapp', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: waData.toString()
    })
    .then(r => r.json())
    .then(data => {
        res.textContent = data.message;
        res.className = 'text-xs font-medium ' + (data.success ? 'text-emerald-600' : 'text-red-600');
    })
    .catch(e => { res.textContent = 'Error: ' + e.message; res.className = 'text-xs font-medium text-red-600'; })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-icons text-sm">rocket_launch</span> Test Semua Notifikasi (8 tipe)';
    });
}

// Test Email - saves SMTP settings first, then tests
function sendTestEmail() {
    const email = document.getElementById('testEmailAddr').value.trim();
    if (!email) { alert('Masukkan alamat email tujuan test'); return; }
    const btn = document.getElementById('testEmailBtn');
    const res = document.getElementById('testEmailResult');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons text-sm animate-spin">sync</span> Menyimpan & Mengirim...';
    res.textContent = '';
    
    // Collect current SMTP form values
    const smtpData = new URLSearchParams();
    smtpData.append('smtp_host', document.querySelector('[name="settings[smtp_host]"]').value);
    smtpData.append('smtp_port', document.querySelector('[name="settings[smtp_port]"]').value);
    smtpData.append('smtp_secure', document.querySelector('[name="settings[smtp_secure]"]').value);
    smtpData.append('smtp_user', document.querySelector('[name="settings[smtp_user]"]').value);
    smtpData.append('smtp_pass', document.querySelector('[name="settings[smtp_pass]"]').value);
    smtpData.append('smtp_from_email', document.querySelector('[name="settings[smtp_from_email]"]').value);
    smtpData.append('smtp_from_name', document.querySelector('[name="settings[smtp_from_name]"]').value);
    
    // Step 1: Save SMTP settings to DB first
    fetch('<?= BASE_URL ?>settings/save-email', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: smtpData.toString()
    })
    .then(r => r.json())
    .then(saveResult => {
        // Step 2: Now send test email (Mailer will read from DB)
        return fetch('<?= BASE_URL ?>settings/test-email', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'test_email=' + encodeURIComponent(email)
        });
    })
    .then(r => r.json())
    .then(data => {
        res.textContent = data.message;
        res.className = 'text-xs font-medium ' + (data.success ? 'text-emerald-600' : 'text-red-600');
    })
    .catch(e => { res.textContent = 'Error: ' + e.message; res.className = 'text-xs font-medium text-red-600'; })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-icons text-sm">send</span> Kirim Test Email';
    });
}
</script>
</body>
</html>
