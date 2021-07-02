<?php 
/*
Template Name: Attendee Report
*/
error_reporting(0);

if(isset($_SESSION['userdata'])){
	$userdata = $_SESSION['userdata'];
}else{
	wp_redirect( site_url().'?page_id=187' );
	exit;
}

global $wpdb;
$token   =  $_SESSION['Api_token'];
$url = $_SERVER['REQUEST_URI'];      
$event = explode('/', $url);
$event_id = $event[3];

if (isset($event_id) && $event_id != '') {
   $ch   = curl_init(API_URL . '/events/' . $event_id);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: ' . $token
   ));
   $result = curl_exec($ch);
   curl_close($ch);
   $apirespons = json_decode($result);

   if ($apirespons->success) {
      $event_detail = $apirespons->event;

      $ch      = curl_init(NEW_API_URL.'/admin/event-report/'.$event_id);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'Content-Type: application/json',
         'Authorization: ' . $token
      ));
      $event_report_response = curl_exec($ch);
      curl_close($ch);
      $event_report = json_decode($event_report_response);
      $attendees_ticket_sold = $event_report->AttendeesReportBreakdown->Ticket_sold;
      $attendees_ticket_scanned = $event_report->AttendeesReportBreakdown->Ticket_scanned;
      $attendees_breakdown = $event_report->AttendeesReport;
      $attendees_breakdown_GCP = $event_report->AttendeesReportBreakdownGCP->url;
   }
}
get_header(); ?>
<style>
@media only screen and (max-width: 900px) {
.mobile-visible{
    display: inline-table !important;
}
.desktop-visible{
    display: none !important;
}
.change-jusitify{
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}
}
.mobile-visible{
    display: none;
}
.desktop-visible{
    display: inline-table;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <div id="main-content">

    <?php echo$event_id ?>

       <div class="outer-wrapper">
           <div class="container container-home">
              <div class="edit-event sale-rep">
        <h3 class="h3-title sales-rep">Attendees Report </h3>
		<div class="row">
      <div class="col-md-8">
      <div class="change-jusitify">
			<?php if (isset($event_detail) && count($event_detail->files) > 0 && $event_detail->files[0]->type == 'image') { ?>
                  <img class="mb-3" src="https://storage.googleapis.com/<?php echo $event_detail->files[0]->bucket ?>/<?php echo $event_detail->files[0]->filename; ?>" style="max-height: 250px; width: 75%;">
              <?php }else{  ?>
                  <img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
               <?php } ?>
            	<h3><?php echo isset($event_detail) ? $event_detail->name : ''; ?></h3> 
				<b class="p-date"> <?php foreach ($event_detail->event_dates as $edate) {
                  if ($edate->start_date == $edate->end_date) { ?>
                     <b class="p-date"><?php echo date('M d, Y h:i a', strtotime($edate->start_date)); ?> to <?php echo date('h:i a', strtotime($edate->end_date)); ?></b>
                  <?php } else { ?>
                     <b class="p-date"><?php echo date('M d, Y h:i a', strtotime($edate->start_date)); ?> to <?php echo date('M d, Y h:i a', strtotime($edate->end_date)); ?></b>
                  <?php } 
				  } ?></b><br>
				<p class=""><?php
				 $country = $wpdb->get_row("Select * from wp_countries where  id = $event_detail->country_id");
                  $state = $wpdb->get_row("select * from wp_states where id = $event_detail->province_id");
				echo isset($event_detail) ? !empty($event_detail->location) ? $event_detail->location.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? !empty($event_detail->address2) ? $event_detail->address2.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? $event_detail->city : '';?>, <?php echo isset($event_detail) ? $state->name : '';?>, <?php echo isset($event_detail) ? $country->name : '';?><br/>
                     <?php echo isset($event_detail) ? $event_detail->postalcode : '';?></p>
			</div>	
		</div>
		
		<div class="col-md-4 card shadow-lg p-3 mb-5 bg-body rounded" style="height: fit-content;"> 
	     	<div class="tkt-sold">
		      <span> <?php echo $attendees_ticket_sold?> </span>
		       Tickets Sold
		       </div>
	     	<div class="tkt-scanned">
		      <span><?php echo $attendees_ticket_scanned ?></span>
		       Scanned or Checked-in
		       </div>
		  
		<span class="scan-date-time">LAST SCAN 05/11/2019 8:45pm</span>
		</div> 
      </div>
      </div>

	     <div class="breakdown">
	       <!-- <div class="sale-outer">  
	       <form role="search" method="get" class="edit-search" action="<?php echo site_url(); ?>">
				     	  <span class="e-search">  
				     	<input type="text" value="" placeholder="Search..." name="s" id="s">
				    	<input type="submit" id="searchsubmit" value="Search"> <i class="fa fa-search"></i></span>
			
			        </form>	
            </div> -->

            <table class="table tkt-summary sale_tkt_brk desktop-visible" style="width:100%">
               <tr>
               <th  class="text-center">Name</th>
               <th  class="text-center">Email</th>
               <th  class="text-center">Ticket Type</th>
                  <th class="text-center">Ticket#</th>
                  <th  class="text-center">Order#</th>
                  <th  class="text-center">Purchase Date</th>
                  <th class="text-center">Check-in</th>
               </tr>
               <?php 
			  
			   $ti = 0; 
               foreach($attendees_breakdown as $key=>$val) {
                   $ti++;
               ?>
                <tr id="tlr_<?=$ti?>" class="tkt_list">
                  <td class="text-center"><?php echo $val->Name;?></td>
                  <td class="text-center"><?php echo $val->Email;?></td>
                  <td class="text-center"><?php echo $val->Ticket_Type;?></td>
                  <td  class="text-center"><?php echo $val->Ticket_Number;?></td>
                  <td  class="text-center"><?php echo $val->Order_Number; ?></td>
                  <td  class="text-center"><?php echo $val->Purchase_Date;?></td>
                  <td  class="text-center"><?php echo $val->Check_In ?></td>
                </tr>
               <?php } ?> 
            </table>

            <table class=" tkt-summary sale_tkt_brk mobile-visible" style="width:100%">
            <?php 
			   $ti = 0; 
               foreach($attendees_breakdown as $key=>$val) {
                   $ti++;
               ?>
                <tr>
                <th>Name</th>
                  <td  class="right-align"><?php echo $val->Name;?></td>

                </tr>
                <tr>
                <th>Email</th>
                  <td  class="right-align"><?php echo $val->Email;?></td>

                </tr>
                <tr>
                <th class="text-left">Ticket Type</th>
                <td class="right-align"><?php echo $val->Ticket_Type;?></td>

                </tr>
                <tr>
                <th class="text-left">Ticket#</th>
                <td class="right-align"><?php echo $val->Ticket_Number;?></td>

                </tr>
                <tr>
                <th class="text-left">Order#</th>
                <td class="right-align"><?php echo $val->Order_Number; ?></td>

                </tr>
                <tr>
                <th class="text-left">Purchase Date</th>
                <td class="right-align"><?php echo $val->Purchase_Date;?></td>

                </tr>
                <tr class="">
                <th class="text-left border-bottom" style="width:50%" >Check-in</th>
                <td class="right-align border-bottom" style="width:40%"><?php echo $val->Check_In; ?></td>

                </tr>
                <?php } ?> 

            </table>
            
            <div class="download-csv"><a href="<?php echo $attendees_breakdown_GCP; ?>">
            <img width="80px" src="http://webdev.snapd.com/wp-content/uploads/2019/11/csv.jpg">
            </a></div>
      </div>
      </div>   <!-- # outer-wrapper-->
    </div> <!-- #main content --> 

    <?php get_footer(); ?>