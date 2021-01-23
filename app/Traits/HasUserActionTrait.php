<?php

namespace App\Traits;

use App\UserAction;

trait HasPurchaseActionTrait
{

    public function PurchaseAction()
    {
        return $this->hasOne("App\UserAction" , "user_id")
                    ->where("type" , "purchase")
                    ->where("status" , true);
    }

    public function getPurchaseAction()
    {
        $action =  $this->PurchaseAction()->first();
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
            'type' => "purchase",
            'data' => $data,
            'customer_id' => $customer_id,
            'status' => true
        ]);
    
       return isset($action->data) ? $action->data : [];
    }

    public function createPurchaseAction($data = [])
    {
        $action =  UserAction::updateOrCreate([
                'user_id' => $this->id,
            ],[            
            'type' => "purchase",
            'data' => $data,
            'customer_id' => null,
            'status' => true
          ]);
        
        return isset($action->data) ? $action->data : [];
    }

    public function updateCustomerId($customer_id)
    {
        $action =  $this->PurchaseAction()->first();

        if(isset($action)){
            $action->update([ 'customer_id' => $customer_id ]);
        }
        
        return $action;
    }

    public function resetPurchaseAction()
    {
        $action =  $this->PurchaseAction()->first();

        if(isset($action)){            
            $action->delete();
        }

        return $action;
    }
}
