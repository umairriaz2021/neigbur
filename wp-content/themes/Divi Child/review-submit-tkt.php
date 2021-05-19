<?php
/*
Template Name: Review and Submit Tickets
*/


global $wpdb;
$success = '';
$event_state = $_SESSION['eventstate'];


if(isset($_GET['edit']) && $event_state == 'upcoming'){
    $_SESSION['edit_ticket_data'] = $_SESSION['ticket_data'];
}


require(__DIR__.'/inc/reviewandsubmit.php');

// echo "<pre>"; print_r($_SESSION['event_data']); die;
get_header();
   if(isset($event_state) && $event_state == 'past' && isset($_GET['edit'])) {
       $token = $_SESSION['Api_token'];
       $event_id = $_GET['edit'];

       $ch      = curl_init(API_URL.'ticketTypes?eventId='.$event_id);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Authorization: ' . $token
       ));
       $tkt_response = curl_exec($ch);
       curl_close($ch);
       $tkt = json_decode($tkt_response);
       if($tkt->success && !empty($tkt->ticketType)) {
          $tickets = $tkt->ticketType;
       }
   }
?>

<link rel="stylesheet" href="/wp-content/themes/Divi Child/datepicker/css/jquery.datetimepicker.min.css">
<script src="/wp-content/themes/Divi Child/datepicker/js/moment.js"></script>
<script src="/wp-content/themes/Divi Child/datepicker/js/jquery.datetimepicker.full.js"></script>
<!--<link rel="stylesheet" href="<?php //echo site_url()?>/wp-content/themes/Divi Child/css/createevent.css">-->
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/css/reviewpage.css">
<style>
.warning-terms{
    color:red;
    display:none;
}
#btnfinal[disabled] {
    background-color: #a9a9a9; cursor: not-allowed;
}
.modal{
    padding-top:0px !important;
    padding-left: 20% !important;
    padding-right: 18% !important;
}
.attachemnts b {
    width: 100px !important;
    padding: 0px 0 !important;
}
img{
    max-width:80% !important;
}
#modal_image {
    max-width: 100% !important;
}
.contact-detail h3{
    font-size:17px;
    margin-top: 25px;
}
.contact-detail b{
    width: 110px;
    float: left;
}
</style>
<div id="main-content">
   <div class="outer-wrapper ">
      <div class="container container-home">
         <h3 class="h3-title">Select Options & Submit</h3>
         <button type="button" class="btn-return"><a href="<?php echo site_url(); ?>?page_id=364"> <i class="fa fa-toggle-left"></i> <span>Return to Event Setup</span></a></button>
         <?php
         if(isset($_GET['edit'])){
         ?>
         <ul class="progressbar">
            <li class="active">Event Details</li>
            <li class="active">Ticket Details</li>
            <li class="active">Options & Update</li>
         </ul>
         <?php }else{ ?>
         <ul class="progressbar">
            <li class="active chk-mark">Page Design</li>
            <li class="active chk-mark">Ticket Options</li>
            <li class="active">Options & Submit</li>
         </ul><?php } ?>

         <p style="float:left;width:100%;text-align:center;margin-bottom:10px;font-size: 15px;">Please review summary and options; select SUBMIT to finalize event setup.</p>
         <hr/>
         <p style="float:left;width:100%;text-align:center;margin-bottom:10px;font-size: 15px;">Select preview to view your event layout...</p>
		 <span class="event-preview"> <a href="javascript:void(0);" onClick="jQuery('#previewModal').show();"> Preview <i class="fa fa-eye"></i></a></span>
         <?php if($success != ''){ ?>
         <p><b><?php echo $success;?></b></p>
         <?php }?>

         <?php if($event_state == 'upcoming'):
              if(isset($_SESSION['ticket_data']) && $_SESSION['ticket_data']['tkt_setup'] != 'Yes 3rd party'){
             if ( have_posts() ) : 
                 while ( have_posts() ) : the_post();
       the_content();
       
      endwhile; else: ?>
   <p>Sorry, no posts matched your criteria.</p>
   <?php endif; } ?>
   <?php endif; ?>



		<div style="clear: both;">
			<h3>Ticket Summary</h3>

		</div>
         <form id="subm_form" action="" method="post">

            <?php if($event_state == "past"){
            if(isset($tickets)){ ?>
            	<p> You have created the following ticket types for sale</p>

                <table class="tkt-summary" style="width:100%">
                <tr>
                    <th>Ticket type Breakdown</th>
                    <th class="right-align">Price</th>
                    <th class="right-align">Quantity</th>
                </tr>

                    <?php foreach($tickets as $key=>$val) { ?>
                        <tr>
                            <td><?php echo $val->name;?></td>
                            <td class="right-align"><?php echo number_format($val->price,2);?></td>
                            <td class="right-align"><?php echo $val->order_limit;?></td>
                        </tr>
                    <?php }?>
                </table>
            <?php } else if(isset($_SESSION['ticket_data']) && $_SESSION['ticket_data']['tkt_setup'] == 'Yes 3rd party'){ ?>
            <p>You are using third-party ticketing at <?php echo $_SESSION['ticket_data']['thirdpartyurl'].$_SESSION['ticket_data']['turl']; ?></p>
            <?php }else{ ?>
                <p>You have no tickets setup for this event.</p>
            <?php } }else {
            if(isset($_SESSION['ticket_data']) && $_SESSION['ticket_data']['tkt_setup'] == 'Yes Tix'){ ?>
            	<p> You have created the following ticket types for sale </p>

                <table class="tkt-summary" style="width:100%">
                <tr>
                    <th>Ticket type Breakdown</th>
                    <th class="right-align">Price</th>
                    <th class="right-align">Quantity</th>
                </tr>

                    <?php for($i=0; $i<$_SESSION['ticket_data']['count']; $i++) { ?>
                        <tr>
                            <td><?php echo stripslashes($_SESSION['ticket_data']['ticket_name'][$i+1]);?></td>
                            <td class="right-align"><?php echo ($_SESSION['ticket_data']['price_per_tkt'][$i+1]) ? '$'.$_SESSION['ticket_data']['price_per_tkt'][$i+1] : 'Free';?></td>
                            <td class="right-align"><?php echo ($_SESSION['ticket_data']['no_of_tkt_available'][$i+1]) ? $_SESSION['ticket_data']['no_of_tkt_available'][$i+1] : $_SESSION['ticket_data']['total_tickets'][$i+1];?></td>
                        </tr>
                    <?php }?>
                </table>
            <?php } else if(isset($_SESSION['ticket_data']) && $_SESSION['ticket_data']['tkt_setup'] == 'Yes 3rd party'){ ?>
            <p>You are using third-party ticketing at <?php echo $_SESSION['ticket_data']['thirdpartyurl'].$_SESSION['ticket_data']['turl']; ?></p>
            <?php }else{ ?>

                <p>You have no tickets setup for this event.</p>
            <?php } } ?>
            <p>To use <i>neighbur tix</i>, select BACK to modify or contact us today for more information on the benefits of our service</p><br/>

          <div class="pay-options">
          <h3 style="display:none;">Options</h3>
          <?php if($event_state == 'upcoming' && !isset($_GET['edit'])): ?>
           <span class="chkbox">
             <input type="checkbox" value="1" name="sendphoto" class="chk_subbox" id="sendphoto">
            <span class="checkmark"></span><label class="chkbox" for="sendphoto"> Send Event Photographer (only applicable in areas with neighbour publication and where resources are available; confirmation will be made by email)
            The following arrival time is requested...</label></span>
         <?php endif; ?>
             <div class="pay_date" style="display:none">
                  <label style="cursor: pointer;" class="pay-date" for="pay_date">Requsted on
				  <input type="text" id="pay_datepicker" class="pay_event_datepicker" name="pay_event_date" placeholder="Select Date" value="NOT SET"></label>
             </div>
          <span class="chkbox" style="display:none">
             <input type="checkbox" value="1" name="makeprivate" class="chk_subbox" id="makeprivate">
            <span class="checkmark"></span><label class="chkbox" for="makeprivate"> Make the event private (not available on public calendar; available via private link only)</label></span>
          <div class="web-url" style="display:none">
               <select name="web-url-http">
                  <option value="http://">http://</option>
                  <option value="https://">https://</option>
               </select>
               <input style="width:90%;" type="text" name="web-url" placeholder="URL">
            </div>

          </div>
          <?php if($event_state != 'past'): ?>
          <div class="pay-options" style="display:none;">
               <h3>Services</h3>
            <p>An experience coordinator will contact you regarding any of the following services...</p>
            <span class="chkbox"><input type="checkbox" value="1" name="learnmoresnapd" id="learnmoresnapd"><span class="checkmark"></span><label class="chkbox" for="learnmoresnapd"> I would like to learn more about neighbur Design & Marketing services.</label></span><br>
            <span class="chkbox"><input type="checkbox" value="1" name="learnmoreagumented" id="learnmoreagumented"><span class="checkmark"></span><label class="chkbox" for="learnmoreagumented"> I would like to learn more about Augmented Reality material.</label></span><br>
            <span class="chkbox"><input type="checkbox" value="1" name="learnmoresnapplication" id="learnmoresnapplication"><span class="checkmark"></span><label class="chkbox" for="learnmoresnapplication"> I would like to learn more about advertising my event in a neighbur publication.</label></span><br>


         </div>
         <?php endif; ?>
        <div class="charitable-receipt"  <?php echo (isset($_GET['edit'])) ? 'style="display:none;"' : '';?>>
         <h3>Event Provider Terms</h3>
           <span class="chkbox">
          <input type="checkbox" name="accept_terms" class="chk_subbox" id="accept_terms" <?php echo (isset($_GET['edit'])) ? 'checked' : '';?> required>
          <span class="checkmark"></span><label id="accept_terms" class="chkbox" for="accept_terms">
          I agree to the <a href="javascript:void(0);" style="color:dodgerblue" onClick="jQuery('#myTermModal').show();">Terms and Conditions </a></label>
          <p id="tc-err" class="warning-terms">You must agree to the Terms and Conditions.</p>
        </span>

         </div>
         <?php if($event_state == 'past'): ?>
         <div class="charitable-receipt">
         <h3>Event Provider Terms</h3>
         <span class="chkbox">
          Terms & Conditions accepted on july 15, 2020. <a href="javascript:void(0);" style="color:dodgerblue" onClick="jQuery('#myTermModal').show();">Terms and Condition </a>
         </span>
         </div>
         <?php endif; ?>

            <div class="btn-botm">
            <!-- <button onclick="history.back();" class="back-btn">BACK </button> -->
            <?php if(isset($_GET['edit'])){ ?>
            <a href="<?php echo site_url().'/create-tickets/?edit='.$_GET['edit'].'#'; ?>" id="back_page" class="back-btn">BACK</a>
            <?php }else{ ?>
            <a href="<?php echo site_url().'/create-tickets/'; ?>" id="back_page" class="back-btn">BACK</a>
            <?php } ?>
            <?php if(isset($_POST['btnUpdate'])) { ?>
                <input type="hidden" name="edit_id" value="<?php echo $_POST['edit']?>">
				        <input type="hidden" name="eventstate" value="<?php echo $_POST['eventstate'];?>">
                <button class="submit-btn chk_subbox" type="submit" id="btnfinal" name="btnFinalUpdate" <?php echo (isset($_GET['edit'])) ? '' : 'disabled';?>><?php echo (isset($_GET['edit'])) ? 'UPDATE' : 'SUBMIT';?></button>
            <?php } else { ?>
                <button class="submit-btn chk_subbox" type="submit" id="btnfinal" name="btnFinalSubmit" <?php echo (isset($_GET['edit'])) ? '' : 'disabled';?>><?php echo (isset($_GET['edit'])) ? 'UPDATE' : 'SUBMIT';?></button>
            <?php }?>
            <!-- <button style="margin-top: 0 !important; margin-right: 9px;" class="next-btn cancel-btn" type="button">CANCEL</button> -->

            <?php
            if(isset($_GET['edit']))
            {
            ?>
                <button onClick="jQuery(this).css('width','350px');jQuery(this).text('Cancelling event changes...');setTimeout(function(){ window.location.href='<?php echo site_url()?>/manage-my-events'; }, 2000);" class="next-btn cancel-btn" id="cancelit" type="button" style="margin-top: 0 !important; margin-right: 9px;">CANCEL</button>
            <?php
            }
            else
            {
            ?>
              <button onClick="jQuery(this).css('width','350px');jQuery(this).text('Cancelling new event...');setTimeout(function(){ window.location.href='<?php echo site_url()?>/event-dashboard?canceled=yes'; }, 2000);" class="next-btn cancel-btn" id="cancelit" type="button" style="margin-top: 0 !important; margin-right: 9px;">CANCEL</button>
            <?php
            }
            ?>
            </div>
         </form>
      </div>
   </div>
   <!-- #outer-wrapper -->

	<div class="modal" id="myTermModal" role="dialog">
		<div class="modal-dialog modal-lg">
		  <div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Event Provider Terms & Conditions </h4><button type="button" class="btn btn-default" onClick="jQuery('#myTermModal').hide();">X</button>
			</div>
			<div class="modal-body">
			  <p><?php
					$args = array('page_id' => 1941);
					$loop = new WP_Query( $args );
					while($loop->have_posts()) { $loop->the_post();
					the_content(); }
				?></p>
			</div>

		  </div>
		</div>
	</div>
