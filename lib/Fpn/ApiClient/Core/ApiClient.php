<?php

namespace Fpn\ApiClient\Core;

class ApiClient
{
    private $host;
    private $port;
    private $ssl;

    public function __construct($host = 'localhost', $port = 80, $ssl = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->ssl  = $ssl;
    }

    public function useSsl($ssl)
    {
        $this->ssl = $ssl;
    }

    public function request($method, $url, $datas = null)
    {
        $url = $this->prepareUrl($url);

        $ch = curl_init();
        $curl_options = $this->prepareCurlOptions($method, $url, $datas);
        curl_setopt_array($ch, $curl_options);

        $response = curl_exec($ch);

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $responseCode) {
            throw new \Exception('Retour autre que 200');
        }

        if (!$response) {
            throw new \Exception('Curl a retourné false, erreur curl');
        }

        $response = $this->prepareResponse($response);

        return $response;
    }

    private function prepareUrl($url)
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
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($datas);
        }

        return $curlOptions;
    }

    private function prepareResponse($response)
    {
        $response = json_decode($response);

        return $response;
    }
}
