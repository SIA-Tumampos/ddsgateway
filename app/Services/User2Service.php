<?php
namespace App\Services;
use App\Traits\ConsumesExternalService;

class User2Service{
    use ConsumesExternalService;
    /**
    * The base uri to consume the User2 Service
    * @var string
    */
    public $baseUri;

    /**
     * The secret to consume the User2 Service
     * @var string
     */
    public $secret;
    
    public function __construct() { 
        $this->baseUri = config('services.users2.base_uri');
        $this->secret = config('services.users2.secret');
        }
    
    public function obtainUsers2()
    {
        return $this->performRequest('GET', '/users');
    }

    public function createUsers2($data)
    {
        return $this->performRequest('POST', '/users', $data);
    }   

    public function obtainUser2($id)
    {
        return $this->performRequest('GET', "/users/{$id}");
    }

    public function editUser2($data, $id)
    {
        return $this->performRequest('PUT', "/users/{$id}", $data);
    }

    public function deleteUser2($id)
    {
        return $this->performRequest('DELETE', "/users/{$id}");
    }
}