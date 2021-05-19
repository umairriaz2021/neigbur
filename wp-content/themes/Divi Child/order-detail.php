<?php
/*
Template Name: Order Detail Page
*/
/*****************************/
	/*for QR code lib*/
/*****************************/
//set it to writable location, a place for ticketsqrcode generated PNG files
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'ticketsqrcode'.DIRECTORY_SEPARATOR;
//html PNG location prefix
$PNG_WEB_DIR = 'ticketsqrcode/';
include dirname(__FILE__)."/phpqrcode/qrlib.php";
//ofcourse we need rights to create ticketsqrcode dir
if (!file_exists($PNG_TEMP_DIR))  mkdir($PNG_TEMP_DIR);
/*****************************/
/*****************************/

$token   =  $_SESSION['Api_token'];
$ch = curl_init(API_URL.'orders/'.$_GET['oid']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);

/* echo "<pre>"; print_r($apirespons); die;  */
global $wpdb;
get_header(); ?>

<div id="main-content">
<div class="outer-wrapper ">
	<div class="container container-home">
		<div class="account-outer">

		<div class="login-form3 account-info3"> <!--   Upcoming start --->
		<h3 style="text-align: center;">Tickets powered by</h3>
         <p style="text-align: center;"><img src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/neighbur_logo.png"></p>
         <div class="print-tkt"><img src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/pdf_icon.svg"> <a href="<?php
				 echo site_url() .'/wp-content/themes/Divi Child/ticketsqrcode/neighbur_tix_'.md5($_GET['oid'].'|H|10').'.pdf';
				 ?>" target="_new" download>Download pdf</a></div>
		<?php
		if($apirespons->success)
		{

			foreach($apirespons->ticketOrder->ticket_order_item as $tktrow)
			{

				foreach($tktrow->tickets as $ticket)
				{


				?>
			 <div class="tab-12">
				 <div class="tab-row12">

					 <?php
						/*important code for qr genration */
						$filename = $PNG_TEMP_DIR.'ticket'.md5($ticket->uuid.'|H|10').'.png';
						//QRcode::png($ticket->uuid, $filename, 'H', 10, 2);
						echo '<img style="width:250px;float:right;" src="'.site_url().'/wp-content/themes/Divi Child/'.$PNG_WEB_DIR.basename($filename).'" />'; ?>
					<h5 class="tkt-del2"><?php echo $apirespons->ticketOrder->event->name; ?></h5>
					 <strong>TICKET ID : <?php echo $ticket->uuid ?></strong><br/>
					 <p>Purchased on <?php echo date('l F j, Y',strtotime($apirespons->ticketOrder->create_date)); ?> at <?php echo date('g:ia',strtotime($apirespons->ticketOrder->create_date)); ?> by <?php echo $apirespons->ticketOrder->user->first.' '.$apirespons->ticketOrder->user->last; ?><br>
						<strong>Ticket Type:</strong> <?php
						echo $tktrow->ticket_type->name;
						?><br>
					 <strong>Ticket Price:</strong> <?php
					 if (floatval($tktrow->ticket_type->price) == 0)
					 	echo 'Free';
					else
						echo '$' . number_format($tktrow->ticket_type->price, 2);
						?><br>
					 <strong>Ticket Holder:</strong> <?php echo $ticket->firstname.' '.$ticket->lastname; ?><br>
					 <div class="left-date-time">
					 <strong>Date & Time</strong>
					 <!--p>Saturday September 22nd, 2019 through Sunday September 23rd, 2019    6:00 PM to 1:00 AM </p-->

					<p><?php
					echo format_dates($apirespons->ticketOrder->event->start, $apirespons->ticketOrder->event->end);
					?>
					</p>

					 <?php
						/*important code for qr genration
						$filename = $PNG_TEMP_DIR.'ticket'.md5($ticket->uuid.'|H|10').'.png';
						QRcode::png($ticket->uuid, $filename, 'H', 10, 2);
						echo '<img style="width:250px" src="'.site_url().'/wp-content/themes/Divi Child/'.$PNG_WEB_DIR.basename($filename).'" />'; */
						$country_id = $apirespons->ticketOrder->event->country_id;
						$province_id = $apirespons->ticketOrder->event->province_id;
						$country = $wpdb->get_row("Select * from wp_countries where  id = $country_id");
						$state = $wpdb->get_row("select * from wp_states where id = $province_id");
					 ?>
					</div>
					<div class="right-location">
					<strong>Location</strong> <br/> <?php echo $apirespons->ticketOrder->event->location ?><br/> <?php echo $apirespons->ticketOrder->event->address1 ?> <?php echo $apirespons->ticketOrder->event->address2 ?><br/>
					<?php echo $apirespons->ticketOrder->event->city ?>, <?php echo $state->state_code ?>, <?php echo $country->name ?><br/> <?php echo $apirespons->ticketOrder->event->postalcode ?>
				 </div> </div>
			 </div>
			<?php }
			}
		} ?>
       </div>  <!--   Upcoming end --->

	 </div>
	</div> <!-- #content-area -->

   </div>  <!-- #End Main -->


<?php get_footer(); ?>
