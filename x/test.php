<?php
$generated_url = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url_start']) && isset($_POST['l'])) {
    $config = json_decode(file_get_contents('config.json'), true);
    $secret_key = $config['secret_key'];
    $links = $config['links'];
    $url_start = trim($_POST['url_start']);
    $link_key = trim($_POST['l']);

    // Expiry time for 10 minutes
    $expiry_time = time() + 600;
    if (!empty($link_key) && isset($links[$link_key])) {
        $hmac = hash_hmac('sha256', "{$link_key}_{$expiry_time}", $secret_key);
        $generated_url = rtrim($url_start, '/') . "/index.php?l={$link_key}&hmac={$hmac}&expires={$expiry_time}";
    } else {
        $generated_url = "❌ Invalid or missing link key.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure URL Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            word-break: break-word;
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
        <form method="post" onsubmit="setTimeout(() => location.reload(), 5000);">
            <label for="url_start">URL Start (e.g. https://www.example.com)</label>
            <input type="text" name="url_start" id="url_start" required>

            <label for="l">Link Key (e.g. abc123)</label>
            <input type="text" name="l" id="l" required>

            <input type="submit" value="Generate Secure URL">
        </form>

        <?php if (!empty($generated_url)): ?>
            <div class="result">
                <strong>Generated URL:</strong><br>
                <input type="text" id="copyTarget" value="<?php echo htmlspecialchars($generated_url) ?>" readonly style="background:#333; border:none; width:100%; color:#fff; margin-top:0.5em; padding:0.5em;">
                <button class="copy-btn" onclick="copyToClipboard()">Copy to Clipboard</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="toast" id="toast">Copied to clipboard ✅</div>

    <script>
        function copyToClipboard() {
            const copyText = document.getElementById("copyTarget");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            const toast = document.getElementById("toast");
            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 2000);
        }
    </script>
</body>
</html>