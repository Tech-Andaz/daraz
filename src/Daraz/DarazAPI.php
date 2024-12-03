<?php

namespace TechAndaz\Daraz;

class DarazAPI
{
    private $DarazClient;

    public function __construct(DarazClient $DarazClient){
        $this->DarazClient = $DarazClient;
    }
    public function generateSellerAuthURL(){
        return 'https://api.daraz.pk/oauth/authorize?response_type=code&force_auth=true&redirect_uri=' . $this->DarazClient->callback_url . '&client_id=' . $this->DarazClient->app_key;
    }
    public function exchangeCodeForToken($code){
        $settings = array(
            "endpoint" => "/auth/token/create",
            "method" => "POST",
            "parameters" => array(
                "code" => $code
            ),
        );
        return $this->DarazClient->makeRequest($settings);
    }
    public function refreshAccessToken($refresh_token){
        $settings = array(
            "endpoint" => "/auth/token/refresh",
            "method" => "POST",
            "parameters" => array(
                "refresh_token" => $refresh_token
            ),
        );
        return $this->DarazClient->makeRequest($settings);
    }
    public function makeRequest($settings){
        return $this->DarazClient->makeRequest($settings);
    }
    public function getAllOrders($config){
        $parameters = isset($config['parameters']) ? $config['parameters'] : throw new DarazException('Parameters are required');
        $parameters = is_array($config['parameters']) ? $config['parameters'] : throw new DarazException('Parameters must be an array');
        $parameters = array_unique($parameters);
        if(count($parameters) == 0){
            throw new DarazException('Must have atleast 1 parameter');
        }
        $settings = array(
            "endpoint" => "/orders/get",
            "method" => "GET",
            "parameters" => $parameters,
        );
        $response = json_decode($this->DarazClient->makeRequest($settings),true);
        if(!isset($response['data']['orders'])){
            return array(
                "status" => 0,
                "error" => "There was an error processing request",
                "response" => $response
            );
        } else {
            return array(
                "status" => 1,
                "data" => $response['data']
            );
        }
    }
    public function getOrder($order_id){
        $order_id = isset($order_id) ? $order_id : throw new DarazException('Order ID is required');
        $settings = array(
            "endpoint" => "/order/get",
            "method" => "GET",
            "parameters" => array(
                "order_id" => "$order_id",
            ),
        );
        $response = json_decode($this->DarazClient->makeRequest($settings),true);
        if(!isset($response['data'])){
            return array(
                "status" => 0,
                "error" => "There was an error processing request",
                "response" => $response
            );
        } else {
            return array(
                "status" => 1,
                "data" => $response['data']
            );
        }
    }
    public function getOrderItems($order_id){
        $order_id = isset($order_id) ? $order_id : throw new DarazException('Order ID is required');
        $settings = array(
            "endpoint" => "/order/items/get",
            "method" => "GET",
            "parameters" => array(
                "order_id" => "$order_id",
            ),
        );
        $response = json_decode($this->DarazClient->makeRequest($settings),true);
        if(!isset($response['data'])){
            return array(
                "status" => 0,
                "error" => "There was an error processing request",
                "response" => $response
            );
        } else {
            return array(
                "status" => 1,
                "data" => $response['data']
            );
        }
    }
    public function packOrderItems($config){
        $order_id = isset($config['order_id']) ? $config['order_id'] : throw new DarazException('Order ID is required');
        $items = isset($config['items']) ? $config['items'] : throw new DarazException('Items are required');
        $items = is_array($config['items']) ? $config['items'] : throw new DarazException('Items must be an array');
        $items = array_unique($items);
        if(count($items) == 0){
            throw new DarazException('Must have atleast 1 item');
        }
        $shipping_allocate_type = isset($config['shipping_allocate_type']) ? $config['shipping_allocate_type'] : "TFS";
        $delivery_type = isset($config['delivery_type']) ? $config['delivery_type'] : "dropship";
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
                    "shipping_allocate_type" => "$shipping_allocate_type",
                    "delivery_type" => "$delivery_type",
                ))
            ),
        );
        $response = json_decode($this->DarazClient->makeRequest($settings),true);
        if($response['result']['success'] != 1){
            return array(
                "status" => 0,
                "error" => "There was an error processing request",
                "response" => $response
            );
        } else {
            $data = $response['result']['data']['pack_order_list'][0]['order_item_list'];
            $packages = array_column($data, "package_id");
            return array(
                "status" => 1,
                "packages" => $packages
            );
        }
    }
    public function printShippingLabel($config){
        $packages = isset($config['packages']) ? $config['packages'] : throw new DarazException('Packages are required');
        $packages = is_array($config['packages']) ? $config['packages'] : throw new DarazException('Packages must be an array');
        $packages = array_unique($packages);
        $packages = array_map(function($packageId) {
            return ["package_id" => $packageId];
        }, $packages);
        if(count($packages) == 0){
            throw new DarazException('Must have atleast 1 package');
        }
        $print_item_list = isset($config['print_item_list']) ? $config['print_item_list'] : true;
        $doc_type = isset($config['doc_type']) ? $config['doc_type'] : "PDF";
        $settings = array(
            "endpoint" => "/order/package/document/get",
            "method" => "POST",
            "parameters" => array(
                "getDocumentReq" => json_encode(array(
                    "packages" => $packages,
                    "doc_type" => "$doc_type",
                    "print_item_list" => $print_item_list,
                ))
            ),
        );
        $response = json_decode($this->DarazClient->makeRequest($settings),true);
        if($response['result']['success'] != 1){
            return array(
                "status" => 0,
                "error" => "There was an error processing request",
                "response" => $response
            );
        } else {
            $data = $response['result']['data'];
            return array(
                "status" => 1,
                "label_url" => $data['pdf_url'],
                "packages" => $packages,
                "response" => $data,
            );
        }
    }
    public function packAndShipOrder($config){
        $response = $this->packOrderItems($config);
        if($response['status'] == 0){
            return $response;
        }
        $packages = $response['packages'];
        $settings = array(
            "packages" => $packages,
        );
        $response = $this->printShippingLabel($settings);
        return $response;
    }
    public function readyToShip($config){
        $packages = isset($config['packages']) ? $config['packages'] : throw new DarazException('Packages are required');
        $packages = is_array($config['packages']) ? $config['packages'] : throw new DarazException('Packages must be an array');
        $packages = array_unique($packages);
        $packages = array_map(function($packageId) {
            return ["package_id" => $packageId];
        }, $packages);
        if(count($packages) == 0){
            throw new DarazException('Must have atleast 1 package');
        }
        $settings = array(
            "endpoint" => "/order/package/rts",
            "method" => "POST",
            "parameters" => array(
                "readyToShipReq" => json_encode(array(
                    "packages" => $packages,
                ))
            ),
        );
        $response = json_decode($this->DarazClient->makeRequest($settings),true);
        if($response['result']['success'] != 1){
            return array(
                "status" => 0,
                "error" => "There was an error processing request",
                "response" => $response
            );
        } else {
            $data = $response['result']['data'];
            return array(
                "status" => 1,
                "response" => $data,
            );
        }
    }
}
