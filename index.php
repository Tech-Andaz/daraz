<?php

require 'vendor/autoload.php';

use TechAndaz\Daraz\DarazClient;
use TechAndaz\Daraz\DarazAPI;

//Test Account
// $DarazClient = new DarazClient("502736", "GapPqYo58gd8bQlVX8OtY9gnrwvmgY5Q", "https://portal.alfatah.pk/integration/daraz", "50000600223xLOTpBFSzerjMIq0QUfZ5mxe13da99a0pqcupjWkEw6NS3Xu5Pk");
//Alfatah Account
$DarazClient = new DarazClient("502736", "GapPqYo58gd8bQlVX8OtY9gnrwvmgY5Q", "https://portal.alfatah.pk/integration/daraz", "50000602033YO3q5r0ThJHLmWHyxlG0HovjMoyxtRX29o13e2b163gIFJYufRu");
$DarazAPI = new DarazAPI($DarazClient);

//Generate Authorization URL
function generateSellerAuthURL($DarazAPI){
    try {
        $response = $DarazAPI->generateSellerAuthURL();
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
//Exchange Auth Code for Access Token
function exchangeCodeForToken($DarazAPI){
    try {
        $code = "4_502736_ByjGgqGLgnPYep6atK2SGmnd1062";
        $response = $DarazAPI->exchangeCodeForToken($code);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
//Refresh Access Token
function refreshAccessToken($DarazAPI){
    try {
        $refresh_token = "50001600123bi1wUPEDULeTlqCsZRvaf5jv18b72f69kiscsvcfFhsgL1or7jN";
        $response = $DarazAPI->refreshAccessToken($refresh_token);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
//Make Request
function makeRequest($DarazAPI){
    try {
        $settings = array(
            "endpoint" => "/orders/get",
            "method" => "GET",
            "parameters" => array(
                "sort_by" => "created_at",
                "sort_direction" => "DESC",
                "created_after" => "2017-02-10T09:00:00+08:00"
            ),
        );
        $response = $DarazAPI->makeRequest($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
function makeRequest3($DarazAPI, $order_id){
    try {
        $settings = array(
            "endpoint" => "/order/get",
            "method" => "GET",
            "parameters" => array(
                "order_id" => "$order_id",
            ),
        );
        $response = $DarazAPI->makeRequest($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
//Make Request 2
function makeRequest2($DarazAPI){
    try {
        $settings = array(
            "endpoint" => "/order/items/get",
            "method" => "GET",
            "parameters" => array(
                "order_id" => "127242507235768"
            ),
        );
        $response = $DarazAPI->makeRequest($settings);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

function convertOrderToShopify($DarazAPI){
    $order_number = "203082453807589";
    $order = json_decode(makeRequest3($DarazAPI, $order_number),true)['data'];
    $order_number = $order['order_number'];

    $settings = array(
        "endpoint" => "/order/items/get",
        "method" => "GET",
        "parameters" => array(
            "order_id" => "$order_number"
        ),
    );
    $order_items = json_decode($DarazAPI->makeRequest($settings),true)['data'];

    $randomKeys = array_rand($order_items, 4);
    $randomSubset = is_array($randomKeys) ? array_intersect_key($order_items, array_flip($randomKeys)) : [$order_items[$randomKeys]];

    packOrder($DarazAPI, $order['order_id'], array_column($randomSubset,"order_item_id"));

}

function packOrder($DarazAPI, $order_id, $items){
    $settings = array(
        "endpoint" => "/order/fulfill/pack",
        "method" => "POST",
        "parameters" => array(
            "packReq" => json_encode(array(
                "pack_order_list" => array(
                    array(
                        "order_item_list" => $items,
                        "order_id" => "$order_id",
                    ),
                ),
                "shipping_allocate_type" => "TFS",
                "delivery_type" => "dropship",
            ))
        ),
    );
    $pack = json_decode($DarazAPI->makeRequest($settings),true);
    if($pack['code'] == 0 && isset($pack['result']['data'])){
        $data = $pack['result']['data']['pack_order_list'][0]['order_item_list'];
        $packgaes = array_column($data, "package_id");
        print_r($packgaes);
        printLabel($DarazAPI, $packgaes);
    }
}
function printLabel($DarazAPI, $packages){
    $packages = array_unique($packages);
    $convertedArray = array_map(function($packageId) {
        return ["package_id" => $packageId];
    }, $packages);

    
    $settings = array(
        "endpoint" => "/order/package/document/get",
        "method" => "POST",
        "parameters" => array(
            "getDocumentReq" => json_encode(array(
                "packages" => $convertedArray,
                "doc_type" => "PDF",
                "print_item_list" => true,
            ))
        ),
    );
    print_r($settings);
    $label = json_decode($DarazAPI->makeRequest($settings),true);
    print_r($label);
}
// echo (generateSellerAuthURL($DarazAPI));
// echo (exchangeCodeForToken($DarazAPI));
// echo (refreshAccessToken($DarazAPI));
// echo (makeRequest($DarazAPI));
// echo (makeRequest2($DarazAPI));
echo (convertOrderToShopify($DarazAPI));
// echo json_encode(getPickupLocation($DarazAPI));
?>