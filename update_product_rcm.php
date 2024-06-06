<?php

/* $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://apis.rentalcarmanager.com/booking/v3.2?apikey=MkFFZDhYcmhKTDhwR202OWM1QkJZWUc4SERWdzV6',
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
    "method": "step2",
    "vehiclecategorytypeid": "0",
    "pickuplocationid": 1,
    "pickupdate": "10/06/2024",
    "pickuptime": "10:00",
    "dropofflocationid": 1,
    "dropoffdate": "17/06/2024",
    "dropofftime": "10:00",
    "ageid": 9
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'signature: 2f38cd39bdfdfbec9f39e8950e02e1592145666e9fbbc6c7871564a7cde17406'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
var_dump($response);
exit; */

require_once 'config.php';
require_once 'functions.php';
$curl = curl_init();
// echo $api_1_signature;exit;
curl_setopt_array($curl, array(
  CURLOPT_URL => $api_1_endpoint."?apikey=".$api_1_key,
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
    "method": "step2",
    "vehiclecategorytypeid": "0",
    "pickuplocationid": 1,
    "pickupdate": "10/06/2024",
    "pickuptime": "10:00",
    "dropofflocationid": 1,
    "dropoffdate": "17/06/2024",
    "dropofftime": "10:00",
    "ageid": 9
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'signature: '.$api_2_signature
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response = json_decode($response,true);
/* echo '<pre>';
print_r($response);
echo '</pre>';exit; */
if($response['status']=='OK'){
  $api_name = 'rental_car_manager';
  
  $date = date('Y-m-d H:i:s');
  $date_added = $date;
  $date_modified = $date;

  if(!empty($response['results']['availablecars'])){
    $availablecars = $response['results']['availablecars'];
    foreach ($availablecars as $car) {
        $vehiclecategoryid = $car['vehiclecategoryid'];
        $minimumage = $car['minimumage'];
        $maximumage = $car['maximumage'];
        $totalrateafterdiscount = $car['totalrateafterdiscount'];
        
        $sql = 'SELECT product_id FROM `oc_product` where rcm_product_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $vehiclecategoryid);
        $stmt->execute();
        $stmt->bind_result($product_id);
        while ($stmt->fetch()) {
          $product_id = $product_id;
        }

        $stmt = $conn->prepare("UPDATE `oc_product` SET price = ?, date_modified = ? where product_id = ?");
        // Bind parameters (s = string, i = integer, d = double, b = blob)
        $stmt->bind_param("ssi", $totalrateafterdiscount, $date_modified, $product_id);
        $stmt->execute();
        $stmt->close();

        /* Insert additional attributes in specifications tab */
        setAttribute($minimumage,$attributes['minimumage'],$product_id);
        setAttribute($maximumage,$attributes['maximumage'],$product_id);
        /* Insert additional attributes in specifications tab */
    }
  }
}
