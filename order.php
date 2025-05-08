<!-- <?php
session_start();
?> -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Get Secured Link - Home</title>
    <link
      rel="shortcut icon"
      href="./asset/img/68872.png"
      type="image/x-icon"
    />
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
    <link rel="stylesheet" href="./asset/style/animate.css" />
  </head>

  <body>
    <main>
      <nav class="navbar">
        <section class="left-nav">
          <button>ERROR 404</button>
        </section>
        <section class="right-nav">
          <button type="button" onclick="location.reload()">
            Refresh Page
          </button>
          <button type="button">Get Link</button>
        </section>
      </nav>
      <section class="main-section" style="height: 300px">
        <div class="loader-container">
          <span class="loader" style=""></span>
        </div>
        <div class="payment-message-container" style="margin: 2.5em 0">
          <div class="payment-type">
            <span>Payment Type: Bitcoin</span>
          </div>
          <div class="wallet-address">
            <span>Wallet Address: <span></span></span>
          </div>
          <div class="payment-status">
            <span>No payment detected, please make payment.</span>
          </div>
          <div class="order-expiry">
            <span>Order expires in: <span></span></span>
          </div>
        </div>
      </section>
    </main>
    <script></script>
  </body>
</html>
