<?php
/**
 * AI Document Parser - ALL Pages View
 * Scan & Extract information from EVERY page of seafarer documents
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AI Document Parser' ?> - PT Indo Ocean</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .drop-zone { transition: all 0.3s ease; }
        .drop-zone.drag-over { border-color: #3b82f6; background: rgba(59,130,246,0.08); transform: scale(1.01); }
        @keyframes aiPulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.7; transform:scale(1.05); } }
        .ai-pulse { animation: aiPulse 2s ease-in-out infinite; }
        @keyframes scanLine { 0% { top:0; } 50% { top:calc(100%-2px); } 100% { top:0; } }
        .scan-line { position:absolute; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,#3b82f6,#8b5cf6,transparent); animation:scanLine 2.5s ease-in-out infinite; box-shadow:0 0 15px rgba(59,130,246,0.6); }
        @keyframes shimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }
        .skeleton { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; border-radius:6px; }
        .confidence-bar { transition: width 0.8s ease-out; }
        @keyframes floatIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .float-in { animation: floatIn 0.4s ease-out forwards; }
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:3px; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex">

<?php include APPPATH . 'Views/partials/modern_sidebar.php'; ?>

<div class="flex-1 ml-64" x-data="documentParser()" x-cloak>

    <!-- Top Header -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-slate-200/60">
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center shadow-lg shadow-violet-200">
                    <span class="material-icons text-white text-xl">smart_toy</span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800"><?= __('document_parser.title') ?></h1>
                    <p class="text-xs text-slate-500"><?= __('document_parser.subtitle') ?></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-violet-50 to-blue-50 border border-violet-200/50">
                    <span class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></span>
                    <span class="text-[11px] font-bold text-violet-700">AI POWERED</span>
                </span>
                <span class="px-2.5 py-1 rounded-full border text-[10px] font-bold"
                      :class="aiProvider === 'claude' ? 'bg-orange-50 border-orange-200/60 text-orange-600' : 'bg-blue-50 border-blue-200/60 text-blue-600'"
                      x-text="providerLabel"></span>
            </div>
        </div>
    </header>

    <main class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN: Upload & Preview -->
            <div class="lg:col-span-4">

                <!-- Upload Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-blue-500 text-lg">cloud_upload</span>
                            <h2 class="text-sm font-semibold text-slate-700"><?= __('document_parser.upload_title') ?></h2>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium">PDF, JPG, PNG • Max 10MB</span>
                    </div>

                    <div class="p-5">
                        <div class="drop-zone relative rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-400 transition-all cursor-pointer"
                             :class="{ 'drag-over': isDragging }"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event)"
                             @click="$refs.fileInput.click()">

                            <input type="file" x-ref="fileInput" class="hidden" accept=".pdf,.jpg,.jpeg,.png" @change="handleFileSelect($event)">

                            <div x-show="!selectedFile" class="py-10 px-6 text-center">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-blue-50 to-violet-50 flex items-center justify-center">
                                    <span class="material-icons text-2xl text-blue-400">document_scanner</span>
                                </div>
                                <p class="text-sm font-semibold text-slate-600 mb-1"><?= __('document_parser.drag_drop') ?></p>
                                <p class="text-xs text-slate-400"><?= __('document_parser.or_click') ?></p>
                                <div class="flex items-center justify-center gap-2 mt-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-50 text-[10px] font-medium text-red-600"><span class="material-icons text-xs">picture_as_pdf</span>PDF</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-50 text-[10px] font-medium text-green-600"><span class="material-icons text-xs">image</span>JPG</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-[10px] font-medium text-blue-600"><span class="material-icons text-xs">image</span>PNG</span>
                                </div>
                            </div>

                            <div x-show="selectedFile" class="p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="fileType === 'pdf' ? 'bg-red-50' : 'bg-blue-50'">
                                        <span class="material-icons text-lg" :class="fileType === 'pdf' ? 'text-red-500' : 'text-blue-500'" x-text="fileType === 'pdf' ? 'picture_as_pdf' : 'image'"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate" x-text="selectedFile?.name"></p>
                                        <p class="text-xs text-slate-400" x-text="formatFileSize(selectedFile?.size)"></p>
                                    </div>
                                    <button @click.stop="clearFile()" class="p-1.5 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors">
                                        <span class="material-icons text-lg">close</span>
                                    </button>
                                </div>
                                <!-- Image preview -->
                                <div x-show="fileType !== 'pdf' && previewUrl" class="relative rounded-lg overflow-hidden bg-slate-100">
                                    <img :src="previewUrl" class="w-full max-h-48 object-contain" alt="Preview">
                                    <div x-show="isProcessing" class="absolute inset-0 bg-blue-900/10"><div class="scan-line"></div></div>
                                </div>
                                <div x-show="fileType === 'pdf'" class="relative flex items-center justify-center py-6 bg-slate-50 rounded-lg">
                                    <div class="text-center"><span class="material-icons text-4xl text-red-400">picture_as_pdf</span><p class="text-xs text-slate-500 mt-1">PDF Document</p></div>
                                    <div x-show="isProcessing" class="absolute inset-0 bg-blue-900/5"><div class="scan-line"></div></div>
                                </div>
                            </div>
                        </div>

                        <button x-show="selectedFile && !isProcessing"
                                @click="processDocument()"
                                class="mt-4 w-full py-3 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 hover:from-violet-700 hover:to-blue-700 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-blue-200 transition-all hover:shadow-xl hover:-translate-y-0.5">
                            <span class="material-icons text-lg">auto_awesome</span>
                            <?= __('document_parser.process_btn') ?>
                        </button>
                    </div>
                </div>

                <!-- Processing Animation -->
                <div x-show="isProcessing" class="mt-4 bg-white rounded-2xl border border-blue-200/50 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-blue-600 flex items-center justify-center ai-pulse shadow-lg shadow-violet-200">
                            <span class="material-icons text-white text-xl">psychology</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700"><?= __('document_parser.processing') ?></p>
                            <p class="text-xs text-slate-400"><?= __('document_parser.processing_sub') ?></p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex gap-3"><div class="skeleton h-4 w-24"></div><div class="skeleton h-4 flex-1"></div></div>
                        <div class="flex gap-3"><div class="skeleton h-4 w-32"></div><div class="skeleton h-4 flex-1"></div></div>
                        <div class="flex gap-3"><div class="skeleton h-4 w-20"></div><div class="skeleton h-4 flex-1"></div></div>
                    </div>
                </div>

                <!-- Scan History -->
                <?php if (!empty($scanHistory)): ?>
                <div class="mt-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="material-icons text-slate-400 text-lg">history</span>
                        <h3 class="text-sm font-semibold text-slate-600"><?= __('document_parser.scan_history') ?></h3>
                    </div>
                    <div class="divide-y divide-slate-50">
                        <?php foreach ($scanHistory as $i => $scan): ?>
                        <div class="px-5 py-3 hover:bg-slate-50/50 transition-colors cursor-pointer flex items-center gap-3" @click="loadHistory(<?= $i ?>)">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-icons text-violet-500 text-sm">description</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-slate-700 truncate"><?= htmlspecialchars($scan['seafarer_name'] ?? '-') ?></p>
                                <p class="text-[10px] text-slate-400"><?= htmlspecialchars($scan['document_type'] ?? '-') ?></p>
                            </div>
                            <span class="text-[10px] text-slate-400 flex-shrink-0"><?= date('H:i', strtotime($scan['timestamp'])) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT COLUMN: Results -->
            <div class="lg:col-span-8" x-show="hasResult" x-transition>

                <!-- Error -->
                <div x-show="errorMessage" class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                    <span class="material-icons text-red-500 mt-0.5">error_outline</span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-700">Terjadi Kesalahan</p>
                        <p class="text-xs text-red-600 mt-1" x-text="errorMessage"></p>
                    </div>
                    <button @click="errorMessage=''; hasResult=false" class="p-1 hover:bg-red-100 rounded-lg"><span class="material-icons text-red-400 text-lg">close</span></button>
                </div>

                <!-- Anomaly Alert (collapsible) -->
                <div x-show="result?.anomaly_notes && result.anomaly_notes !== ''" class="mb-4 bg-amber-50 border border-amber-200 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 flex items-center justify-between cursor-pointer" @click="showAnomaly = !showAnomaly">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-amber-500">warning</span>
                            <p class="text-sm font-semibold text-amber-700"><?= __('document_parser.anomaly_detected') ?></p>
                        </div>
                        <span class="material-icons text-amber-400 text-lg transition-transform" :class="showAnomaly ? 'rotate-180' : ''">expand_more</span>
                    </div>
                    <div x-show="showAnomaly" x-transition class="px-4 pb-3">
                        <p class="text-xs text-amber-600 whitespace-pre-line" x-text="result?.anomaly_notes"></p>
                    </div>
                </div>

                <!-- Seafarer Info Card -->
                <div x-show="result?.seafarer_info && !errorMessage" class="mb-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden float-in">
                    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-blue-500 text-lg">person</span>
                            <h2 class="text-sm font-semibold text-slate-700">Data Pelaut</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <template x-if="result?.is_document_clear === true">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-semibold text-emerald-700">
                                    <span class="material-icons text-xs">check_circle</span> <?= __('document_parser.doc_clear') ?>
                                </span>
                            </template>
                            <template x-if="result?.is_document_clear === false">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-50 border border-red-200 text-[10px] font-semibold text-red-700">
                                    <span class="material-icons text-xs">warning</span> <?= __('document_parser.doc_unclear') ?>
                                </span>
                            </template>
                            <span class="px-2 py-1 rounded-full bg-violet-50 border border-violet-200 text-[10px] font-bold text-violet-600" x-text="(result?.total_pages_scanned || result?.certificates?.length || 0) + ' Halaman'"></span>
                            <span class="px-2 py-1 rounded-full bg-blue-50 border border-blue-200 text-[10px] font-bold text-blue-600" x-text="(result?.total_certificates_found || 0) + ' Sertifikat'"></span>
                        </div>
                    </div>
                    <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Nama Lengkap</label>
                            <input type="text" :value="getNestedField('seafarer_info', 'name')" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Kewarganegaraan</label>
                            <input type="text" :value="getNestedField('seafarer_info', 'nationality')" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Tanggal Lahir</label>
                            <input type="text" :value="getNestedField('seafarer_info', 'date_of_birth')" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-slate-400 mb-1">No. Seaman Book</label>
                            <input type="text" :value="getNestedField('seafarer_info', 'seaman_book_number')" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div x-show="result?.certificates && !errorMessage" class="mb-3 flex items-center gap-2">
                    <button @click="pageFilter = 'all'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                            :class="pageFilter === 'all' ? 'bg-violet-100 text-violet-700' : 'bg-white text-slate-500 hover:bg-slate-100'">
                        Semua <span class="ml-1 text-[10px]" x-text="'(' + (result?.certificates?.length || 0) + ')'"></span>
                    </button>
                    <button @click="pageFilter = 'cert'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                            :class="pageFilter === 'cert' ? 'bg-blue-100 text-blue-700' : 'bg-white text-slate-500 hover:bg-slate-100'">
                        Sertifikat <span class="ml-1 text-[10px]" x-text="'(' + certCount() + ')'"></span>
                    </button>
                    <button @click="pageFilter = 'other'" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                            :class="pageFilter === 'other' ? 'bg-slate-200 text-slate-700' : 'bg-white text-slate-500 hover:bg-slate-100'">
                        Halaman Lain <span class="ml-1 text-[10px]" x-text="'(' + otherCount() + ')'"></span>
                    </button>
                </div>

                <!-- Certificate/Page Cards -->
                <div x-show="result?.certificates && !errorMessage" class="space-y-3">
                    <template x-for="(cert, idx) in filteredCerts()" :key="idx">
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden float-in" :style="'animation-delay:' + (idx * 0.05) + 's'">
                            <!-- Card Header -->
                            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                         :class="getPageIconBg(cert)">
                                        <span class="material-icons text-sm" :class="getPageIconColor(cert)"
                                              x-text="getPageIcon(cert)"></span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-700" x-text="getFieldVal(cert.document_type)"></h3>
                                            <template x-if="isCertificate(cert)">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold"
                                                      :class="cert.is_valid === false ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'"
                                                      x-text="cert.is_valid === false ? 'EXPIRED' : 'VALID'"></span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-slate-400">
                                            <span x-text="'Hal. ' + (cert.page_number || '?')"></span>
                                            <span x-show="cert.page_content_summary" x-text="' • ' + cert.page_content_summary" class="text-slate-400"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-[10px] text-slate-400" x-text="'#' + (cert.page_number || (idx+1))"></span>
                                </div>
                            </div>

                            <!-- Card Body (only for certificates) -->
                            <template x-if="isCertificate(cert)">
                                <div class="p-5">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <!-- Doc Number -->
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Nomor Dokumen</label>
                                            <div class="flex items-center gap-1">
                                                <input type="text" :value="getFieldVal(cert.document_number)" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                                                <div x-show="getFieldConf(cert.document_number) > 0" class="w-16 flex-shrink-0">
                                                    <div class="flex items-center gap-1">
                                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                            <div class="confidence-bar h-full rounded-full" :class="confColor(getFieldConf(cert.document_number))" :style="'width:'+getFieldConf(cert.document_number)+'%'"></div>
                                                        </div>
                                                        <span class="text-[9px] text-slate-400" x-text="getFieldConf(cert.document_number)+'%'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Rank -->
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Jabatan/Kapasitas</label>
                                            <input type="text" :value="getFieldVal(cert.rank_capacity)" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                                        </div>
                                        <!-- Issuer -->
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Penerbit</label>
                                            <input type="text" :value="getFieldVal(cert.issuing_authority)" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                                        </div>
                                        <!-- Issue Date -->
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Tanggal Terbit</label>
                                            <input type="text" :value="getFieldVal(cert.issue_date)" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                                        </div>
                                        <!-- Expiry Date -->
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Tanggal Kadaluarsa</label>
                                            <div class="flex items-center gap-2">
                                                <input type="text" :value="getFieldVal(cert.expiry_date)" class="w-full px-3 py-2 rounded-lg border text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                                                       :class="cert.is_valid === false ? 'border-red-300 bg-red-50 text-red-700' : 'border-slate-200 text-slate-700'">
                                                <span x-show="getExpiryBadge(cert)" class="flex-shrink-0 px-2 py-1 rounded-full text-[9px] font-bold"
                                                      :class="getExpiryBadgeClass(cert)" x-text="getExpiryBadge(cert)"></span>
                                            </div>
                                        </div>
                                        <!-- Limitations -->
                                        <div x-show="cert.limitations && (getFieldVal(cert.limitations?.gross_tonnage) || getFieldVal(cert.limitations?.engine_kw) || getFieldVal(cert.limitations?.voyage_area))">
                                            <label class="block text-[10px] font-medium text-slate-400 mb-1">Batasan</label>
                                            <div class="text-xs text-slate-600 space-y-0.5">
                                                <div x-show="getFieldVal(cert.limitations?.gross_tonnage)"><span class="text-slate-400">GT:</span> <span x-text="getFieldVal(cert.limitations?.gross_tonnage)"></span></div>
                                                <div x-show="getFieldVal(cert.limitations?.engine_kw)"><span class="text-slate-400">kW:</span> <span x-text="getFieldVal(cert.limitations?.engine_kw)"></span></div>
                                                <div x-show="getFieldVal(cert.limitations?.voyage_area)"><span class="text-slate-400">Area:</span> <span x-text="getFieldVal(cert.limitations?.voyage_area)"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Non-cert page: show summary only -->
                            <template x-if="!isCertificate(cert) && cert.page_content_summary">
                                <div class="px-5 py-3 bg-slate-50/50">
                                    <p class="text-xs text-slate-500" x-text="cert.page_content_summary"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Action Buttons -->
                <div x-show="result?.certificates && !errorMessage" class="mt-4 flex items-center gap-3">
                    <button @click="saveToDatabase()"
                            class="flex-1 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-lg shadow-emerald-100 transition-all hover:-translate-y-0.5">
                        <span class="material-icons text-lg">save</span>
                        <?= __('document_parser.save_btn') ?>
                    </button>
                    <button @click="resetAll()"
                            class="px-6 py-3 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2">
                        <span class="material-icons text-lg">refresh</span>
                        <?= __('document_parser.scan_again') ?>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast -->
    <div x-show="showSaveToast" x-transition class="fixed bottom-6 right-6 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-2 z-50">
        <span class="material-icons">check_circle</span>
        <span class="text-sm font-medium"><?= __('document_parser.save_success') ?></span>
    </div>
</div>

<script>
function documentParser() {
    const NON_CERT_TYPES = ['cv/resume', 'cover page', 'blank page', 'back page', 'photo page', 'stamp page', 'stamp/endorsement page', 'halaman pendukung'];

    return {
        selectedFile: null, fileType: '', previewUrl: null, isDragging: false,
        isProcessing: false, hasResult: false, result: null, errorMessage: '',
        showSaveToast: false, modelName: '', showAnomaly: false,
        pageFilter: 'all',
        aiProvider: '<?= $aiProvider ?? 'gemini' ?>',
        providerLabel: '<?= ($aiProvider ?? 'gemini') === 'claude' ? 'CLAUDE SONNET' : 'GEMINI 2.5-FLASH' ?>',
        scanHistory: <?= json_encode($scanHistory ?? []) ?>,

        handleDrop(e) { this.isDragging = false; if (e.dataTransfer.files.length > 0) this.setFile(e.dataTransfer.files[0]); },
        handleFileSelect(e) { if (e.target.files.length > 0) this.setFile(e.target.files[0]); },

        setFile(file) {
            const ok = ['application/pdf','image/jpeg','image/png','image/jpg'];
            if (!ok.includes(file.type)) { alert('Format tidak didukung. Hanya PDF, JPG, PNG.'); return; }
            if (file.size > 10*1024*1024) { alert('File terlalu besar. Maks 10MB.'); return; }
            this.selectedFile = file;
            this.fileType = file.type === 'application/pdf' ? 'pdf' : 'image';
            this.hasResult = false; this.result = null; this.errorMessage = '';
            if (this.fileType !== 'pdf') {
                const r = new FileReader(); r.onload = e => this.previewUrl = e.target.result; r.readAsDataURL(file);
            } else { this.previewUrl = null; }
        },

        clearFile() { this.selectedFile = null; this.previewUrl = null; this.fileType = ''; this.$refs.fileInput.value = ''; },
        formatFileSize(b) { if (!b) return ''; if (b<1024) return b+' B'; if (b<1048576) return (b/1024).toFixed(1)+' KB'; return (b/1048576).toFixed(1)+' MB'; },

        async processDocument() {
            if (!this.selectedFile) return;
            this.isProcessing = true; this.hasResult = false; this.errorMessage = ''; this.pageFilter = 'all';
            const fd = new FormData(); fd.append('document', this.selectedFile);
            try {
                const res = await fetch('<?= BASE_URL ?>DocumentParser/process', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= $csrf_token ?>' },
                    body: fd
                });
                const data = await res.json();
                if (data.success) {
                    this.result = data.data;
                    this.hasResult = true;
                    if (data.model) {
                        this.modelName = data.model;
                        this.aiProvider = data.provider || this.aiProvider;
                        if (this.aiProvider === 'claude') {
                            this.providerLabel = 'CLAUDE ' + data.model.replace('claude-','').replace(/-\d+$/,'').toUpperCase();
                        } else {
                            this.providerLabel = 'GEMINI ' + data.model.replace('gemini-','').toUpperCase();
                        }
                    }
                } else {
                    this.errorMessage = data.message || 'Terjadi kesalahan.';
                    this.hasResult = true;
                }
            } catch(e) {
                this.errorMessage = 'Gagal menghubungi server.';
                this.hasResult = true;
            } finally { this.isProcessing = false; }
        },

        // Field helpers
        getFieldVal(f) { if (!f) return ''; if (typeof f === 'object' && f !== null) return f.value || ''; return f || ''; },
        getFieldConf(f) { if (!f) return 0; if (typeof f === 'object' && f !== null) return f.confidence || 0; return 0; },
        getNestedField(p, c) { if (!this.result || !this.result[p] || !this.result[p][c]) return ''; return this.getFieldVal(this.result[p][c]); },
        confColor(c) { if (c >= 80) return 'bg-emerald-500'; if (c >= 50) return 'bg-amber-500'; return 'bg-red-500'; },

        // Certificate vs non-certificate detection
        isCertificate(cert) {
            const t = this.getFieldVal(cert.document_type).toLowerCase();
            return !NON_CERT_TYPES.some(nc => t.includes(nc));
        },

        // Filtering
        filteredCerts() {
            if (!this.result?.certificates) return [];
            if (this.pageFilter === 'all') return this.result.certificates;
            if (this.pageFilter === 'cert') return this.result.certificates.filter(c => this.isCertificate(c));
            return this.result.certificates.filter(c => !this.isCertificate(c));
        },
        certCount() {
            if (!this.result?.certificates) return 0;
            return this.result.certificates.filter(c => this.isCertificate(c)).length;
        },
        otherCount() {
            if (!this.result?.certificates) return 0;
            return this.result.certificates.filter(c => !this.isCertificate(c)).length;
        },

        // Page type icons
        getPageIcon(cert) {
            if (!this.isCertificate(cert)) {
                const t = this.getFieldVal(cert.document_type).toLowerCase();
                if (t.includes('cv') || t.includes('resume')) return 'article';
                if (t.includes('blank')) return 'block';
                if (t.includes('photo')) return 'photo_camera';
                if (t.includes('back page')) return 'flip_to_back';
                if (t.includes('stamp') || t.includes('endorsement page')) return 'approval';
                if (t.includes('cover')) return 'menu_book';
                return 'description';
            }
            return cert.is_valid === false ? 'cancel' : 'verified';
        },
        getPageIconBg(cert) {
            if (!this.isCertificate(cert)) return 'bg-slate-100';
            return cert.is_valid === false ? 'bg-red-50' : 'bg-emerald-50';
        },
        getPageIconColor(cert) {
            if (!this.isCertificate(cert)) return 'text-slate-400';
            return cert.is_valid === false ? 'text-red-500' : 'text-emerald-500';
        },

        getExpiryBadge(cert) {
            const exp = this.getFieldVal(cert.expiry_date);
            if (!exp) return '';
            if (exp.toUpperCase() === 'UNLIMITED') return 'UNLIMITED';
            const d = new Date(exp); if (isNaN(d.getTime())) return '';
            const diff = Math.ceil((d - new Date()) / 86400000);
            if (diff < 0) return 'EXPIRED';
            if (diff <= 90) return 'SEGERA';
            return 'VALID';
        },
        getExpiryBadgeClass(cert) {
            const b = this.getExpiryBadge(cert);
            if (b === 'EXPIRED') return 'bg-red-100 text-red-600';
            if (b === 'SEGERA') return 'bg-amber-100 text-amber-600';
            if (b === 'UNLIMITED') return 'bg-blue-100 text-blue-600';
            return 'bg-emerald-100 text-emerald-600';
        },

        loadHistory(i) {
            const e = this.scanHistory[i];
            if (e?.data) { this.result = e.data; this.hasResult = true; this.errorMessage = ''; this.pageFilter = 'all'; }
        },
        saveToDatabase() { this.showSaveToast = true; setTimeout(() => this.showSaveToast = false, 3000); },
        resetAll() {
            this.selectedFile = null; this.previewUrl = null; this.fileType = '';
            this.hasResult = false; this.result = null; this.errorMessage = '';
            this.isProcessing = false; this.pageFilter = 'all';
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        }
    };
}
</script>
</body>
</html>
