<?php
// API URL for inserting product
/* 
http://localhost/opencart3/admin/index.php?route=catalog/product.save&user_token=29543060bc2f0e0ecc1bf285b90556a1
product_description[1][name]: test
product_description[1][meta_title]: test
master_id: 0
model: test model
tax_class_id: 0
quantity: 1
minimum: 1
subtract: 0
subtract: 1
stock_status_id: 6
date_available: 2024-03-28
shipping: 0
shipping: 1
length_class_id: 1
weight_class_id: 1
status: 0
status: 1
sort_order: 1
manufacturer: 
manufacturer_id: 0
product_store[]: 0
product_seo_url[0][1]: test-product
product_id: 0
*/
// $insert_product_url = 'http://localhost/opencart3/admin/index.php?route=catalog/product.save&user_token=29543060bc2f0e0ecc1bf285b90556a1';
// $insert_product_url = 'http://localhost/opencart3/index.php?route=api/product/add';
// $insert_product_url = 'http://localhost/opencart3/admin/index.php?route=catalog/product.customProductSave';
$insert_product_url = 'http://localhost/opencart3/admin/index.php?route=catalog/product.save&user_token=';

// Product data (replace with your own product details)
/* $productData = array(
    "product_description" => array(
        "1"=>array(
                'name' => 'Sample Product',
                'meta_title' => 'Sample Product',
                'model' => 'PROD123',
                'price' => '19.99',
                'quantity' => '100',
                'status' => '1', // 1 for enabled, 0 for disabled
                'description' => 'This is a sample product description.',
                // Add more fields as needed
            )
    )
);
echo '<pre>';
print_r($productData);
echo '</pre>';exit; */
// OpenCart API URL
$api_url = 'http://localhost/opencart3/index.php?route=api/account/login';

// API credentials
$username = 'Default';
$key = 'bfee6f664688842c277967e1eb0ef2e3184b11375af53a9e7d803b63a0e88e47302676ccda1cc8d8feca344518f72bc9dd5bf7b2830a5d370bce5183afb957c09bf079d857bc77440dd291f0cdf5c618d4ccf0d6195fe0635de27d1094ed042866273d3a023b4d469d1dd38c13024d6b7a645d6cce738879a9ba8938b5561341';

// API request data
$data = array(
    'username' => $username,
    'key' => $key
);

// Initialize cURL
$curl = curl_init($api_url);

// Set cURL options
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$response = curl_exec($curl);

// Check for errors
if ($response === false) {
    die(curl_error($curl));
}
// Decode JSON response
$json = json_decode($response, true);

// Extract API session token
$api_token = isset($json['api_token']) ? $json['api_token'] : '';

// Close cURL
curl_close($curl);

// Check if API token is obtained
if ($api_token) {
    // Initialize cURL session
    $ch = curl_init($insert_product_url.$api_token);

    $productData = array(
        "product_description" => array(
            "1"=>array(
                    'name' => 'Sample Product',
                    'meta_title' => 'Sample Product',
                    'model' => 'PROD123',
                    'price' => '19.99',
                    'quantity' => '100',
                    'status' => '1', // 1 for enabled, 0 for disabled
                    'description' => 'This is a sample product description.',
                    // Add more fields as needed
                )
        )
    );

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($productData));

    // Execute cURL session
    $response = curl_exec($ch);

    // Check for errors
    if($response === false) {
        die('Error: ' . curl_error($ch));
    }
    echo '<pre>....';
    print_r($response);
    echo '</pre>';exit;
    // Decode JSON response
    $json = json_decode($response, true);

    // Check if JSON decoding was successful
    if ($json === null || json_last_error() !== JSON_ERROR_NONE) {
        die('Error decoding JSON: ' . json_last_error_msg());
    }

    // Check if product insertion was successful
    if (isset($json['success'])) {
        echo 'Product inserted successfully.';
    } else {
        // Product insertion failed, handle error
        echo 'Product insertion failed: ' . $json['error'];
    }
}
else {
    echo 'Failed to obtain API token.';
}
?>
