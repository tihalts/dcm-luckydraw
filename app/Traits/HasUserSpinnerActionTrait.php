<?php

namespace App\Traits;

use App\UserAction;

trait HasSpinnerActionTrait
{

    public function SpinnerAction()
    {
        return $this->hasOne("App\UserAction" , "user_id")
                    ->where("type" , "spinner")
                    ->where("status" , true);
    }

    public function getSpinnerAction()
    {
        $action =  $this->SpinnerAction()->first();
        $data = [];

        if(isset($action)){
            $data = (array) $action->data;
            $data['customer_id'] = $action->customer_id;

            if(isset($data['nationality'])){
                $data['country'] = $data["nationality"]["name"];
            }
        }

        return $data;
    }

    public function updateAction($customer_id , $data = [])
    {
        $action =  UserAction::updateOrCreate([
            'user_id' => $this->id,
        ],[            
            'type' => "spinner",
            'data' => $data,
            'customer_id' => $customer_id,
            'status' => true
        ]);
    
       return isset($action->data) ? $action->data : [];
    }

    public function createSpinnerAction($data = [])
    {
        $action =  UserAction::updateOrCreate([
                'user_id' => $this->id,
            ],[            
            'type' => "spinner",
            'data' => $data,
            'customer_id' => null,
            'status' => true
          ]);
        
        return isset($action->data) ? $action->data : [];
    }

    public function updateCustomerId($customer_id)
    {
        $action =  $this->SpinnerAction()->first();

        if(isset($action)){
            $action->update([ 'customer_id' => $customer_id ]);
        }
        
        return $action;
    }

    public function resetSpinnerAction()
    {
        $action =  $this->SpinnerAction()->first();

        if(isset($action)){            
            $action->delete();
        }

        return $action;
    }
}
