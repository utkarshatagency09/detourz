<?php
include '../config.php';//opencart config for using constants(DIR_IMAGE)
include 'config.php';//custom config variables
include 'functions.php';
include 'get_products_rcm.php';
/* echo '<pre>';
print_r($product_list);
echo '</pre>';exit; */
//Fetch Last product id
$sql = 'SELECT product_id FROM `oc_product` ORDER BY `oc_product`.`product_id` DESC LIMIT 0,1';
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->bind_result($product_id);
while ($stmt->fetch()) {
    $last_product_id = $product_id;
}
$products = array();
$i=0;
foreach($product_list as $product){
    /* Fetch Image */
    $imageUrl = $product['imageurl'];//'//rentalcarmanagerdev.blob.core.windows.net/public/sandboxrcmdb406/economy_large.jpg';
    if(stripos($imageUrl,'https:')===false){
        $imageUrl = 'https:'.$imageUrl;
    }
    $image_name = basename($imageUrl);
    $localPath = DIR_IMAGE.'catalog/api/'.$image_name;
    $image_db_path = 'catalog/api/'.$image_name;
    $img_save = saveImage($imageUrl, $localPath);
    if($img_save!=1){//default image
        $image_db_path = 'catalog/api/car.png';
    }
    /* Fetch Image */

    /* Fetch Category */
    switch ($product['vehiclecategorytypeid']) {
        case 1://Car
            $category_id = 59;
            break;

        case 2://Sports Car
            $category_id = 60;
            break;
                
        case 3://4WD
            $category_id = 61;
            break;
            
        case 4://Luxury
            $category_id = 62;
            break;

        case 5://Ute
            $category_id = 63;
            break;
                
        case 6://Van
            $category_id = 64;
            break;            
        
        default:
            $category_id = 59;
    }
    /* Fetch Category */

    /* Fetch Description */
    $description = "";
    if(!empty($product['description'])){
        $description.="<div class='description'>".$product['description']."</div>";
    }
    if(!empty($product['vehicledescription1'])){
        $description.="<div class='vehicledescription1'>".$product['vehicledescription1']."</div>";
    }
    if(!empty($product['vehicledescription2'])){
        $description.="<div class='vehicledescription2'>".$product['vehicledescription2']."</div>";
    }
    if(!empty($product['vehicledescription3'])){
        $description.="<div class='vehicledescription3'>".$product['vehicledescription3']."</div>";
    }
    if(!empty($product['vehicledescriptionurl'])){
        $description.="<div class='vehicledescriptionurl'>For more info <a href='".$product['vehicledescriptionurl']."' target='_blank'>Click here</a></div>";
    }
    /* Fetch Description */

    $products[$i]['product_id'] = $last_product_id;
    $products[$i]['model'] = $last_product_id."-".$product['id'];
    $products[$i]['name'] = addslashes($product['vehiclecategoryname']);
    $products[$i]['image'] = $image_db_path;
    $products[$i]['category_id'] = $category_id;
    $products[$i]['language_id'] = 1;
    $products[$i]['description'] = addslashes($description);
    $products[$i]['description_fr'] = addslashes($description);
    $products[$i]['price'] = 0.00;
    $products[$i]['quantity'] = 1;
    $products[$i]['status'] = 1;
    $products[$i]['api_name'] = 'rental_car_manager';
    $products[$i]['rcm_product_id'] = $product['id'];

    $last_product_id++;
    $i++;
}
            
$postData = [
    'username' => $opencart_username,
    'key'      => $key,
    'products'      => $products,
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, HTTPS_SERVER."index.php?route=api/exchange/add_products");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
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
