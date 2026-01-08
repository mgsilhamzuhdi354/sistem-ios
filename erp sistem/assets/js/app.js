/**
 * PT Indo Ocean ERP - Language and Theme Handler
 * Full Translation Support
 */

// Complete Translations
const translations = {
    en: {
        // Navigation
        'nav_dashboard': 'Dashboard',
        'nav_contracts': 'Contracts',
        'nav_vessels': 'Vessels',
        'nav_clients': 'Clients',
        'nav_payroll': 'Payroll',
        'nav_reports': 'Reports',
        'nav_expiring': 'Expiring Contracts',
        'nav_notifications': 'Notifications',
        'nav_settings': 'Settings',
        'nav_users': 'User Management',

        // Settings page
        'settings_title': 'Settings',
        'settings_subtitle': 'System configuration and preferences',
        'reset_defaults': 'Reset Defaults',
        'save_settings': 'Save Settings',

        // Company Information
        'company_info': 'Company Information',
        'company_name': 'Company Name',
        'company_email': 'Email',
        'company_phone': 'Phone',
        'company_address': 'Address',

        // Currency & Tax
        'currency_tax': 'Currency & Tax',
        'default_currency': 'Default Currency',
        'currency_position': 'Currency Position',
        'before_amount': 'Before amount ($100)',
        'after_amount': 'After amount (100$)',
        'default_tax_rate': 'Default Tax Rate (%)',
        'tax_calculation': 'Tax Calculation Base',
        'gross_salary': 'Gross Salary',
        'net_salary': 'Net Salary (after deductions)',

        // Contract Settings
        'contract_settings': 'Contract Settings',
        'contract_prefix': 'Contract Number Prefix',
        'default_duration': 'Default Duration (months)',
        'expiry_alert_days': 'Expiry Alert Days',
        'expiry_alert_hint': 'Days before expiry to show alerts',

        // Payroll Settings
        'payroll_settings': 'Payroll Settings',
        'payroll_day': 'Payroll Processing Day',
        'auto_generate': 'Auto Generate Payroll',
        'enabled': 'Enabled',
        'disabled': 'Disabled',

        // Notification Settings
        'notification_settings': 'Notification Settings',
        'email_notifications': 'Email Notifications',
        'contract_expiry_alerts': 'Contract Expiry Alerts',
        'payroll_complete_alerts': 'Payroll Complete Alerts',

        // Appearance
        'appearance': 'Appearance',
        'language': 'Language',
        'theme_color': 'Theme Color',
        'select_language': 'Select Language',
        'select_theme': 'Select Theme Color',
        'lang_hint': 'Select display language',
        'theme_hint': 'Click to change theme color',

        // Theme Names
        'theme_gold': 'Gold (Default)',
        'theme_blue': 'Ocean Blue',
        'theme_green': 'Emerald Green',
        'theme_purple': 'Royal Purple',
        'theme_red': 'Ruby Red',
        'theme_teal': 'Teal',

        // Backup & Restore
        'backup_restore': 'Backup & Restore',
        'backup_hint': 'Backup your data before performing a reset or major changes. Backup files in JSON format can be imported back at any time.',
        'export_data': 'Export Data',
        'export_hint': 'Download backup of all data (contracts, payroll, settings, etc.) in JSON format.',
        'download_backup': 'Download Backup',
        'import_data': 'Import Data',
        'import_hint': 'Restore data from a JSON backup file. Existing data will be overwritten.',
        'import_btn': 'Import',

        // Danger Zone
        'danger_zone': 'Danger Zone',
        'danger_hint': 'The actions below are destructive and cannot be undone. Please make sure you are certain before proceeding.',
        'reset_payroll': 'Reset Payroll',
        'delete_contracts': 'Delete Contracts',
        'delete_notifications': 'Delete Notifications',
        'delete_all_data': 'Delete All Data',
        'type_confirm': 'Type',
        'to_confirm': 'to confirm:',
        'cancel': 'Cancel',
        'delete_now': 'Delete Now',

        // Delete Modal Titles
        'delete_payroll_title': 'Delete Payroll Data',
        'delete_contracts_title': 'Delete All Contracts',
        'delete_notifications_title': 'Delete All Notifications',
        'delete_all_title': 'DELETE ALL DATA',

        // Confirmation Messages
        'confirm_delete_payroll': 'All payroll data (periods and items) will be deleted. Contract and salary data will not be affected.',
        'confirm_delete_contracts': 'All contracts, salary, tax, deductions, approvals, and related documents will be deleted.',
        'confirm_delete_notifications': 'All notifications will be deleted.',
        'confirm_delete_all': 'WARNING: All data including contracts, payroll, notifications, and settings will be PERMANENTLY DELETED. Only master data (vessels, clients, ranks, currencies) will remain.',

        // Alerts
        'select_file_first': 'Please select a JSON backup file first',
        'file_must_be_json': 'File format must be JSON',
        'import_warning': 'WARNING: Import will overwrite existing data!',
        'file': 'File',
        'size': 'Size',
        'confirm_import': 'Are you sure you want to continue?'
    },
    id: {
        // Navigation
        'nav_dashboard': 'Dasbor',
        'nav_contracts': 'Kontrak',
        'nav_vessels': 'Kapal',
        'nav_clients': 'Klien',
        'nav_payroll': 'Penggajian',
        'nav_reports': 'Laporan',
        'nav_expiring': 'Kontrak Berakhir',
        'nav_notifications': 'Notifikasi',
        'nav_settings': 'Pengaturan',
        'nav_users': 'Manajemen User',

        // Settings page
        'settings_title': 'Pengaturan',
        'settings_subtitle': 'Konfigurasi sistem dan preferensi',
        'reset_defaults': 'Reset Default',
        'save_settings': 'Simpan Pengaturan',

        // Company Information
        'company_info': 'Informasi Perusahaan',
        'company_name': 'Nama Perusahaan',
        'company_email': 'Email',
        'company_phone': 'Telepon',
        'company_address': 'Alamat',

        // Currency & Tax
        'currency_tax': 'Mata Uang & Pajak',
        'default_currency': 'Mata Uang Default',
        'currency_position': 'Posisi Mata Uang',
        'before_amount': 'Sebelum jumlah ($100)',
        'after_amount': 'Setelah jumlah (100$)',
        'default_tax_rate': 'Tarif Pajak Default (%)',
        'tax_calculation': 'Dasar Perhitungan Pajak',
        'gross_salary': 'Gaji Kotor',
        'net_salary': 'Gaji Bersih (setelah potongan)',

        // Contract Settings
        'contract_settings': 'Pengaturan Kontrak',
        'contract_prefix': 'Awalan Nomor Kontrak',
        'default_duration': 'Durasi Default (bulan)',
        'expiry_alert_days': 'Hari Peringatan Berakhir',
        'expiry_alert_hint': 'Hari sebelum berakhir untuk menampilkan peringatan',

        // Payroll Settings
        'payroll_settings': 'Pengaturan Penggajian',
        'payroll_day': 'Hari Pemrosesan Gaji',
        'auto_generate': 'Otomatis Generate Payroll',
        'enabled': 'Aktif',
        'disabled': 'Nonaktif',

        // Notification Settings
        'notification_settings': 'Pengaturan Notifikasi',
        'email_notifications': 'Notifikasi Email',
        'contract_expiry_alerts': 'Peringatan Kontrak Berakhir',
        'payroll_complete_alerts': 'Peringatan Payroll Selesai',

        // Appearance
        'appearance': 'Tampilan',
        'language': 'Bahasa',
        'theme_color': 'Warna Tema',
        'select_language': 'Pilih Bahasa',
        'select_theme': 'Pilih Warna Tema',
        'lang_hint': 'Pilih bahasa tampilan',
        'theme_hint': 'Klik untuk mengubah warna tema',

        // Theme Names
        'theme_gold': 'Emas (Default)',
        'theme_blue': 'Biru Laut',
        'theme_green': 'Hijau Zamrud',
        'theme_purple': 'Ungu Royal',
        'theme_red': 'Merah Rubi',
        'theme_teal': 'Hijau Toska',

        // Backup & Restore
        'backup_restore': 'Backup & Restore',
        'backup_hint': 'Backup data Anda sebelum melakukan reset atau perubahan besar. File backup dalam format JSON dapat diimport kembali kapan saja.',
        'export_data': 'Ekspor Data',
        'export_hint': 'Download backup semua data (kontrak, payroll, settings, dll) dalam format JSON.',
        'download_backup': 'Unduh Backup',
        'import_data': 'Impor Data',
        'import_hint': 'Restore data dari file backup JSON. Data yang ada akan ditimpa.',
        'import_btn': 'Impor',

        // Danger Zone
        'danger_zone': 'Zona Berbahaya',
        'danger_hint': 'Tindakan di bawah ini bersifat destruktif dan tidak dapat dibatalkan. Harap pastikan Anda yakin sebelum melanjutkan.',
        'reset_payroll': 'Reset Payroll',
        'delete_contracts': 'Hapus Kontrak',
        'delete_notifications': 'Hapus Notifikasi',
        'delete_all_data': 'Hapus Semua Data',
        'type_confirm': 'Ketik',
        'to_confirm': 'untuk konfirmasi:',
        'cancel': 'Batal',
        'delete_now': 'Hapus Sekarang',

        // Delete Modal Titles
        'delete_payroll_title': 'Hapus Data Payroll',
        'delete_contracts_title': 'Hapus Semua Kontrak',
        'delete_notifications_title': 'Hapus Semua Notifikasi',
        'delete_all_title': 'HAPUS SEMUA DATA',

        // Confirmation Messages
        'confirm_delete_payroll': 'Semua data payroll (period dan items) akan dihapus. Data kontrak dan salary tidak akan terpengaruh.',
        'confirm_delete_contracts': 'Semua kontrak, salary, tax, deductions, approvals, dan dokumen terkait akan dihapus.',
        'confirm_delete_notifications': 'Semua notifikasi akan dihapus.',
        'confirm_delete_all': 'PERHATIAN: Semua data termasuk kontrak, payroll, notifikasi, dan settings akan DIHAPUS PERMANEN. Hanya data master (vessels, clients, ranks, currencies) yang akan tetap ada.',

        // Alerts
        'select_file_first': 'Silakan pilih file backup JSON terlebih dahulu',
        'file_must_be_json': 'Format file harus JSON',
        'import_warning': 'PERHATIAN: Import akan menimpa data yang sudah ada!',
        'file': 'File',
        'size': 'Ukuran',
        'confirm_import': 'Apakah Anda yakin ingin melanjutkan?'
    }
};

