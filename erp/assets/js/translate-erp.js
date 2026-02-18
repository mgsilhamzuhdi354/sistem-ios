/**
 * PT Indo Ocean ERP - Translation System
 * Supports: English (en) and Indonesian (id)
 */

const TranslationService = {
    // Current language
    currentLang: localStorage.getItem('erp_language') || 'id',

    // Translation dictionary
    translations: {
        // ===============================
        // NAVIGATION
        // ===============================
        nav_dashboard: {
            en: 'Dashboard',
            id: 'Dasbor'
        },
        nav_contracts: {
            en: 'Contracts',
            id: 'Kontrak'
        },
        nav_all_contracts: {
            en: 'All Contracts',
            id: 'Semua Kontrak'
        },
        nav_new_contract: {
            en: 'New Contract',
            id: 'Buat Kontrak'
        },
        nav_expiring: {
            en: 'Expiring',
            id: 'Kontrak Expire'
        },
        nav_vessels: {
            en: 'Vessels',
            id: 'Kapal'
        },
        nav_clients: {
            en: 'Clients',
            id: 'Klien'
        },
        client_list: {
            en: 'Client List',
            id: 'Daftar Klien'
        },
        profit_per_client: {
            en: 'Profit per Client',
            id: 'Profit per Client'
        },
        nav_ranks: {
            en: 'Master Ranks',
            id: 'Master Jabatan'
        },
        nav_notifications: {
            en: 'Notifications',
            id: 'Notifikasi'
        },
        nav_settings: {
            en: 'Settings',
            id: 'Pengaturan'
        },
        nav_users: {
            en: 'Users',
            id: 'Manajemen User'
        },
        nav_data_crew: {
            en: 'Crew Data',
            id: 'Data Crew'
        },
        nav_all_crew: {
            en: 'All Crew',
            id: 'Semua Crew'
        },
        nav_on_board: {
            en: 'On Board',
            id: 'Sedang Bekerja'
        },
        nav_stand_by: {
            en: 'Stand By',
            id: 'Menunggu'
        },
        nav_skill_matrix: {
            en: 'Skill Matrix',
            id: 'Matriks Skill'
        },
        nav_payroll_crew: {
            en: 'Crew Payroll',
            id: 'Payroll Crew'
        },
        nav_generate_payroll: {
            en: 'Generate Payroll',
            id: 'Generate Payroll'
        },
        nav_payroll_history: {
            en: 'Payroll History',
            id: 'Histori Payroll'
        },
        nav_crew_documents: {
            en: 'Crew Documents',
            id: 'Dokumen Crew'
        },
        nav_employee_data: {
            en: 'Employee Data',
            id: 'Data Karyawan'
        },
        nav_attendance: {
            en: 'Attendance',
            id: 'Absensi'
        },
        nav_employee_payroll: {
            en: 'Employee Payroll',
            id: 'Payroll Karyawan'
        },
        nav_pipeline: {
            en: 'Pipeline',
            id: 'Pipeline'
        },
        nav_onboarding: {
            en: 'Onboarding',
            id: 'Onboarding'
        },
        nav_visitor_cp: {
            en: 'Visitor CP',
            id: 'Visitor CP'
        },
        nav_activity_log: {
            en: 'Activity Log',
            id: 'Log Aktivitas'
        },
        nav_reports: {
            en: 'Reports',
            id: 'Laporan'
        },
        nav_overview: {
            en: 'Overview',
            id: 'Ringkasan'
        },
        nav_crew_report: {
            en: 'Crew Report',
            id: 'Laporan Crew'
        },
        nav_financial_report: {
            en: 'Financial Report',
            id: 'Laporan Keuangan'
        },
        nav_audit_log: {
            en: 'Audit Log',
            id: 'Log Audit'
        },

        // ===============================
        // COMMON BUTTONS & ACTIONS
        // ===============================
        btn_save: {
            en: 'Save',
            id: 'Simpan'
        },
        btn_cancel: {
            en: 'Cancel',
            id: 'Batal'
        },
        btn_delete: {
            en: 'Delete',
            id: 'Hapus'
        },
        btn_edit: {
            en: 'Edit',
            id: 'Edit'
        },
        btn_add: {
            en: 'Add',
            id: 'Tambah'
        },
        btn_back: {
            en: 'Back',
            id: 'Kembali'
        },
        btn_search: {
            en: 'Search',
            id: 'Cari'
        },
        btn_filter: {
            en: 'Filter',
            id: 'Filter'
        },
        btn_export: {
            en: 'Export',
            id: 'Ekspor'
        },
        btn_import: {
            en: 'Import',
            id: 'Impor'
        },
        btn_refresh: {
            en: 'Refresh',
            id: 'Segarkan'
        },
        btn_view: {
            en: 'View',
            id: 'Lihat'
        },
        btn_download: {
            en: 'Download',
            id: 'Unduh'
        },
        btn_upload: {
            en: 'Upload',
            id: 'Unggah'
        },
        btn_submit: {
            en: 'Submit',
            id: 'Kirim'
        },
        btn_confirm: {
            en: 'Confirm',
            id: 'Konfirmasi'
        },
        btn_close: {
            en: 'Close',
            id: 'Tutup'
        },

        // ===============================
        // COMMON LABELS
        // ===============================
        label_name: {
            en: 'Name',
            id: 'Nama'
        },
        label_email: {
            en: 'Email',
            id: 'Email'
        },
        label_phone: {
            en: 'Phone',
            id: 'Telepon'
        },
        label_address: {
            en: 'Address',
            id: 'Alamat'
        },
        label_status: {
            en: 'Status',
            id: 'Status'
        },
        label_date: {
            en: 'Date',
            id: 'Tanggal'
        },
        label_action: {
            en: 'Action',
            id: 'Aksi'
        },
        label_description: {
            en: 'Description',
            id: 'Deskripsi'
        },
        label_notes: {
            en: 'Notes',
            id: 'Catatan'
        },
        label_created_at: {
            en: 'Created At',
            id: 'Dibuat'
        },
        label_updated_at: {
            en: 'Updated At',
            id: 'Diperbarui'
        },
        label_type: {
            en: 'Type',
            id: 'Tipe'
        },
        label_total: {
            en: 'Total',
            id: 'Total'
        },
        label_amount: {
            en: 'Amount',
            id: 'Jumlah'
        },

        // ===============================
        // DASHBOARD
        // ===============================
        dashboard_title: {
            en: 'Dashboard',
            id: 'Dasbor'
        },
        dashboard_welcome: {
            en: 'Welcome back! Here\'s your contract overview.',
            id: 'Selamat datang! Berikut ringkasan kontrak Anda.'
        },
        active_contracts: {
            en: 'Active Contracts',
            id: 'Kontrak Aktif'
        },
        expiring_soon: {
            en: 'Expiring Soon',
            id: 'Segera Berakhir'
        },
        total_crew: {
            en: 'Total Crew',
            id: 'Total Crew'
        },
        monthly_payroll: {
            en: 'Monthly Payroll',
            id: 'Payroll Bulanan'
        },
        contract_alerts: {
            en: 'Contract Alerts',
            id: 'Peringatan Kontrak'
        },
        contracts_expiring_7days: {
            en: 'Contracts expiring in 7 days',
            id: 'Kontrak berakhir dalam 7 hari'
        },
        contracts_expiring_30days: {
            en: 'Contracts expiring in 30 days',
            id: 'Kontrak berakhir dalam 30 hari'
        },
        contracts_expiring_60days: {
            en: 'Contracts expiring in 60 days',
            id: 'Kontrak berakhir dalam 60 hari'
        },
        contracts_by_vessel: {
            en: 'Contracts by Vessel',
            id: 'Kontrak per Kapal'
        },
        monthly_contracts: {
            en: 'Monthly Contracts',
            id: 'Kontrak Bulanan'
        },
        recent_contracts: {
            en: 'Recent Contracts',
            id: 'Kontrak Terbaru'
        },
        view_all: {
            en: 'View All',
            id: 'Lihat Semua'
        },
        new_contract: {
            en: 'New Contract',
            id: 'Kontrak Baru'
        },
        btn_new_contract: {
            en: 'New Contract',
            id: 'Kontrak Baru'
        },

        // Dashboard Stats
        stat_active_contracts: {
            en: 'Active Contracts',
            id: 'Kontrak Aktif'
        },
        stat_expiring_soon: {
            en: 'Expiring Soon',
            id: 'Akan Expire'
        },
        stat_total_crew: {
            en: 'Total Crew',
            id: 'Total Crew'
        },
        stat_monthly_payroll: {
            en: 'Monthly Payroll',
            id: 'Payroll Bulanan'
        },

        // Dashboard Filters
        filter_today: {
            en: 'Today',
            id: 'Hari Ini'
        },
        filter_this_week: {
            en: 'This Week',
            id: 'Minggu Ini'
        },
        filter_this_month: {
            en: 'This Month',
            id: 'Bulan Ini'
        },
        filter_this_quarter: {
            en: 'This Quarter',
            id: 'Kuartal Ini'
        },
        filter_this_year: {
            en: 'This Year',
            id: 'Tahun Ini'
        },
        filter_last_3_months: {
            en: 'Last 3 Months',
            id: '3 Bulan Terakhir'
        },

        // Dashboard Alerts
        alert: {
            en: 'Alert',
            id: 'Peringatan'
        },
        contracts_expiring_7: {
            en: 'Contracts expire in 7 days',
            id: 'Kontrak expire dalam 7 hari'
        },
        contracts_expiring_30: {
            en: 'Contracts expire in 30 days',
            id: 'Kontrak expire dalam 30 hari'
        },
        contracts_expiring_60: {
            en: 'Contracts expire in 60 days',
            id: 'Kontrak expire dalam 60 hari'
        },

        // Dashboard Charts
        chart_contracts_by_vessel: {
            en: 'Contracts by Vessel',
            id: 'Kontrak per Kapal'
        },
        chart_monthly_contracts: {
            en: 'Monthly Contracts',
            id: 'Kontrak Bulanan'
        },

        // Dashboard Table Headers
        th_contract_no: {
            en: 'Contract No',
            id: 'No Kontrak'
        },
        th_crew_name: {
            en: 'Crew Name',
            id: 'Nama Crew'
        },
        th_rank: {
            en: 'Rank',
            id: 'Jabatan'
        },
        th_vessel: {
            en: 'Vessel',
            id: 'Kapal'
        },
        th_sign_on: {
            en: 'Sign On',
            id: 'Naik Kapal'
        },
        th_sign_off: {
            en: 'Sign Off',
            id: 'Turun Kapal'
        },
        th_status: {
            en: 'Status',
            id: 'Status'
        },
        th_actions: {
            en: 'Actions',
            id: 'Aksi'
        },

        // Other Dashboard
        just_now: {
            en: 'Just now',
            id: 'Baru saja'
        },
        no_contracts_found: {
            en: 'No contracts found',
            id: 'Tidak ada kontrak'
        },

        // Section Headers
        employee_management: {
            en: 'Employee Management',
            id: 'Manajemen Karyawan'
        },
        recruitment: {
            en: 'Recruitment',
            id: 'Rekrutmen'
        },
        monitoring: {
            en: 'Monitoring',
            id: 'Pemantauan'
        },

        // ===============================
        // CREW MANAGEMENT
        // ===============================
        crew_management: {
            en: 'Crew Management',
            id: 'Manajemen Crew'
        },
        crew_list: {
            en: 'Crew List',
            id: 'Daftar Crew'
        },
        add_crew: {
            en: 'Add Crew',
            id: 'Tambah Crew'
        },
        edit_crew: {
            en: 'Edit Crew',
            id: 'Edit Crew'
        },
        crew_details: {
            en: 'Crew Details',
            id: 'Detail Crew'
        },
        personal_info: {
            en: 'Personal Information',
            id: 'Informasi Pribadi'
        },
        full_name: {
            en: 'Full Name',
            id: 'Nama Lengkap'
        },
        date_of_birth: {
            en: 'Date of Birth',
            id: 'Tanggal Lahir'
        },
        place_of_birth: {
            en: 'Place of Birth',
            id: 'Tempat Lahir'
        },
        gender: {
            en: 'Gender',
            id: 'Jenis Kelamin'
        },
        male: {
            en: 'Male',
            id: 'Laki-laki'
        },
        female: {
            en: 'Female',
            id: 'Perempuan'
        },
        marital_status: {
            en: 'Marital Status',
            id: 'Status Pernikahan'
        },
        single: {
            en: 'Single',
            id: 'Belum Menikah'
        },
        married: {
            en: 'Married',
            id: 'Menikah'
        },
        divorced: {
            en: 'Divorced',
            id: 'Cerai'
        },
        nationality: {
            en: 'Nationality',
            id: 'Kewarganegaraan'
        },
        religion: {
            en: 'Religion',
            id: 'Agama'
        },
        rank: {
            en: 'Rank',
            id: 'Jabatan'
        },
        experience: {
            en: 'Experience',
            id: 'Pengalaman'
        },
        years_experience: {
            en: 'Years of Experience',
            id: 'Tahun Pengalaman'
        },
        emergency_contact: {
            en: 'Emergency Contact',
            id: 'Kontak Darurat'
        },
        bank_info: {
            en: 'Bank Information',
            id: 'Informasi Bank'
        },
        bank_name: {
            en: 'Bank Name',
            id: 'Nama Bank'
        },
        account_number: {
            en: 'Account Number',
            id: 'Nomor Rekening'
        },
        account_holder: {
            en: 'Account Holder',
            id: 'Nama Pemilik Rekening'
        },
        status_available: {
            en: 'Available',
            id: 'Tersedia'
        },
        status_onboard: {
            en: 'On Board',
            id: 'Sedang Bekerja'
        },
        status_standby: {
            en: 'Stand By',
            id: 'Menunggu'
        },
        status_terminated: {
            en: 'Terminated',
            id: 'Diberhentikan'
        },
        no_crew_found: {
            en: 'No crew found',
            id: 'Tidak ada crew ditemukan'
        },

        // ===============================
        // CONTRACTS
        // ===============================
        contract_management: {
            en: 'Contract Management',
            id: 'Manajemen Kontrak'
        },
        contract_list: {
            en: 'Contract List',
            id: 'Daftar Kontrak'
        },
        contract_number: {
            en: 'Contract Number',
            id: 'Nomor Kontrak'
        },
        contract_date: {
            en: 'Contract Date',
            id: 'Tanggal Kontrak'
        },
        start_date: {
            en: 'Start Date',
            id: 'Tanggal Mulai'
        },
        end_date: {
            en: 'End Date',
            id: 'Tanggal Berakhir'
        },
        contract_value: {
            en: 'Contract Value',
            id: 'Nilai Kontrak'
        },
        vessel: {
            en: 'Vessel',
            id: 'Kapal'
        },
        client: {
            en: 'Client',
            id: 'Klien'
        },
        status_active: {
            en: 'Active',
            id: 'Aktif'
        },
        status_expired: {
            en: 'Expired',
            id: 'Berakhir'
        },
        status_pending: {
            en: 'Pending',
            id: 'Menunggu'
        },
        no_contracts_found: {
            en: 'No contracts found',
            id: 'Tidak ada kontrak ditemukan'
        },
        contract_management_subtitle: {
            en: 'Manage crew contracts, renewals, and terminations',
            id: 'Kelola kontrak crew, perpanjangan, dan pemutusan'
        },
        search_contract_crew: {
            en: 'Contract No or Crew Name',
            id: 'No Kontrak atau Nama Crew'
        },
        filter_all_status: {
            en: 'All Status',
            id: 'Semua Status'
        },
        filter_all_vessels: {
            en: 'All Vessels',
            id: 'Semua Kapal'
        },
        filter_all_clients: {
            en: 'All Clients',
            id: 'Semua Klien'
        },
        th_remaining: {
            en: 'Remaining',
            id: 'Sisa'
        },
        days: {
            en: 'days',
            id: 'hari'
        },
        showing: {
            en: 'Showing',
            id: 'Menampilkan'
        },
        of: {
            en: 'of',
            id: 'dari'
        },
        contracts: {
            en: 'contracts',
            id: 'kontrak'
        },

        // ===============================
        // EMPLOYEES
        // ===============================
        employee_attendance: {
            en: 'Employee Attendance',
            id: 'Absensi Karyawan'
        },
        attendance_summary: {
            en: 'Attendance summary for',
            id: 'Rekap absensi bulan'
        },
        hris_connection_error: {
            en: 'Unable to connect to HRIS system.',
            id: 'Tidak dapat terhubung ke sistem HRIS.'
        },
        employee: {
            en: 'Employee',
            id: 'Karyawan'
        },
        position: {
            en: 'Position',
            id: 'Jabatan'
        },
        total_days: {
            en: 'Total Days',
            id: 'Total Hari'
        },
        present: {
            en: 'Present',
            id: 'Hadir'
        },
        leave_permitted: {
            en: 'Permitted Leave',
            id: 'Izin'
        },
        sick: {
            en: 'Sick',
            id: 'Sakit'
        },
        absent: {
            en: 'Absent',
            id: 'Alpha'
        },
        late: {
            en: 'Late',
            id: 'Telat'
        },
        no_attendance_data: {
            en: 'No attendance data available for this period',
            id: 'Belum ada data absensi untuk periode ini'
        },

        // ===============================
        // RECRUITMENT
        // ===============================
        recruitment_pipeline: {
            en: 'Recruitment Pipeline',
            id: 'Recruitment Pipeline'
        },
        recruitment_subtitle: {
            en: 'Candidate management from recruitment system',
            id: 'Manajemen kandidat dari sistem recruitment'
        },
        view_onboarding: {
            en: 'View Onboarding',
            id: 'Lihat Onboarding'
        },
        connection_error: {
            en: 'Connection Error',
            id: 'Error Koneksi'
        },
        recruitment_api_error: {
            en: 'Unable to connect to recruitment system:',
            id: 'Tidak dapat terhubung ke sistem recruitment:'
        },
        total_candidates: {
            en: 'Total Candidates',
            id: 'Total Kandidat'
        },
        in_interview: {
            en: 'In Interview',
            id: 'Sedang Interview'
        },
        approved: {
            en: 'Approved',
            id: 'Disetujui'
        },
        synced_to_erp: {
            en: 'Synced to ERP',
            id: 'Sinkron ke ERP'
        },
        avatar: {
            en: 'Avatar',
            id: 'Avatar'
        },
        name: {
            en: 'Name',
            id: 'Nama'
        },
        department: {
            en: 'Department',
            id: 'Departemen'
        },
        applied: {
            en: 'Applied',
            id: 'Tanggal Melamar'
        },
        no_candidates_found: {
            en: 'No candidates found',
            id: 'Tidak ada kandidat ditemukan'
        },
        candidate_onboarding: {
            en: 'Candidate Onboarding',
            id: 'Candidate Onboarding'
        },
        onboarding_subtitle: {
            en: 'Approved candidates ready for import to ERP',
            id: 'Kandidat yang disetujui siap untuk import ke ERP'
        },
        back_to_pipeline: {
            en: 'Back to Pipeline',
            id: 'Kembali ke Pipeline'
        },
        approved_candidates_title: {
            en: 'Approved Candidates - Ready for Import',
            id: 'Kandidat Disetujui - Siap untuk Import'
        },
        bulk_import_selected: {
            en: 'Bulk Import Selected',
            id: 'Bulk Import Terpilih'
        },
        documents: {
            en: 'Documents',
            id: 'Dokumen'
        },
        sync_status: {
            en: 'Sync Status',
            id: 'Status Sinkronisasi'
        },
        no_approved_candidates: {
            en: 'No approved candidates',
            id: 'Tidak ada kandidat yang disetujui'
        },
        onboarding_empty_message: {
            en: 'Candidates will appear here once approved in recruitment system',
            id: 'Kandidat akan muncul di sini setelah disetujui di sistem recruitment'
        },
        view_all_candidates: {
            en: 'View All Candidates',
            id: 'Lihat Semua Kandidat'
        },
        client_management: {
            en: 'Client / Principal Management',
            id: 'Manajemen Client / Principal'
        },
        client_subtitle: {
            en: 'Manage ship owners and principals',
            id: 'Kelola pemilik kapal dan principal'
        },
        btn_add_client: {
            en: 'Add Client',
            id: 'Tambah Client'
        },
        th_vessels: {
            en: 'Vessels',
            id: 'Kapal'
        },
        active_crew: {
            en: 'Active Crew',
            id: 'Crew Aktif'
        },
        monthly: {
            en: 'Monthly',
            id: 'Bulanan'
        },
        employee_data: {
            en: 'Employee Data',
            id: 'Data Karyawan'
        },
        employee_subtitle: {
            en: 'Employee data from HRIS system',
            id: 'Data karyawan dari sistem HRIS'
        },
        total_employees: {
            en: 'Total Employees',
            id: 'Total Karyawan'
        },
        status_active: {
            en: 'Active',
            id: 'Aktif'
        },
        status_probation: {
            en: 'Probation',
            id: 'Probation'
        },
        status_resign: {
            en: 'Resign',
            id: 'Resign'
        },
        email: {
            en: 'Email',
            id: 'Email'
        },
        phone: {
            en: 'Phone',
            id: 'No. HP'
        },
        no_employee_data: {
            en: 'No employee data available yet',
            id: 'Belum ada data karyawan'
        },
        create_new_contract: {
            en: 'Create New Contract',
            id: 'Buat Kontrak Baru'
        },
        edit_contract: {
            en: 'Edit Contract',
            id: 'Edit Kontrak'
        },
        create_contract_subtitle: {
            en: 'Fill in the contract details below',
            id: 'Isi detail kontrak di bawah ini'
        },
        update_contract_subtitle: {
            en: 'Update contract details for',
            id: 'Perbarui detail kontrak untuk'
        },
        contract_information: {
            en: 'Contract Information',
            id: 'Informasi Kontrak'
        },
        contract_number: {
            en: 'Contract Number',
            id: 'Nomor Kontrak'
        },
        contract_type: {
            en: 'Contract Type',
            id: 'Tipe Kontrak'
        },
        crew_assignment: {
            en: 'Crew Assignment',
            id: 'Penugasan Crew'
        },
        crew_id: {
            en: 'Crew ID (from Recruitment)',
            id: 'Crew ID (dari Recruitment)'
        },
        contract_period: {
            en: 'Contract Period',
            id: 'Periode Kontrak'
        },
        salary_structure: {
            en: 'Salary Structure',
            id: 'Struktur Gaji'
        },
        tax_settings: {
            en: 'Tax Settings (PPh 21)',
            id: 'Pengaturan Pajak (PPh 21)'
        },
        save_as_draft: {
            en: 'Save as Draft',
            id: 'Simpan sebagai Draft'
        },
        submit_for_approval: {
            en: 'Submit for Approval',
            id: 'Kirim untuk Persetujuan'
        },
        payroll_management: {
            en: 'Payroll Management',
            id: 'Manajemen Payroll'
        },
        payroll_subtitle: {
            en: 'Process crew salaries and taxes',
            id: 'Proses gaji crew dan pajak'
        },
        btn_export_csv: {
            en: 'Export CSV',
            id: 'Export CSV'
        },
        run_payroll: {
            en: 'Run Payroll',
            id: 'Jalankan Payroll'
        },
        total_crew: {
            en: 'Total Crew',
            id: 'Total Crew'
        },
        gross_salary: {
            en: 'Gross Salary',
            id: 'Gaji Kotor'
        },
        total_tax: {
            en: 'Total Tax (5%)',
            id: 'Total Pajak (5%)'
        },
        net_payable: {
            en: 'Net Payable',
            id: 'Total Dibayarkan'
        },
        crew_name: {
            en: 'Crew Name',
            id: 'Nama Crew'
        },
        original: {
            en: 'Original',
            id: 'Original'
        },
        basic: {
            en: 'Basic',
            id: 'Basic'
        },
        allowances: {
            en: 'Allowances',
            id: 'Tunjangan'
        },
        gross: {
            en: 'Gross',
            id: 'Bruto'
        },
        tax: {
            en: 'Tax (5%)',
            id: 'Pajak (5%)'
        },
        net: {
            en: 'Net',
            id: 'Netto'
        },
        no_payroll_data: {
            en: 'No payroll data. Click "Run Payroll" to generate.',
            id: 'Belum ada data payroll. Klik "Jalankan Payroll" untuk generate.'
        },
        add_new_vessel: {
            en: 'Add New Vessel',
            id: 'Tambah Kapal Baru'
        },
        edit_vessel: {
            en: 'Edit Vessel',
            id: 'Edit Kapal'
        },
        vessel_form_subtitle: {
            en: 'Enter vessel information below',
            id: 'Masukkan informasi kapal di bawah ini'
        },
        update_vessel_subtitle: {
            en: 'Update vessel details',
            id: 'Perbarui detail kapal'
        },
        vessel_information: {
            en: 'Vessel Information',
            id: 'Informasi Kapal'
        },
        technical_details: {
            en: 'Technical Details',
            id: 'Detail Teknis'
        },
        add_vessel: {
            en: 'Add Vessel',
            id: 'Tambah Kapal'
        },
        update_vessel: {
            en: 'Update Vessel',
            id: 'Update Kapal'
        },
        candidate_detail: {
            en: 'Candidate Detail',
            id: 'Detail Kandidat'
        },
        address: {
            en: 'Address',
            id: 'Alamat'
        },
        emergency_contact: {
            en: 'Emergency Contact',
            id: 'Kontak Darurat'
        },
        interview_history: {
            en: 'Interview History',
            id: 'Riwayat Interview'
        },
        medical_checkups: {
            en: 'Medical Checkups',
            id: 'Medical Checkup'
        },
        monitoring_center: {
            en: 'Monitoring Center',
            id: 'Pusat Monitoring'
        },
        nav_analytics_dashboard: {
            en: 'Analytics Dashboard',
            id: 'Dashboard Analytics'
        },
        nav_visitor_analytics: {
            en: 'Visitor Analytics',
            id: 'Analitik Pengunjung'
        },
        nav_recruitment_metrics: {
            en: 'Recruitment Metrics',
            id: 'Metrik Rekrutmen'
        },

        // ===============================
        // REPORTS
        // ===============================
        profit_per_vessel: {
            en: 'Profit per Vessel',
            id: 'Profit per Kapal'
        },
        vessel_profit_subtitle: {
            en: 'Profit margin analysis for each vessel',
            id: 'Analisis profit margin untuk setiap kapal'
        },
        btn_back: {
            en: 'Back',
            id: 'Kembali'
        },
        total_revenue: {
            en: 'Total Revenue',
            id: 'Total Pendapatan'
        },
        total_cost: {
            en: 'Total Cost',
            id: 'Total Biaya'
        },
        total_profit: {
            en: 'Total Profit',
            id: 'Total Profit'
        },
        total_loss: {
            en: 'Total Loss',
            id: 'Total Rugi'
        },
        avg_margin: {
            en: 'Avg Margin',
            id: 'Rata-rata Margin'
        },
        th_vessel: {
            en: 'Vessel',
            id: 'Kapal'
        },
        client: {
            en: 'Client',
            id: 'Klien'
        },
        revenue_usd: {
            en: 'Revenue (USD)',
            id: 'Pendapatan (USD)'
        },
        cost_usd: {
            en: 'Cost (USD)',
            id: 'Biaya (USD)'
        },
        profit_usd: {
            en: 'Profit (USD)',
            id: 'Profit (USD)'
        },
        margin: {
            en: 'Margin',
            id: 'Margin'
        },
        no_vessel_data: {
            en: 'No vessel data available',
            id: 'Belum ada data vessel'
        },
        profit_comparison_chart: {
            en: 'Profit Comparison Chart',
            id: 'Grafik Perbandingan Profit'
        },

        // ===============================
        // VESSELS
        // ===============================
        vessel_management: {
            en: 'Vessel Management',
            id: 'Manajemen Kapal'
        },
        vessel_list: {
            en: 'Vessel List',
            id: 'Daftar Kapal'
        },
        vessel_name: {
            en: 'Vessel Name',
            id: 'Nama Kapal'
        },
        vessel_type: {
            en: 'Vessel Type',
            id: 'Tipe Kapal'
        },
        vessel_flag: {
            en: 'Flag',
            id: 'Bendera'
        },
        gross_tonnage: {
            en: 'Gross Tonnage',
            id: 'Gross Tonnage'
        },
        engine_type: {
            en: 'Engine Type',
            id: 'Tipe Mesin'
        },
        profit_per_vessel: {
            en: 'Profit per Vessel',
            id: 'Profit per Kapal'
        },
        total_revenue: {
            en: 'Total Revenue',
            id: 'Total Pendapatan'
        },
        total_cost: {
            en: 'Total Cost',
            id: 'Total Biaya'
        },
        total_profit: {
            en: 'Total Profit',
            id: 'Total Profit'
        },
        avg_margin: {
            en: 'Avg Margin',
            id: 'Rata-rata Margin'
        },
        no_vessel_found: {
            en: 'No vessel found',
            id: 'Tidak ada kapal ditemukan'
        },
        vessel_management_subtitle: {
            en: 'Manage fleet vessels and their crews',
            id: 'Kelola kapal armada dan crew-nya'
        },
        btn_add_vessel: {
            en: 'Add Vessel',
            id: 'Tambah Kapal'
        },
        vessel_type: {
            en: 'Type:',
            id: 'Tipe:'
        },
        vessel_imo: {
            en: 'IMO:',
            id: 'IMO:'
        },
        vessel_flag: {
            en: 'Flag:',
            id: 'Bendera:'
        },
        vessel_owner: {
            en: 'Owner:',
            id: 'Pemilik:'
        },
        crew_onboard: {
            en: 'Crew Onboard',
            id: 'Crew di Kapal'
        },
        crew_database: {
            en: 'Crew Database',
            id: 'Database Crew'
        },
        crew_subtitle: {
            en: 'Manage ship crew data',
            id: 'Kelola data kru kapal'
        },
        search_crew: {
            en: 'Name, ID, email, phone...',
            id: 'Nama, ID, email, telepon...'
        },
        status_on_leave: {
            en: 'On Leave',
            id: 'Cuti'
        },
        status_blacklisted: {
            en: 'Blacklisted',
            id: 'Blacklisted'
        },
        employee_id: {
            en: 'Employee ID',
            id: 'Employee ID'
        },
        contact: {
            en: 'Contact',
            id: 'Kontak'
        },
        crew: {
            en: 'Crew',
            id: 'Crew'
        },
        btn_reset: {
            en: 'Reset',
            id: 'Reset'
        },

        // ===============================
        // DOCUMENTS
        // ===============================
        document_management: {
            en: 'Document Management',
            id: 'Manajemen Dokumen'
        },
        document_subtitle: {
            en: 'Manage crew documents and track expiry',
            id: 'Kelola dokumen kru dan tracking expiry'
        },
        status_valid: {
            en: 'Valid',
            id: 'Valid'
        },
        stat_expiring_soon: {
            en: 'Expiring Soon',
            id: 'Segera Berakhir'
        },
        status_expired: {
            en: 'Expired',
            id: 'Expired'
        },
        total_documents: {
            en: 'Total Documents',
            id: 'Total Dokumen'
        },
        expired_documents: {
            en: 'Expired Documents',
            id: 'Dokumen Expired'
        },
        document: {
            en: 'Document',
            id: 'Dokumen'
        },
        type: {
            en: 'Type',
            id: 'Tipe'
        },
        expired_on: {
            en: 'Expired On',
            id: 'Expired Pada'
        },
        expiring_within_90days: {
            en: 'Expiring Within 90 Days',
            id: 'Akan Expired dalam 90 Hari'
        },
        all_documents_valid: {
            en: 'All Documents Valid!',
            id: 'Semua Dokumen Valid!'
        },
        no_expired_docs_message: {
            en: 'No documents expired or will expire within 90 days.',
            id: 'Tidak ada dokumen yang expired atau akan expired dalam 90 hari.'
        },
        document_list: {
            en: 'Document List',
            id: 'Daftar Dokumen'
        },
        document_type: {
            en: 'Document Type',
            id: 'Tipe Dokumen'
        },
        document_number: {
            en: 'Document Number',
            id: 'Nomor Dokumen'
        },
        issue_date: {
            en: 'Issue Date',
            id: 'Tanggal Terbit'
        },
        expiry_date: {
            en: 'Expiry Date',
            id: 'Tanggal Berakhir'
        },
        issuing_authority: {
            en: 'Issuing Authority',
            id: 'Pihak Penerbit'
        },
        doc_status_valid: {
            en: 'Valid',
            id: 'Berlaku'
        },
        doc_status_expired: {
            en: 'Expired',
            id: 'Kadaluarsa'
        },
        doc_status_expiring: {
            en: 'Expiring Soon',
            id: 'Segera Berakhir'
        },
        no_documents_found: {
            en: 'No documents found',
            id: 'Tidak ada dokumen ditemukan'
        },

        // ===============================
        // PAYROLL
        // ===============================
        payroll_management: {
            en: 'Payroll Management',
            id: 'Manajemen Penggajian'
        },
        payroll_list: {
            en: 'Payroll List',
            id: 'Daftar Gaji'
        },
        basic_salary: {
            en: 'Basic Salary',
            id: 'Gaji Pokok'
        },
        allowances: {
            en: 'Allowances',
            id: 'Tunjangan'
        },
        deductions: {
            en: 'Deductions',
            id: 'Potongan'
        },
        net_salary: {
            en: 'Net Salary',
            id: 'Gaji Bersih'
        },
        pay_period: {
            en: 'Pay Period',
            id: 'Periode Gaji'
        },
        payment_date: {
            en: 'Payment Date',
            id: 'Tanggal Pembayaran'
        },
        payment_status: {
            en: 'Payment Status',
            id: 'Status Pembayaran'
        },
        paid: {
            en: 'Paid',
            id: 'Dibayar'
        },
        unpaid: {
            en: 'Unpaid',
            id: 'Belum Dibayar'
        },

        // ===============================
        // SETTINGS
        // ===============================
        settings_title: {
            en: 'Settings',
            id: 'Pengaturan'
        },
        settings_subtitle: {
            en: 'System configuration and preferences',
            id: 'Konfigurasi sistem dan preferensi'
        },
        company_info: {
            en: 'Company Information',
            id: 'Informasi Perusahaan'
        },
        company_name: {
            en: 'Company Name',
            id: 'Nama Perusahaan'
        },
        company_email: {
            en: 'Email',
            id: 'Email'
        },
        company_phone: {
            en: 'Phone',
            id: 'Telepon'
        },
        company_address: {
            en: 'Address',
            id: 'Alamat'
        },
        currency_tax: {
            en: 'Currency & Tax',
            id: 'Mata Uang & Pajak'
        },
        default_currency: {
            en: 'Default Currency',
            id: 'Mata Uang Default'
        },
        currency_position: {
            en: 'Currency Position',
            id: 'Posisi Mata Uang'
        },
        before_amount: {
            en: 'Before amount ($100)',
            id: 'Sebelum jumlah ($100)'
        },
        after_amount: {
            en: 'After amount (100$)',
            id: 'Setelah jumlah (100$)'
        },
        default_tax_rate: {
            en: 'Default Tax Rate (%)',
            id: 'Tarif Pajak Default (%)'
        },
        tax_calculation: {
            en: 'Tax Calculation Basis',
            id: 'Dasar Perhitungan Pajak'
        },
        gross_salary: {
            en: 'Gross Salary',
            id: 'Gaji Kotor'
        },
        contract_settings: {
            en: 'Contract Settings',
            id: 'Pengaturan Kontrak'
        },
        contract_prefix: {
            en: 'Contract Number Prefix',
            id: 'Awalan Nomor Kontrak'
        },
        default_duration: {
            en: 'Default Duration (months)',
            id: 'Durasi Default (bulan)'
        },
        expiry_alert_days: {
            en: 'Expiry Alert Days',
            id: 'Hari Peringatan Berakhir'
        },
        expiry_alert_hint: {
            en: 'Days before expiry to show alert',
            id: 'Hari sebelum berakhir untuk menampilkan peringatan'
        },
        payroll_settings: {
            en: 'Payroll Settings',
            id: 'Pengaturan Penggajian'
        },
        payroll_day: {
            en: 'Payroll Processing Day',
            id: 'Hari Pemrosesan Gaji'
        },
        auto_generate: {
            en: 'Auto Generate Payroll',
            id: 'Otomatis Generate Payroll'
        },
        notification_settings: {
            en: 'Notification Settings',
            id: 'Pengaturan Notifikasi'
        },
        email_notifications: {
            en: 'Email Notifications',
            id: 'Notifikasi Email'
        },
        contract_expiry_alerts: {
            en: 'Contract Expiry Alerts',
            id: 'Peringatan Kontrak Berakhir'
        },
        payroll_complete_alerts: {
            en: 'Payroll Complete Alerts',
            id: 'Peringatan Payroll Selesai'
        },
        enabled: {
            en: 'Enabled',
            id: 'Aktif'
        },
        disabled: {
            en: 'Disabled',
            id: 'Nonaktif'
        },
        appearance: {
            en: 'Appearance',
            id: 'Tampilan'
        },
        language: {
            en: 'Language',
            id: 'Bahasa'
        },
        lang_hint: {
            en: 'Select display language',
            id: 'Pilih bahasa tampilan'
        },
        theme_color: {
            en: 'Theme Color',
            id: 'Warna Tema'
        },
        reset_defaults: {
            en: 'Reset Defaults',
            id: 'Reset Default'
        },

        // ===============================
        // RECRUITMENT
        // ===============================
        recruitment: {
            en: 'Recruitment',
            id: 'Rekrutmen'
        },
        recruitment_pipeline: {
            en: 'Recruitment Pipeline',
            id: 'Pipeline Rekrutmen'
        },
        candidate_onboarding: {
            en: 'Candidate Onboarding',
            id: 'Onboarding Kandidat'
        },
        approved_candidates: {
            en: 'Approved Candidates',
            id: 'Kandidat Disetujui'
        },
        import_to_erp: {
            en: 'Import to ERP',
            id: 'Impor ke ERP'
        },
        bulk_import: {
            en: 'Bulk Import Selected',
            id: 'Impor Sekaligus'
        },
        synced_status: {
            en: 'Sync Status',
            id: 'Status Sinkronisasi'
        },
        not_synced: {
            en: 'Not Synced',
            id: 'Belum Sinkron'
        },
        synced: {
            en: 'Synced',
            id: 'Sudah Sinkron'
        },

        // ===============================
        // EMPLOYEE MANAGEMENT
        // ===============================
        employee_management: {
            en: 'Employee Management',
            id: 'Manajemen Karyawan'
        },
        employee_list: {
            en: 'Employee List',
            id: 'Daftar Karyawan'
        },
        position: {
            en: 'Position',
            id: 'Posisi'
        },
        department: {
            en: 'Department',
            id: 'Departemen'
        },
        join_date: {
            en: 'Join Date',
            id: 'Tanggal Bergabung'
        },

        // ===============================
        // MESSAGES & ALERTS
        // ===============================
        msg_save_success: {
            en: 'Data saved successfully',
            id: 'Data berhasil disimpan'
        },
        msg_delete_success: {
            en: 'Data deleted successfully',
            id: 'Data berhasil dihapus'
        },
        msg_delete_confirm: {
            en: 'Are you sure you want to delete this data?',
            id: 'Apakah Anda yakin ingin menghapus data ini?'
        },
        msg_error: {
            en: 'An error occurred',
            id: 'Terjadi kesalahan'
        },
        msg_loading: {
            en: 'Loading...',
            id: 'Memuat...'
        },
        msg_no_data: {
            en: 'No data available',
            id: 'Tidak ada data'
        },
        msg_required_field: {
            en: 'This field is required',
            id: 'Kolom ini wajib diisi'
        },

        // ===============================
        // MONITORING
        // ===============================
        monitoring: {
            en: 'Monitoring',
            id: 'Pemantauan'
        },
        visitor_monitoring: {
            en: 'Visitor Monitoring',
            id: 'Pemantauan Pengunjung'
        },
        activity_log: {
            en: 'Activity Log',
            id: 'Log Aktivitas'
        },
        ip_address: {
            en: 'IP Address',
            id: 'Alamat IP'
        },
        browser: {
            en: 'Browser',
            id: 'Browser'
        },
        device: {
            en: 'Device',
            id: 'Perangkat'
        },
        visit_time: {
            en: 'Visit Time',
            id: 'Waktu Kunjungan'
        },
        page_visited: {
            en: 'Page Visited',
            id: 'Halaman Dikunjungi'
        },

        // ===============================
        // TIME & DATE
        // ===============================
        today: {
            en: 'Today',
            id: 'Hari Ini'
        },
        yesterday: {
            en: 'Yesterday',
            id: 'Kemarin'
        },
        this_week: {
            en: 'This Week',
            id: 'Minggu Ini'
        },
        this_month: {
            en: 'This Month',
            id: 'Bulan Ini'
        },
        this_year: {
            en: 'This Year',
            id: 'Tahun Ini'
        },
        all_time: {
            en: 'All Time',
            id: 'Semua Waktu'
        },

        // ===============================
        // USER MANAGEMENT
        // ===============================
        user_management: {
            en: 'User Management',
            id: 'Manajemen User'
        },
        user_list: {
            en: 'User List',
            id: 'Daftar User'
        },
        username: {
            en: 'Username',
            id: 'Username'
        },
        password: {
            en: 'Password',
            id: 'Password'
        },
        role: {
            en: 'Role',
            id: 'Peran'
        },
        last_login: {
            en: 'Last Login',
            id: 'Login Terakhir'
        },
        logout: {
            en: 'Logout',
            id: 'Keluar'
        },
        login: {
            en: 'Login',
            id: 'Masuk'
        },
        register: {
            en: 'Register',
            id: 'Daftar'
        }
    },

    /**
     * Get translation for a key
     * @param {string} key - Translation key
     * @param {string} lang - Language code (optional, uses current lang if not provided)
     * @returns {string} - Translated text or key if not found
     */
    get(key, lang = null) {
        const useLang = lang || this.currentLang;
        if (this.translations[key] && this.translations[key][useLang]) {
            return this.translations[key][useLang];
        }
        // Fallback to key if translation not found
        return key;
    },

    /**
     * Set the current language
     * @param {string} lang - Language code ('en' or 'id')
     */
    setLanguage(lang) {
        if (lang === 'en' || lang === 'id') {
            this.currentLang = lang;
            localStorage.setItem('erp_language', lang);
            this.applyTranslations();
            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang } }));
        }
    },

    /**
     * Toggle between languages
     */
    toggleLanguage() {
        const newLang = this.currentLang === 'en' ? 'id' : 'en';
        this.setLanguage(newLang);
    },

    /**
     * Apply translations to all elements with data-translate attribute
     */
    applyTranslations() {
        // Translate text content
        document.querySelectorAll('[data-translate]').forEach(el => {
            const key = el.getAttribute('data-translate');
            const translation = this.get(key);
            if (translation !== key) {
                el.textContent = translation;
            }
        });

        // Translate placeholders
        document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
            const key = el.getAttribute('data-translate-placeholder');
            const translation = this.get(key);
            if (translation !== key) {
                el.placeholder = translation;
            }
        });

        // Translate titles
        document.querySelectorAll('[data-translate-title]').forEach(el => {
            const key = el.getAttribute('data-translate-title');
            const translation = this.get(key);
            if (translation !== key) {
                el.title = translation;
            }
        });

        // Update HTML lang attribute
        document.documentElement.lang = this.currentLang;

        // Update language selector if exists
        const langSelector = document.getElementById('language-selector');
        if (langSelector) {
            langSelector.value = this.currentLang;
        }
    },

    /**
     * Initialize the translation service
     */
    init() {
        // Apply translations on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.applyTranslations();
        });

        // Listen for language selector changes
        document.addEventListener('change', (e) => {
            if (e.target && e.target.id === 'language-selector') {
                this.setLanguage(e.target.value);
            }
        });
    }
};

// Initialize translation service
TranslationService.init();

// Make it globally available
window.TranslationService = TranslationService;
window.__ = (key) => TranslationService.get(key);
