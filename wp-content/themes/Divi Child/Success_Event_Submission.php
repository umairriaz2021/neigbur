<?php
/*
Template Name: Success– Event Submission
*/
global $wpdb;
   $token   =  $_SESSION['Api_token'];

   if(isset($_GET['event_id']) && $_GET['event_id'] != '') {

       $event_id = $_GET['event_id'];

       $ch   = curl_init(API_URL.'/events/'.$event_id);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Authorization: ' . $token
       ));
       $result = curl_exec($ch);
       curl_close($ch);
       $apirespons=json_decode($result);
   
       if($apirespons->success) {

           $event_detail = $apirespons->event;
           $metadata = unserialize($event_detail->metadata);

          if(isset($metadata['file_id'])) {

           //echo API_URL.'files/'.$metadata['file_id'];die;

               $ch   = curl_init(API_URL.'files/'.$metadata['file_id']);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/pdf',
                   'Authorization: ' . $token
               ));
               $result1 = curl_exec($ch);
               curl_close($ch);
               $fileresponse=json_decode($result1);

               if($fileresponse->success) {

                   $file = $fileresponse->file;
               }
          }
       }
   }
  // echo "<pre>"; print_r($event_detail); print_r($file); die();
  //if(isset($_POST['share']))
  {
        $eve_img = 'https://storage.googleapis.com/' . $event_detail->files[0]->bucket . '/' . $event_detail->files[0]->filename;
        $eve_img = "<img src='$eve_img' width='250' height='auto'>";
        $eve_name = $event_detail->name;
        foreach($event_detail->event_dates as $edate) {
            if($edate->start_date == $edate->end_date) {
              $start_d = date('M d, Y h:i a', strtotime($edate->start_date));
              $end_d =  date('h:i a', strtotime($edate->end_date));
            } else {
              $start_d = date('M d, Y h:i a', strtotime($edate->start_date));
              $end_d =  date('M d, Y h:i a', strtotime($edate->end_date));
            }
        }
        $add1 = $event_detail->location;
        $add2 = $event_detail->address2;
        $city = $event_detail->city;
        //$province = $state->name;
        $province = $event_detail->province->province_name;
        //$country = $country->name;
        $country = $event_detail->country->country_name;
        $pincode = $event_detail->postalcode;
        $to = $event_detail->contact_email;

        //$emailtemplate   = get_post(2094);
        $emailtemplate = get_post(2432);
        $emailoutput =  apply_filters('the_content', $emailtemplate->post_content );
        $eventURL = site_url() . "/view-event/" . $event_detail->id;
        $logo_img = 'https://webdev.snapd.com/wp-content/uploads/2019/09/neighbur_logo.png';
        $eventURL = "<a href='$eventURL'>$eventURL</a>";
        $contactName = $event_detail->contact_name;

        // send to account holder, not event contact
        $to = $_SESSION['userdata']->email;
        $contactName = ucfirst($_SESSION['userdata']->first.' '.$_SESSION['userdata']->last);

        $emailContent= str_replace(array('[event_image]','[event_URL]','[contact_name]', '[event_date]','[end_date]','[event_name]','[address_1]','[address_2]','[city]','[province]','[country]','[postal_code]'), array($eve_img,$eventURL,$contactName,$start_d,$end_d,$eve_name,$add1,$add2,$city,$province,$country,$pincode), $emailoutput);
        $subject = $emailtemplate->post_title;
        $message = '<DOCTYPE! html>
                        <html>
                            <head>
                                <title>
                                    ' . $subject . '
                                </title>
                            </head>
                            <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
                                <div class="container" style="margin:0 auto;max-width:1080px;">
                                    <img src="'.$logo_img.'" width="100px" height="50px">
                                    <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
                                    '.$emailContent.'
                              </div>
                            </body>
                        </html>
                    </DOCTYPE>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Neighbur Inc.;<invisionsqa@gmail.com>' . "\r\n";


function my_phpmailer_example( $phpmailer ) {
    $phpmailer->isSMTP();     
    $phpmailer->Host = 'smtp.gmail.com';
    $phpmailer->SMTPAuth = true; // Ask it to use authenticate using the Username and Password properties
    $phpmailer->Port = 465;
    $phpmailer->Username = 'invisionsqa@gmail.com';
    $phpmailer->Password = 'Pakistan.2021';
 
    // Additional settings…
    //$phpmailer->SMTPSecure = 'tls'; // Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587
    //$phpmailer->From = "you@yourdomail.com";
    //$phpmailer->FromName = "Your Name";
}
add_action( 'phpmailer_init', 'my_phpmailer_example' );
        wp_mail($to, $subject, $message, $headers);
/*$mailResult = false;
$mailResult = wp_mail('invisionsqa@yopmail.com', 'test if mail works', 'hurray', $headers );
echo $mailResult;*/

/*if(wp_mail('invisionsqa@yopmail.com', 'test if mail works', 'hurray', $headers ))
{
echo "sending mail test";
}
else
{
     echo "not";
}*/

  }


