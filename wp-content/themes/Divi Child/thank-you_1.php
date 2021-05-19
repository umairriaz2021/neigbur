<?php 
/*
Template Name: Thank You 

*/


global $wpdb;
$token   =  $_SESSION['Api_token'];
$ch = curl_init(API_URL.'orders/'.$_GET['order_id']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);
get_header(); ?>

    <div id="main-content">


       <div class="outer-wrapper">
           <div class="container container-home">
      <?php
           while ( have_posts() ) : the_post(); ?> 
           <div class="entry-content-page">
            <?php the_content(); ?>
           </div>

    <?php
    endwhile;
    wp_reset_query(); 
    ?>
                  
      <div class="download-link">
        <p class="return-home2"><a href="<?php echo site_url(); ?>/my-tickets/">View My Tickets</a></p>
      </div>
	  </div>
      </div>   <!-- # outer-wrapper-->
    </div> <!-- #main content --> 
    
    <?php
    

/* echo "<pre>"; print_r($apirespons); die;  */


           $order_detail = $apirespons->ticketOrder;
          $emailtemplate = get_post(2466);
        $emailoutput =  apply_filters('the_content', $emailtemplate->post_content );
       // $eventURL = site_url() . "/view-event/" . $event_detail->id;
      //$eventURL = "<a href='$eventURL'>$eventURL</a>";
        //$contactName = $event_detail->contact_name;

        // send to account holder, not event contact
        $to = $_SESSION['userdata']->email;
        //$contactName = ucfirst($_SESSION['userdata']->first.' '.$_SESSION['userdata']->last);
        $contactName = $apirespons->ticketOrder->user->first.' '.$apirespons->ticketOrder->user->last;
       /* $start_d= format_dates($apirespons->ticketOrder->event->start);
        $end_d= format_dates($apirespons->ticketOrder->event->end);*/
        
         if (floatval($apirespons->ticketOrder->total) == 0)
              $price= "free";
					else
						$price =  number_format($apirespons->ticketOrder->total, 2);
        $order_id = $apirespons->ticketOrder->id;
        $order_qty = $apirespons->ticketOrder->ticket_order_item->qantity;
        $location =  $apirespons->ticketOrder->event->address1.$apirespons->ticketOrder->event->address2;
        $venue =$apirespons->ticketOrder->event->location;
        $purchase_date = date('l F j, Y',strtotime($apirespons->ticketOrder->create_date));
        $time = date('g:ia',strtotime($apirespons->ticketOrder->create_date));
        $event_name = $apirespons->ticketOrder->event->name; 
        $eventURL = site_url() . "/view-event/" . $order_detail->event->id;
          
        $emailContent= str_replace(array('[user_name]','[event_URL]','[event_venu]', '[event_name]','[event_time]','[purchase_date]','[event_address]','[order_id]','[order_qnty]','[order_total]'), 
        array($contactName,$eventURL,$venue,$event_name,$time,$purchase_date,$location,$order_id,$order_qty,$price), $emailoutput);
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
                                    <img src="'.site_url().'/wp-content/themes/Divi Child/img/neighbur_logo.png">
                                    <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
                                    '.$emailContent.'
                              </div>
                            </body>
                        </html>
                    </DOCTYPE>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Neighbur Inc.;<invisionsqa@gmail.com>' . "\r\n";


/*function my_phpmailer_example( $phpmailer ) {
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
        wp_mail($to, $subject, $message, $headers);*/
    
        ?>

    <?php get_footer(); ?>