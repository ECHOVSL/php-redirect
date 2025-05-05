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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Get Secured Link - Home</title>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
      crossorigin="anonymous"
    ></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="./asset/style/css.css" />
  </head>
  <body>
    <main>
      <nav class="navbar">
        <section class="left-nav">
          <button>ERROR 404</button>
        </section>
        <section class="right-nav">
          <button type="button">Get Key</button>
        </section>
      </nav>
      <section class="main-section">
        <div class="title">
          <h2>Generate Secured Link</h2>
          <p>TG: H4CKECHO</p>
        </div>
        <div class="form-container">
          <form action="" method="post">
            <div class="form-field" style="display: flex; gap: 10px; align-items: center;">
              <label for="">Original Link:</label>
              <div class="input-div" style="width: 70%">
                <input type="text" name="" id="" class="input-bar" />
              </div>
            </div>
            <div class="form-field" style="display: flex; gap: 10px; align-items: center;">
              <label for="">Secured Key:</label>
              <div class="input-div" style="width: 70%">
                <input type="text" name="" id="" class="input-bar" />
              </div>
            </div>
            <div class="form-field" style="display: flex; gap: 10px; align-items: center;">
              <label for="">Email Address:</label>
              <div class="input-div" style="width: 70%">
                <input type="email" name="" id="" class="input-bar" />
              </div>
            </div>
            
            <div class="button-field">
                <button type="button" style="width: 100%;">Generate Link</button>
            </div>
          </form>
        </div>
      </section>
    </main>
  </body>
</html>

