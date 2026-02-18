<?php
// router.php for PT Indo Ocean

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$rootDir = __DIR__;

// 1. Serve static files if they exist directly
if ($uri !== '/' && file_exists($rootDir . $uri) && is_file($rootDir . $uri)) {
    return false; // PHP server handles it
}

// 2. Handle /recruitment/assets/ mapping
// Pattern: ^recruitment/assets/(.*)$ -> recruitment/public/assets/$1
if (strpos($uri, '/recruitment/assets/') === 0) {
    $relativePath = substr($uri, strlen('/recruitment/assets/'));
    $file = $rootDir . '/recruitment/public/assets/' . $relativePath;
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimes = [
            'css' => 'text/css',
            'js'  => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        if (isset($mimes[$ext])) header("Content-Type: $mimes[$ext]");
        readfile($file);
        return true;
    }
}

// 3. Handle Recruitment System
if (strpos($uri, '/recruitment') === 0) {
    $script = $rootDir . '/recruitment/public/index.php';
    if (file_exists($script)) {
        $_SERVER['SCRIPT_NAME'] = '/recruitment/public/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $script;
        // Fix for CodeIgniter/Laravel paths
        $_SERVER['PHP_SELF'] = '/recruitment/public/index.php' . $uri;
        
        chdir(dirname($script));
        require $script;
        return true;
    }
}

// 4. Handle ERP System
if (strpos($uri, '/erp') === 0) {
    $script = $rootDir . '/erp/index.php';
    if (file_exists($script)) {
        // RewriteRule ^erp/?(.*)$ erp/index.php?url=$1 [L,QSA]
        // We need to set the GET parameter 'url'
        $pathInfo = substr($uri, 4); // remove /erp
        if (empty($pathInfo) || $pathInfo === '/') {
             // defaults
        } else {
             $_GET['url'] = ltrim($pathInfo, '/');
        }

        $_SERVER['SCRIPT_NAME'] = '/erp/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $script;
        chdir(dirname($script));
        require $script;
        return true;
    }
}

// 5. Default: Serve index.html or index.php
if ($uri === '/' || $uri === '/index.html') {
    if (file_exists($rootDir . '/index.html')) {
        readfile($rootDir . '/index.html');
        return true;
    }
}

return false;
?>
