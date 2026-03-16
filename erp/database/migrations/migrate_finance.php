<?php
/**
 * PT Indo Ocean - ERP System
 * Financial & Accounting Module - Database Migration
 * 
 * International Standard: Chart of Accounts follows IFRS/PSAK numbering convention
 * 1xxx = Assets, 2xxx = Liabilities, 3xxx = Equity, 4xxx = Revenue, 5xxx = COGS, 6xxx = Expenses
 */

// Load the database config (returns an array)
$dbConfig = require __DIR__ . '/../../app/Config/Database.php';
$cfg = $dbConfig['default'];

echo "=== Finance Module Migration ===\n\n";

try {
    // Connect directly via mysqli using the config values
    $db = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port']);
    if ($db->connect_error) {
        throw new Exception("DB Connection failed: " . $db->connect_error);
    }
    $db->set_charset('utf8mb4');
    
    // ─────────────────────────────────────────────
    // 1. CHART OF ACCOUNTS (Bagan Akun - IFRS/PSAK)
    // ─────────────────────────────────────────────
    echo "Creating finance_chart_of_accounts...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_chart_of_accounts (
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(20) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            name_en VARCHAR(150) DEFAULT NULL COMMENT 'English name for international',
            type ENUM('asset','liability','equity','revenue','cogs','expense') NOT NULL,
            parent_id INT DEFAULT NULL,
            is_system TINYINT(1) DEFAULT 0 COMMENT '1=cannot be deleted',
            is_active TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_type (type),
            INDEX idx_parent (parent_id),
            INDEX idx_code (code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 2. COST CENTERS (Pusat Biaya)
    // ─────────────────────────────────────────────
    echo "Creating finance_cost_centers...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_cost_centers (
            id INT PRIMARY KEY AUTO_INCREMENT,
            code VARCHAR(20) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            name_en VARCHAR(100) DEFAULT NULL,
            description TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_code (code),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 3. INVOICES - Accounts Receivable (AR)
    // ─────────────────────────────────────────────
    echo "Creating finance_invoices...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_invoices (
            id INT PRIMARY KEY AUTO_INCREMENT,
            invoice_no VARCHAR(50) NOT NULL UNIQUE,
            client_id INT NOT NULL,
            vessel_id INT DEFAULT NULL,
            
            invoice_date DATE NOT NULL,
            due_date DATE NOT NULL,
            
            subtotal DECIMAL(18,2) DEFAULT 0.00,
            discount_percent DECIMAL(5,2) DEFAULT 0.00,
            discount_amount DECIMAL(18,2) DEFAULT 0.00,
            tax_percent DECIMAL(5,2) DEFAULT 0.00,
            tax_amount DECIMAL(18,2) DEFAULT 0.00,
            total DECIMAL(18,2) DEFAULT 0.00,
            amount_paid DECIMAL(18,2) DEFAULT 0.00,
            
            currency_code VARCHAR(3) DEFAULT 'IDR',
            exchange_rate DECIMAL(18,4) DEFAULT 1.0000 COMMENT 'Rate to IDR',
            
            status ENUM('draft','sent','unpaid','partial','paid','overdue','cancelled','void') DEFAULT 'draft',
            cost_center_id INT DEFAULT NULL,
            revenue_account_id INT DEFAULT NULL COMMENT 'COA for revenue recognition',
            
            terms TEXT COMMENT 'Payment terms',
            notes TEXT,
            internal_notes TEXT,
            
            sent_at DATETIME DEFAULT NULL,
            paid_at DATETIME DEFAULT NULL,
            cancelled_at DATETIME DEFAULT NULL,
            
            created_by INT,
            updated_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_client (client_id),
            INDEX idx_vessel (vessel_id),
            INDEX idx_status (status),
            INDEX idx_date (invoice_date),
            INDEX idx_due (due_date),
            INDEX idx_cost_center (cost_center_id),
            FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE,
            FOREIGN KEY (cost_center_id) REFERENCES finance_cost_centers(id) ON UPDATE CASCADE ON DELETE SET NULL,
            FOREIGN KEY (revenue_account_id) REFERENCES finance_chart_of_accounts(id) ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 4. INVOICE ITEMS (Line Items)
    // ─────────────────────────────────────────────
    echo "Creating finance_invoice_items...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_invoice_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            invoice_id INT NOT NULL,
            description VARCHAR(500) NOT NULL,
            quantity DECIMAL(12,4) DEFAULT 1.0000,
            unit VARCHAR(20) DEFAULT 'unit',
            unit_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
            discount_percent DECIMAL(5,2) DEFAULT 0.00,
            amount DECIMAL(18,2) DEFAULT 0.00,
            account_id INT DEFAULT NULL COMMENT 'Revenue account for this line',
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            INDEX idx_invoice (invoice_id),
            FOREIGN KEY (invoice_id) REFERENCES finance_invoices(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (account_id) REFERENCES finance_chart_of_accounts(id) ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 5. BILLS - Accounts Payable (AP)
    // ─────────────────────────────────────────────
    echo "Creating finance_bills...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_bills (
            id INT PRIMARY KEY AUTO_INCREMENT,
            bill_no VARCHAR(50) NOT NULL,
            vendor_name VARCHAR(200) NOT NULL,
            vendor_address TEXT,
            vendor_phone VARCHAR(50),
            vendor_email VARCHAR(100),
            
            bill_date DATE NOT NULL,
            due_date DATE NOT NULL,
            
            subtotal DECIMAL(18,2) DEFAULT 0.00,
            tax_percent DECIMAL(5,2) DEFAULT 0.00,
            tax_amount DECIMAL(18,2) DEFAULT 0.00,
            total DECIMAL(18,2) DEFAULT 0.00,
            amount_paid DECIMAL(18,2) DEFAULT 0.00,
            
            currency_code VARCHAR(3) DEFAULT 'IDR',
            exchange_rate DECIMAL(18,4) DEFAULT 1.0000,
            
            status ENUM('draft','unpaid','partial','paid','overdue','cancelled','void') DEFAULT 'draft',
            cost_center_id INT DEFAULT NULL,
            expense_account_id INT DEFAULT NULL COMMENT 'Default expense account',
            category ENUM('mcu','travel','supplier','training','insurance','utilities','salary','tax','other') DEFAULT 'other',
            
            notes TEXT,
            receipt_file VARCHAR(500) COMMENT 'Path to uploaded receipt/scan',
            
            paid_at DATETIME DEFAULT NULL,
            cancelled_at DATETIME DEFAULT NULL,
            
            created_by INT,
            updated_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_vendor (vendor_name),
            INDEX idx_status (status),
            INDEX idx_date (bill_date),
            INDEX idx_due (due_date),
            INDEX idx_category (category),
            INDEX idx_cost_center (cost_center_id),
            FOREIGN KEY (cost_center_id) REFERENCES finance_cost_centers(id) ON UPDATE CASCADE ON DELETE SET NULL,
            FOREIGN KEY (expense_account_id) REFERENCES finance_chart_of_accounts(id) ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 6. BILL ITEMS (Line Items)
    // ─────────────────────────────────────────────
    echo "Creating finance_bill_items...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_bill_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            bill_id INT NOT NULL,
            description VARCHAR(500) NOT NULL,
            quantity DECIMAL(12,4) DEFAULT 1.0000,
            unit VARCHAR(20) DEFAULT 'unit',
            unit_price DECIMAL(18,2) NOT NULL DEFAULT 0.00,
            amount DECIMAL(18,2) DEFAULT 0.00,
            account_id INT DEFAULT NULL COMMENT 'Expense/COGS account for this line',
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            INDEX idx_bill (bill_id),
            FOREIGN KEY (bill_id) REFERENCES finance_bills(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (account_id) REFERENCES finance_chart_of_accounts(id) ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 7. JOURNAL ENTRIES - General Ledger
    // ─────────────────────────────────────────────
    echo "Creating finance_journal_entries...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_journal_entries (
            id INT PRIMARY KEY AUTO_INCREMENT,
            entry_no VARCHAR(50) DEFAULT NULL UNIQUE,
            entry_date DATE NOT NULL,
            reference_no VARCHAR(50) DEFAULT NULL,
            
            source_type ENUM('manual','invoice','invoice_payment','bill','bill_payment','payroll','adjustment','opening') DEFAULT 'manual',
            source_id INT DEFAULT NULL,
            
            description VARCHAR(500) NOT NULL,
            total_debit DECIMAL(18,2) DEFAULT 0.00,
            total_credit DECIMAL(18,2) DEFAULT 0.00,
            
            is_auto TINYINT(1) DEFAULT 0 COMMENT '1=system generated, 0=manual entry',
            is_posted TINYINT(1) DEFAULT 1 COMMENT '1=posted to ledger, 0=draft',
            is_reversed TINYINT(1) DEFAULT 0,
            reversed_by INT DEFAULT NULL,
            
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_date (entry_date),
            INDEX idx_source (source_type, source_id),
            INDEX idx_posted (is_posted),
            INDEX idx_auto (is_auto)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 8. JOURNAL LINES (Double-Entry Lines)
    // ─────────────────────────────────────────────
    echo "Creating finance_journal_lines...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_journal_lines (
            id INT PRIMARY KEY AUTO_INCREMENT,
            journal_entry_id INT NOT NULL,
            account_id INT NOT NULL,
            cost_center_id INT DEFAULT NULL,
            
            debit DECIMAL(18,2) DEFAULT 0.00,
            credit DECIMAL(18,2) DEFAULT 0.00,
            
            description VARCHAR(255) DEFAULT NULL,
            
            INDEX idx_journal (journal_entry_id),
            INDEX idx_account (account_id),
            INDEX idx_cost_center (cost_center_id),
            FOREIGN KEY (journal_entry_id) REFERENCES finance_journal_entries(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (account_id) REFERENCES finance_chart_of_accounts(id) ON UPDATE CASCADE,
            FOREIGN KEY (cost_center_id) REFERENCES finance_cost_centers(id) ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // ─────────────────────────────────────────────
    // 9. PAYMENTS (Shared for Invoice & Bill payments)
    // ─────────────────────────────────────────────
    echo "Creating finance_payments...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS finance_payments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            payment_no VARCHAR(50) DEFAULT NULL,
            payment_type ENUM('receivable','payable') NOT NULL COMMENT 'receivable=invoice, payable=bill',
            reference_id INT NOT NULL COMMENT 'invoice_id or bill_id',
            
            payment_date DATE NOT NULL,
            amount DECIMAL(18,2) NOT NULL,
            
            payment_method ENUM('cash','bank_transfer','check','giro','other') DEFAULT 'bank_transfer',
            bank_account_id INT DEFAULT NULL COMMENT 'COA cash/bank account',
            
            reference_number VARCHAR(100) DEFAULT NULL COMMENT 'Transfer ref / check no',
            notes TEXT,
            
            journal_entry_id INT DEFAULT NULL COMMENT 'Auto-generated journal entry',
            
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            INDEX idx_type_ref (payment_type, reference_id),
            INDEX idx_date (payment_date),
            INDEX idx_journal (journal_entry_id),
            FOREIGN KEY (bank_account_id) REFERENCES finance_chart_of_accounts(id) ON UPDATE CASCADE ON DELETE SET NULL,
            FOREIGN KEY (journal_entry_id) REFERENCES finance_journal_entries(id) ON UPDATE CASCADE ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "\n✅ All tables created successfully!\n\n";

    // ═══════════════════════════════════════════════
    // INSERT DEFAULT DATA
    // ═══════════════════════════════════════════════

    // --- Default Chart of Accounts (IFRS/PSAK Standard) ---
    echo "Inserting default Chart of Accounts (IFRS/PSAK)...\n";
    
    $accounts = [
        // ASSETS (1xxx)
        ['1-0000', 'Aset', 'Assets', 'asset', 1, 1, 10],
        ['1-1000', 'Kas', 'Cash on Hand', 'asset', 1, 1, 11],
        ['1-1100', 'Bank BCA', 'Bank BCA', 'asset', 1, 1, 12],
        ['1-1200', 'Bank Mandiri', 'Bank Mandiri', 'asset', 1, 1, 13],
        ['1-1300', 'Bank BNI', 'Bank BNI', 'asset', 1, 1, 14],
        ['1-1400', 'Piutang Usaha', 'Accounts Receivable', 'asset', 1, 1, 20],
        ['1-1500', 'Piutang Karyawan', 'Employee Receivable', 'asset', 1, 0, 21],
        ['1-1600', 'Uang Muka', 'Prepaid Expenses', 'asset', 1, 0, 22],
        ['1-2000', 'Aset Tetap', 'Fixed Assets', 'asset', 1, 0, 30],
        ['1-2100', 'Akumulasi Penyusutan', 'Accumulated Depreciation', 'asset', 1, 0, 31],
        
        // LIABILITIES (2xxx)
        ['2-0000', 'Kewajiban', 'Liabilities', 'liability', 1, 1, 40],
        ['2-1000', 'Hutang Usaha', 'Accounts Payable', 'liability', 1, 1, 41],
        ['2-1100', 'Hutang Pajak', 'Tax Payable', 'liability', 1, 1, 42],
        ['2-1200', 'Hutang Gaji', 'Salaries Payable', 'liability', 1, 0, 43],
        ['2-1300', 'Pendapatan Diterima Dimuka', 'Unearned Revenue', 'liability', 1, 0, 44],
        
        // EQUITY (3xxx)
        ['3-0000', 'Ekuitas', 'Equity', 'equity', 1, 1, 50],
        ['3-1000', 'Modal Disetor', 'Paid-in Capital', 'equity', 1, 1, 51],
        ['3-2000', 'Laba Ditahan', 'Retained Earnings', 'equity', 1, 1, 52],
        ['3-3000', 'Laba Tahun Berjalan', 'Current Year Earnings', 'equity', 1, 1, 53],
        
        // REVENUE (4xxx)
        ['4-0000', 'Pendapatan', 'Revenue', 'revenue', 1, 1, 60],
        ['4-1000', 'Pendapatan Jasa Crewing', 'Crewing Service Revenue', 'revenue', 1, 1, 61],
        ['4-1100', 'Pendapatan Ship Chandler', 'Ship Chandler Revenue', 'revenue', 1, 1, 62],
        ['4-1200', 'Management Fee', 'Management Fee', 'revenue', 1, 1, 63],
        ['4-1300', 'Placement Fee', 'Placement Fee', 'revenue', 1, 1, 64],
        ['4-1400', 'Service Charge', 'Service Charge', 'revenue', 1, 0, 65],
        ['4-1500', 'Handling Fee', 'Handling Fee', 'revenue', 1, 0, 66],
        ['4-1900', 'Pendapatan Lainnya', 'Other Revenue', 'revenue', 1, 0, 69],
        
        // COGS (5xxx)
        ['5-0000', 'Harga Pokok', 'Cost of Goods Sold', 'cogs', 1, 1, 70],
        ['5-1000', 'Pembelian Barang Supplier', 'Supplier Purchases', 'cogs', 1, 1, 71],
        ['5-1100', 'Biaya Dokumen Crew', 'Crew Documentation Cost', 'cogs', 1, 1, 72],
        ['5-1200', 'Gaji Crew (Payroll)', 'Crew Salary (Payroll)', 'cogs', 1, 1, 73],
        ['5-1300', 'Biaya MCU Crew', 'Crew Medical Cost', 'cogs', 1, 0, 74],
        ['5-1400', 'Biaya Tiket & Transportasi Crew', 'Crew Travel & Transport', 'cogs', 1, 0, 75],
        ['5-1500', 'Biaya Training Crew', 'Crew Training Cost', 'cogs', 1, 0, 76],
        ['5-1900', 'Biaya Langsung Lainnya', 'Other Direct Costs', 'cogs', 1, 0, 79],
        
        // EXPENSES (6xxx)
        ['6-0000', 'Biaya Operasional', 'Operating Expenses', 'expense', 1, 1, 80],
        ['6-1000', 'Gaji Karyawan Internal', 'Internal Staff Salary', 'expense', 1, 1, 81],
        ['6-1100', 'Tunjangan Karyawan', 'Employee Benefits', 'expense', 1, 0, 82],
        ['6-1200', 'BPJS Kesehatan & Ketenagakerjaan', 'BPJS Health & Employment', 'expense', 1, 0, 83],
        ['6-2000', 'Sewa Kantor', 'Office Rent', 'expense', 1, 1, 84],
        ['6-2100', 'Listrik, Air & Internet', 'Utilities', 'expense', 1, 1, 85],
        ['6-2200', 'Perlengkapan Kantor (ATK)', 'Office Supplies', 'expense', 1, 0, 86],
        ['6-3000', 'Transportasi & Perjalanan Dinas', 'Travel & Transportation', 'expense', 1, 0, 87],
        ['6-3100', 'Akomodasi', 'Accommodation', 'expense', 1, 0, 88],
        ['6-4000', 'Pelatihan & Pengembangan', 'Training & Development', 'expense', 1, 0, 89],
        ['6-5000', 'Asuransi', 'Insurance', 'expense', 1, 0, 90],
        ['6-5100', 'Pajak Perusahaan', 'Corporate Tax', 'expense', 1, 0, 91],
        ['6-6000', 'Marketing & Promosi', 'Marketing & Promotion', 'expense', 1, 0, 92],
        ['6-7000', 'Biaya Bank & Administrasi', 'Bank & Admin Fees', 'expense', 1, 0, 93],
        ['6-8000', 'Penyusutan Aset', 'Depreciation', 'expense', 1, 0, 94],
        ['6-9000', 'Biaya Operasional Lainnya', 'Other Operating Expenses', 'expense', 1, 0, 99],
    ];

    $insertSql = "INSERT IGNORE INTO finance_chart_of_accounts (code, name, name_en, type, is_system, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertSql);
    
    foreach ($accounts as $acc) {
        $stmt->bind_param('ssssiis', $acc[0], $acc[1], $acc[2], $acc[3], $acc[4], $acc[5], $acc[6]);
        $stmt->execute();
    }
    $stmt->close();
    echo "  → " . count($accounts) . " accounts inserted\n";

    // --- Default Cost Centers ---
    echo "Inserting default Cost Centers...\n";
    
    $costCenters = [
        ['CC-HQ',      'Kantor Pusat',         'Head Office',          'Biaya operasional kantor pusat'],
        ['CC-CREW',    'Divisi Crewing',        'Crewing Division',     'Operasional penempatan dan manajemen crew'],
        ['CC-SHIP',    'Divisi Ship Chandler',  'Ship Chandler Division','Operasional supply dan logistik kapal'],
        ['CC-RECRUIT', 'Divisi Rekrutmen',      'Recruitment Division',  'Operasional rekrutmen dan seleksi'],
        ['CC-ADMIN',   'Administrasi & Umum',   'General & Admin',      'Biaya administrasi umum perusahaan'],
        ['CC-FINANCE', 'Divisi Keuangan',       'Finance Division',     'Operasional keuangan dan akuntansi'],
        ['CC-IT',      'Divisi IT',             'IT Division',          'Infrastruktur teknologi dan sistem'],
    ];

    $insertCC = "INSERT IGNORE INTO finance_cost_centers (code, name, name_en, description) VALUES (?, ?, ?, ?)";
    $stmtCC = $db->prepare($insertCC);
    
    foreach ($costCenters as $cc) {
        $stmtCC->bind_param('ssss', $cc[0], $cc[1], $cc[2], $cc[3]);
        $stmtCC->execute();
    }
    $stmtCC->close();
    echo "  → " . count($costCenters) . " cost centers inserted\n";

    $db->close();

    echo "\n✅ Migration completed successfully!\n";
    echo "Tables created: 9\n";
    echo "Default accounts: " . count($accounts) . "\n";
    echo "Default cost centers: " . count($costCenters) . "\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

