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


	public function search($query =[])
	{
	$res=[];
	$f_query=true;
	
	if (isset($query['phones']) || isset($query['name']) || isset($query['license']))	
		{
		if ($query['phones'])
			{
			$phones=[];
			for ($i=0;$i<count($query['phones']);$i++)
				{
				if ($this->validate_russian_phone_number($query['phones'][$i])){array_push($phones,$this->normalise_phone($query['phones'][$i]));}
				}
			
			
			}else{$phones=[];}
		if ($query['name'])
			{
			$name=trim(mb_strtoupper($query['name']));
			}else{$name=false;}
		
		if ($query['license'])
			{
			$license = trim($this->normalize_license(mb_strtoupper($query['license'])));
			}else{$license=false;}
			
		}else{$f_query=false;}
	
	
		
	if ($f_query)
	{
	$params = [
            'offset' => 0,
            'limit' => 1000,
            'query' => $this->prepareQuery($query)
        ];

    $res = $this->httpSend('POST','/parks/driver-profiles/list',$params);
	
	if ($res['headers']['http']['result_code']==200)
		{
		if (isset($res['body']['driver_profiles']))
			{
			$a=[];	
			$drivers=$res['body']['driver_profiles'];	
			for ($i=0;$i<count($drivers);$i++)
				{
				if ($phones)
					{
					for ($j=0;$j<count($phones);$j++)
						{
						
						if (md5($this->normalise_phone($drivers[$i]['driver_profile']['phones'][0])) == md5($phones[$j]))
							{
							$a=$this->driver_push($a,$drivers[$i]);
							}
						}
					}
				
				if ($name)
					{
					$driver_name=mb_strtoupper($drivers[$i]['driver_profile']['last_name'].' '.$drivers[$i]['driver_profile']['first_name'].' '.$drivers[$i]['driver_profile']['middle_name']);
					if (md5($driver_name) == md5($name))
						{
						$a=$this->driver_push($a,$drivers[$i]);
						}
						
					}
				
				if ($license)
					{
					$driver_license=mb_strtoupper($drivers[$i]['driver_profile']['driver_license']['normalized_number']);
					if (md5($driver_license) == md5($license))
						{
						$a=$this->driver_push($a,$drivers[$i]);
						}
					
					}
				
				
				}
			
			
			if (count($a) > 0)
				{
				$aa=[];
				for ($i=0;$i<count($a);$i++)
					{
					
					if ($phones){$d_phones=$phones;}else{$d_phones=$a[$i]['driver_profile']['phones'];}
					if ($name){$d_name=trim(mb_strtoupper($name));}else{$d_name=trim(mb_strtoupper($a[$i]['driver_profile']['last_name'].' '.$a[$i]['driver_profile']['first_name'].' '.$a[$i]['driver_profile']['middle_name']));}
					if ($license){$d_license=trim(mb_strtoupper($license));}else{$d_license=trim(mb_strtoupper($a[$i]['driver_profile']['driver_license']['normalized_number']));}
					
					
					
					$f=false;
					for ($j=0;$j<count($d_phones);$j++)
						{
						for ($k=0;$k<count($a[$i]['driver_profile']['phones']);$k++)
							{
							
							if ($this->validate_russian_phone_number($d_phones[$j]) && $this->validate_russian_phone_number($a[$i]['driver_profile']['phones'][$k]))
								{
								if (md5($this->validate_russian_phone_number($d_phones[$j]))==md5($this->validate_russian_phone_number($a[$i]['driver_profile']['phones'][$k]))){$f=true;break;}
								}
							}
						}
					
					
					
					if ($f)
						{
						if ($d_name == mb_strtoupper($a[$i]['driver_profile']['last_name'].' '.$a[$i]['driver_profile']['first_name'].' '.$a[$i]['driver_profile']['middle_name']))
						{}else{$f=false;}
						}
					
					if ($f)
						{
						if ($d_license == $a[$i]['driver_profile']['driver_license']['normalized_number']){}else{$f=false;}
						}
					
					if ($f)
						{
						if ($a[$i]['driver_profile']['work_status']=='working'){}else{$f=false;}
						}
					if ($f){array_push($aa,$a[$i]);}
					
					
					
					}
				$a=$aa;
				}
			
			if (count($a) > 0)
				{
				$res['body']=[];	
				$res['body']['driver_profiles']=$a;
				$res['headers']['content-length']=mb_strlen(json_encode($res['body']));	
				}else{unset($res['body']);$res['error']=$this->toError(404,'Driver not found');$res['headers']['content-length']=strlen(json_encode($res['error']));}
			
			
			
			}
		}
	
	
	}else{$res=$this->toError(400,'Invalid request parameters');}
		
		
	return $res;	
	}

}