</div>
<!-- #main content -->

<div class="modal" id="previewModal" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Preview</h4>
            <button type="button" class="btn btn-default" onClick="jQuery('#previewModal').hide();">X</button>
         </div>
         <div class="modal-body">
            <div id="main-content">
               <div class="outer-wrapper ">
                  <div class="container container-home">
                     <div class="event-detail">
                         <?php if(isset($_SESSION['event_data'])){ ?>
                        <div class="upload-image">
                           <img id="modal_image" src="<?php echo $_SESSION['event_data']['event_image_base64']; ?>" alt="No Image Set">
                        </div>
                        <form class="event-details">
                            <!--<h3>Venue Information*</h3>-->
                           <h3 id="modal_event_title"><?php if(isset($_SESSION['event_data']['title']) && $_SESSION['event_data']['title'] !=''){ echo stripslashes($_SESSION['event_data']['title']); } ?></h3>
                         <!--  <span class="p-date">
                              <p><span id="prev_start_date"><?php// if(isset($_SESSION['event_data']['event_start_date'][0]) && $_SESSION['event_data']['event_start_date'][0] !=''){echo $_SESSION['event_data']['event_start_date'][0]; } ?></span>
                              &nbsp;to&nbsp;
                              <span id="prev_end_date"><?php// if(isset($_SESSION['event_data']['event_end_date'][0]) && $_SESSION['event_data']['event_end_date'][0] !=''){echo $_SESSION['event_data']['event_end_date'][0]; } ?></span>
                              </p>
                           </span> -->
                           <?php if(isset($_SESSION['event_data'])){
                            for ($d=0;$d<=count($_SESSION['event_data']['event_start_date']);$d++)
                            {
                            if($_SESSION['event_data']['event_start_date'][$d] != '' && $_SESSION['event_data']['event_end_date'][$d] != '')
                            {
                            ?>
                     <snap class="p-date"><?php
                     echo format_dates($_SESSION['event_data']['event_start_date'][$d], $_SESSION['event_data']['event_end_date'][$d]);
                     ?></snap><br>
                  <?php
                            }
                            }

                           }
               ?>

<?php
$country_id = $_SESSION['event_data']['country'];
$state_id = $_SESSION['event_data']['state'];
$country = $wpdb->get_row("Select * from wp_countries where id=$country_id");
$state = $wpdb->get_row("Select * from wp_states where id=$state_id");
?>
                           <p>
                              <span id="modal_venue"><?php if(isset($_SESSION['event_data']['address1']) && $_SESSION['event_data']['address1'] !=''){echo stripslashes($_SESSION['event_data']['address1']); } ?> </span> <br/>
                              <span id="modal_address"><?php if(isset($_SESSION['event_data']['streetaddress2']) && $_SESSION['event_data']['streetaddress2'] !=''){echo stripslashes($_SESSION['event_data']['streetaddress2']); } ?></span> <br/>
                              <span id="modal_city"><?php if(isset($_SESSION['event_data']['city']) && $_SESSION['event_data']['city'] !=''){echo stripslashes($_SESSION['event_data']['city']); } ?> </span>, <span id="modal_province"><?php echo $state->name; ?></span>, <span id="modal_country"><?php echo $country->name; ?></span><br/>
                              <span id="modal_zip"><?php if(isset($_SESSION['event_data']['postalcode']) && $_SESSION['event_data']['postalcode'] !=''){echo $_SESSION['event_data']['postalcode']; } ?></span>
                           </p>
                           <div class="p-description">
                              <h3>Description</h3>
                              <p id="modal_description">
								<?php if(isset($_SESSION['event_data']['description']) && $_SESSION['event_data']['description'] !=''){echo stripslashes($_SESSION['event_data']['description']); } ?>
							  </p>
                              <!-- <div class="exp-more"> Read More <span> <img src="http://webdev.snapd.com/wp-content/uploads/2019/09/down-arrow.png"></span></div>-->
                           </div>
                           <!--<div class="p-catg">Category</div>-->
                           <div id="modal_catg">
                           <?php
                           if(isset($_SESSION['event_data']['category_id'])){
                           foreach($_SESSION['event_data']['category_id'] as $cat_id){
                           $categories = $wpdb->get_results("SELECT * FROM api_category where api_cat_id =$cat_id");
                           ?>
                               <div class="p-catg" style="margin-right:5px;"><?php echo $categories[0]->title;  ?></div>
                               <?php } } ?>
                               </div>
                           <!--h2 class="f-tkt">This is a free Event</h2-->
                           <div class="contact-detail">
                                <?php
                                  $contactDetails = "";
                                  if(isset($_SESSION['event_data']['org']) && $_SESSION['event_data']['org'] != '')
                                  {
                                    $contactDetails .= '<p><b>Organization:</b> <span id="modal_org">' . stripslashes($_SESSION['event_data']['org']) . '</span></p>';
                                  }
                                  if(isset($_SESSION['event_data']['contact_name']) && $_SESSION['event_data']['exclude_name'] != 'on' && $_SESSION['event_data']['contact_name'] != '')
                                  {
                                    $contactDetails .= '<p><b>Name:</b> <span id="modal_name">' . stripslashes($_SESSION['event_data']['contact_name']) . '</span></p>';
                                  }
                                  if(isset($_SESSION['event_data']['contact_phone']) && $_SESSION['event_data']['exclude_phone'] != 'on' && $_SESSION['event_data']['contact_phone'] !='')
                                  {
                                    $contactDetails .= '<p><b>Phone:</b> <span id="modal_phone">' . stripslashes($_SESSION['event_data']['contact_phone']) . '</span></p>';
                                  }
                                  if(isset($_SESSION['event_data']['extension']) && $_SESSION['event_data']['extension'] != '' && $_SESSION['event_data']['exclude_phone'] != 'on')
                                  {
                                    $contactDetails .= '<p><b>Extension:</b> <span id="modal_extension">' . stripslashes($_SESSION['event_data']['extension']) . '</span></p>';
                                  }
                                  if(isset($_SESSION['event_data']['website_url']) && $_SESSION['event_data']['website_url'] !='')
                                  {
                                    $contactDetails .= '<p><b>Website: </b><a href="#"><span id="modal_website">' . stripslashes($_SESSION['event_data']['website_url']) . '</span></a></p>';
                                  }
                                  if(isset($_SESSION['event_data']['email']) && $_SESSION['event_data']['exclude_email'] != 'on' && $_SESSION['event_data']['email'] !='')
                                  {
                                    $contactDetails .= '<p><b>Email: </b><a href="mailto:"><span id="modal_email">' . stripslashes($_SESSION['event_data']['email']) . '</span></a></p>';
                                  }

                                  if ($contactDetails != "")
                                  {
                                    echo "<h3>Contact Details</h3>$contactDetails";
                                  }
                                ?>
                            </div>
                            <div class="attachemnts">
                                <?php if(isset($_SESSION['event_data']['logo_image'])) { ?>
                              <div class="logo-details">
                                 <p class="up-logo">

										<img id="modal_logo" src="<?php echo 'data:'.$_SESSION['event_data']['logo_image_type'].';base64,'.$_SESSION['event_data']['logo_image_base64'] ?>" alt="No Logo uploaded">

								 </p>
								 </div>
								 <?php	} ?>
								 <?php if(isset($_SESSION['event_data']['attach_image']) && $_SESSION['event_data']['attach_image'] !=''){ ?>
                                 <p class="up-attach">
									 <h3>Attachment</h3>
									 <!--<span>Include a single PDF for all instructions, waivers, map etc...</span> -->
									 <a class="btn-downlaod" id="modal_attachment"  href="<?php echo 'data:application/pdf;base64,'.$_SESSION['event_data']['attach_image_base64'] ?>" download="<?php echo $_SESSION['event_data']['attach_image']; ?>" target="_blank"><i class="fa fa-download"></i> Download</a>
									 <p><span>Don’t have Adobe Reader – </span><a href="https://get.adobe.com/reader/" target="blank">click here</a> to download.</p>
								 </p><?php } ?>

                              <div class="map-details" <?php echo 'style="float: left !important;margin-left: 0px !important;"'?>>
                                 <h3>Map Details</h3>
								 <div id="preview_map">
									<?php
									$mapsrc= (isset($_SESSION['event_data']['lat'])&& isset($_SESSION['event_data']['long']))?"https://webdev.snapd.com/map.php?lat=".$_SESSION['event_data']['lat']."&lng=".$_SESSION['event_data']['long']:"https://webdev.snapd.com/map.php?lat=56.1304&lng=-106.346771";
									?>
									<iframe class="mapIframe" src="<?php echo $mapsrc; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
								 </div>
                              </div>
                           </div>
                        </form>
                        <?php }
                        ?>
                        <?php if(isset($_SESSION['event_edit_data'])){
                        $ch      = curl_init(API_URL.'events/'.$_GET['edit']);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                         'Content-Type: application/json',
                         'Authorization: ' . $token
                        ));
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $event = json_decode($response)->event;
                        //print_r($event->files[0]);
                        $metadata = unserialize($_SESSION['event_edit_data']['metadata']);
                        ?>
                        <div class="upload-image">
                           <img id="modal_image" src="<?php echo isset($_SESSION['event_edit_data']['filetouploadname']) ? 'data:image/'.pathinfo($_SESSION['event_edit_data']['filetouploadname'], PATHINFO_EXTENSION).';base64,'.$_SESSION['event_edit_data']['filetouploadname_base64'] : "https://storage.googleapis.com/".$event->files[0]->bucket."/".$event->files[0]->filename ;  ?>" alt="No Image Set">
                        </div>
                        <form class="event-details">
                            <!--<h3>Venue Information*</h3>-->
                           <h3 id="modal_event_title"><?php if(isset($_SESSION['event_edit_data']['name']) && $_SESSION['event_edit_data']['name'] !=''){ echo stripslashes($_SESSION['event_edit_data']['name']); } ?></h3>
                         <!--  <span class="p-date">
                              <p><span id="prev_start_date"><?php// if(isset($_SESSION['event_edit_data']['event_start_date'][0]) && $_SESSION['event_edit_data']['event_start_date'][0] !=''){echo $_SESSION['event_edit_data']['event_start_date'][0]; } ?></span>
                              &nbsp;to&nbsp;
                              <span id="prev_end_date"><?php// if(isset($_SESSION['event_edit_data']['event_end_date'][0]) && $_SESSION['event_edit_data']['event_end_date'][0] !=''){echo $_SESSION['event_edit_data']['event_end_date'][0]; } ?></span>
                              </p>
                           </span> -->
                           <?php  if(isset($_SESSION['event_edit_data']['dateRanges'])){
                                    foreach($_SESSION['event_edit_data']['dateRanges'] as $key=>$val) {
                                $start_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][$key][0]));
                                $end_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][$key][1]));
                                if(date("Y-m-d", strtotime($start_date)) == date("Y-m-d", strtotime($end_date))) {
                                $daterange = $start_date." to ".date("h:i a", strtotime($end_date));
                                }else{
                                $daterange = $start_date." to ".$end_date;
                                }
                                ?>
                     <snap class="p-date"><?php echo $daterange; ?></snap><br>
                  <?php
                            }
                            }


               ?>

