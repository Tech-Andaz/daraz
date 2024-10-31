<?php

namespace TechAndaz\Daraz;
use Lazada\LazopClient;
use Lazada\LazopRequest;
include __DIR__ . '/LazopSdk.php';

class DarazClient
{
    public $api_url;
    public $app_key;
    public $app_secret; 
    public $callback_url; 
    public $daraz_client;
    private $access_token;

    public function __construct($app_key, $app_secret, $callback_url, $access_token = "")
    {
        $this->api_url = 'https://api.daraz.pk/rest';
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $this->callback_url = $callback_url;
        $this->access_token = $access_token;
        $this->daraz_client = new LazopClient($this->api_url, $this->app_key, $this->app_secret);
    }
    public function makeRequest($config){
        $endpoint = isset($config['endpoint']) ? $config['endpoint'] : throw new DarazException('Endpoint is required');
        $method = isset($config['method']) ? $config['method'] : "GET";
        $parameters = isset($config['parameters']) ? $config['parameters'] : array();
        $files = isset($config['files']) ? $config['files'] : array();
        $access_token = isset($config['access_token']) ? $config['access_token'] : $this->access_token;
        if($access_token == "" && $endpoint != "/auth/token/create" && $endpoint != "/auth/token/refresh"){
            throw new DarazException('Access Token is required. Can be set during initialization or each request.');
        }
        if(!is_array($parameters)){
            $parameters = array();
        }
        if(!is_array($files)){
            $files = array();
        }
        $request = new LazopRequest($endpoint, $method);
        foreach($parameters as $key => $param){
            $request->addApiParam($key, $param);
        }
        foreach($files as $file){
            $request->addFileParam($file['type'], file_get_contents($file['url']));
        }
        try {
            if($access_token != ""){
                $response = $this->daraz_client->execute($request, $access_token);
            } else {
                $response = $this->daraz_client->execute($request);
            }
        } catch(Exception $e) {
            throw new DarazException($e);
        }
        return $response;
    }
}
