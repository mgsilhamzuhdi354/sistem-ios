<?php
/**
 * Deploy Landing Page - Run via browser
 * Access: https://indooceancrewservice.com/deploy_landing.php
 * DELETE THIS FILE AFTER USE!
 */

echo "<h2>🚀 Deploying Landing Page...</h2><pre>";

// 1. Create landing page index.html
$indexHtml = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Indo Ocean Crew Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:"Inter",sans-serif;min-height:100vh;background:linear-gradient(135deg,#0a1628 0%,#1a2a4a 40%,#0d2137 100%);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
        .ocean-bg{position:fixed;bottom:0;left:0;right:0;height:40%;background:linear-gradient(180deg,transparent 0%,rgba(6,78,130,.15) 50%,rgba(6,78,130,.25) 100%);z-index:0}
        .wave{position:absolute;bottom:0;left:-50%;width:200%;height:100px;background:url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1440 100\'%3E%3Cpath fill=\'rgba(59,130,246,0.08)\' d=\'M0,50 C360,100 720,0 1080,50 C1260,75 1350,25 1440,50 L1440,100 L0,100 Z\'/%3E%3C/svg%3E") repeat-x;animation:wave 8s linear infinite}
        .wave:nth-child(2){bottom:10px;animation:wave 12s linear infinite reverse;opacity:.5}
        @keyframes wave{0%{transform:translateX(0)}100%{transform:translateX(50%)}}
        .stars{position:fixed;top:0;left:0;right:0;bottom:0;z-index:0}
        .star{position:absolute;width:2px;height:2px;background:#fff;border-radius:50%;animation:twinkle 3s infinite}
        @keyframes twinkle{0%,100%{opacity:.2}50%{opacity:.8}}
        .container{position:relative;z-index:10;text-align:center;padding:20px;width:100%;max-width:900px}
        .logo-section{margin-bottom:48px;animation:fadeInDown .8s ease-out}
        .logo-icon{width:80px;height:80px;background:linear-gradient(135deg,#3b82f6,#1e40af);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:24px;box-shadow:0 20px 40px rgba(59,130,246,.3)}
        .logo-icon .material-icons-round{font-size:40px;color:#fff}
        .company-name{font-size:32px;font-weight:800;color:#fff;letter-spacing:-.5px;margin-bottom:8px}
        .company-name span{background:linear-gradient(135deg,#fbbf24,#f59e0b);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
        .tagline{font-size:16px;color:rgba(255,255,255,.5);font-weight:400}
        .cards{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:40px}
        .card{background:rgba(255,255,255,.06);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);border-radius:24px;padding:40px 32px;text-decoration:none;color:#fff;transition:all .4s cubic-bezier(.4,0,.2,1);position:relative;overflow:hidden}
        .card:hover{transform:translateY(-8px);border-color:rgba(255,255,255,.2);box-shadow:0 30px 60px rgba(0,0,0,.3)}
        .card-icon{width:64px;height:64px;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px}
        .card-erp .card-icon{background:linear-gradient(135deg,#3b82f6,#1e40af);box-shadow:0 12px 24px rgba(59,130,246,.25)}
        .card-recruit .card-icon{background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 12px 24px rgba(16,185,129,.25)}
        .card-icon .material-icons-round{font-size:32px;color:#fff}
        .card h2{font-size:22px;font-weight:700;margin-bottom:8px}
        .card p{font-size:14px;color:rgba(255,255,255,.5);line-height:1.6;margin-bottom:24px}
        .features{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:16px}
        .feature-tag{font-size:11px;padding:4px 10px;background:rgba(255,255,255,.08);border-radius:20px;color:rgba(255,255,255,.5);font-weight:500}
        .card-btn{display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:#fff;opacity:.7;transition:all .3s}
        .card:hover .card-btn{opacity:1;gap:12px}
        .footer{color:rgba(255,255,255,.25);font-size:13px;animation:fadeInUp .8s ease-out .4s both}
        @keyframes fadeInDown{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .card-erp{animation:fadeInUp .6s ease-out .1s both}
        .card-recruit{animation:fadeInUp .6s ease-out .25s both}
        @media(max-width:640px){.cards{grid-template-columns:1fr;gap:16px}.company-name{font-size:24px}.card{padding:28px 24px}}
    </style>
</head>
<body>
    <div class="stars" id="stars"></div>
    <div class="ocean-bg"><div class="wave"></div><div class="wave"></div></div>
    <div class="container">
        <div class="logo-section">
            <div class="logo-icon"><span class="material-icons-round">sailing</span></div>
            <h1 class="company-name">Indo<span>Ocean</span></h1>
            <p class="tagline">Maritime Crew Management System</p>
        </div>
        <div class="cards">
            <a href="/erp/" class="card card-erp">
                <div class="card-icon"><span class="material-icons-round">dashboard</span></div>
                <h2>ERP System</h2>
                <p>Kelola kontrak, crew, kapal, payroll, dan operasional maritim.</p>
                <div class="features">
                    <span class="feature-tag">Kontrak</span><span class="feature-tag">Crew</span><span class="feature-tag">Kapal</span><span class="feature-tag">Payroll</span>
                </div>
                <div class="card-btn">Masuk ERP <span class="material-icons-round">arrow_forward</span></div>
            </a>
            <a href="/recruitment/public/login" class="card card-recruit">
                <div class="card-icon"><span class="material-icons-round">groups</span></div>
                <h2>Recruitment</h2>
                <p>Sistem rekrutmen kru kapal — dari aplikasi hingga on-boarding.</p>
                <div class="features">
                    <span class="feature-tag">Pipeline</span><span class="feature-tag">Interview</span><span class="feature-tag">Dokumen</span><span class="feature-tag">Onboarding</span>
                </div>
                <div class="card-btn">Masuk Recruitment <span class="material-icons-round">arrow_forward</span></div>
            </a>
        </div>
        <p class="footer">&copy; 2026 PT Indo Ocean Crew Services. All rights reserved.</p>
    </div>
    <script>const s=document.getElementById("stars");for(let i=0;i<60;i++){const d=document.createElement("div");d.className="star";d.style.left=Math.random()*100+"%";d.style.top=Math.random()*60+"%";d.style.animationDelay=Math.random()*3+"s";d.style.width=d.style.height=(Math.random()*2+1)+"px";s.appendChild(d)}</script>
</body>
</html>';

$indexPath = __DIR__ . '/index.html';
if (file_put_contents($indexPath, $indexHtml)) {
    echo "✅ index.html created/updated successfully\n";
} else {
    echo "❌ Failed to write index.html\n";
}

// 2. Update .htaccess - remove redirect to ERP
$htaccessPath = __DIR__ . '/.htaccess';
$htaccess = file_get_contents($htaccessPath);
if ($htaccess !== false) {
    echo "📄 Current .htaccess loaded (" . strlen($htaccess) . " bytes)\n";
    
    // Check if redirect rule exists
    if (strpos($htaccess, 'RewriteRule ^$ erp/') !== false) {
        $htaccess = str_replace(
            "    # Redirect root to ERP\n    RewriteRule ^\$ erp/ [L,R=302]",
            "    # Root URL serves index.html landing page (no redirect)\n    # Users choose between ERP and Recruitment",
            $htaccess
        );
        // Try alternative line endings
        $htaccess = str_replace(
            "    # Redirect root to ERP\r\n    RewriteRule ^\$ erp/ [L,R=302]",
            "    # Root URL serves index.html landing page (no redirect)\r\n    # Users choose between ERP and Recruitment",
            $htaccess
        );
        // Fallback: just replace the rewrite rule line
        $htaccess = preg_replace(
            '/^\s*RewriteRule \^\$ erp\/.*$/m',
            '    # Landing page served by index.html (no redirect)',
            $htaccess
        );
        
        if (file_put_contents($htaccessPath, $htaccess)) {
            echo "✅ .htaccess updated - removed redirect to ERP\n";
        } else {
            echo "❌ Failed to write .htaccess\n";
        }
    } else {
        echo "ℹ️ .htaccess already doesn't have ERP redirect (already fixed or different format)\n";
        // Show current content
        echo "\nCurrent .htaccess content:\n";
        echo htmlspecialchars($htaccess);
    }
} else {
    // Create new .htaccess
    $newHtaccess = 'Options -Indexes +FollowSymLinks
DirectoryIndex index.html index.php

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Root URL serves index.html landing page (no redirect)
    # Users choose between ERP and Recruitment

    # Handle recruitment routing
    RewriteCond %{REQUEST_URI} ^/recruitment
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^recruitment/(.*)$ recruitment/public/index.php?url=$1 [L,QSA]

    # Handle ERP routing
    RewriteCond %{REQUEST_URI} ^/erp
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^erp/(.*)$ erp/index.php?url=$1 [L,QSA]
</IfModule>

# Security: deny access to sensitive files
<FilesMatch "\.(env|log|sql|sh|git)$">
    Require all denied
</FilesMatch>
';
    if (file_put_contents($htaccessPath, $newHtaccess)) {
        echo "✅ .htaccess created from scratch\n";
    } else {
        echo "❌ Failed to create .htaccess\n";
    }
}

// 3. Fix permissions
echo "\n🔧 Fixing permissions...\n";
@chmod($indexPath, 0644);
@chmod($htaccessPath, 0644);
echo "✅ Permissions set\n";

// 4. Verify
echo "\n📋 Verification:\n";
echo "- index.html exists: " . (file_exists($indexPath) ? "✅ YES" : "❌ NO") . "\n";
echo "- index.html size: " . filesize($indexPath) . " bytes\n";
echo "- .htaccess exists: " . (file_exists($htaccessPath) ? "✅ YES" : "❌ NO") . "\n";
echo "- DirectoryIndex in .htaccess: " . (strpos(file_get_contents($htaccessPath), 'DirectoryIndex') !== false ? "✅ YES" : "❌ NO") . "\n";
echo "- ERP redirect removed: " . (strpos(file_get_contents($htaccessPath), 'RewriteRule ^$ erp/') === false ? "✅ YES" : "❌ NO (still there!)") . "\n";

echo "\n</pre>";
echo "<h3>✅ Done! Now visit <a href='/'>indooceancrewservice.com</a> to see the landing page.</h3>";
echo "<p style='color:red;font-weight:bold'>⚠️ DELETE THIS FILE after confirming it works!</p>";
?>
