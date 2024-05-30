<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $api_1_endpoint.'?apikey='.$api_1_key,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "method": "categorylist"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'signature: 3a46c99f9f3926155157c64db7027128f01c9d61d5f4d5fb1dce56059a3f31c2'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response = json_decode($response,true);
$product_list = array();
if($response['status']=='OK'){
    $product_list  = $response['results'];
}