// Theme Colors
const themeColors = {
    gold: {
        name: 'Gold (Default)',
        accent: '#D4AF37',
        accentLight: '#E8C547'
    },
    blue: {
        name: 'Ocean Blue',
        accent: '#3B82F6',
        accentLight: '#60A5FA'
    },
    green: {
        name: 'Emerald Green',
        accent: '#10B981',
        accentLight: '#34D399'
    },
    purple: {
        name: 'Royal Purple',
        accent: '#8B5CF6',
        accentLight: '#A78BFA'
    },
    red: {
        name: 'Ruby Red',
        accent: '#EF4444',
        accentLight: '#F87171'
    },
    teal: {
        name: 'Teal',
        accent: '#14B8A6',
        accentLight: '#2DD4BF'
    }
};

// Get current settings
function getCurrentLanguage() {
    return localStorage.getItem('erp_language') || 'id';
}

function getCurrentTheme() {
    return localStorage.getItem('erp_theme') || 'gold';
}

// Set language
function setLanguage(lang) {
    localStorage.setItem('erp_language', lang);
    applyTranslations();
}

// Set theme
function setTheme(theme) {
    localStorage.setItem('erp_theme', theme);
    applyTheme();
}

// Apply translations to page
function applyTranslations() {
    const lang = getCurrentLanguage();
    const trans = translations[lang] || translations['id'];

    // Update HTML lang attribute
    document.documentElement.lang = lang === 'id' ? 'id' : 'en';

    // Translate elements with data-translate attribute
    document.querySelectorAll('[data-translate]').forEach(el => {
        const key = el.getAttribute('data-translate');
        if (trans[key]) {
            el.textContent = trans[key];
        }
    });

    // Translate placeholder attributes
    document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
        const key = el.getAttribute('data-translate-placeholder');
        if (trans[key]) {
            el.placeholder = trans[key];
        }
    });

    // Translate title attributes (tooltips)
    document.querySelectorAll('[data-translate-title]').forEach(el => {
        const key = el.getAttribute('data-translate-title');
        if (trans[key]) {
            el.title = trans[key];
        }
    });

    // Translate select options
    document.querySelectorAll('select option[data-translate]').forEach(el => {
        const key = el.getAttribute('data-translate');
        if (trans[key]) {
            el.textContent = trans[key];
        }
    });
}

// Apply theme to page
function applyTheme() {
    const theme = getCurrentTheme();
    const colors = themeColors[theme] || themeColors['gold'];

    document.documentElement.style.setProperty('--accent-gold', colors.accent);
    document.documentElement.style.setProperty('--accent-gold-light', colors.accentLight);
}

// Get translation (global function for JS usage)
function t(key) {
    const lang = getCurrentLanguage();
    const trans = translations[lang] || translations['id'];
    return trans[key] || key;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    // Apply theme first
    applyTheme();

    // Apply translations
    applyTranslations();

    // Update language selector if exists
    const langSelect = document.getElementById('languageSelect');
    if (langSelect) {
        langSelect.value = getCurrentLanguage();
    }

    // Update theme selector if exists
    const themeSelect = document.getElementById('themeSelect');
    if (themeSelect) {
        themeSelect.value = getCurrentTheme();
    }

    // Highlight active theme button
    const currentTheme = getCurrentTheme();
    document.querySelectorAll('.theme-btn').forEach(btn => {
        if (btn.dataset.theme === currentTheme) {
            btn.style.borderColor = '#fff';
            btn.style.boxShadow = '0 0 10px rgba(255,255,255,0.5)';
        }
    });
});
