<?php

require 'vendor/autoload.php';

use TechAndaz\Daraz\DarazClient;
use TechAndaz\Daraz\DarazAPI;

$DarazClient = new DarazClient("502736", "GapPqYo58gd8bQlVX8OtY9gnrwvmgY5Q", "https://portal.alfatah.pk/integration/daraz", "50000600a13MQJ7jlSYRqaBdo194cb7dfwhLUfsvjadiq6ns0coHQjYE0nO42C");
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
        $code = "4_502736_YQ4sbwzo7bQYbQ25GNLuu0Vp56";
        $response = $DarazAPI->exchangeCodeForToken($code);
        return $response;
    } catch (TechAndaz\Daraz\DarazException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
//Refresh Access Token
function refreshAccessToken($DarazAPI){
    try {
        $refresh_token = "50001600313aivobCUxIr7Kow1e90c1b7yrTEezm0gjxaurddbjTAnUUmaslmz";
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
                "created_after" => "2017-02-10T09:00:00+08:00"
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

// echo (generateSellerAuthURL($DarazAPI));
// echo (exchangeCodeForToken($DarazAPI));
// echo (refreshAccessToken($DarazAPI));
// echo (makeRequest($DarazAPI));
echo (makeRequest2($DarazAPI));
// echo json_encode(getPickupLocation($DarazAPI));
?>

