<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successResponse($response){
        $this->response["status"] = true;
        foreach($response as $key => $value){
            $this->response[$key] = $value;
        }
        return response()->json($this->response , 200);
    }

    public function errorResponse($response){
        $this->response["status"] = false;
        foreach($response as $key => $value){
            $this->response[$key] = $value;
        }
        return response()->json($this->response , 500);
    }

    public function authFailedResponse($response){
        $this->response["status"] = false;
        foreach($response as $key => $value){
            $this->response[$key] = $value;
        }
        return response()->json($this->response , 401);
    }

    public function invalidResponse($response){
        $this->response["status"] = false;
        foreach($response as $key => $value){
            $this->response[$key] = $value;
        }
        return response()->json($this->response , 403);
    }


}