<?php
$country_id = $_SESSION['event_edit_data']['country_id'];
$state_id = $_SESSION['event_edit_data']['province_id'];
$country = $wpdb->get_row("Select * from wp_countries where id=$country_id");
$state = $wpdb->get_row("Select * from wp_states where id=$state_id");
?>
                           <p>
                              <span id="modal_venue"><?php if(isset($_SESSION['event_edit_data']['location']) && $_SESSION['event_edit_data']['location'] !=''){echo stripslashes($_SESSION['event_edit_data']['location']); } ?> </span> <br/>
                              <span id="modal_address"><?php if(isset($_SESSION['event_edit_data']['address2']) && $_SESSION['event_edit_data']['address2'] !=''){echo stripslashes($_SESSION['event_edit_data']['address2']); } ?></span> <br/>
                              <span id="modal_city"><?php if(isset($_SESSION['event_edit_data']['city']) && $_SESSION['event_edit_data']['city'] !=''){echo stripslashes($_SESSION['event_edit_data']['city']); } ?> </span>, <span id="modal_province"><?php echo $state->name; ?></span>, <span id="modal_country"><?php echo $country->name; ?></span><br/>
                              <span id="modal_zip"><?php if(isset($_SESSION['event_edit_data']['postalcode']) && $_SESSION['event_edit_data']['postalcode'] !=''){echo $_SESSION['event_edit_data']['postalcode']; } ?></span>
                           </p>
                           <div class="p-description">
                              <h3>Description</h3>
                              <p id="modal_description">
								<?php if(isset($_SESSION['event_edit_data']['description']) && $_SESSION['event_edit_data']['description'] !=''){echo stripslashes($_SESSION['event_edit_data']['description']); } ?>
							  </p>
                              <!-- <div class="exp-more"> Read More <span> <img src="http://webdev.snapd.com/wp-content/uploads/2019/09/down-arrow.png"></span></div>-->
                           </div>
                           <!--<div class="p-catg">Category</div>-->
                           <div id="modal_catg">
                           <?php
                           if(isset($_SESSION['event_edit_data']['categories'])){
                           foreach($_SESSION['event_edit_data']['categories'] as $cat_id){
                           $categories = $wpdb->get_results("SELECT * FROM api_category where api_cat_id =$cat_id");
                           ?>
                               <div class="p-catg" style="margin-right:5px;"><?php echo $categories[0]->title;  ?></div>
                               <?php } } ?>
                               </div>
                           <!--h2 class="f-tkt">This is a free Event</h2-->
                           <div class="contact-detail">
                             <?php
                               $contactDetails = "";
                               if(isset($metadata['org']) && $metadata['org'] != '')
                               {
                                 $contactDetails .= '<p><b>Organization:</b> <span id="modal_org">' . stripslashes($metadata['org']) . '</span></p>';
                               }
                               if(isset($_SESSION['event_edit_data']['contact_name']) && $metadata['exclude_name'] != 'on' && $_SESSION['event_edit_data']['contact_name'] != '')
                               {
                                 $contactDetails .= '<p><b>Name:</b> <span id="modal_name">' . stripslashes($_SESSION['event_edit_data']['contact_name']) . '</span></p>';
                               }
                               if(isset($_SESSION['event_edit_data']['contact_phone']) && $metadata['exclude_phone'] != 'on' && $_SESSION['event_edit_data']['contact_phone'] !='')
                               {
                                 $contactDetails .= '<p><b>Phone:</b> <span id="modal_phone">' . stripslashes($_SESSION['event_edit_data']['contact_phone']) . '</span></p>';
                               }
                               if(isset($metadata['extension']) && $metadata['extension'] != '' && $metadata['exclude_phone'] != 'on')
                               {
                                 $contactDetails .= '<p><b>Extension:</b> <span id="modal_extension">' . stripslashes($metadata['extension']) . '</span></p>';
                               }
                               if(isset($_SESSION['event_edit_data']['contact_url']) && $_SESSION['event_edit_data']['contact_url'] !='')
                               {
                                 $contactDetails .= '<p><b>Website: </b><a href="#"><span id="modal_website">' . stripslashes($_SESSION['event_edit_data']['contact_url']) . '</span></a></p>';
                               }
                               if(isset($_SESSION['event_edit_data']['contact_email']) && $metadata['exclude_email'] !='on' && $_SESSION['event_edit_data']['contact_email'] !='')
                               {
                                 $contactDetails .= '<p><b>Email: </b><a href="mailto:"><span id="modal_email">' . stripslashes($_SESSION['event_edit_data']['contact_email']) . '</span></a></p>';
                               }

                               if ($contactDetails != "")
                               {
                                 echo "<h3>Contact Details</h3>$contactDetails";
                               }
                             ?>
                            </div>
                            <div class="attachemnts">
                                <?php if(isset($_SESSION['event_edit_data']['logo_image_base64'])) { ?>
                              <div class="logo-details">
                                 <p class="up-logo">

										<img id="modal_logo" src="<?php echo 'data:'.pathinfo($_SESSION['event_edit_data']['logo_image'], PATHINFO_EXTENSION).';base64,'.$_SESSION['event_edit_data']['logo_image_base64'] ?>" alt="No Logo uploaded">

								 </p>
								 </div>
								 <?php	}else{
								     if(isset($event) && count($event->files) > 0) {
                                     $i=0;
                                     foreach($event->files as $row) {
                                     if($row->type == 'logo') {
                                     ?>
								 <div class="logo-details">
                                 <p class="up-logo">
                                    <img id="modal_logo" src="https://storage.googleapis.com/<?php echo $row->bucket?>/<?php echo $row->filename;?>" alt="No Logo uploaded">
								 </p>
								 </div>
								 <?php }
									}
									} } ?>
								 <?php if(isset($_SESSION['event_edit_data']['attach_image']) && $_SESSION['event_edit_data']['attach_image'] !=''){ ?>
                                 <p class="up-attach">
									 <h3>Attachment</h3>
									 <!--<span>Include a single PDF for all instructions, waivers, map etc...</span> -->
									 <a class="btn-downlaod" id="modal_attachment"  href="<?php echo 'data:application/pdf;base64,'.$_SESSION['event_edit_data']['attach_image_base64'] ?>" download="<?php echo $_SESSION['event_edit_data']['attach_image']; ?>" target="_blank"><i class="fa fa-download"></i> Download</a>
									 <p><span>Don’t have Adobe Reader – </span><a href="https://get.adobe.com/reader/" target="blank">click here</a> to download.</p>
								 </p><?php }else{
								 if(isset($event) && count($event->files) > 0) {
                                     $i=0;
                                     foreach($event->files as $file) {
                                   if($file->type == 'Pdf file') {
								 ?>
								 <p class="up-attach">
									 <h3>Attachment</h3>
									 <!--<span>Include a single PDF for all instructions, waivers, map etc...</span> -->
									 <a class="btn-downlaod" id="modal_attachment"  href="<?php echo isset($file) ? 'https://storage.cloud.google.com/' . $file->bucket . '/' . $file->filename : '#' ?>" download="<?php echo $file->filename ?>" target="_blank"><i class="fa fa-download"></i> Download</a>
									 <p><span>Don’t have Adobe Reader – </span><a href="https://get.adobe.com/reader/" target="blank">click here</a> to download.</p>
								 </p>
								 <?php } } } } ?>

                              <div class="map-details" <?php echo 'style="float: left !important;margin-left: 0px !important;"'?>>
                                 <h3>Map Details</h3>
								 <div id="preview_map">
									<?php
									$mapsrc= isset($event->lat) && isset($event->long) ? "https://webdev.snapd.com/map.php?lat=".$event->lat."&lng=".$event->long : "https://webdev.snapd.com/map.php?lat=56.1304&lng=-106.346771";
									?>
									<iframe class="mapIframe" src="<?php echo $mapsrc; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
								 </div>
                              </div>
                           </div>
                        </form>
                        <?php
                    // echo "<pre>"; print_r($_SESSION['event_edit_data']);// print_r($event);
                        } ?>


                        <!--	<div class="help-btn"><i class="fa fa-question"></i> Need Help? <a href="#">Visit our support site for answers</a></div> -->
                     </div>
                  </div>
                  <!-- #container -->

               </div>
               <!-- #outer-wrapper -->
            </div>
            <!-- #main content -->
         </div>
      </div>
   </div>
