<?php 
session_start();
require('../../../../wp-config.php');
$token   =  $_SESSION['Api_token'];

/* creating ticket order hold start */
if(isset($_POST['tickets']) && count($_POST['tickets'])>0){
	foreach($_POST['tickets'] as $tkt){
		if((int)$tkt['tqty']>0){			
			/*unhold the ticket first if any */
			if(isset($_SESSION['holdticketID'][(int)$tkt['id']])){
				$ch   = curl_init(API_URL . '/orders/hold/'.$_SESSION['holdticketID'][(int)$tkt['id']]);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: ' . $token
				));
				$res = curl_exec($ch);
				curl_close($ch);
				$holdresponse=json_decode($res);
				/* echo "<pre>"; print_r($holdresponse); */
			}
		}
	}
}

?>

