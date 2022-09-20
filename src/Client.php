<?php
namespace Webazon\ApiTaxiYandex;

use http\Exception\InvalidArgumentException;

class Client {
	const API_URL = "https://fleet-api.taxi.yandex.net";
	private $clientId;
	private $apiKey;
	private $parkId;
	private $httpClient;
	private $proxy;

    
    public function __construct($parkId, $apiKey)
    {
        $this->parkId = $parkId;
        $this->apiKey = $apiKey;
    }

   
    
    
    public function api($name)
    {
        switch($name) {
            case 'cars':
                $api = new Api\Cars($this);
                break;

            case 'drivers':
                $api = new Api\DriverProfile($this);
                break;

            case  'rules':
                $api = new Api\DriverWorkRule($this);
                break;

            case 'orders':
                $api = new Api\Order($this);
                break;

            case 'transactions':
                $api = new Api\Transaction($this);
                break;

            default:
                throw new \Exception(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    /**
     * Create new object
     *
     * @return \Webazon\ApiTaxiYandex\Car
     */
    public function cars()
    {
        return new Api\Cars($this);
    }

    /**
     * Create new object
     *
     * @return \Webazon\ApiTaxiYandex\DriverProfile
     */
    public function drivers()
    {
        return new Api\DriverProfile($this);
    }

    /**
     * Create new object
     *
     * @return \Webazon\ApiTaxiYandex\DriverWorkRule
     */
    public function rules()
    {
        return new Api\DriverWorkRule($this);
    }

    /**
     * Create new object
     *
     * @return \Webazon\ApiTaxiYandex\Order
     */
    public function orders()
    {
        return new Api\Order($this);
    }

    /**
     * Create new object
     *
     * @return \Webazon\ApiTaxiYandex\Transaction
     */
    public function transactions()
    {
        return new Api\Transaction($this);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return 'taxi/park/'.$this->parkId;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getParkId()
    {
        return $this->parkId;
    }
	public function getApiUrl()
    {
        return $this->API_URL;
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param $proxy string
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }
	
}