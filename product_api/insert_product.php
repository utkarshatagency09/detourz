<?php
include '../config.php';
include 'functions.php'; 

$imageUrl = '//rentalcarmanagerdev.blob.core.windows.net/public/sandboxrcmdb406/economy_large.jpg';
if(stripos($imageUrl,'https:')===false){
    $imageUrl = 'https:'.$imageUrl;
}
$image_name = basename($imageUrl);
$localPath = DIR_IMAGE.'catalog/api/'.$image_name;
$db_path = 'catalog/api/'.$image_name;
$img_save = saveImage($imageUrl, $localPath);
if($img_save!=1){
    $db_path = 'catalog/api/car.png';
}

$username = 'Default';
$key = 'aP7zunLSug0DP5LVwQX4mTAANcEBAZG5mTAzQEALAJOocictt3rxxdBMMljv7cHBAGWcgvpExxqZLldnlPqnYsA6n3rP15vSer0Mn0jbZ2byH9vFFCgpjTgerYzFtp95hidmxYRjY5eNWGBDM5Q6gmZVelRf15SGb1r7uDf0RRzdf3ovCUuDmtp9ShqsMA2xsSUYyd61BaPvcGN4RMI7NmOhIxzCJb4hKyTubpLdGeaYOWfs5UWattBPs22UBnVN';
$products = array(
                array(
                    'product_id' => 77,
                    'model' => "product model 3",
                    'name' => addslashes("y"),
                    'image' => $db_path,
                    'category_id' => 20,
                    'language_id' => 1,
                    'description' => 'eng description',
                    'description_fr' => 'french description',
                    'price' => '100.00',
                    'quantity' => 1,
                    'status' => 1,
                    'length' => '1.00',
                    'height' => '2.00',
                    'width' => '3.00'
                )/* ,
                array(
                    'product_id' => 63,
                    'model' => "product model 2",
                    'name' => addslashes("product name 2"),
                    'image' => $db_path,
                    'category_id' => 20,
                    'language_id' => 1,
                    'status' => 1,
                    'length' => '1.00',
                    'height' => '2.00',
                    'width' => '3.00s'
                ) */
            );
            
$postData = [
    'username' => $username,
    'key'      => $key,
    'products'      => $products,
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://localhost/opencart3/index.php?route=api/exchange/add_products");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close ($ch);

echo $result;
