<?php
// Process only if accessed via GET method and 'url_start' and 'l' are provided
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['url_start'], $_GET['l'])) {
    $config = json_decode(file_get_contents('config.json'), true);
    $secret_key = $config['secret_key'];
    $links = $config['links'];
    $url_start = rtrim($_GET['url_start'], '/');
    $link_key = $_GET['l'];

    if (empty($link_key) || !isset($links[$link_key])) {
        die('Invalid or missing link key');
    }

    $hmac = hash_hmac('sha256', $link_key, $secret_key);
    $full_url = $url_start . "/redirect.php?l=" . $link_key . "&hmac=" . $hmac;

    echo "<p><strong>Generated URL:</strong><br><code>{$full_url}</code></p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate URL</title>
    <style>
        *{
            border: 0;
            padding: 0;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <h2>Generate a Secured URL</h2>
    <form method="get">
        <label for="url_start">Enter URL Start:</label><br>
        <input type="text" id="url_start" name="url_start" placeholder="https://www.linkurls.com" required><br><br>
        <label for="l">Enter Link Key:</label><br>
        <input type="text" id="l" name="l" placeholder="abc123" required><br><br>
        <input type="submit" value="Generate URL">
    </form>
</body>
</html>