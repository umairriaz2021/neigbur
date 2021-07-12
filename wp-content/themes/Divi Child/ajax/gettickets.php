<?php 
session_start();
require('../../../../wp-config.php');
$token   =  $_SESSION['Api_token'];
//echo $_POST['tickets'];
//print_r($_SESSION['holdticketID']);
/* creating ticket order hold start */
if(isset($_POST['tickets']) && count($_POST['tickets'])>0){
	foreach($_POST['tickets'] as $key=>$tkt){
		//if((int)$tkt['tqty']>0){
		    //echo $_POST['tickets'];
		   // echo $tkt['tqty'];
			/*unhold the ticket first if any */
			//if(isset($_SESSION['holdticketID'][(int)$tkt['id']])){
				$ch   = curl_init(API_URL . '/events/'.$_POST['event_id']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: ' . $token
				));
				$result = curl_exec($ch);
				curl_close($ch);
				$holdresponse=json_decode($result);
				//print_r($holdresponse);
				//$_SESSION['holdticketID'][(int)$tkt['id']] = $holdresponse->held_tickets->id;
				$cart = array($b => $sum);
				foreach($holdresponse->event->ticketTypes as $key=>$val){
				     $sum = 0;
				     
				     if($val->held_tickets){
				    foreach($val->held_tickets as $key=>$val){
				//if( $_SESSION['holdticketID'][(int)$tkt['id']] == $holdresponse->held_tickets->id && $tkt['tqty']>0 ){
				//echo $val->quantity;
				     
				     $a = $val->quantity;
				     $b = $val->ticket_type_id;
                     $sum+= $a;
                     $cart[$b] = $sum;
			//	}
				    }
				    
				     }
				     if(!$val->held_tickets){
				        $a = 0;
				     $b = $val->id;
                     $cart[$b] = $a;
				     }
				    
				   // echo $sum;
				     ///$cart[] = $sum;
				    
				}
				 
				//	foreach($cart as $key){
					//   echo $cart[$key];
				//	}
			   
				
		//	}
			
			/*	if(isset($_SESSION['holdticketID'][(int)$tkt['id']])){
				$ch   = curl_init(API_URL . '/orders/hold/'.$_SESSION['holdticketID'][(int)$tkt['id']]);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: ' . $token
				));
				$res = curl_exec($ch);
				curl_close($ch);
				$holdresponse=json_decode($res);
				 echo "<pre>"; print_r($holdresponse); 
				 
				  $cart = array();
				   $sum = 0;
                 foreach($holdresponse as $key=>$val){
                           $a = $val->ticket_type_id;
                           $sum+= $a;
                     //print_r($cart);
                     // $cart[] = $a;
                       // echo $sum;
                        $cart[] = $sum;
                 }
			}*/
	//	}
	}
	// print_r($cart); 
	echo json_encode($cart);

}

?>

