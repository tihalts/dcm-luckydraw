<?php

if(!function_exists("send_sms")){
    
    function send_sms($mobileno, $message, $sender = null, $dsc = 0){
       $mobileno = trim($mobileno,'+');       
       $message = urlencode($message);
       $url = "https://bhsms.net/httpget/?username=dragoncity&apikey=8GK6TT5D6OOJOCUNNWT1";
       $url .= "&to=" . $mobileno;
       $url .= "&text=".$message;
       if(isset($sender)){
        $url .= "&sender=".$sender;
       }
       $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if(!$response){
            return  $json = json_decode($response, true);
        }else{
            return  $json = json_decode($response, true);
        }
        
    }

}
if(!function_exists("sendsms")){
    function sendsms($numbers, $text, $sender= null , $scheduled= null)
    {
        // Account details
        $username="dragoncity";
        $apikey ="P0F1FI77I1Q6Z4TTHMM7";
        $numbers = trim($numbers,'+'); 
        $numbers = urlencode($numbers);
        $sender = urlencode($sender);
        $text = rawurlencode($text);
        $str = 'username=' . $username . '&apikey=' . $apikey . '&to=' . $numbers . "&text=" . $text;
        if(isset($sender)){
          $str .= "&sender=".$sender;
        }
        if(isset($scheduled)){
            $str .= "&scheduled=".$scheduled;
          }
        $ch = curl_init('https://bhsms.net/httpget/?' . $str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}