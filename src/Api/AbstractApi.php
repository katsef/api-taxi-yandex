<?php
namespace Webazon\ApiTaxiYandex\Api;

abstract class AbstractApi {

    /**
     * Yandex API version
     *
     * @var string
     */
    protected $apiVersion = 'v1';
	protected $apiUrl = "https://fleet-api.taxi.yandex.net";
    /**
     * The client
     *
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

	
	protected function httpSend($method = '',$line,$query = [])
	{
	$api_key = $this->client->getApiKey();
	$client_id = $this->client->getClientId();	
	$ho = array("X-Api-Key: $api_key",
				"X-Client-ID: $client_id");
$url = 'https://fleet-api.taxi.yandex.net/v1/'.trim($line,'/');

$ch = curl_init('https://fleet-api.taxi.yandex.net/v1/parks/driver-profiles/list');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $ho);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query)); 
 
// Или предать массив строкой: 
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&'));
 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, true);
$result = curl_exec($ch);
curl_close($ch);	

$out = preg_split('/(\r?\n){2}/', $result, 2);
$headers = $out[0];
$headersArray = preg_split('/\r?\n/', $headers);
$headersArray = array_map(function($h) {
return preg_split('/:\s{1,}/', $h, 2);
}, $headersArray);

$tmp = [];
foreach($headersArray as $h) {

$pos = strpos($h[0], 'HTTP');
if ($pos === false)
	{
	$tmp[strtolower($h[0])] = isset($h[1]) ? $h[1] : $h[0];
	}else{
	$a=explode(' ',isset($h[1]) ? $h[1] : $h[0],3);
	$tmp['http']['protocol'] = $a[0];
	$tmp['http']['result_code'] = intval($a[1]);
	$tmp['http']['message'] = $a[2];
}
}

$headersArray = $tmp; $tmp = null;
if (isset($headersArray['content-length'])){$headersArray['content-length']=intval($headersArray['content-length']);}
$h=$out[0];
$b=$out[1];

$r['headers']=$headersArray;
$r['body']=json_decode($b,true);
		
return $r;
		
	}
	
	
	
    
    protected function get($uri, array $params = [], array $headers = [])
    {
        $http = $this->client->getHttpClient($this->apiVersion);

        if (count($params) > 0) {
            $uri .= '?' . http_build_query($params);
        }

        if ($headers) {
            $options['headers'] = $headers;
        }

        try {
            $response = $http->request('GET', $uri, $options);
        }
        catch(GuzzleException $e) {
            return $e;
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Send a POST request with JSON-encoded parameters.
     *
     * @param $uri
     * @param array $params
     * @param array $headers
     *
     * @return GuzzleException|ResponseInterface
     */
    protected function post($uri, array $params, array $headers = [])
    {
        $http = $this->client->getHttpClient($this->apiVersion);

        $options = ['json' => $params];

        if ($headers) {
            $options['headers'] = $headers;
        }

        try {
            $response = $http->request('POST', $uri, $options);
        }
        catch(GuzzleException $e) {
            return $e;
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Send a PUT request with JSON-encoded parameters.
     *
     * @param $uri
     * @param array $params
     * @param array $headers
     *
     * @return GuzzleException|ResponseInterface
     */
    protected function put($uri, array $params, array $headers = [])
    {
        $http = $this->client->getHttpClient($this->apiVersion);

        if (count($params) > 0) {
            $uri .= '?' . http_build_query($params);
        }

        if ($headers) {
            $options['headers'] = $headers;
        }

        try {
            $response = $http->request('PUT', $uri, $options);
        }
        catch(GuzzleException $e) {
            return $e;
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Send a DELETE request with JSON-encoded parameters.
     *
     * @param $uri
     * @param array $params
     * @param array $headers
     *
     * @return GuzzleException|ResponseInterface
     */
    protected function delete($uri, array $params, array $headers = [])
    {
        $http = $this->client->getHttpClient($this->apiVersion);

        if (count($params) > 0) {
            $uri .= '?' . http_build_query($params);
        }

        if ($headers) {
            $options['headers'] = $headers;
        }

        try {
            $response = $http->request('DELETE', $uri, $options);
        }
        catch(GuzzleException $e) {
            return $e;
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    protected function prepareQuery($query)
    {
        if (!isset($query['park']['id'])) {
            $query['park']['id'] = $this->client->getParkId();
        }

        return $query;
    }

}