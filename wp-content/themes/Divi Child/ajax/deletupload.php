<?php 
session_start();
require('../../../../wp-config.php');

$token   =  $_SESSION['Api_token'];

$ch      = curl_init(API_URL.'uploads/'.$_POST['uploadid']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: ' . $token
));
echo $result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);
 ?>