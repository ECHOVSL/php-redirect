<?php
$generated_url = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['url_start']) && isset($_GET['l'])) {
    $config = json_decode(file_get_contents('config.json'), true);
    $secret_key = $config['secret_key'];
    $links = $config['links'];
    $url_start = trim($_GET['url_start']);
    $link_key = trim($_GET['l']);

    if (!empty($link_key) && isset($links[$link_key])) {
        $hmac = hash_hmac('sha256', $link_key, $secret_key);
        $generated_url = rtrim($url_start, '/') . "/redirect.php?l={$link_key}&hmac={$hmac}";
    } else {
        $generated_url = "❌ Invalid or missing link key.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark light">
    <title>Secure URL Generator</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: monospace;
            background-color: #121212;
            color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2em;
            min-height: 100vh;
        }
        .container {
            background: #1e1e1e;
            padding: 2em;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.05);
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5em;
        }
        label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.6em;
            margin-bottom: 1.5em;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 6px;
            color: #fff;
        }
        input[type="submit"], .copy-btn {
            width: 100%;
            padding: 0.7em;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 1em;
        }
        input[type="submit"]:hover, .copy-btn:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 1.5em;
            background: #2a2a2a;
            padding: 1em;
            border-radius: 8px;
            word-break: break-all;
        }
        .toast {
            visibility: hidden;
            min-width: 200px;
            background-color: #28a745;
            color: white;
            text-align: center;
            border-radius: 8px;
            padding: 0.75em 1em;
            position: fixed;
            z-index: 1;
            bottom: 30px;
            right: 30px;
            font-size: 1em;
            opacity: 0;
            transition: opacity 0.4s ease, visibility 0s linear 0.4s;
        }

        .toast.show {
            visibility: visible;
            opacity: 1;
            transition: opacity 0.4s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Generate a Secured URL</h2>
        <form method="get">
            <label for="url_start">URL Start (e.g. https://www.linkurls.com)</label>
            <input type="text" name="url_start" id="url_start" required value="<?= isset($_GET['url_start']) ? htmlspecialchars($_GET['url_start']) : '' ?>">

            <label for="l">Link Key (e.g. abc123)</label>
            <input type="text" name="l" id="l" required value="<?= isset($_GET['l']) ? htmlspecialchars($_GET['l']) : '' ?>">

            <input type="submit" value="Generate Secure URL">
        </form>

        <?php if (!empty($generated_url)): ?>
            <div class="result">
                <strong>Generated URL:</strong><br>
                <input type="text" id="copyTarget" value="<?= htmlspecialchars($generated_url) ?>" readonly style="background:#333; border:none; width:100%; color:#fff; margin-top:0.5em; padding:0.5em;">
                <button class="copy-btn" onclick="copyToClipboard()">Copy to Clipboard</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="toast" id="toast">Copied to clipboard ✅</div>

    <script>
        function copyToClipboard() {
            const copyText = document.getElementById("copyTarget");
            if (navigator.clipboard) {
                navigator.clipboard.writeText(copyText.value).then(() => {
                    showToast();
                });
            } else {
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                showToast();
            }
        }

        function showToast() {
            const toast = document.getElementById("toast");
            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 2000);
        }
    </script>
</body>
</html>