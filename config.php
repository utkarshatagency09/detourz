<?php 
//To run opencart api. This info will get from System -> API
$opencart_username = 'Default';
$key = 'aP7zunLSug0DP5LVwQX4mTAANcEBAZG5mTAzQEALAJOocictt3rxxdBMMljv7cHBAGWcgvpExxqZLldnlPqnYsA6n3rP15vSer0Mn0jbZ2byH9vFFCgpjTgerYzFtp95hidmxYRjY5eNWGBDM5Q6gmZVelRf15SGb1r7uDf0RRzdf3ovCUuDmtp9ShqsMA2xsSUYyd61BaPvcGN4RMI7NmOhIxzCJb4hKyTubpLdGeaYOWfs5UWattBPs22UBnVN';


$api_1_endpoint = 'https://apis.rentalcarmanager.com/booking/v3.2';
$api_1_key = 'MkFFZDhYcmhKTDhwR202OWM1QkJZWUc4SERWdzV6';
$api_1_signature = '2feff70eee56752cde148fa651ed45893cb6421e9ee3269c57be82385e285f46';

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