get_header(); ?>


				
				
				
				<div id="tempdata1" style="display:none"><?php echo date('Y-m-d h:ia', strtotime($event_detail->start)); ?></div>
				<div id="tempdata2" style="display:none"><?php echo date('Y-m-d h:ia', strtotime($event_detail->end)); ?></div>
				<div id="tempdata3" style="display:none"><?php echo ucfirst($event_detail->name); ?></div>
				<div id="tempdata4" style="display:none"><?php echo $event_detail->location; ?></div>
				<div id="tempdata5" style="display:none"><?php echo $event_detail->address2; ?></div>
				<div id="tempdata6" style="display:none"><?php echo $event_detail->city; ?></div>
				<div id="tempdata7" style="display:none"><?php echo $state->state_code; ?></div>
				<div id="tempdata8" style="display:none"><?php echo $country->name; ?></div>
				<div id="tempdata9" style="display:none"><?php echo $event_detail->postalcode; ?></div>
				<div id="tempdata10" style="display:none"><?php echo site_url(); ?>."/wp-content/uploads/2019/08/r1.jpg"</div>
				<div id="tempdata11" style="display:none"><?php echo $event_id; ?></div>
				<div id="tempdata12" style="display:none"><?php echo $ticktype; ?></div>
               <h3>
                <script async src="https://static.addtoany.com/menu/page.js"></script>
                <script>
                var tempd1 = jQuery('#tempdata1').html();
                var tempd2 = jQuery('#tempdata2').html();
                var tempd3 = jQuery('#tempdata3').html();
                var tempd4 = jQuery('#tempdata4').html();
                var tempd5 = jQuery('#tempdata5').html();
                var tempd6 = jQuery('#tempdata6').html();
                var tempd7 = jQuery('#tempdata7').html();
                var tempd8 = jQuery('#tempdata8').html();
                var tempd9 = jQuery('#tempdata9').html();
                var tempd10 = jQuery('#tempdata10').html();
                var tempd11 = jQuery('#tempdata11').html();
                var tempd12 = jQuery('#tempdata12').html();
                
                a2a_config.onclick = 1;
                var a2a_config = a2a_config || {};
                a2a_config.templates = a2a_config.templates || {};
                a2a_config.templates.email = {
                    subject: "Neighbur Event For You",
					 //subject: "Check this out: ${title}",
                    body:'This Neighbur event has been recommended for you... \n \n'+tempd3+'\n \n '+tempd1+' to ' +tempd2+' \n \nThe Event Venue \n '+tempd4+'\n '+tempd6+', '+tempd5+'\n '+tempd9+' \n \n You can view this event anytime at https://webdev.snapd.com/view-event/'+tempd11+'/'
                };

                </script></h3>
