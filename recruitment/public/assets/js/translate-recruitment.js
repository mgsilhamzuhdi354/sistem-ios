/**
 * Translation System for PT Indo Ocean Crew Services Recruitment
 * Supports: English (en), Indonesian (id), Chinese (zh)
 */

class RecruitmentTranslator {
    constructor() {
        this.currentLang = localStorage.getItem('recruitmentLang') || 'en';
        this.translations = this.getTranslations();
        this.initialize();
    }

    getTranslations() {
        return {
            // ===================== ENGLISH =====================
            en: {
                // Navigation
                nav: {
                    home: 'Home',
                    jobs: 'Job Vacancies',
                    applications: 'My Applications',
                    documents: 'Documents',
                    interview: 'Interview',
                    profile: 'My Profile',
                    dashboard: 'Dashboard',
                    logout: 'Logout',
                    login: 'Login',
                    register: 'Register'
                },

                // Admin Navigation
                admin: {
                    dashboard: 'Dashboard',
                    applicants: 'Applicants',
                    vacancies: 'Vacancies',
                    interviews: 'Interviews',
                    documents: 'Documents',
                    medical: 'Medical',
                    settings: 'Settings',
                    pipeline: 'Pipeline'
                },

                // Auth
                auth: {
                    loginTitle: 'Sign In',
                    loginSubtitle: 'Welcome back! Please sign in to your account',
                    registerTitle: 'Create Account',
                    registerSubtitle: 'Join our professional team of seafarers',
                    email: 'Email Address',
                    password: 'Password',
                    confirmPassword: 'Confirm Password',
                    fullName: 'Full Name',
                    phone: 'Phone Number',
                    rememberMe: 'Remember me',
                    forgotPassword: 'Forgot Password?',
                    noAccount: "Don't have an account?",
                    hasAccount: 'Already have an account?',
                    signUp: 'Sign Up',
                    signIn: 'Sign In',
                    loginBtn: 'Sign In',
                    registerBtn: 'Create Account',
                    tagline: 'Join our professional team of seafarers and embark on an exciting maritime career.'
                },

                // Dashboard
                dashboard: {
                    welcome: 'Welcome back',
                    subtitle: 'Track your applications and manage your profile',
                    profileCompletion: 'Profile Complete',
                    totalApplications: 'Total Applications',
                    activeApplications: 'Active Applications',
                    pendingDocuments: 'Pending Documents',
                    scheduledInterviews: 'Scheduled Interviews',
                    pendingReview: 'Pending Review',
                    inInterview: 'In Interview',
                    documentsUploaded: 'Documents Uploaded',
                    recentApplications: 'Recent Applications',
                    notifications: 'Notifications',
                    noNotifications: 'No new notifications',
                    quickActions: 'Quick Actions',
                    browseJobs: 'Browse Jobs',
                    uploadDocuments: 'Upload Documents',
                    completeProfile: 'Complete your profile',
                    completeProfileDesc: 'Upload all required documents to increase your chances of getting hired.',
                    viewAllApplications: 'View All Applications',
                    hiredThisMonth: 'Hired This Month',
                    activeVacancies: 'Active Vacancies'
                },

                // Applications
                application: {
                    title: 'My Applications',
                    detail: 'Application Detail',
                    timeline: 'Application Timeline',
                    apply: 'Apply Now',
                    viewDetail: 'View Detail',
                    appliedOn: 'Applied On',
                    status: 'Status',
                    position: 'Position',
                    department: 'Department',
                    vesselType: 'Vessel Type',
                    salaryRange: 'Salary Range',
                    coverLetter: 'Cover Letter',
                    expectedSalary: 'Expected Salary',
                    availableDate: 'Available From',
                    noApplications: 'No applications yet',
                    startApplying: 'Start applying for positions'
                },

                // Documents
                documents: {
                    title: 'My Documents',
                    upload: 'Upload Document',
                    uploadNew: 'Upload New Document',
                    documentType: 'Document Type',
                    documentNumber: 'Document Number',
                    issueDate: 'Issue Date',
                    expiryDate: 'Expiry Date',
                    issuingAuthority: 'Issuing Authority',
                    selectFile: 'Select File',
                    uploadBtn: 'Upload',
                    pending: 'Pending Review',
                    verified: 'Verified',
                    rejected: 'Rejected',
                    noDocuments: 'No documents uploaded',
                    uploadFirst: 'Upload your first document'
                },

                // Interview
                interview: {
                    title: 'AI Interview',
                    sessions: 'Interview Sessions',
                    start: 'Start Interview',
                    continue: 'Continue Interview',
                    retry: 'Retry Interview',
                    startRetry: 'Start Retry Interview',
                    completed: 'Completed',
                    pending: 'Not Started',
                    inProgress: 'In Progress',
                    expired: 'Expired',
                    score: 'SCORE',
                    result: 'Result',
                    question: 'Question',
                    questions: 'Questions',
                    answer: 'Your Answer',
                    submit: 'Submit Answer',
                    next: 'Next Question',
                    finish: 'Finish Interview',
                    timeRemaining: 'Time Remaining',
                    retryCount: 'Retry',
                    noSessions: 'No Interview Sessions Yet',
                    noSessionsDesc: "You don't have any interview sessions. After your application reaches the interview stage, you will see interview sessions here.",
                    retryBanner: 'RETRY INTERVIEW',
                    attempt: 'Attempt',
                    deadline: 'Deadline',
                    completedOn: 'Completed',
                    retried: 'Retried',
                    excellent: 'Excellent!',
                    good: 'Good',
                    needsImprovement: 'Needs Improvement',
                    scoreOutOf: 'Score out of',
                    interviewCompleted: 'Interview Completed',
                    sessionExpired: 'Session Expired'
                },

                // Medical
                medical: {
                    title: 'Medical Check-up',
                    date: 'Scheduled Date',
                    time: 'Time',
                    hospital: 'Hospital',
                    result: 'Result',
                    fit: 'Fit',
                    unfit: 'Unfit'
                },

                // Jobs
                jobs: {
                    title: 'Job Vacancies',
                    heroTitle: 'Find Your Career at Sea',
                    heroSubtitle: 'Explore exciting maritime job opportunities with competitive packages',
                    search: 'Search positions...',
                    filter: 'Filters',
                    allDepartments: 'All Departments',
                    positionsAvailable: 'Positions Available',
                    newestFirst: 'Newest First',
                    salaryHighLow: 'Salary: High to Low',
                    salaryLowHigh: 'Salary: Low to High',
                    viewDetails: 'View Details',
                    applyNow: 'Apply Now',
                    applyFilters: 'Apply Filters',
                    noPositions: 'No positions found',
                    adjustFilters: 'Try adjusting your search filters',
                    contract: 'Contract Duration',
                    months: 'months',
                    negotiable: 'Negotiable',
                    perMonth: '/month',
                    featured: 'Featured',
                    ctaTitle: "Can't find the right position?",
                    ctaDesc: "Register your profile and we'll notify you when matching positions become available.",
                    registerNow: 'Register Now'
                },

                // Common
                common: {
                    save: 'Save',
                    cancel: 'Cancel',
                    submit: 'Submit',
                    delete: 'Delete',
                    edit: 'Edit',
                    view: 'View',
                    viewAll: 'View All',
                    search: 'Search',
                    filter: 'Filter',
                    loading: 'Loading...',
                    noData: 'No data available',
                    actions: 'Actions',
                    back: 'Back',
                    next: 'Next',
                    previous: 'Previous',
                    confirm: 'Confirm',
                    yes: 'Yes',
                    no: 'No',
                    success: 'Success',
                    error: 'Error',
                    warning: 'Warning',
                    all: 'All',
                    date: 'Date',
                    contactHR: 'Contact HR',
                    needHelp: 'Need Help?',
                    helpText: 'Contact our recruitment team for any questions about your application.'
                },

                // Status
                status: {
                    newApplication: 'New Application',
                    submitted: 'Application submitted',
                    underReview: 'Under Review',
                    interview: 'Interview',
                    medicalCheck: 'Medical Check',
                    documentVerification: 'Document Verification',
                    approved: 'Approved',
                    rejected: 'Rejected',
                    onHold: 'On Hold',
                    hired: 'Hired'
                }
            },

            // ===================== INDONESIAN =====================
            id: {
                // Navigation
                nav: {
                    home: 'Beranda',
                    jobs: 'Lowongan Kerja',
                    applications: 'Lamaran Saya',
                    documents: 'Dokumen',
                    interview: 'Wawancara',
                    profile: 'Profil Saya',
                    dashboard: 'Dashboard',
                    logout: 'Keluar',
                    login: 'Masuk',
                    register: 'Daftar'
                },

                // Admin Navigation
                admin: {
                    dashboard: 'Dashboard',
                    applicants: 'Pelamar',
                    vacancies: 'Lowongan',
                    interviews: 'Wawancara',
                    documents: 'Dokumen',
                    medical: 'Medical',
                    settings: 'Pengaturan',
                    pipeline: 'Pipeline'
                },

                // Auth
                auth: {
                    loginTitle: 'Masuk',
                    loginSubtitle: 'Selamat datang! Silakan masuk ke akun Anda',
                    registerTitle: 'Buat Akun',
                    registerSubtitle: 'Bergabunglah dengan tim pelaut profesional kami',
                    email: 'Alamat Email',
                    password: 'Kata Sandi',
                    confirmPassword: 'Konfirmasi Kata Sandi',
                    fullName: 'Nama Lengkap',
                    phone: 'Nomor Telepon',
                    rememberMe: 'Ingat saya',
                    forgotPassword: 'Lupa Kata Sandi?',
                    noAccount: 'Belum punya akun?',
                    hasAccount: 'Sudah punya akun?',
                    signUp: 'Daftar',
                    signIn: 'Masuk',
                    loginBtn: 'Masuk',
                    registerBtn: 'Buat Akun',
                    tagline: 'Bergabunglah dengan tim pelaut profesional kami dan mulai karir maritim yang menarik.'
                },

                // Dashboard
                dashboard: {
                    welcome: 'Selamat Datang',
                    subtitle: 'Lacak lamaran Anda dan kelola profil Anda',
                    profileCompletion: 'Profil Lengkap',
                    totalApplications: 'Total Lamaran',
                    activeApplications: 'Lamaran Aktif',
                    pendingDocuments: 'Dokumen Tertunda',
                    scheduledInterviews: 'Wawancara Terjadwal',
                    pendingReview: 'Menunggu Review',
                    inInterview: 'Dalam Wawancara',
                    documentsUploaded: 'Dokumen Terunggah',
                    recentApplications: 'Lamaran Terbaru',
                    notifications: 'Notifikasi',
                    noNotifications: 'Tidak ada notifikasi baru',
                    quickActions: 'Aksi Cepat',
                    browseJobs: 'Cari Lowongan',
                    uploadDocuments: 'Unggah Dokumen',
                    completeProfile: 'Lengkapi Profil Anda',
                    completeProfileDesc: 'Unggah semua dokumen yang diperlukan untuk meningkatkan peluang Anda diterima.',
                    viewAllApplications: 'Lihat Semua Lamaran',
                    hiredThisMonth: 'Diterima Bulan Ini',
                    activeVacancies: 'Lowongan Aktif'
                },

                // Applications
                application: {
                    title: 'Lamaran Saya',
                    detail: 'Detail Lamaran',
                    timeline: 'Timeline Lamaran',
                    apply: 'Lamar Sekarang',
                    viewDetail: 'Lihat Detail',
                    appliedOn: 'Tanggal Melamar',
                    status: 'Status',
                    position: 'Posisi',
                    department: 'Departemen',
                    vesselType: 'Jenis Kapal',
                    salaryRange: 'Kisaran Gaji',
                    coverLetter: 'Surat Lamaran',
                    expectedSalary: 'Gaji yang Diharapkan',
                    availableDate: 'Tersedia Mulai',
                    noApplications: 'Belum ada lamaran',
                    startApplying: 'Mulai melamar posisi'
                },

                // Documents
                documents: {
                    title: 'Dokumen Saya',
                    upload: 'Unggah Dokumen',
                    uploadNew: 'Unggah Dokumen Baru',
                    documentType: 'Jenis Dokumen',
                    documentNumber: 'Nomor Dokumen',
                    issueDate: 'Tanggal Terbit',
                    expiryDate: 'Tanggal Kedaluwarsa',
                    issuingAuthority: 'Penerbit',
                    selectFile: 'Pilih File',
                    uploadBtn: 'Unggah',
                    pending: 'Menunggu Verifikasi',
                    verified: 'Terverifikasi',
                    rejected: 'Ditolak',
                    noDocuments: 'Belum ada dokumen',
                    uploadFirst: 'Unggah dokumen pertama Anda'
                },

                // Interview
                interview: {
                    title: 'Wawancara AI',
                    sessions: 'Sesi Wawancara',
                    start: 'Mulai Wawancara',
                    continue: 'Lanjutkan Wawancara',
                    retry: 'Ulangi Wawancara',
                    completed: 'Selesai',
                    pending: 'Menunggu',
                    inProgress: 'Sedang Berlangsung',
                    expired: 'Kedaluwarsa',
                    score: 'Skor',
                    result: 'Hasil',
                    question: 'Pertanyaan',
                    answer: 'Jawaban Anda',
                    submit: 'Kirim Jawaban',
                    next: 'Pertanyaan Berikutnya',
                    finish: 'Selesai Wawancara',
                    timeRemaining: 'Waktu Tersisa',
                    retryCount: 'Jumlah Mengulang'
                },

                // Medical
                medical: {
                    title: 'Medical Check-up',
                    date: 'Tanggal Terjadwal',
                    time: 'Waktu',
                    hospital: 'Rumah Sakit',
                    result: 'Hasil',
                    fit: 'Layak',
                    unfit: 'Tidak Layak'
                },

                // Jobs
                jobs: {
                    title: 'Lowongan Kerja',
                    search: 'Cari posisi...',
                    filter: 'Filter',
                    allDepartments: 'Semua Departemen',
                    positionsAvailable: 'Posisi Tersedia',
                    newestFirst: 'Terbaru',
                    salaryHighLow: 'Gaji: Tinggi ke Rendah',
                    salaryLowHigh: 'Gaji: Rendah ke Tinggi',
                    viewDetails: 'Lihat Detail',
                    applyNow: 'Lamar Sekarang',
                    noPositions: 'Tidak ada posisi ditemukan',
                    adjustFilters: 'Coba sesuaikan filter pencarian',
                    contract: 'Durasi Kontrak',
                    months: 'bulan',
                    negotiable: 'Nego',
                    perMonth: '/bulan',
                    featured: 'Unggulan'
                },

                // Common
                common: {
                    save: 'Simpan',
                    cancel: 'Batal',
                    submit: 'Kirim',
                    delete: 'Hapus',
                    edit: 'Edit',
                    view: 'Lihat',
                    viewAll: 'Lihat Semua',
                    search: 'Cari',
                    filter: 'Filter',
                    loading: 'Memuat...',
                    noData: 'Tidak ada data',
                    actions: 'Aksi',
                    back: 'Kembali',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya',
                    confirm: 'Konfirmasi',
                    yes: 'Ya',
                    no: 'Tidak',
                    success: 'Berhasil',
                    error: 'Gagal',
                    warning: 'Peringatan',
                    all: 'Semua',
                    date: 'Tanggal',
                    contactHR: 'Hubungi HR',
                    needHelp: 'Butuh Bantuan?',
                    helpText: 'Hubungi tim rekrutmen kami untuk pertanyaan tentang lamaran Anda.'
                },

                // Status
                status: {
                    newApplication: 'Lamaran Baru',
                    submitted: 'Lamaran terkirim',
                    underReview: 'Sedang Ditinjau',
                    interview: 'Wawancara',
                    medicalCheck: 'Medical Check',
                    documentVerification: 'Verifikasi Dokumen',
                    approved: 'Disetujui',
                    rejected: 'Ditolak',
                    onHold: 'Ditunda',
                    hired: 'Diterima'
                }
            },

            // ===================== CHINESE =====================
            zh: {
                // Navigation
                nav: {
                    home: '首页',
                    jobs: '职位空缺',
                    applications: '我的申请',
                    documents: '文件',
                    interview: '面试',
                    profile: '我的资料',
                    dashboard: '仪表板',
                    logout: '登出',
                    login: '登录',
                    register: '注册'
                },

                // Admin Navigation
                admin: {
                    dashboard: '仪表板',
                    applicants: '申请者',
                    vacancies: '职位',
                    interviews: '面试',
                    documents: '文件',
                    medical: '医疗',
                    settings: '设置',
                    pipeline: '流程'
                },

                // Auth
                auth: {
                    loginTitle: '登录',
                    loginSubtitle: '欢迎回来！请登录您的账户',
                    registerTitle: '创建账户',
                    registerSubtitle: '加入我们专业的海员团队',
                    email: '电子邮箱',
                    password: '密码',
                    confirmPassword: '确认密码',
                    fullName: '全名',
                    phone: '电话号码',
                    rememberMe: '记住我',
                    forgotPassword: '忘记密码？',
                    noAccount: '还没有账户？',
                    hasAccount: '已有账户？',
                    signUp: '注册',
                    signIn: '登录',
                    loginBtn: '登录',
                    registerBtn: '创建账户',
                    tagline: '加入我们专业的海员团队，开启精彩的海事职业生涯。'
                },

                // Dashboard
                dashboard: {
                    welcome: '欢迎回来',
                    subtitle: '跟踪您的申请并管理您的个人资料',
                    profileCompletion: '资料完成度',
                    totalApplications: '总申请数',
                    activeApplications: '活跃申请',
                    pendingDocuments: '待处理文件',
                    scheduledInterviews: '预定面试',
                    pendingReview: '待审核',
                    inInterview: '面试中',
                    documentsUploaded: '已上传文件',
                    recentApplications: '最近申请',
                    notifications: '通知',
                    noNotifications: '没有新通知',
                    quickActions: '快捷操作',
                    browseJobs: '浏览职位',
                    uploadDocuments: '上传文件',
                    completeProfile: '完善您的资料',
                    completeProfileDesc: '上传所有必需的文件以增加您被录用的机会。',
                    viewAllApplications: '查看所有申请',
                    hiredThisMonth: '本月录用',
                    activeVacancies: '活跃职位'
                },

                // Applications
                application: {
                    title: '我的申请',
                    detail: '申请详情',
                    timeline: '申请时间线',
                    apply: '立即申请',
                    viewDetail: '查看详情',
                    appliedOn: '申请日期',
                    status: '状态',
                    position: '职位',
                    department: '部门',
                    vesselType: '船舶类型',
                    salaryRange: '薪资范围',
                    coverLetter: '求职信',
                    expectedSalary: '期望薪资',
                    availableDate: '可入职日期',
                    noApplications: '暂无申请',
                    startApplying: '开始申请职位'
                },

                // Documents
                documents: {
                    title: '我的文件',
                    upload: '上传文件',
                    uploadNew: '上传新文件',
                    documentType: '文件类型',
                    documentNumber: '文件编号',
                    issueDate: '签发日期',
                    expiryDate: '到期日期',
                    issuingAuthority: '签发机构',
                    selectFile: '选择文件',
                    uploadBtn: '上传',
                    pending: '待审核',
                    verified: '已验证',
                    rejected: '已拒绝',
                    noDocuments: '暂无文件',
                    uploadFirst: '上传您的第一份文件'
                },

                // Interview
                interview: {
                    title: 'AI面试',
                    sessions: '面试场次',
                    start: '开始面试',
                    continue: '继续面试',
                    retry: '重新面试',
                    completed: '已完成',
                    pending: '待处理',
                    inProgress: '进行中',
                    expired: '已过期',
                    score: '分数',
                    result: '结果',
                    question: '问题',
                    answer: '您的答案',
                    submit: '提交答案',
                    next: '下一题',
                    finish: '完成面试',
                    timeRemaining: '剩余时间',
                    retryCount: '重试次数'
                },

                // Medical
                medical: {
                    title: '体检',
                    date: '预约日期',
                    time: '时间',
                    hospital: '医院',
                    result: '结果',
                    fit: '合格',
                    unfit: '不合格'
                },

                // Jobs
                jobs: {
                    title: '职位空缺',
                    search: '搜索职位...',
                    filter: '筛选',
                    allDepartments: '所有部门',
                    positionsAvailable: '可用职位',
                    newestFirst: '最新优先',
                    salaryHighLow: '薪资：从高到低',
                    salaryLowHigh: '薪资：从低到高',
                    viewDetails: '查看详情',
                    applyNow: '立即申请',
                    noPositions: '未找到职位',
                    adjustFilters: '尝试调整搜索条件',
                    contract: '合同期限',
                    months: '个月',
                    negotiable: '面议',
                    perMonth: '/月',
                    featured: '推荐'
                },

                // Common
                common: {
                    save: '保存',
                    cancel: '取消',
                    submit: '提交',
                    delete: '删除',
                    edit: '编辑',
                    view: '查看',
                    viewAll: '查看全部',
                    search: '搜索',
                    filter: '筛选',
                    loading: '加载中...',
                    noData: '暂无数据',
                    actions: '操作',
                    back: '返回',
                    next: '下一步',
                    previous: '上一步',
                    confirm: '确认',
                    yes: '是',
                    no: '否',
                    success: '成功',
                    error: '错误',
                    warning: '警告',
                    all: '全部',
                    date: '日期',
                    contactHR: '联系HR',
                    needHelp: '需要帮助？',
                    helpText: '如有任何申请问题，请联系我们的招聘团队。'
                },

                // Status
                status: {
                    newApplication: '新申请',
                    submitted: '已提交申请',
                    underReview: '审核中',
                    interview: '面试',
                    medicalCheck: '体检',
                    documentVerification: '文件验证',
                    approved: '已批准',
                    rejected: '已拒绝',
                    onHold: '暂停',
                    hired: '已录用'
                }
            }
        };
    }

