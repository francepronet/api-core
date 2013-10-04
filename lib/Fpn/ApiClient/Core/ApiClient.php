<?php

namespace Fpn\ApiClient\Core;

use Guzzle\Http\Client;
use Atst\Guzzle\Http\Plugin\WsseAuthPlugin;
use Fpn\ApiClient\Core\Utility\Caster;

class ApiClient
{
    private $host;
    private $port;
    private $ssl;
    private $username;
    private $password;

    public function __construct($host = 'localhost', $port = 80, $ssl = false, $username = false, $password = false)
    {
        $this->host 	= $host;
        $this->port 	= $port;
        $this->ssl  	= $ssl;
        $this->username = $username;
        $this->password = $password;
    }

    public function useSsl($ssl)
    {
        $this->ssl = $ssl;
    }

    public function request($method, $url, $datas = null)
    {
        $url = $this->prepareUrl($url);

        if (isset($datas['upload'])) {
            $upload = $datas['upload'];
            unset($datas['upload']);
        }

        $client  = new Client($this->prepareUrl());

        if($this->username && $this->password) {
        	$wsse = new WsseAuthPlugin($this->username, $this->password);
        	$client->getEventDispatcher()->addSubscriber($wsse);
        }
        
        $request = $client->createRequest(strtoupper($method), $url, null, $datas);

        if (isset($upload)) {
            $request->addPostFile('upload', $upload);
        }

        $response = $request->send();

        if (!$response->isSuccessful()) {
            throw new \Exception('Retour autre que 200');
        }

        return Caster::arrayToStdObject($response->json());
    }

    private function prepareUrl($url = null)
    {
        $preparedUrl = $this->ssl ? 'https://' : 'http://';
        $preparedUrl .= $this->host;
        $preparedUrl .= ':'.$this->port;
        $preparedUrl .= $url;

        return $preparedUrl;
    }

    private function prepareCurlOptions($method, $url, $datas = null)
    {
        $curlOptions = array(
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
        );

        if (null !== $datas) {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $datas;
        }

        return $curlOptions;
    }

    private function prepareResponse($response)
    {
        $response = json_decode($response);

        return $response;
    }
}
