<?php
require_once 'config.php';
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
    "method": "step1"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'signature: '.$api_1_signature
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response = json_decode($response,true);
echo '<pre>';
print_r($response);
echo '</pre>';
if($response['status']=='OK'){
  $api_name = 'rental_car_manager';
  
  $date = date('Y-m-d H:i:s');
  $date_added = $date;
  $date_modified = $date;

  if(!empty($response['results']['locations'])){
    $locations = $response['results']['locations'];
    foreach ($locations as $location) {
        $id = $location['id'];
        $locationName = $location['location'];
        $isdefault = $location['isdefault'];
        $ispickupavailable = $location['ispickupavailable'];
        $isdropoffavailable = $location['isdropoffavailable'];
        $isflightinrequired = $location['isflightinrequired'];
        $minimumbookingday = $location['minimumbookingday'];
        $noticerequired_numberofdays = $location['noticerequired_numberofdays'];
        $quoteisvalid_numberofdays = $location['quoteisvalid_numberofdays'];
        $officeopeningtime = $location['officeopeningtime'];
        $officeclosingtime = $location['officeclosingtime'];
        $afterhourbookingaccepted = $location['afterhourbookingaccepted'];
        $afterhourfeeid = $location['afterhourfeeid'];
        $unattendeddropoffaccepted = $location['unattendeddropoffaccepted'];
        $unattendeddropofffeeid = $location['unattendeddropofffeeid'];
        $minimumage = $location['minimumage'];
        $phone = $location['phone'];
        $email = $location['email'];

        $stmt = $conn->prepare("INSERT INTO `oc_api_locations`(`id`, `location`, `isdefault`, `ispickupavailable`, `isdropoffavailable`, `isflightinrequired`, `minimumbookingday`, `noticerequired_numberofdays`, `quoteisvalid_numberofdays`, `officeopeningtime`, `officeclosingtime`, `afterhourbookingaccepted`, `afterhourfeeid`, `unattendeddropoffaccepted`, `unattendeddropofffeeid`, `minimumage`, `phone`, `email`, `api_name`, `date_added`, `date_modified`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `location` = VALUES(`location`), `isdefault` = VALUES(`isdefault`), `ispickupavailable` = VALUES(`ispickupavailable`), `isdropoffavailable` = VALUES(`isdropoffavailable`), `isflightinrequired` = VALUES(`isflightinrequired`), `minimumbookingday` = VALUES(`minimumbookingday`), `noticerequired_numberofdays` = VALUES(`noticerequired_numberofdays`), `quoteisvalid_numberofdays` = VALUES(`quoteisvalid_numberofdays`), `officeopeningtime` = VALUES(`officeopeningtime`), `officeclosingtime` = VALUES(`officeclosingtime`), `afterhourbookingaccepted` = VALUES(`afterhourbookingaccepted`), `afterhourfeeid` = VALUES(`afterhourfeeid`), `unattendeddropoffaccepted` = VALUES(`unattendeddropoffaccepted`), `unattendeddropofffeeid` = VALUES(`unattendeddropofffeeid`), `minimumage` = VALUES(`minimumage`), `phone` = VALUES(`phone`), `email` = VALUES(`email`), `date_modified` = VALUES(`date_modified`)");
        $stmt->bind_param("sssssssssssssssssssss", $id, $locationName, $isdefault, $ispickupavailable, $isdropoffavailable, $isflightinrequired, $minimumbookingday, $noticerequired_numberofdays, $quoteisvalid_numberofdays, $officeopeningtime, $officeclosingtime, $afterhourbookingaccepted, $afterhourfeeid, $unattendeddropoffaccepted, $unattendeddropofffeeid, $minimumage, $phone, $email, $api_name, $date_added, $date_modified);
        $stmt->execute();
        $stmt->close();
    }
  }

  if(!empty($response['results']['officetimes'])){
    $officetimes = $response['results']['officetimes'];
    $deleteStmt = $conn->prepare("DELETE FROM `oc_api_officetimes` WHERE `api_name` = ?");
    $deleteStmt->bind_param("s", $api_name);
    $deleteStmt->execute();
    $deleteStmt->close();
    foreach ($officetimes as $officetime) {
        $locationid = $officetime['locationid'];
        $dayofweek = $officetime['dayofweek'];
        $openingtime = $officetime['openingtime'];
        $closingtime = $officetime['closingtime'];
        $startpickup = $officetime['startpickup'];
        $endpickup = $officetime['endpickup'];
        $startdropoff = $officetime['startdropoff'];
        $enddropoff = $officetime['enddropoff'];
        $startdate = $officetime['startdate'];
        $enddate = $officetime['enddate'];
        
        $stmt = $conn->prepare("INSERT INTO `oc_api_officetimes`(`locationid`, `dayofweek`, `openingtime`, `closingtime`, `startpickup`, `endpickup`, `startdropoff`, `enddropoff`, `startdate`, `enddate`, `api_name`, `date_added`, `date_modified`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssssss", $locationid, $dayofweek, $openingtime, $closingtime, $startpickup, $endpickup, $startdropoff, $enddropoff, $startdate, $enddate, $api_name, $date_added, $date_modified);
        $stmt->execute();
        $stmt->close();
    }
  }

  if(!empty($response['results']['categorytypes'])){
    $categorytypes = $response['results']['categorytypes'];
    foreach ($categorytypes as $categorytype) {
        $id = $categorytype['id'];
        $vehiclecategorytype = $categorytype['vehiclecategorytype'];
        $displayorder = $categorytype['displayorder'];
        
        $stmt = $conn->prepare("INSERT INTO `oc_api_categorytypes`(`id`, `vehiclecategorytype`, `displayorder`, `api_name`, `date_added`, `date_modified`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `vehiclecategorytype` = VALUES(`vehiclecategorytype`), `displayorder` = VALUES(`displayorder`), `date_modified` = VALUES(`date_modified`)");
        $stmt->bind_param("ssssss", $id, $vehiclecategorytype, $displayorder, $api_name, $date_added, $date_modified);
        $stmt->execute();
        $stmt->close();
    }
  }

  if(!empty($response['results']['driverages'])){
    $driverages = $response['results']['driverages'];
    foreach ($driverages as $categorytype) {
        $id = $categorytype['id'];
        $driverage = $categorytype['driverage'];
        $isdefault = $categorytype['isdefault'];
        
        $stmt = $conn->prepare("INSERT INTO `oc_api_driverages`(`id`, `driverage`, `isdefault`, `api_name`, `date_added`, `date_modified`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `driverage` = VALUES(`driverage`), `isdefault` = VALUES(`isdefault`), `date_modified` = VALUES(`date_modified`)");
        $stmt->bind_param("ssssss", $id, $driverage, $isdefault, $api_name, $date_added, $date_modified);
        $stmt->execute();
        $stmt->close();
    }
  }

  if(!empty($response['results']['holidays'])){
    $holidays = $response['results']['holidays'];
    foreach ($holidays as $holiday) {
        $id = $holiday['id'];
        $locationid = $holiday['locationid'];
        $startdate = $holiday['startdate'];
        $enddate = $holiday['enddate'];
        $type = $holiday['type'];
        $weekdays = $holiday['weekdays'];
        $holidayname = $holiday['holidayname'];
        $closingtime = $holiday['closingtime'];
        
        $stmt = $conn->prepare("INSERT INTO `oc_api_holidays`(`id`, `locationid`, `startdate`,`enddate`,`type`,`weekdays`,`holidayname`,`closingtime`, `api_name`, `date_added`, `date_modified`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `locationid` = VALUES(`locationid`), `startdate` = VALUES(`startdate`), `enddate` = VALUES(`enddate`), `type` = VALUES(`type`), `weekdays` = VALUES(`weekdays`), `holidayname` = VALUES(`holidayname`), `closingtime` = VALUES(`closingtime`), `date_modified` = VALUES(`date_modified`)");
        $stmt->bind_param("ssssss", $id, $locationid, $startdate, $enddate, $type, $weekdays, $holidayname, $closingtime, $api_name, $date_added, $date_modified);
        $stmt->execute();
        $stmt->close();
    }
  }
}