    initialize() {
        document.addEventListener('DOMContentLoaded', () => {
            this.applyTranslations();
            this.setupLanguageSelector();
        });
    }

    setLanguage(lang) {
        this.currentLang = lang;
        localStorage.setItem('recruitmentLang', lang);
        this.applyTranslations();
    }

    getTranslation(key) {
        const keys = key.split('.');
        let value = this.translations[this.currentLang];

        for (const k of keys) {
            if (value && value[k]) {
                value = value[k];
            } else {
                // Fallback to English
                value = this.translations['en'];
                for (const fallbackKey of keys) {
                    if (value && value[fallbackKey]) {
                        value = value[fallbackKey];
                    } else {
                        return key; // Return key if translation not found
                    }
                }
                break;
            }
        }

        return value;
    }

    applyTranslations() {
        // Translate elements with data-translate attribute
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            const translation = this.getTranslation(key);

            if (element.tagName === 'INPUT' && element.type !== 'submit' && element.type !== 'button') {
                element.placeholder = translation;
            } else if (element.tagName === 'OPTION') {
                element.textContent = translation;
            } else {
                element.textContent = translation;
            }
        });

        // Translate elements with data-translate-placeholder
        document.querySelectorAll('[data-translate-placeholder]').forEach(element => {
            const key = element.getAttribute('data-translate-placeholder');
            element.placeholder = this.getTranslation(key);
        });

        // Translate elements with data-translate-title
        document.querySelectorAll('[data-translate-title]').forEach(element => {
            const key = element.getAttribute('data-translate-title');
            element.title = this.getTranslation(key);
        });

        // Update language selector display
        const langSelector = document.getElementById('langSelect');
        if (langSelector) {
            langSelector.value = this.currentLang;
        }
    }

    setupLanguageSelector() {
        const langSelector = document.getElementById('langSelect');
        if (langSelector) {
            langSelector.value = this.currentLang;
            langSelector.addEventListener('change', (e) => {
                this.setLanguage(e.target.value);
            });
        }
    }
}

// Initialize translator
const recruitmentTranslator = new RecruitmentTranslator();

// Global function for changing language
function changeRecruitmentLanguage(lang) {
    recruitmentTranslator.setLanguage(lang);
}
