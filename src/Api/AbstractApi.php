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
if ($r['headers']['http']['result_code']==200){}else{unset($r['body']);$r['error']=json_decode($b,true);}
		
return $r;
		
	}
	
	protected function validate_russian_phone_number($tel = '')
	{
	$tel = trim((string)$tel);
    if (!$tel) return false;
    $tel = preg_replace('#[^0-9+]+#uis', '', $tel);
    if (!preg_match('#^(?:\\+?7|8|)(.*?)$#uis', $tel, $m)) return false;
    $tel = '+7' . preg_replace('#[^0-9]+#uis', '', $m[1]);
    if (!preg_match('#^\\+7[0-9]{10}$#uis', $tel, $m)) return false;
    return $tel;	
		
	}
	
	protected function normalise_phone($phone) {
		$resPhone = preg_replace("/[^0-9]/", "", $phone);
		if (strlen($resPhone) === 11) {
			$resPhone = preg_replace("/^8/", "7", $resPhone);
		}
		return $resPhone;
	}	
	
	
	protected function driver_push($a,$driver)
	{
	$f=false;
	for ($i=0;$i<count($a);$i++){
		
		if($a[$i]['driver_profile']['id']==$driver['driver_profile']['id']){$f=true;}}
		
	
	if ($f===false){array_push($a,$driver);}	
	return $a;	
	}
	
	protected function utf_encode($string) {
  		
  $arrayUtfa = array('%u0410', '%u0430', '%u0411', '%u0431', '%u0412', '%u0432', '%u0413', '%u0433', '%u0414', '%u0434', '%u0415', '%u0435', '%u0401', '%u0451', '%u0416', '%u0436', '%u0417', '%u0437', '%u0418', '%u0438', '%u0419', '%u0439', '%u041a', '%u043a', '%u041b', '%u043b', '%u041c', '%u043c', '%u041d', '%u043d', '%u041e', '%u043e', '%u041f', '%u043f', '%u0420', '%u0440', '%u0421', '%u0441', '%u0422', '%u0442', '%u0423', '%u0443', '%u0424', '%u0444', '%u0425', '%u0445', '%u0426', '%u0446', '%u0427', '%u0447', '%u0428', '%u0448', '%u0429', '%u0449', '%u042a', '%u044a', '%u042b', '%u044b', '%u042c', '%u044c', '%u042d', '%u044d', '%u042e', '%u044e', '%u042f', '%u044f');

  $arrayCyra = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш',  'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я');
	
 return str_replace($arrayUtfa,$arrayCyra,$string); 
}
	
	protected function utf8_decode($string)
{
 $arrayUtfa = array('\u0410', '\u0430', '\u0411', '\u0431', '\u0412', '\u0432', '\u0413', '\u0433', '\u0414', '\u0434', '\u0415', '\u0435', '\u0401', '\u0451', '\u0416', '\u0436', '\u0417', '\u0437', '\u0418', '\u0438', '\u0419', '\u0439', '\u041a', '\u043a', '\u041b', '\u043b', '\u041c', '\u043c', '\u041d', '\u043d', '\u041e', '\u043e', '\u041f', '\u043f', '\u0420', '\u0440', '\u0421', '\u0441', '\u0422', '\u0442', '\u0423', '\u0443', '\u0424', '\u0444', '\u0425', '\u0445', '\u0426', '\u0446', '\u0427', '\u0447', '\u0428', '\u0448', '\u0429', '\u0449', '\u042a', '\u044a', '\u042b', '\u044b', '\u042c', '\u044c', '\u042d', '\u044d', '\u042e', '\u044e', '\u042f', '\u044f');

  $arrayCyra = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш',  'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я');
	
 return str_replace($arrayUtfa,$arrayCyra,$string); 
	}

	protected function normalize_license( $s ) {
	
    $s = preg_replace("/[^a-zA-Zа-яА-Я0-9]/ui", '', $s);
	
	if (mb_strlen($s)==10){}else{$s=false;}
    return $s;
}

	
	protected function toError($code = 400,$error='Invalid request parameters')
	{
	$message='';
	switch($code) {
            case 200:
                $message = 'OK';
                break;
			case 400:
                $message = 'BadRequest';
                break;
			case 401:
                $message = 'Unauthorized';
                break;
			case 403:
                $message = 'Forbidden';
                break;
			case 404:
                $message = 'NotFound';
                break;
			case 409:
                $message = 'Conflict';
                break;
			case 429:
                $message = 'TooManyRequests';
                break;
			case 500:
                $message = 'InternalServerError';
                break;

           

            default:
                $message = '';
        }
		
		
	$res = [];
	$res['headers']['http']['protocol'] = null;	
	$res['headers']['http']['result_code'] = $code;
	$res['headers']['http']['message'] = $message;
		
	$res['error']['message']=$error;	
		
	$res['headers']['content-length']=strlen(json_encode($res['error']));
	$res['headers']['content-type']='application/json; charset=utf-8';
	$res['headers']['date']=date(DATE_RFC2822);
	$res['headers']['vary']='Accept-Encoding';
	$res['headers']['x-yarequestid']=null;
	return $res;	
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