<?php
include '../config.php';//opencart config for using constants(DIR_IMAGE)
include 'config.php';//custom config variables
include 'functions.php';
include 'get_products_jucy.php';
echo '<pre>';
print_r($product_list);
echo '</pre>';exit;
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
    $last_product_id++;

    /* Fetch Image */
    $imageUrl = $product['image'];
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
    switch ($product['fleetType']) {
        case 'Car'://Car
            $category_id = 59;
            break;

        case 'Campervan'://Campervan
            $category_id = 66;
            break;
                
        case 'Motorhome'://Motorhome
            $category_id = 65;
            break;
            
        default:
            $category_id = 59;
    }
    /* Fetch Category */

    /* Fetch Description */
    $description = "";
    if(!empty($product['vehicleDescription'])){
        $description.="<div class='description'>".$product['vehicleDescription']."</div>";
    }
    /* Fetch Description */

    $sql_jucy_product = "SELECT product_id FROM `" . DB_PREFIX . "product` WHERE jucy_product_id = ? LIMIT 1";
    $stmt_jucy_product = $conn->prepare($sql_jucy_product);
    $stmt_jucy_product->bind_param("s", $product['id']);
    $stmt_jucy_product->execute();
    $stmt_jucy_product->bind_result($result_product_id);
    
    if ($stmt_jucy_product->fetch()) {
        $products[$i]['product_id'] = $result_product_id;
    } else {
        $products[$i]['product_id'] = $last_product_id;
    }
// echo $product['id']."----------p-----------".$stmt_jucy_product->fetch()."pr".$products[$i]['product_id'];exit;

    $stmt_jucy_product->close();

    $products[$i]['model'] = $last_product_id."-".$product['id'];
    $products[$i]['name'] = addslashes($product['name']);
    $products[$i]['image'] = $image_db_path;
    $products[$i]['category_id'] = $category_id;
    $products[$i]['language_id'] = 1;
    $products[$i]['description'] = addslashes($description);
    $products[$i]['description_fr'] = addslashes($description);
    $products[$i]['price'] = 0.00;
    $products[$i]['quantity'] = 1;
    $products[$i]['status'] = 1;
    $products[$i]['api_name'] = 'jucy';
    $products[$i]['jucy_product_id'] = $product['id'];

    $feature=1;
    foreach ($product['highlightedFeatures'] as $highlighted_feature) {
        if(empty($attributes['feature_'.$feature])){
            break;
        }
        setAttribute($highlighted_feature,$attributes['feature_'.$feature],$products[$i]['product_id']);
        $feature++;
    }
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