<!-- http://webdev.snapd.com/success-event-submission/?event_id=XXX -->
    <div id="main-content">
     <div class="outer-wrapper">
           <div class="container container-home">
              <div class="edit-event">
             	<h3 class="h3-title">Congratulations! Your event has been submitted. </h3>
    			 	<div class="event-type">
                  		<h3>Promote your event now...</h3>
                        <p> Share your event details on your social media pages</p>
                    </div>
                	  <div class="event-detail">
                			<div  class="upload-image">
                			   <img src="https://storage.googleapis.com/<?php echo $event_detail->files[0]->bucket.'/'.$event_detail->files[0]->filename ?>">
    
                			</div>
                            	<div class="event-details">
                            				<h3><?php echo isset($event_detail) ? $event_detail->name : '';?></h3>
                            			       	<?php foreach($event_detail->event_dates as $edate)
                                          {
                                            echo "<b class='p-date'>" . format_dates($edate->start_date, $edate->end_date) . "</b>";
                                          }

                     if(isset($event_detail)) {

                        $country = $wpdb->get_row("Select * from wp_countries where  id = $event_detail->country_id");
                        $state = $wpdb->get_row("select * from wp_states where id = $event_detail->province_id");
                     }
                     ?>
                  <p><?php echo isset($event_detail) ? !empty($event_detail->location) ? $event_detail->location.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? !empty($event_detail->address2) ? $event_detail->address2.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? $event_detail->city : '';?>, <?php echo isset($event_detail) ? $state->name : '';?>, <?php echo isset($event_detail) ? $country->name : '';?><br/>
                     <?php echo isset($event_detail) ? $event_detail->postalcode : '';?>
                  </p>
                            				<p>
                            				<!--<button onclick="jQuery('#shareModal').show();">SHARE</button>-->
                            					<div class="a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="<?php echo site_url().'/view-event/'.$event_id; ?>" data-a2a-title="<?php echo isset($event_detail) ? $event_detail->name : '';?>">
                                                    <a class="a2a_button_facebook"></a>
                                                   <a class="a2a_button_twitter"></a>
                                                    <a class="a2a_button_pinterest"></a>
                                                    <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                                </div>

                                                <script async src="https://static.addtoany.com/menu/page.js"></script>
                                          </p>
                                 </div>

                    	   </div>
                    	   <div class="events_links">
                        				<h3>Your Event address is...</h3>

                        	        	<p class="copy-event-link">
                        	        	<a id="eveUrl" href="<?php echo site_url(); ?>/view-event/<?php echo $event_id;?>"><?php echo site_url(); ?>/view-event/<?php echo $event_id;?></a>
                        					<button class="cpy" onclick="copyEventurl('#eveUrl')">COPY</button><span class="copied" style="display:none"></span>
                        				  </p>
                        			</div>
                        <em style="float: left;padding-top: 13px; width: 100%;">Please use this event ID when contacting our support department for event related queries at <a target="_blank" href="https://support.neighbur.com/portal/home">support.neighbur.com</a></em>
                                  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; else: ?>
                                     <p>Sorry, no posts matched your criteria.</p>
                                    <?php endif; ?>

	   </div>
      </div>   <!-- # outer-wrapper-->
    </div> <!-- #main content -->

<div class="modal" id="shareModal" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Share Event</h4>
            <button type="button" class="btn btn-default" onClick="jQuery('#shareModal').hide();">X</button>
         </div>
         <div class="modal-body">
            <div id="main-content">
                <form method="post">
                <Button type="submit" name="share" class="btn-fit" onclick="share_event()"> Share using Gmail </button>
                <Button class="btn-fit">Share using Facebook </button>
                </form>
            </div>
         </div>
      </div>
   </div>
