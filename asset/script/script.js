window.addEventListener("DOMContentLoaded", () => {
  const emailaddr = document.querySelector(".user-email");
  const period = document.querySelector(".duration");
  const paymenttype = document.querySelector(".payment-select");
  const orderbtn = document.querySelector(".order-btn");
  const orderStatus = document.querySelector(".order-status");
  const price = document.querySelector(".order-price");
  const usdtTokenField = document.querySelector(".usdt-token");
  const showErrorMessage = document.querySelector(".error-status");

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
        error: "Network error or invalid response.",
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
    const payment = paymenttype.value === "---" ? false : paymenttype.value;
    if (email == "") {
      showErrorMessage.innerHTML = "* Email Address is required";
      return;
    }
    if (!testemail(email)) {
      showErrorMessage.innerHTML = "* Valid email address required";
      return;
    }

    if (!days || days < 1 || !payment) {
      showErrorMessage.innerHTML = "Select valid duration and payment type";
      return;
    }

    if (payment === "tether") {
      const token = document.querySelector('input[name="token-type"]:checked');
      tokenType = token ? token.value : "";
      if (!tokenType) {
        showErrorMessage.innerHTML = "Select a USDT token type";
        return;
      }
    }

    const data = {
      email: email,
      days: days,
      paymentType: payment,
      token: tokenType,
    };

    showErrorMessage.innerHTML = "";

    sendDataToPHP(data).then((result) => {
      const order = {
        success: result.response,
        email: result.email,
        price: result.price,
        paymenttype: result.token,
        wallet: result.wallet,
      };

      localStorage.setItem("PaymentSession", JSON.stringify(order));
    });
  });
});
