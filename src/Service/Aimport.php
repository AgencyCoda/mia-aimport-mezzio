<?php 

namespace Mia\Aimport\Service;

use GuzzleHttp\Psr7\Request;

class Aimport 
{
    /**
     * 
     */
    const BASE_URL = 'https://api.iamport.kr/';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $apiKey = '';
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $apiSecret = '';
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $authToken = '';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function getToken()
    {
        $body = json_encode(array(
            'imp_key' => $this->apiKey,
            'imp_secret' => $this->apiSecret
        ));

        $request = new Request('POST', self::BASE_URL . 'users/getToken', ['Content-Type' => 'application/json'], $body);

        $response = $this->guzzle->send($request);
        if($response->getStatusCode() != 200){
            return false;
        }

        $result = json_decode($response->getBody()->getContents());

        return $result->response->access_token;
    }

    public function initAuthToken()
    {
        $this->authToken = $this->getToken();
    }

    public function getPaymentInfo($impUid)
    {
        if($this->authToken == ''){
            $this->initAuthToken();
        }

        return $this->generateRequest('GET', 'payments/' + $impUid);
    }

    protected function generateRequest($method, $path, $params = null)
    {
        $body = null;
        if($params != null){
            $body = json_encode($params);
        }

        $request = new Request(
            $method, 
            self::BASE_URL . $path, 
            [
                'Content-Type' => 'application/json',
                'Authorization' => $this->authToken
            ], $body);

        $response = $this->guzzle->send($request);
        if($response->getStatusCode() == 200){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }
}