</div>

    <?php get_footer(); ?>
    <style>
    .modal-open {
   overflow: hidden;
   }
   .btn-fit{
    width:100%;
    border-radius: 3px;
    background: #f1f1f1;
    border: 1px solid #ccc;
    padding: 7px 0px;
    font-size: 17px;
    margin-right: 14px;
    font-weight: 600;
    margin-top: 10px;
   }
   .modal {
   background: #00000054;
   position: fixed;
   top: 0;
   right: 0;
   bottom: 0;
   left: 0;
   z-index: 2000000;
   display: none;
   overflow: hidden;
   outline: 0;
   }
   .modal-open .modal {
   overflow-x: hidden;
   overflow-y: auto;
   }
   .modal-dialog {
   position: relative;
   width: auto;
   margin: 0.5rem;
   pointer-events: none;
   }
   .modal.fade .modal-dialog {
   transition: -webkit-transform 0.3s ease-out;
   transition: transform 0.3s ease-out;
   transition: transform 0.3s ease-out, -webkit-transform 0.3s ease-out;
   -webkit-transform: translate(0, -25%);
   transform: translate(0, -25%);
   }
   .btn-default {
   color: #fefefe;
   background-color: #333;
   border-color: #000;
   padding: 4px 11px;
   font-size: 18px;
   border-radius: 30px;
   border: 0;
   }
   @media screen and (prefers-reduced-motion: reduce) {
   .modal.fade .modal-dialog {
   transition: none;
   }
   }
   .modal.show .modal-dialog {
   -webkit-transform: translate(0, 0);
   transform: translate(0, 0);
   }
   .modal-dialog-centered {
   display: -ms-flexbox;
   display: flex;
   -ms-flex-align: center;
   align-items: center;
   min-height: calc(100% - (0.5rem * 2));
   }
   .modal-content {
   position: relative;
   display: -ms-flexbox;
   display: flex;
   -ms-flex-direction: column;
   flex-direction: column;
   width: 100%;
   pointer-events: auto;
   background-color: #fff;
   background-clip: padding-box;
   border: 1px solid rgba(0, 0, 0, 0.2);
   border-radius: 0.3rem;
   outline: 0;
   }
   .modal-backdrop {
   position: fixed;
   top: 0;
   right: 0;
   bottom: 0;
   left: 0;
   z-index: 1040;
   background-color: #000;
   }
   .modal-backdrop.fade {
   opacity: 0;
   }
   .modal-backdrop.show {
   opacity: 0.5;
   }
   .modal-header {
   display: -ms-flexbox;
   display: flex;
   -ms-flex-align: start;
   align-items: flex-start;
   -ms-flex-pack: justify;
   justify-content: space-between;
   padding: 1rem;
   border-bottom: 1px solid #e9ecef;
   border-top-left-radius: 0.3rem;
   border-top-right-radius: 0.3rem;background: #333333;
   }
   .modal-header .close {
   padding: 1rem;
   margin: -1rem -1rem -1rem auto;
   }
   .modal-title {
   margin-bottom: 0;
   line-height: 1.5;
   padding-bottom: 0;
   font-size: 26px;
   font-weight: bold;
   color: #fff !important;
   }
   .modal-dialog.modal-lg {
   margin-top: 10%;
   }
   h4.modal-title {
   width: 100%;
   }
   .modal-body {
   position: relative;
   -ms-flex: 1 1 auto;
   flex: 1 1 auto;
   padding: 1.3rem;
   max-height: 600px;
   overflow-x: hidden;;
   }
   .modal-footer {
   display: -ms-flexbox;
   display: flex;
   -ms-flex-align: center;
   align-items: center;
   -ms-flex-pack: end;
   justify-content: flex-end;
   padding: 1rem;
   border-top: 1px solid #e9ecef;
   }
   .modal-footer > :not(:first-child) {
   margin-left: .25rem;
   }
   .modal-footer > :not(:last-child) {
   margin-right: .25rem;
   }
   .modal-scrollbar-measure {
   position: absolute;
   top: -9999px;
   width: 50px;
   height: 50px;
   overflow: scroll;
   }
   @media (min-width: 576px) {
   .modal-dialog {
   max-width: 500px;
   margin: 1.75rem auto;
   }
   .modal-dialog-centered {
   min-height: calc(100% - (1.75rem * 2));
   }
   .modal-sm {
   max-width: 300px;
   }
   }
   @media (min-width: 992px) {
   .modal-lg {
   max-width: 450px;
   }
   }
    span.copied {
    line-height: 34px;
    padding: 3px 16px;
    border-radius: 3px;
    color: #257914;
    font-size: 18px;
    background: #e7ffe3;
    position: relative;
    float: left;
    margin-top: 9px;
    }</style>
    <script>
        function copyEventurl(e) {
                var $temp = jQuery("<input>");
                jQuery("body").append($temp);
                $temp.val(jQuery(e).text()).select();
                document.execCommand("copy");
                $temp.remove();

				jQuery(e).parent().find('.copied').show().text('Event address copied to clipboard');
            }
    </script>
