<?php
/*
Template Name: Get tickets
*/
global $wpdb;

unset($_SESSION['page_refresh']);

$token   =  $_SESSION['Api_token'];
$url = $_SERVER['REQUEST_URI'];
$event = explode('/', $url);
$event_id = $event[2];

//       $ch   = curl_init(API_URL . 'orders/hold');
//       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 			'Content-Type: application/json',
// 			'Authorization: ' . $token
// 	  ));
// 	  $hold_orders = curl_exec($ch);
// 	  curl_close($ch);
// 	  print_r($hold_orders);die;

if(!isset($_SESSION['Api_token'])){
   /* set this page url in session to get back here after login */
   $_SESSION['loginredirect']= site_url().'/get-tickets/'.$event_id;
   wp_redirect( site_url().'?page_id=187' );
   exit;
}

if(isset($event_id) && $event_id != ''){
	$finaltickets = cartTickets($event_id);   /* definition in function.php */

}



$event = getEventById($event_id);
$event = $event->event;
//  echo "<pre>";
// print_r($event);die;

 


get_header(); ?>



<style>
#holdTicketBtn[disabled] {
    background-color: #a9a9a9;
    cursor: not-allowed;
}
</style>
<script src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/getticketscript.js"></script>

<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/css/createevent.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

<!-- please don't delete these hidden filds -->
<input type="hidden" value="<?=$_SESSION['userdata']->first;?>" id="holderFirst"/>
<input type="hidden" value="<?=$_SESSION['userdata']->last;?>" id="holderLast"/>
<input type="hidden" value="<?=$_SESSION['userdata']->email;?>" id="holderEmail"/>
<div id="main-content">
    
	<div class="outer-wrapper ">
		<div class="container container-home">
	
			<h3 class="session_expired message-box" style="display:none;">Checkout time limit has expired. Tickets have been released. Please select again.</h3>
					
			
			<form id="GetTicketForm" method="post" action="">
		    <input type="hidden" name="sal_val" id="sal_val" value="0">
		    <input type="hidden" name="sal_val1" id="sal_val1" data="">
			<input type="hidden" name="event_id" value="<?=$event_id?>">
			<!-- below filed get value on successful bambora payment -->
			<input type="hidden" id="bambora_transaction_id" name="bambora_transaction_id" value="">
			<div id="step1" class="getticket">
				<div class="head-h2">
				    
					<h2>Select Tickets</h2>
					<p style="color:grey; text-transform:uppercase; font-size:1.5rem; font-weight:700; text-align:center;" class="e_name"><?php echo ($event->name);?></p>
					<button class="btn-return" onclick="window.location.href='<?php echo site_url()?>/view-event/<?php echo $event_id;?>'"><i class="fa fa-toggle-left"></i> <span>Return to Event</span></button>
					<ul class="progressbar">
						<li class="active">Select Tickets</li>
						<li>Order Details</li>
						<li class="last-li">Payment</li>
					</ul>
				</div>
                <?php 
                $idss = $_GET['tid'];
                
             
                 
              
                if($finaltickets[0]->id == $idss){
                        
                        $ticket_date =$finaltickets[0]->start;
                        $finalticketsdata = $finaltickets[0];
                        $ticket_promo_id = $finalticketsdata->ticketPromo->id;
                        $ticket_promo_name = $finalticketsdata->ticketPromo->name;
                        $ticket_promo_value = $finalticketsdata->ticketPromo->value;
                        $ticket_promo_metric = $finalticketsdata->ticketPromo->metric;
                        // echo "<pre>";
                        // print_r($finalticketsdata );
                        // echo "</pre>";die;
                        $date1 = $_GET['abc'];
                         $ticket_date = date('Y-m-d',strtotime($date1));
                        //print_r($ticket_date);die;
                }
                // elseif($finaltickets[1]->id == $idss){
                        
                //         $ticket_date =$finaltickets[1]->start;
                        
                // }   
                //echo "<pre>"; print_r($ticket_date); echo "</pre>"; die;
                ?>
				<div class="text_head">
					<p>Get your tickets by selecting the quantity and proceeding to checkout.</p>
					<h3>CONFIRMATION OF TICKET AVAILABILITY ON NEXT STEP</h3>
				</div>

				<!-- Mobile Table
				<div class="table-ticket table-responsive mobile-table">
					  <table class="table table-borderless">
						  <div class="h3 font-weight-bold pb-4">Available Tickets</div>
						<?php foreach($finaltickets as $key=>$tkt){ ?>
					       <tr class="ticket-details">
						   <th style="width: 25%;padding-left:16px;">Ticket(s)</th>
                              <td>
                              <?php echo stripslashes(ucfirst($tkt->name)); ?><br/>
                              <?php echo ($tkt->start!='')?date('M j, Y @ g:ia', strtotime($tkt->start)):''; ?>
                              <?php if($tkt->note){?>
                                <div class="tooltip">Ticket Details
                                     <span class="tooltiptext"><?php echo stripslashes($tkt->note); ?></span>
                                </div>
                             <?php } ?>
                              </td>
							  </tr>
							  <tr class="ticket-details">
							  <th style="width: 15%;padding-left:16px;">Price</th>
                              <?php if(strtotime($tkt->expiration_date) < get_time_in_prov($event->province->province_code)){ ?>
                                    <td style="text-align:center;color:#f56d3a" colspan="5">Tickets no longer available.</td>
                              <?php }else if(strtotime($tkt->release) > get_time_in_prov($event->province->province_code)){ ?>
                                    <td style="text-align:center;color:#f56d3a" colspan="5">Tickets available on <?php echo date('M j, Y @ g:ia', strtotime($tkt->release));  //echo date('M d, Y @ h:ia') ?>   </td>
                              <?php }else{ ?>
                              <td class="apply-promo">
                                  $<?php echo number_format($tkt->TP,2,'.',','); ?>
                                  <input type="hidden" name="tickets[<?=$key?>][id]" class="tid" value="<?php echo $tkt->id; ?>"/>
                                  <input type="hidden" name="tickets[<?=$key?>][tname]" class="tname" value="<?php echo $tkt->name; ?>"/>
                                  <input type="hidden" name="tickets[<?=$key?>][tprice]" class="tprice" value="<?php echo $tkt->TP; ?>"/>
                                  <?php
                                    //uncomment it when start working on promocoe
                                  if($tkt->ticketPromo): ?>
                                        <input type="text" name="tickets[<?=$key?>][promo][code]" class="tpromoCode" value="<?php echo $tkt->ticketPromo->code; ?>"/>
                                        <input type="text" name="tickets[<?=$key?>][promo][metric]" class="tpromoMetric" value="<?php echo $tkt->ticketPromo->metric; ?>"/>
                                        <input type="text" name="tickets[<?=$key?>][promo][value]" class="tpromoValue" value="<?php echo $tkt->ticketPromo->value; ?>"/>
                                   
                                  <?php endif;?>
                              </td>
							  </tr>
							  <tr class="ticket-details">
							  <th style="width:15%;padding-left:16px;">Tax</th>
                              <td>
                                <?php $txtTax = ($tkt->TP>0)?$tkt->Ttax:0;?>
                                $<?php echo number_format($txtTax,2,'.',','); ?>
                                <input type="hidden" name="tickets[<?=$key?>][ttax]" class="ttax" value="<?php echo number_format($txtTax,2,'.',','); ?>"/>
                              </td>
							  </tr>
							  <tr class="ticket-details">
							  <th style="width: 10%;padding-left:16px;">Fees*</th>
                              <td>
                                <?php $txtTCF = ($tkt->TP>0)?$tkt->TCF:0;?>
                                  $<?php echo number_format($txtTCF,2,'.',','); ?>
                                  <input type="hidden" name="tickets[<?=$key?>][tfee]" class="tfee" value="<?php echo number_format($txtTCF,2,'.',','); ?>"/>
                              </td>
							  </tr>
							  <tr class="ticket-details">
							  <th style="width: 15%;padding-left:16px;">Quantity</th>
                              <td >

                              <?php
                              $remainingtick = $tkt->max - $tkt->ticket_allocation;
                $limitMessage = "";
                              if($remainingtick > 10){ ?>
                                <input type="number" min="0" name="tickets[<?=$key?>][tqty]" value="0" class="td-p tqty" max="<?php echo ($remainingtick > $tkt->order_limit)?$tkt->order_limit+1:$remainingtick+1; ?>">
                             <?php }else if(($remainingtick > 0) && ($remainingtick <= 10)){ ?>
                                <input type="number" min="0" name="tickets[<?=$key?>][tqty]" value="0" class="td-p tqty" max="<?php echo ($remainingtick > $tkt->order_limit)?$tkt->order_limit+1:$remainingtick+1; ?>">
                              <?php $limitMessage = "Limited Availability"; }else{ $limitMessage = "SOLD OUT";?>
                              <?php } ?>
                <p class="remainingMessage" style=""><?php echo $limitMessage; ?></p>
                              <p class="limitMessage" style="display:none"></p>
                              <input type="hidden" class="torderlimit" name="tickets[<?=$key?>][tolimit]" value="<?php echo $tkt->order_limit; ?>"/>
                <input type="hidden" class="tremaining" name="tickets[<?=$key?>][tremaining]" value="<?php echo $remainingtick; ?>"/>
                              </td>
							  </tr>
							  <tr class="ticket-details" style="border-bottom: 2px solid; margin-bottom: 2rem;">
							  <th style="width: 15%;padding-left:16px;" class="text-nowrap">Sub-Total</th>
                              <td class="al-right">
                                $<span class="ttoltxt1">0.00</span><br>
                                <span class="ttoltxt-discount" value=""></span>
                                <input type="hidden" name="sub_total_row" value="" class="sub_total_each">
                              </td>
                              <?php } ?>
                          </tr>
					   
					  
					 
						   
						<?php } ?>


					  </table>
					</div> -->


				<!-- Desktop Table -->
				<div class="table-ticket table-responsive">
					  <table class="table">
						<tr style="background-color:white;">
							<th style="width: 25%;padding-left:16px;">Ticket(s)</th>
						<!--	<th style="width: 10%;padding-left:16px;">Details</th>-->
							<th style="width: 15%;padding-left:16px;">Price</th>
							 <th  class="desktop-visible" style="width:15%;padding-left:16px;">Tax</th>
							<th class="desktop-visible" style="width: 10%;padding-left:16px;">Fees*</th>
							<th style="width: 20%;text-align: center;">Quantity</th>
							<th style="width: 15%;padding-left:16px;text-align:right;">Sub-Total</th>
						</tr>
						<?php foreach($finaltickets as $key=>$tkt){ ?>
					    
					   
					       <tr class="ticket-details">
                              <td>
                              <?php echo stripslashes(ucfirst($tkt->name)); ?><br/>
                              <?php echo ($tkt->start!='')?date('M j, Y @ g:ia', strtotime($tkt->start)):''; ?>
                              <?php if($tkt->note){?>
                                <div class="tooltip">Ticket Details
                                     <span class="tooltiptext"><?php echo stripslashes($tkt->note); ?></span>
                                </div>
                             <?php } ?>
                              </td>
                             <!-- <td><?php echo stripslashes($tkt->note); ?></td>-->
                              <?php if(strtotime($tkt->expiration_date) < get_time_in_prov($event->province->province_code)){ ?>
                                    <td style="text-align:center;color:#f56d3a" colspan="5">Tickets no longer available.</td>
                              <?php }else if(strtotime($tkt->release) > get_time_in_prov($event->province->province_code)){ ?>
                                    <td style="text-align:center;color:#f56d3a" colspan="5">Tickets available on <?php echo date('M j, Y @ g:ia', strtotime($tkt->release));  //echo date('M d, Y @ h:ia') ?>   </td>
                              <?php }else{ ?>
                              <td class="apply-promo">
                                  $<?php echo number_format($tkt->TP,2,'.',','); ?> <!-- TP is ticket price total function.php -->
                                  <input type="hidden" name="tickets[<?=$key?>][id]" class="tid" value="<?php echo $tkt->id; ?>"/>
                                  <input type="hidden" name="tickets[<?=$key?>][tname]" class="tname" value="<?php echo $tkt->name; ?>"/>
                                  <input type="hidden" name="tickets[<?=$key?>][tprice]" class="tprice" value="<?php echo $tkt->TP; ?>"/>
                                  <!-- TP is ticket price total function.php -->
                                  <?php
                                    //uncomment it when start working on promocoe
                                  if($tkt->ticketPromo): ?>
                                        <input type="text" name="tickets[<?=$key?>][promo][code]" class="tpromoCode" value="<?php echo $tkt->ticketPromo->code; ?>"/>
                                        <input type="text" name="tickets[<?=$key?>][promo][metric]" class="tpromoMetric" value="<?php echo $tkt->ticketPromo->metric; ?>"/>
                                        <input type="text" name="tickets[<?=$key?>][promo][value]" class="tpromoValue" value="<?php echo $tkt->ticketPromo->value; ?>"/>
                                   
                                  <?php endif;?>
                              </td>
                              <td   class="desktop-visible">
                                <?php $txtTax = ($tkt->TP>0)?$tkt->Ttax:0;?>
                                $<?php echo number_format($txtTax,2,'.',','); ?>
                                <input type="hidden" name="tickets[<?=$key?>][ttax]" class="ttax" value="<?php echo number_format($txtTax,2,'.',','); ?>"/>
                              </td>
                              <td  class="desktop-visible">
                                <?php $txtTCF = ($tkt->TP>0)?$tkt->TCF:0;?>
                                  $<?php echo number_format($txtTCF,2,'.',','); ?>
                                  <input type="hidden" name="tickets[<?=$key?>][tfee]" class="tfee" value="<?php echo number_format($txtTCF,2,'.',','); ?>"/>
                              </td>
                              <td height="170px" style="text-align: center;">

                              <?php
                              $remainingtick = $tkt->max - $tkt->ticket_allocation;
                $limitMessage = "";
                              if($remainingtick > 10){ ?>
                                <input type="number" min="0" pattern="[0-9]" onkeypress="return !(event.charCode == 46)" step="1"  name="tickets[<?=$key?>][tqty]"  value="0" class="td-p tqty" max="<?php echo ($remainingtick > $tkt->order_limit)?$tkt->order_limit+1:$remainingtick+1; ?>">
                             <?php }else if(($remainingtick > 0) && ($remainingtick <= 10)){ ?>
                                <input type="number" min="0" name="tickets[<?=$key?>][tqty]" value="0" class="td-p tqty" max="<?php echo ($remainingtick > $tkt->order_limit)?$tkt->order_limit+1:$remainingtick+1; ?>">
                              <?php $limitMessage = "Limited Availability"; }else{ $limitMessage = "SOLD OUT";?>
                              <?php } ?>
                <p class="remainingMessage" style=""><?php echo $limitMessage; ?></p>
                              <p class="limitMessage" style="display:none"></p>
                              <?php if($tkt->max == $tkt->order_limit){?>
                                   <input type="hidden" class="torderlimit" name="tickets[<?=$key?>][tolimit]" value="<?php echo  $remainingtick; ?>"/>
                                <?php  }
                                
                                else {?>
                                 <input type="hidden" class="torderlimit" name="tickets[<?=$key?>][tolimit]" value="<?php echo $tkt->order_limit; ?>"/>
                             <?php } ?>
                             
                <input type="hidden" class="tremaining" name="tickets[<?=$key?>][tremaining]" value="<?php echo $remainingtick; ?>"/>
                              </td>
                              <td class="al-right">
                                $<span class="ttoltxt">0.00</span><br>
                                <span class="ttoltxt-discount" value=""></span>
                                <input type="hidden" name="sub_total_row" value="" class="sub_total_each">
                              </td>
                              <?php } ?>
                          </tr>
					   
					  
					 
						   
						<?php } ?>


					  </table>
					  <label class="label-tab">*Fees include payment gateway and administrative costs</label>

					  <div class="promo-sec">
						 <div class="sub-promo"> <p class="p-code">Enter Promo Code</p> 
						 <input type="text" name="promocode" placeholder="Enter Code">
						 <button class="applybtn" onclick="applyPromo();" disabled>APPLY</button> 
						 <a href="void:javascript(0)" class="clear-code" onclick="clearcode();">Clear Code</a>
						 </div>
						  <p class="promo-msg"></p>
						  <p>Only one promotional code per transaction</p>
					  </div>

					<div class="pay-total-sec">
						<div class="left-pay-im">
							<p>We accept the following methods of payment.</p>
							<img src="<?php echo site_url(); ?>/wp-content/uploads/2020/09/credit_card_bambora.png">
							<!--<p>Powered by </p>-->
							<!--<img src="<?php echo site_url(); ?>/wp-content/uploads/2019/11/bambora-logo.jpg">-->
						</div>
						<div class="right-total">
							<ul class="tot-li">
								<li>Ticket(s)<span class="tkttotal"> $0.00</span></li>
								<li>Discount <span class="tktDiscount" value=""> $0.00</span></li>
								<li>Fees<span class="tktFee"> $0.00</span></li>
								<li>Tax<span class="tktTax"> $0.00</span></li>
								<li><strong>Total<span class="Total"> $0.00</span></strong></li>
							</ul>
						</div>
					</div>

					<button class="next-btn" type="button" name="btnSubmit" onClick="holdTicket();" id="holdTicketBtn" disabled>NEXT</a></button>
				</div>
			</div>
			<!----- second step start ------>
			<div id="step2" class="getticket" style="display:none">
				<div class="head-h2">
				   <h2>Order Details</h2>
				   <p style="color:grey; text-transform:uppercase; font-size:1.5rem; font-weight:700; text-align:center;" class="e_name"><?php echo ($event->name);?></p>
				   <button class="btn-return"><a href="<?php echo site_url()?>/view-event/<?php echo $event_id;?>"><i class="fa fa-toggle-left"></i> <span>Return to Event</span></a></button>
				   <ul class="progressbar desktop-visible">
					  <li class="active chk-mark">Select Tickets</li>
					  <li class="active">Order Details</li>
					  <li class="last-li">Payment</li>
				   </ul>
				</div>
				<div class="time-sec"><p>Order Expires in: <span class="countdown">05:00</span></p></div>
				<div class="message-box"><p>Your ticket selection has been confirmed and will be held for the time shown.</p></div>
				<div class="special-request">
				   <h2>Special Requests</h2>
				   <p>If the ticket holder(s) have any special request (i.e dietary needs, wheelchair accessibility, medical conditions) pease contact the event organizer</p>
				   <?php $evmeta = unserialize($finaltickets[0]->event->metadata); ?>							 
				   
				 <!-- Desktop Only -->
				   <div class="desktop-table">  
				   <div class="sec1-li" >
					  <ul>
						 <?php if($evmeta['org']){ ?><li>Organization: </li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_name){ ?><li>Name: </li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_phone){ ?><li>Phone: </li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_url){ ?><li>Website:</li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_email){ ?><li>Email: </li><?php } ?>
					  </ul>
				   </div>
				   <div class="sec2-li">
					  <ul>
						 <?php if($evmeta['org']){ ?><li>&nbsp;<?php echo $evmeta['org']; ?></li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_name){ ?><li>&nbsp;<?php echo $finaltickets[0]->event->contact_name ?></li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_phone){ ?><li>&nbsp;<?php echo $finaltickets[0]->event->contact_phone ?></li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_url){ ?><li>&nbsp;<a href="<?php echo $finaltickets[0]->event->contact_url ?>" target="_new"><?php echo $finaltickets[0]->event->contact_url ?></a></li><?php } ?>
						 <?php if($finaltickets[0]->event->contact_email){ ?><li>&nbsp;<a href="mailto:<?php echo $finaltickets[0]->event->contact_email ?>"><?php echo $finaltickets[0]->event->contact_email ?></a></li><?php } ?>
					  </ul>
				   </div>
				</div>

				   <div class="mobile-table">
				   <ul>
				   <?php if($evmeta['org']){ ?><li>Organization: &nbsp;<?php echo $evmeta['org']; ?> </li><?php } ?>
				   <?php if($finaltickets[0]->event->contact_name){ ?><li>Name: &nbsp;<?php echo $finaltickets[0]->event->contact_name ?></li><?php } ?>
				   <?php if($finaltickets[0]->event->contact_phone){ ?><li>Phone: &nbsp;<?php echo $finaltickets[0]->event->contact_phone ?> </li><?php } ?>
				   <?php if($finaltickets[0]->event->contact_url){ ?><li>Website: &nbsp;<a href="<?php echo $finaltickets[0]->event->contact_url ?>" target="_new"><?php echo $finaltickets[0]->event->contact_url ?></a></li><?php } ?>
				   <?php if($finaltickets[0]->event->contact_email){ ?><li>Email: &nbsp;<a href="mailto:<?php echo $finaltickets[0]->event->contact_email ?>"><?php echo $finaltickets[0]->event->contact_email ?></a></li><?php } ?>

				   </ul>
					</div>

				</div>
        <?php
          $charitableDisplay = 'none';
          if (isset($evmeta['charitablereceipt']) && $evmeta['charitablereceipt'] == true)
          {
            $charitableDisplay = '';
          }
        ?>
				<div class="charitable-receipt" style="display: <?php echo $charitableDisplay; ?>;">
				   <h2>Charitable Donation Receipt</h2>
				   <span class="chkbox">
				   <input type="checkbox" name="charity" value="1" id="donation_receipt">
				   <span class="checkmark"></span>
				   If you would like to receive a charitable donation receipt for your purchase, please check this box and provide the required name and address information when prompted. Please note, this receipt will be sent by the organization hosting the event, not by Neighbur.</span>
				   <div class="charitable-yes" id="charitable-yes" style="display:none">
					  <div class="one-half">  <label> First Name<em>*</em> <input type="text" placeholder="First" name="charityfname" id="" required="" title="Please enter your first name" value=""> </label></div>
					  <div class="one-half"><label> Last Name<em>*</em> <input type="text" placeholder="Last" name="charitylname" id="" required="" title="Please enter your last Name" value=""> </label></div>
					  <div class="one-full"><label> Address<em>*</em> <input type="text" placeholder="Address" name="charityaddress" id="" required="" title="Please enter your Address" value=""> </label></div>
					  <div class="one-half">
						 <label>Country<em>*</em></label>
						 <select name="charitycountry" id="charitycountry" style="height: auto;">
							<option value="2" selected>Canada</option>
							<option value="3">United States</option>
						 </select>
					  </div>
					  <div class="one-half">
						 <label>Province/State<em>*</em></label>
						 <select name="charityregoin" id="charityregoin"  style="height: auto;">

               <?php $state  =   $wpdb->get_results("Select * from wp_states where country_id = 2"); ?>

               <option value="">Select...</option>
               <?php foreach($state as $row) { ?>

                   <option value="<?php echo $row->name?>"><?php echo $row->name;?> </option>

               <?php } ?>
						 </select>
					  </div>
					  <div class="one-half"> <label> City<em>*</em><input type="text" placeholder="City" name="charitycity" id="" required="" title="Please enter your city" value=""> </label></div>
					  <div class="one-half"><label> Postal code<em>*</em> <input type="text" placeholder="Postal code" name="charityzip" id="" required="" title="Please enter your postal code" value=""> </label></div>
				   </div>
				</div>
				<div class="ticketholders">
				   <h2>Ticketholders</h2>
				   <p><strong>It looks like you are purchasing multiple tickets!</strong> If these tickets aren't all for you, let us know which of your friends will be joining you and we will email them their assigned tickets.</p>
				   <div id="tickeTHoldersDiv">
					   <!--
					   <div class="ticket_type_div">Ticket Type: <span>Adult</span></div>
					   <table style="width:100%" class="table_ordering_ticket_list">
						  <tr><th></th><th>First Name</th><th>Last Name</th><th>Email</th></tr>
						  <tr>
							 <td id="order_tab">1</td>
							 <td><input type="text" name="lastname" placeholder="FIRST" class="frm_ordering_page"></td>
							 <td><input type="text" name="lastname" placeholder="LAST" class="frm_ordering_page"></td>
							 <td><input type="text" name="lastname" placeholder="EMAIL" class="frm_ordering_page"></td>
						  </tr>
					   </table>
					   -->
					   <!-- these html loaded through getticketscript.js file -->
					</div>
				</div>
				<div class="button_order_page">
				   <div style="
    border: 0;
    text-align: center;
    margin-bottom: 20px!important;
    padding: 13px 33px!important;
    color: #fff;
    font-size: 20px;
    letter-spacing: 3px;
    border-radius: 3px;
    margin-top: 10px!important;
    width: fit-content;
    cursor: pointer;  
				   " class="back-btn"><a href="javascript:void(0)" onClick="ticketpaymentBack(1,2)">BACK</a></div>
				   <button class="next-btn" type="button" name="btnnext" onClick="ticketpaymentNext(2,3)">NEXT</button>
				</div>
			 </div>
			<!----- second step ends ------>

			<!----- third step start ------>
			<div  id="step3" class="getticket" style="display:none">
				<div class="head-h2">
				   <h2>Order Details</h2>
				   <p style="color:grey; text-transform:uppercase; font-size:1.5rem; font-weight:700; text-align:center;" class="e_name"><?php echo ($event->name);?></p>
				   <button class="btn-return"><a href="<?php echo site_url()?>/view-event/<?php echo $event_id;?>"><i class="fa fa-toggle-left"></i> <span>Return to Event</span></a></button>
				   <ul class="progressbar desktop-visible">
					  <li class="active chk-mark">Select Tickets</li>
					  <li class="active chk-mark">Order Details</li>
					  <li class="active last-li">Payment</li>
				   </ul>
				</div>
				<div class="time-sec">
				   <p>Order Expires in: <span class="countdown">05:00</span></p>
				</div>
				<div class="message-box"><p> All Tickets Are NON-REFUNDABLE</p></div>
				<!-- CREDIT CARD FORM STARTS HERE -->
				<div class="credit-card">
				   <div class="creit-card-box">
					  <div class="card-head">
						 <h3>Neighbur TIX Event</h3>
						 <p> <?php echo $finaltickets[0]->event->name ?></p>
					  </div>
					  <div class="form-group" id="nameGroup">
						 <label for="email"> <i class="fa fa-user"></i> </label>
						 <input type="hidden" id="TotalAmount" name="totalamount">
						 <input type="text" id="email" name="cardOwnername" placeholder="Name on Card">
					  </div>
					  <div class="form-group" id="cardNumberGroup">
						 <label for="cardNumber"> <i class="fa fa-credit-card"></i> </label>
						 <input  type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="Card Number"  autocomplete="cc-number" required autofocus />
					  </div>
					  <div class="form-group" id="cardExpiryGroup">
						 <label for="cardExpiry"><i class="fa fa-calendar"></i></label>
						 <input  type="text"  class="form-control"  id="cardExpiry" name="cardExpiry" placeholder="MM/YY" autocomplete="cc-exp" required />
					  </div>
					  <div class="form-group" id="cardCVCGroup">
						 <label for="cardCVC"> <i class="fa fa-lock"></i></label>
						 <input  type="text"  class="form-control" id="cardCVC" name="cardCVC" placeholder="CVC" autocomplete="cc-csc" required/>
					  </div>
            <div class="form-group" id="spacer" style="display:none;">
              <p></p>
            </div>
            <div>
					  <!-- p class="remember" id="rememberGroup">
						 <span class="chkbox"><input type="checkbox" value="1" checked="checked" name="remembercard"><span class="checkmark"></span> &nbsp;  &nbsp; Save card</span>
                </p -->
					  <p class="d-flex justify-content-center"><button style="width: fit-content" type="button" name="buytickets" id="buytickets" class="signupbtn">Buy Tickets <span class="Total">0.00</span>(<?php echo $finaltickets[0]->currency_code;?>)</button> </p>
                 <div class="message-box" id="PayMentError" style="background-color: #c50e0e;"></div>
				   </div>
				</div>
				<!-- CREDIT CARD FORM ENDS HERE -->
				<div class="button_order_page">
				   <div class="back-btn"><a href="javascript:void(0)" onClick="ticketpaymentBack(2,3)">BACK</a></div>
				</div>
			 </div>
			<!----- third step ends ------>
			</form>
		</div>
	</div> <!-- #content-area -->
</div>  <!-- #End Main -->
<div class="modal" id="loadingModal" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 220px !important;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="email-confomation">
                    <p class="mail-img" style="padding: 0px;"><img src="<?php echo site_url(); ?>/wp-content/uploads/loading.gif"></p>
					<p id="modal_loader_text">Loading...</p>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
function disable_f5(e)
{
  if ((e.which || e.keyCode) == 116)
  {
      e.preventDefault();
  }
}

function keydown(e) { 

    if ((e.which || e.keyCode) == 116 || ((e.which || e.keyCode) == 82 && ctrlKeyDown)) {
        // Pressing F5 or Ctrl+R
        e.preventDefault();
    } else if ((e.which || e.keyCode) == 17) {
        // Pressing  only Ctrl
        ctrlKeyDown = true;
    }
};

function keyup(e){
    // Key up Ctrl
    if ((e.which || e.keyCode) == 17) 
        ctrlKeyDown = false;
};


    $('#holdTicketBtn').click(function(){
        //$(document).bind("keydown", disable_f5);
         $(document).on("keydown", keydown);
        $(document).on("keyup", keyup);
    });
</script>
<?php get_footer(); ?>
