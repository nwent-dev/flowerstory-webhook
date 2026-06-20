<?php
/**
 * Webhook receiver for Tilda forms — diagnostic version.
 * Logs absolutely everything: method, headers, raw body, $_POST, $_GET.
 */

header('Access-Control-Allow-Origin: *');

$logFile = __DIR__ . '/webhook_log.txt';

$rawInput = file_get_contents('php://input');

$headers = [];
if (function_exists('getallheaders')) {
    $headers = getallheaders();
} else {
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $headers[$key] = $value;
        }
    }
}

$logEntry  = "==== " . date('Y-m-d H:i:s') . " ====" . PHP_EOL;
$logEntry .= "METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'unknown') . PHP_EOL;
$logEntry .= "CONTENT-TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set') . PHP_EOL;
$logEntry .= "CONTENT-LENGTH: " . ($_SERVER['CONTENT_LENGTH'] ?? 'not set') . PHP_EOL;
$logEntry .= "HEADERS:" . PHP_EOL . print_r($headers, true) . PHP_EOL;
$logEntry .= "RAW INPUT (" . strlen($rawInput) . " bytes):" . PHP_EOL . $rawInput . PHP_EOL . PHP_EOL;
$logEntry .= "PARSED \$_POST:" . PHP_EOL . print_r($_POST, true) . PHP_EOL;
$logEntry .= "PARSED \$_GET:" . PHP_EOL . print_r($_GET, true) . PHP_EOL;
$logEntry .= "PARSED \$_REQUEST:" . PHP_EOL . print_r($_REQUEST, true) . PHP_EOL;
$logEntry .= str_repeat('-', 60) . PHP_EOL . PHP_EOL;

// Write to stderr line by line — php -S forwards stderr straight to Render's Logs tab
foreach (explode(PHP_EOL, $logEntry) as $line) {
    fwrite(STDERR, $line . PHP_EOL);
}

// Also try to write to a file, in case it's useful locally
@file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

http_response_code(200);
echo "ok";
