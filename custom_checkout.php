<?php

ini_set('max_execution_time', '1700');
set_time_limit(1700);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json; charset=utf-8');
http_response_code(200);

ini_set('display_errors', '1');
error_reporting(E_ERROR);

function send_bearer($url, $token, $type = "GET", $param = []){
    $descriptor = curl_init($url);
     curl_setopt($descriptor, CURLOPT_POSTFIELDS, json_encode($param));
     curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($descriptor, CURLOPT_HTTPHEADER, array("User-Agent: M-Soft Integration", "Content-Type: application/json", "Authorization: Bearer ".$token)); 
     curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $type);
    $itog = curl_exec($descriptor);
    curl_close($descriptor);
    return $itog;
}

$input = json_decode(file_get_contents("php://input"), true);

$result["state"] = true;

if ($input["userId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'userId' is missing";
}
if ($input["token"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'token' is missing";
}
if ($input["message"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'message' is missing";
}
if ($input["string"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'string' is missing";
}
if ($input["delimiter"] == NULL) {
    $input["delimiter"] = PHP_EOL;
}
if ($result["state"] == false) {
    echo json_encode($result);
    exit;
}

// Шаблонные наборы
$productSearch = ["%number%","%product%","%essence%","%price%","%amount%","%cash%","%currency%","%quantity%","%sum%"];
$messageSearch = ["%checkout%","%stringCount%","%productCount%","%sum%","%currency%"];

// Получение списка корзины
$ps = 1;
$countS = 0;
$countP = [];
$sum = [];
for ($p=1; $p<=$ps; $p++) {
    $checkout = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$input["userId"]."/checkout?limitation=20&page=".$p, $input["token"]), true);
    if ($checkout["collection"] != NULL) {
        $ps = $checkout["cursor"]["pages"];
        foreach ($checkout["collection"] as $k => $product) {
            $productReplace = [
                (($p - 1) * 20) + $k + 1,
                $product["product"]["name"],
                $product["name"],
                $product["price"],
                $product["amount"],
                $product["cash"]["amount"],
                $product["cash"]["currency"],
                $product["pivot"]["quantity"],
                $product["cash"]["amount"] * $product["pivot"]["quantity"],
            ];
            $checkoutString[] = str_ireplace($productSearch, $productReplace, $input["string"]);
            $countS++;
            $countP[] = $product["pivot"]["quantity"];
            $sum[] = $product["cash"]["amount"] * $product["pivot"]["quantity"];
            $currency = $product["cash"]["currency"];
        }
    } else {
        $result["state"] = false;
        $result["error"]["message"][] = "failed load from checkout";
        $result["error"]["SmartSender"] = $checkout;
        echo json_encode($result);
        exit;
    }
}

$messageReplace = [
    implode($input["delimiter"], $checkoutString),
    $countS,
    array_sum($countP),
    array_sum($sum),
    $currency,
];

$message = str_ireplace($messageSearch,$messageReplace,$input["message"]);

$result["message"] = $message;
$result["checkout"] = $checkoutString;

if ($input["send"] !== false) {
    $sendMessage = [
        "type" => "text",
        "watermark" => 1,
        "content" => $message,
    ];
    $result["sendMessage"] = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$input["userId"]."/send", $input["token"], "POST", $sendMessage), true);
}


echo json_encode($result, JSON_UNESCAPED_UNICODE);