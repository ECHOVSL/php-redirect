<?php
session_start();
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
    crossorigin="anonymous"></script>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
    crossorigin="anonymous" />
  <link rel="stylesheet" href="./asset/style/css.css" />
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
    <section class="main-section">
      <div class="title">
        <h2>Choose A Plan</h2>
        <p>$20 : Day</p>
      </div>
      <div class="form-container">
        <form action="" method="post" novalidate>
          <div
            class="form-field"
            style="
                display: flex;
                flex-direction: column;
                gap: 10px;
                justify-content: center;
              ">
            <div style="display: flex">
              <label for="">Email Address:</label>
              <div class="input-div" style="width: 70%; margin: 0 10px">
                <input
                  type="email"
                  name=""
                  id=""
                  class="input-bar user-email" />
              </div>
            </div>
          </div>

          <div class="form-field field-box">
            <label for="">Choose Duration:</label>
            <select id="mode" name="mode" class="duration duration-select">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
            </select>
            <span>Days</span>
          </div>
          <div class="form-field">
            <label for="">Payment Type:</label>
            <select
              id="mode"
              name="mode"
              class="payment-select duration-select">
              <option value="NULL">---</option>
              <option value="bitcoin">BTC</option>
              <option value="ethereum">ETH</option>
              <option value="tether">USDT</option>
            </select>
            <span style="" class="order-price"></span>
            <span>
              <span class="usdt-token" style="margin-left: 30px">
                <span>
                  <label for="">TRC20</label>
                  <input
                    type="radio"
                    name="token-type"
                    class="token-type"
                    value="TRC20" />
                </span>
                <span>
                  <label for="">ERC20</label>
                  <input
                    type="radio"
                    name="token-type"
                    class="token-type"
                    value="ERC20" />
                </span>
              </span>
            </span>
          </div>

          <div class="form-field">
            <span for="">Wallet Address:
              <span class="wallet-bar"></span>
            </span>
          </div>
          <div class="form-field">
            <span for="" class="order-status">Place an order</span>
          </div>
          <div class="button-field btn-flex">
            <button type="button" class="order-btn" style="width: 100%">
              Place Order
            </button>
            <button type="button" style="width: 100%">Get Link</button>
          </div>
        </form>
      </div>
    </section>
  </main>
  <script>
    window.addEventListener("DOMContentLoaded", () => {
      const emailaddr = document.querySelector(".user-email");
      const period = document.querySelector(".duration");
      const paymenttype = document.querySelector(".payment-select");
      const orderbtn = document.querySelector(".order-btn");
      const orderStatus = document.querySelector(".order-status");
      const price = document.querySelector(".order-price");
      const usdtTokenField = document.querySelector(".usdt-token");

      function testemail(e) {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(e);
      }

      async function sendDataToPHP(data) {
        try {
          const response = await fetch("./asset/script/script.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams(data),
          });

          const result = await response.json();
          return result;
        } catch (err) {
          return {
            success: false,
            error: "Network error or invalid response."
          };
        }
      }

      let tokenType = "";

      paymenttype.addEventListener("change", (e) => {
        if (e.target.value === "tether") {
          usdtTokenField.style.display = "inline-block";
        } else {
          usdtTokenField.style.display = "none";
        }
      });

      orderbtn.addEventListener("click", (e) => {
        e.preventDefault();

        const email = emailaddr.value.trim();
        const days = parseInt(period.value);
        const payment =
          paymenttype.value === "---" ? false : paymenttype.value;

        if (!testemail(email)) {
          alert("Invalid email address");
          return;
        }

        if (!days || days <= 0 || !payment) {
          alert("Select valid duration and payment type");
          return;
        }

        if (payment === "tether") {
          const token = document.querySelector(
            'input[name="token-type"]:checked'
          );
          tokenType = token ? token.value : "";
          if (!tokenType) {
            alert("Select a USDT token type");
            return;
          }
        }

        const data = {
          email: email,
          days: days,
          paymentType: payment,
          token: tokenType,
        };

        orderStatus.innerHTML = "Processing order...";

        sendDataToPHP(data).then((result) => {
          if (result.success) {
            price.innerHTML = `${result.price} ${result.token}`;
            usdtTokenField.style.display = "none";
            orderStatus.innerHTML = `Processing order...`;
          } else {
            orderStatus.innerHTML = `Error: ${result.error || "Something went wrong."}`;
          }
        });
      });
    });
  </script>
</body>

</html>