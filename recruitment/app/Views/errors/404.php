<?php
// Detect Laragon Pretty URL
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLaragon = (strpos($host, '.test') !== false || strpos($host, '.local') !== false);
$basePath = $isLaragon ? '' : '/indoocean';
$recruitmentPath = $basePath . '/recruitment/public';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0A2463 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .error-container {
            text-align: center;
            padding: 40px;
        }
        .error-icon {
            font-size: 120px;
            color: #D4AF37;
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        h1 {
            font-size: 72px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        p {
            font-size: 16px;
            opacity: 0.7;
            margin-bottom: 40px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: #D4AF37;
            color: #0A2463;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <img src="<?= $recruitmentPath ?>/assets/images/logo.jpg" alt="Indo Ocean" style="height: 80px;">
        </div>
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for seems to have sailed away.</p>
        <a href="<?= $recruitmentPath ?>/" class="btn">
            <i class="fas fa-home"></i> Back to Home
        </a>
    </div>
</body>
</html>
