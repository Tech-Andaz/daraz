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
}
