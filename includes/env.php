<?php
// Load environment variables from .env file
// Ưu tiên: Docker environment variables > .env file
$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse line
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Chỉ set nếu chưa có từ Docker environment
            if (!getenv($name) || getenv($name) === '') {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            } else {
                // Đồng bộ từ Docker env vào $_ENV và $_SERVER
                $_ENV[$name] = getenv($name);
                $_SERVER[$name] = getenv($name);
            }
        }
    }
}
?>
