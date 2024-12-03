<?php

require 'vendor/autoload.php';

use TechAndaz\Daraz\DarazClient;
use TechAndaz\Daraz\DarazAPI;

//Test Account
// $DarazClient = new DarazClient("502736", "GapPqYo58gd8bQlVX8OtY9gnrwvmgY5Q", "https://portal.alfatah.pk/integration/daraz", "50000600223xLOTpBFSzerjMIq0QUfZ5mxe13da99a0pqcupjWkEw6NS3Xu5Pk");
//Alfatah Account
$DarazClient = new DarazClient("502736", "GapPqYo58gd8bQlVX8OtY9gnrwvmgY5Q", "https://portal.alfatah.pk/integration/daraz", "50000602033YO3q5r0ThJHLmWHyxlG0HovjMoyxtRX29o13e2b163gIFJYufRu");
$DarazAPI = new DarazAPI($DarazClient);

function generateSellerAuthURL($DarazAPI){
    try {
        $response = $DarazAPI->generateSellerAuthURL();
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function exchangeCodeForToken($DarazAPI){
    try {
        $code = "4_502736_ByjGgqGLgnPYep6atK2SGmnd1062";
        $response = $DarazAPI->exchangeCodeForToken($code);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function refreshAccessToken($DarazAPI){
    try {
        $refresh_token = "50001600123bi1wUPEDULeTlqCsZRvaf5jv18b72f69kiscsvcfFhsgL1or7jN";
        $response = $DarazAPI->refreshAccessToken($refresh_token);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function getAllOrders($DarazAPI){
    try {
        $settings = array(
            "parameters" => array(
                "sort_by" => "created_at",
                "sort_direction" => "DESC",
                "created_after" => "2017-02-10T09:00:00+08:00"
            ),
        );
        $response = $DarazAPI->getAllOrders($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function getOrder($DarazAPI, $order_id){
    try {
        $response = $DarazAPI->getOrder($order_id);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function getOrderItems($DarazAPI, $order_id){
    try {
        $response = $DarazAPI->getOrderItems($order_id);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function fetchAndShipOrder($DarazAPI){
    $order = getOrder($DarazAPI, "203082453807589")['data'];
    $order_items = getOrderItems($DarazAPI, $order['order_number'])['data'];
    $response = packAndShipOrder($DarazAPI, $order['order_id'], array_column($order_items, "order_item_id"));
    print_r($response);
    exit;
}
function packOrder($DarazAPI, $order_id, $items){
    try {
        $settings = array(
            "order_id" => "$order_id",
            "items" => $items,
        );
        $response = $DarazAPI->packOrderItems($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function printLabel($DarazAPI, $packages){
    try {
        $settings = array(
            "packages" => $packages,
        );
        $response = $DarazAPI->printShippingLabel($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function packAndShipOrder($DarazAPI, $order_id, $items){
    try {
        $settings = array(
            "order_id" => "$order_id",
            "items" => $items,
        );
        $response = $DarazAPI->packAndShipOrder($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function readyToShip($DarazAPI, $packages){
    try {
        $settings = array(
            "packages" => $packages,
        );
        $response = $DarazAPI->readyToShip($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
// print_r (generateSellerAuthURL($DarazAPI));
// print_r (exchangeCodeForToken($DarazAPI));
// print_r (refreshAccessToken($DarazAPI));
// print_r (getAllOrders($DarazAPI));
// print_r (getOrder($DarazAPI, "203082453807589"));
// print_r (getOrderItems($DarazAPI, "203082453807589"));
// print_r (fetchAndShipOrder($DarazAPI));
// print_r json_encode(getPickupLocation($DarazAPI));
?>