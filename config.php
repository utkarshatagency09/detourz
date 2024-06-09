<<<<<<< HEAD
<?php 
//To run opencart api. This info will get from System -> API
$opencart_username = 'Default';
$key = 'aP7zunLSug0DP5LVwQX4mTAANcEBAZG5mTAzQEALAJOocictt3rxxdBMMljv7cHBAGWcgvpExxqZLldnlPqnYsA6n3rP15vSer0Mn0jbZ2byH9vFFCgpjTgerYzFtp95hidmxYRjY5eNWGBDM5Q6gmZVelRf15SGb1r7uDf0RRzdf3ovCUuDmtp9ShqsMA2xsSUYyd61BaPvcGN4RMI7NmOhIxzCJb4hKyTubpLdGeaYOWfs5UWattBPs22UBnVN';

$languages = array(1,3);//English, Russia

//oc_attribute_description 
$attributes = array(
					'minimumage'=>15,
					'maximumage'=>16,
				);

$api_1_endpoint = 'https://apis.rentalcarmanager.com/booking/v3.2';
$api_1_key = 'MkFFZDhYcmhKTDhwR202OWM1QkJZWUc4SERWdzV6';
$api_1_signature = '2feff70eee56752cde148fa651ed45893cb6421e9ee3269c57be82385e285f46';//step1
$api_2_signature = '2f38cd39bdfdfbec9f39e8950e02e1592145666e9fbbc6c7871564a7cde17406';//step2

/* JUCY */
$api_endpoint_jucy = 'https://lanier.test.jucy.cloud';//staging
// $api_endpoint_jucy = 'https://rentals.api.jucy.cloud';//production

$endpoint_products = '/api/rental-catalog';
/* JUCY */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "opencart3";
// Create connection
date_default_timezone_set('Asia/Calcutta');

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	// die("Connection failed: " . $conn->connect_error);
	die("Connection failed: ");
}
?>
=======
<?php
// HTTP
define('HTTP_SERVER', 'http://localhost/opencart3/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost/opencart3/');

// DIR
define('DIR_APPLICATION', 'D:/wamp/www/opencart3/catalog/');
define('DIR_SYSTEM', 'D:/wamp/www/opencart3/system/');
define('DIR_IMAGE', 'D:/wamp/www/opencart3/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'opencart3');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
>>>>>>> master
