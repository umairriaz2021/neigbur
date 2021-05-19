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
			
			/* sending tickettype hold request */
			
			$fields=[
						"quantity"=>(int)$tkt['tqty'],
						"fees"=> $tkt['tfee'],
						"taxes"=> $tkt['ttax'],
						"total"=> (int)$tkt['tqty'] * ($tkt['tprice']+$tkt['tfee']+$tkt['ttax']),
						"ticket_type_id"=>(int)$tkt['id'],
						"user_id"=>$_SESSION['userdata']->id
					];  
			echo $payload = json_encode($fields);	     

			$ch   = curl_init(API_URL . 'orders/hold');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: ' . $token
			));
			$result = curl_exec($ch);
			curl_close($ch);
			$response=json_decode($result);
			$_SESSION['holdticketID'][(int)$tkt['id']]=$response->heldTickets->id;
			echo "<pre>"; print_r($response); 

		}
	}
}
/* creating ticket order hold ends */
/* 
<pre>Array
(
    [quantity] => 2
    [ticket_type_id] => 315
    [user_id] => 39
)
{"quantity":2,"ticket_type_id":315,"user_id":39}
<pre>stdClass Object
(
    [heldTickets] => stdClass Object
        (
            [id] => 53
            [quantity] => 1
            [ticket_type_id] => 315
            [user_id] => 39
            [updated_at] => 2020-07-01T09:22:09.795Z
            [created_at] => 2020-07-01T09:22:09.795Z
        )

    [success] => 1
)
<pre>stdClass Object
(
    [heldTickets] => stdClass Object
        (
            [id] => 54
            [quantity] => 1
            [ticket_type_id] => 316
            [user_id] => 39
            [updated_at] => 2020-07-01T09:22:09.851Z
            [created_at] => 2020-07-01T09:22:09.851Z
        )

    [success] => 1
) */
?>

