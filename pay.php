<?php

/**
 * Todo: doku payment
 */
function pay(){
     try{
          // initiate var
          date_default_timezone_set('UTC');
          $timestamp = date('Y-m-d\TH:i:s\Z', strtotime(date('Y-m-d H:i:s')));
          $clientId = "MCH-0208-9787424638448";
          $requestId = "cc682442-6c22-493e-8121-b9ef6b3fa728";
          $requestDate = $timestamp;
          $targetPath = "/doku-virtual-account/v2/payment-code"; 
          $secretKey = "SK-PcOaIeZtgsagVKIPoWk1";

          // payload data
          $payload = [     
               "order"=>[
                    "amount"=>100000,
                    "invoice_number"=> "asdsad-qweqre-sadsad-asdsd",
                    "currency"=>"IDR",
               ],
               "customer"=>[
                    "name"=>"john doe",
                    "email"=>"john@gmail.com",
                    "address"=>"Jakarta",
                    "country"=> "ID"
               ],
               "payment"=>[
                    "payment_due_date"=>60,
                    "payment_method_types"=>[
                         "VIRTUAL_ACCOUNT_BCA",
                         "VIRTUAL_ACCOUNT_BANK_MANDIRI",
                         "VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI",
                         "VIRTUAL_ACCOUNT_DOKU"
                    ]
               ],
               "additional_info"=>[
                    "settlement"=>[
                         [
                              "back_account_settlement_id"=>"1",
                              "value"=>15,
                              "type"=>"percentage"
                         ],
                         [
                              "back_account_settlement_id"=>"2",
                              "value"=>85,
                              "type"=>"percentage"
                         ],
                    ]
               ]
          ];

          // generating digest & signature
          $digestValue = base64_encode(hash('sha256', json_encode($payload), true));
          $componentSignature = "Client-Id:" . $clientId . "\n" . "Request-Id:" . $requestId . "\n" . "Request-Timestamp:" . $requestDate . "\n" . "Request-Target:" . $targetPath . "\n" . "Digest:" . $digestValue;
          $signature = base64_encode(hash_hmac('sha256', $componentSignature, $secretKey, true));
          $headerSignature =  "Client-Id:" . $clientId ."\n"."Request-Id:". $requestId . "\n". "Request-Timestamp:" . $requestDate . "\n" . "Signature:" . "HMACSHA256=" . $signature;
          
          // http request
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api-sandbox.doku.com/checkout/v1/payment',
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
          'Client-Id: '.$clientId,
          'Request-Id: '.$requestId,
          'Request-Timestamp: 2021-11-09T07:38:54Z',
          'Signature: '."HMACSHA256=".$signature
          ),
          ));

          $response = curl_exec($curl);
          curl_close($curl);
          $hasil = json_decode($response, true);

          echo($hasil);

     }catch(\Exception $e){
          // some stuff here
     }
}