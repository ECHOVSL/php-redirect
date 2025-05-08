<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

class UserOrder {
    public $email, $payment, $wallet;
    public function __construct($email, $payment, $wallet) {
        $this->email = $email;
        $this->payment = $payment;
        $this->wallet = $wallet;
    }
}

function fetchWallet($type, $token, $price_in_token){}

function get_rate($asset, $currency){
    $get_rate_url = "https://api.coingecko.com/api/v3/simple/price?ids=$asset&vs_currencies=$currency";
    $rate_response = file_get_contents($get_rate_url);
    $rate_data = json_decode($rate_response, true);
    return $rate_data[$asset][$currency] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $price = 20;
    $emailaddr = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
    $duration = (int)($_POST["days"] ?? 0);
    $paymentMethod = $_POST["paymentType"] ?? '';
    $token = ($paymentMethod === "tether" && isset($_POST["token"])) ? $_POST["token"] : $paymentMethod;
    $rate = (float)get_rate($paymentMethod, "usd");
    $totalprice = $price * $duration;
    $price_in_token = round($totalprice / $rate, 8);
    $payment = $price_in_token . " " . $token;
    $wallet = fetchWallet($type, $token, $price_in_token);
    $_SESSION["PaymentSession"] = new UserOrder($emailaddr, $payment, $wallet);

    echo json_encode([
        "response" => true,
        "email" => $emailaddr,
        "price" => $price_in_token,
        "token" => $token,
        "wallet" => $wallet,
    ]);
    exit;
}