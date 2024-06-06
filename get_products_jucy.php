<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $api_endpoint_jucy.$endpoint_products,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response = json_decode($response,true);
$product_list = array();
if(!empty($response['products'])){
    $product_list  = $response['products'];
}