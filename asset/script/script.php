<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
$_SESSION["price"] = "";

function get_rate($asset, $currency)
{
    $get_rate_url = "https://api.coingecko.com/api/v3/simple/price?ids=$asset&vs_currencies=$currency";
    $rate_response = file_get_contents($get_rate_url);
    $rate_data = json_decode($rate_response, true);
    return $rate_data[$asset][$currency] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $emailaddr = $_POST["email"] ?? '';
    $duration = (int)($_POST["days"] ?? 0);
    $paymentMethod = $_POST["paymentType"] ?? '';
    $token = ($paymentMethod === "tether" && isset($_POST["token"])) ? $_POST["token"] : $_POST["paymentType"];

    if (!filter_var($emailaddr, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "error" => "Invalid email address."]);
        exit;
    }

    $rate = (float)get_rate($paymentMethod, "usd");
    if ($rate <= 0) {
        echo json_encode(["success" => false, "error" => "Invalid rate or unsupported currency."]);
        exit;
    }

    $price = 20;
    $price_by_duration = $price * $duration;
    $price_in_token = $price_by_duration / $rate;
    $_SESSION["price"] = round($price_in_token, 8);

    echo json_encode([
        "success" => true,
        "usd_rate" => $rate,
        "price_usd" => $price,
        "duration" => $duration,
        "payment_type" => $paymentMethod,
        "token" => $token,
        "price" => round($price_in_token, 8)
    ]);
    exit;
}