</div>
<div class="modal" id="loadingModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="width: 220px !important;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="email-confomation">
                    <p class="mail-img" style="padding: 0px;"><img src="<?php echo site_url(); ?>/wp-content/uploads/loading.gif"></p>
					<p id="modal_loader_text"></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(isset($_GET['edit'])){ ?>
<script>	jQuery("#subm_form").submit(function(event){
		//if($("#subm_form").valid()){
			jQuery('#modal_loader_text').text('Updating event...');
			jQuery('#loadingModal').show();
	//	}
	});</script>
<?php }else{ ?>
<script>	jQuery("#subm_form").submit(function(event){
		//if($("#subm_form").valid()){
			jQuery('#modal_loader_text').text('Submitting event...');
			jQuery('#loadingModal').show();
	//	}
	});</script>
<?php } ?>
<script>
 jQuery(document).on('click', '.chk_subbox', function () {
   if(jQuery('#sendphoto').prop('checked') == true){
	   jQuery('.pay_date').show();
   }else{
	jQuery('.pay_date').hide();
   }
   if(jQuery('#makeprivate').prop('checked') == true){
	   //jQuery('.web-url').show();
   }else{
	//jQuery('.web-url').hide();
   }

   if(jQuery('#accept_terms').prop('checked') == true){
	   /* jQuery('#tc-err').hide(); */
	   jQuery('#btnfinal').prop('disabled', false);
   }else{
		/* jQuery('#tc-err').show(); */
		jQuery('#btnfinal').prop('disabled', true);
   }
});

$.datetimepicker.setDateFormatter('moment');
jQuery("#pay_datepicker").datetimepicker({
	step: 15,
	//format:'M d, Y h:i a',
	//formatTime:	'h:i a',
  format: 'MMM D YYYY h:mm a',
  formatTime: 'h:mma',
  formatDate: 'YYYY-MM-DD',
	//minDate:'-1970/01/02',
	closeOnDateSelect:false,
	closeOnTimeSelect:true,
	validateOnBlur: false,
});

    jQuery("#back_page").on('click',function(){
		    jQuery('#modal_loader_text').text('Back to previous page...');
			jQuery('#loadingModal').show();
	});
	jQuery("#cancelit").on('click',function(){
		    jQuery('#modal_loader_text').text('In progress...');
			jQuery('#loadingModal').show();
	});
</script>
<?php get_footer(); ?>
