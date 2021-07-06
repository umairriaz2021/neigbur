<?php
/*
Template Name: My Ticket Page
*/
@include_once 'functions.php';

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
$ch = curl_init(API_URL.'orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);

/* echo "<pre>"; print_r($apirespons); die;  */

/* echo "<pre>"; print_r($_SESSION); die; */

/**************************************/
/**************************************/


/* $filename = $PNG_TEMP_DIR.'ticket'.md5('183c661d-58a8-4e9e-a198-dd6732025853|H|10').'.png';
QRcode::png('183c661d-58a8-4e9e-a198-dd6732025853', $filename, 'H', 10, 2);
echo '<img src="'.site_url().'/wp-content/themes/Divi Child/'.$PNG_WEB_DIR.basename($filename).'" />';  */


/**************************************/
/**************************************/

get_header();

?>

<div id="main-content">
<div class="outer-wrapper ">
	<div class="container container-home">
		<div class="account-outer">
		<div class="login-form account-info">
			 <!--   Upcoming start --->
		 <div class="">
			 <div class="tab-header"> <h3> Upcoming</h3> </div>
			 <div class="my-tkts"><?php
		if($apirespons->success) {

			foreach($apirespons->ticketOrders as $tktrow){
				$totqnty=0;
				foreach($tktrow->ticket_order_item as $row){
					$totqnty= $totqnty + $row->quantity;
				}

				?>
			 <?php 
			 	if(((strtotime((date('Y-m-d',strtotime($tktrow->event->end)))) - strtotime(date("Y-m-d")))/60/60/24) > 0){  ?>
					<div class="tab-1">
					<div class="tab-row12">
   
					   <h5 class="tkt-del"><a href="<?php echo site_url(); ?>?page_id=2498&oid=<?php echo $tktrow->id; ?>"><?php echo $tktrow->event->name; ?></a></h5> <br/>
						<strong>ORDER ID : <?php echo $tktrow->id ?></strong>
						<p>Purchased on <?php echo date('l F j, Y',strtotime($tktrow->create_date)); ?> at <?php echo date('g:ia',strtotime($tktrow->create_date)); ?> by <?php echo $tktrow->user->first.' '.$tktrow->user->last; ?></p>
						<h5>Qty: <?php echo $totqnty ?>      TOTAL COST: $<?php echo number_format($tktrow->total, 2); ?></h5>
						<strong>Date & Time</strong>
						<!--p>Saturday September 22nd, 2019 through Sunday September 23rd, 2019    6:00 PM to 1:00 AM </p-->
						<p><?php
						echo format_dates($tktrow->event->start, $tktrow->event->end);
						?></p>
						<?php
						/*important code for qr genration */
						   /* $filename = $PNG_TEMP_DIR.'ticket'.md5($tktrow->uuid.'|H|10').'.png';
						   QRcode::png($tktrow->uuid, $filename, 'H', 2, 2);
						   echo '<img src="'.site_url().'/wp-content/themes/Divi Child/'.$PNG_WEB_DIR.basename($filename).'" />'; */
					   ?>
						<span class="email-recpt"><a style="font-weight: bold;" href="<?php echo site_url(); ?>?page_id=2498&oid=<?php echo $tktrow->id; ?>">VIEW TICKETS</a></span>
					</div>
				</div>
				<?php	 
				}
			 ?>
		<?php }
		} ?> </div>
			 <div class="tab-row1">
				 <p style="text-align: center;"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/tck.png"/><br>
					  <strong>No more Upcoming Tickets Available</strong>
					</p>
			 </div>
		 </div>
        </div>
				<!--   Upcoming end --->

		<div class="login-form account-info"> <!--  Order History start --->
			 <div class="tab-header"> <h3> Order History </h3></div>
		 <div class="tab-1">
			 <div class="tab-row12">
				 <p>View your order history</p>
			 </div>
			 <div class="">
				 <strong>Filter by Year</strong>
				 <p> <select name="Years">
				    <option value="">Select...</option>
						<option value="2021" selected>2021</option>
				  </select></p>
			 </div>

		 </div>

		 <div class="my-tkts"><?php
		if($apirespons->success) {

			foreach($apirespons->ticketOrders as $tktrow){
				$totqnty=0;
				foreach($tktrow->ticket_order_item as $row){
					$totqnty= $totqnty + $row->quantity;
				}

				?>
			 <div class="tab-1">
				 <div class="tab-row12">

					<h5 class="tkt-del"><a href="<?php echo site_url(); ?>?page_id=2498&oid=<?php echo $tktrow->id; ?>"><?php echo $tktrow->event->name; ?></a></h5> <br/>
					 <strong>ORDER ID : <?php echo $tktrow->id ?></strong>
					 <p>Purchased on <?php echo date('l F j, Y',strtotime($tktrow->create_date)); ?> at <?php echo date('g:ia',strtotime($tktrow->create_date)); ?> by <?php echo $tktrow->user->first.' '.$tktrow->user->last; ?></p>
					 <h5>Qty: <?php echo $totqnty ?>      TOTAL COST: $<?php echo number_format($tktrow->total, 2); ?></h5>
					 <strong>Date & Time</strong>
					 <!--p>Saturday September 22nd, 2019 through Sunday September 23rd, 2019    6:00 PM to 1:00 AM </p-->
					 <p><?php
					 echo format_dates($tktrow->event->start, $tktrow->event->end);
					 ?></p>
					 <?php
					 /*important code for qr genration */
						/* $filename = $PNG_TEMP_DIR.'ticket'.md5($tktrow->uuid.'|H|10').'.png';
						QRcode::png($tktrow->uuid, $filename, 'H', 2, 2);
						echo '<img src="'.site_url().'/wp-content/themes/Divi Child/'.$PNG_WEB_DIR.basename($filename).'" />'; */
					?>
					 <span class="email-recpt"><a style="font-weight: bold;" href="<?php echo site_url(); ?>?page_id=2498&oid=<?php echo $tktrow->id; ?>">VIEW TICKETS</a></span>
				 </div>
			 </div>
		<?php }
		} ?> </div>
       </div>
       </div>  <!--   Order History end --->
	 </div>
	</div> <!-- #content-area -->

   </div>  <!-- #End Main -->
<?php get_footer(); ?>
<script>
    $(document).change('[name="Years"]', function(){
       var year = $('[name="Years"]').val();
       //alert(year);
       $.ajax({
           url    :"<? echo site_url() ?>/wp-content/themes/Divi Child/inc/getmytickets.php",
           method :"POST",
           data   :"year="+year,
           success:function(data){
              // alert(data);
               $('.my-tkts').html(data);
              }
           });
        });
</script>
