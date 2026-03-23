<?php  

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;
use App\Services\User1Service;

Class User1Controller extends Controller
{
    use ApiResponser;
    /**
    * The service to consume the User1 Microservice
    * @var User1Service
    */
    
    public $user1Service;
    /**
    * Create a new controller instance
    * @return void
    */
    public function __construct(User1Service $user1Service) {
        $this->user1Service = $user1Service;
    }

    public function GetUsers()
    {
        return $this->successResponse($this->user1Service->obtainUsers1());
    }

    public function index()
    {
        return $this->successResponse($this->user1Service->obtainUsers1());
    }

    public function add(Request $request)
    {
        return $this->successResponse($this->user1Service->createUsers1($request->all(), Response::HTTP_CREATED));
    }

    public function show($id)
    {
      return $this->successResponse($this->user1Service->obtainUser1($id));
    }

    public function update($id, Request $request)
    {
      return $this->successResponse($this->user1Service->editUser1($request->all(), $id));
    }

    public function delete($id)
    {
      return $this->successResponse($this->user1Service->deleteUser1($id));
    }

}
