<?php
session_start();
require('../../../../wp-config.php');
    if(isset($_POST['year'])){

$token   =  $_SESSION['Api_token'];
$ch = curl_init(API_URL.'orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);
		if($apirespons->success) {

			foreach($apirespons->ticketOrders as $tktrow){
				$totqnty=0;
				foreach($tktrow->ticket_order_item as $row){
					$totqnty= $totqnty + $row->quantity;
				}
				if(date('Y',strtotime($tktrow->create_date)) == $_POST['year']){

		$tkt .=' <div class="tab-1">
				 <div class="tab-row12">
				    <h5 class="tkt-del"><a href="'.site_url().'?page_id=2498&oid='.$tktrow->id.'">'.$tktrow->event->name.'</a></h5> <br/>
					 <strong>ORDER ID : '.$tktrow->id.'</strong>
					 <p>Purchased on '.date('l F j, Y',strtotime($tktrow->create_date)).' at '.date('g:ia',strtotime($tktrow->create_date)).' by '.$tktrow->user->first.' '.$tktrow->user->last .'</p>
					 <h5>Qty: '.$totqnty .'      TOTAL COST: $'. number_format($tktrow->total,2) .'</h5>
					 <strong>Date & Time</strong>
					 <!--p>Saturday September 22nd, 2019 through Sunday September 23rd, 2019    6:00 PM to 1:00 AM </p-->
					 <p>'. format_dates($tktrow->event->start, $tktrow->event->end) .'</p>
					 <span class="email-recpt"><a href="'.site_url().'?page_id=2498&oid='.$tktrow->id.'">'.'VIEW TICKETS</a></span>
				 </div>
			 </div>';
				}
			 }
		}
    }

	//echo $tkt;
	if($tkt != ''){
	    echo $tkt;
	}else{
	    echo '<p class="mt-tkt">No tickets in the year '.$_POST['year'].'</p>';
	}
