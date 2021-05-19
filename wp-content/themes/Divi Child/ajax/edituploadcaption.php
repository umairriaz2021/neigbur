<?php 
session_start();
require('../../../../wp-config.php');

$meta_data = array('caption' => $_POST['newcaption']);
$payload = json_encode($meta_data);

$token   =  $_SESSION['Api_token'];

$ch      = curl_init(API_URL.'uploads/'.$_POST['uploadID']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Content-Length:' . strlen($payload),
	'Authorization: ' . $token
));

$result12 = curl_exec($ch);
curl_close($ch);
$editresponse = json_decode($result12); 

 ?>