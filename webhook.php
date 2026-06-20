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

$writeResult = @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

if ($writeResult === false) {
    // If writing failed, at least try to report it via response (won't be seen by Tilda, but helps manual testing)
    http_response_code(200);
    echo "log_write_failed";
    exit;
}

http_response_code(200);
echo "ok";
