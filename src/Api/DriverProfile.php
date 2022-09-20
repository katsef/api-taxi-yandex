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
	
	if ($res['headers']['http']['result_code']==200)
		{
		if (isset($res['body']['driver_profiles']))
			{
			$drivers=$res['body']['driver_profiles'];
			$balances = 0;
			for ($i=0;$i<count($drivers);$i++)
				{
				$a=$drivers[$i]['accounts'];
				for ($j=0;$j<count($a);$j++){$balances=$balances + $a[$j]['balance'];}
				
				}
			$res['body']['balances']=$balances;
			}
		
		}
		
	return $res;	
		
	}


	public function search($phones = [], $license = '', $full_name = '')
	{
		
		
		
	}

}