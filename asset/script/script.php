<?php
session_start();

function get_rate($asset, $currency)
{
    $get_rate_url = "https://api.coingecko.com/api/v3/simple/price?ids=$asset&vs_currencies=$currency";
    $rate_response = file_get_contents($get_rate_url);
    $rate_data = json_decode($rate_response, true);
    return $rate_data[$asset][$currency] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $token = '';
    $emailaddr = $_POST["email"] ?? '';
    $duration = $_POST["days"] ?? '';
    $paymentMethod = $_POST["paymentType"] ?? '';
    $token = ($paymentMethod === "usdt" && isset($_POST["token"])) ? $_POST["token"] : '';

    $rate = get_rate($paymentMethod, "usd");
    $price = 20;
    $price_by_duration = $price * $duration;
    $_SESSION["price"] = $price_by_duration / $rate;
}
