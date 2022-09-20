<?php
namespace Webazon\ApiTaxiYandex\Api;

class DriverProfile extends AbstractApi {

    public function all($fields = [], $query = [], $page = 1, $limit = 1000)
    {
        $params = [
            'offset' => ($page - 1) * $limit,
            'limit' => $limit,
            'query' => $this->prepareQuery($query)
        ];

        if ($fields) {
            $params['fields'] = $fields;
        }
	
	$res = 	$this->httpSend('POST','/parks/driver-profiles/list',$params);
		
	return $res;	
		
	}
}