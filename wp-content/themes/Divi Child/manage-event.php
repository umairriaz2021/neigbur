<?php
/*
Template Name: Manage My Events
*/


if (isset($_SESSION['userdata'])) {
    $userdata = $_SESSION['userdata'];
} else {
    wp_redirect(site_url() . '?page_id=187');
    exit;
}

global $wpdb;
$token = $_SESSION['Api_token'];
$event_state = '';
$success = '';
$err = '';
//print_r($token);

if (isset($_GET['action']) && $_GET['action'] == 'invalid') {
    $err = 'Invalid Event Id.';
}

if(isset($_GET['cancelevent']) && $_GET['cancelevent'] != ''){

    $event_id = $_GET['cancelevent'];
    $reason = $_GET['reason'];

    $pdata = array(
        'canceled' => 1
    );

    $payload = json_encode($pdata);
    $ch = curl_init(API_URL . 'events/' . $event_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($payload),
        'Authorization: ' . $token
    ));

    $result = curl_exec($ch);
    $response = json_decode($result);



       $ch   = curl_init(API_URL . '/events/' . $event_id);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: ' . $token
   ));
   $result = curl_exec($ch);
   curl_close($ch);
   $apirespons = json_decode($result);
   $event_detail = $apirespons->event;

   $country = $wpdb->get_row("Select * from wp_countries where  id = $event_detail->country_id");
   $state = $wpdb->get_row("select * from wp_states where id = $event_detail->province_id");

   $eve_img="https://storage.googleapis.com/".$event_detail->files[0]->bucket."/".$event_detail->files[0]->filename;
   $to = $event_detail->contact_email;
   $ownertemplate   = get_post(2447);
   $emailowneroutput =  apply_filters('the_content', $ownertemplate->post_content );
   $owner_cncl_Content = str_replace(array('[[first_name]]','[[eve_img]]','[[eve_name]]','[[start_d]]','[[end_d]]','[[address_1]]','[[address_2]]','[[city]]','[[province]]','[[country]]','[[pin_code]]'), array(ucfirst($userdata->first),$eve_img,$event_detail->name,$event_detail->start,$event_detail->end,$event_detail->location,$event_detail->address2,$event_detail->city,$state->name,$country->name,$event_detail->postalcode), $emailowneroutput);
   $subject = $ownertemplate->post_title;
   $message = '<DOCTYPE! html>
                        <html>
                            <head>
                                <title>

                                </title>
                            </head>
                            <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
                                <div class="container" style="margin:0 auto;max-width:1080px;">
                                    <div style="width:100%;background: #e4e4e4;padding-left: 15px;padding-top: 10px;padding-bottom: 8px;">
                                        <img src="'.site_url().'/wp-content/uploads/2020/01/new-logo.png" style="width: 200px;">
                                        </div>
                                        <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
                                    '.$owner_cncl_Content.'
                              </div>
                            </body>
                        </html>
                    </DOCTYPE>';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Neighbur Inc.;<no-reply@snapd.com>' . "\r\n";
   // echo $message;
    wp_mail($to, $subject, $message, $headers);



   $ch = curl_init(API_URL . '/admin/orders?eventId=' . $event_id);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: ' . $token
   ));
   $result = curl_exec($ch);
   curl_close($ch);
   $apirespons = json_decode($result);
   $orders = $apirespons->orders;
   //echo "<pre>"; //print_r($orders);
   //print_r($orders[0]->user->email);
   $dts = array();
   $i = 0;
   foreach($orders as $d){
       foreach($d->ticket_order_item as $ti){
           foreach($ti->tickets as $tks){
       //print_r($tks);
       if($tks->firstname !='' && $tks->email != '' && $tks->email != ''){
       $dts[$i] = array('f_name'=>$tks->firstname,'l_name'=>$tks->lastname,'email'=>$tks->email);
       $i++;
       }}}}
//print_r($dts);


    for($l=0;$l<=count($dts);$l++){
     $thf_name = ucfirst($dts[$l][f_name]);
     $user_country = $wpdb->get_row("Select * from wp_countries where  id = $userdata->country_id");
     $user_state = $wpdb->get_row("select * from wp_states where id = $userdata->province_id");
     $to = $dts[$l][email];
     $thtemplate   = get_post(2445);
     $emailthoutput =  apply_filters('the_content', $thtemplate->post_content );
     $th_cncl_Content = str_replace(array('[[first_name]]','[[eve_img]]','[[eve_name]]','[[start_d]]','[[end_d]]','[[address_1]]','[[address_2]]','[[city]]','[[province]]','[[country]]','[[pin_code]]','[[cancellation_message]]','[[o_name]]','[[o_address]]','[[o_city]]','[[o_province]]','[[o_country]]','[[o_pin_code]]','[[o_email]]','[[o_phone]]'), array($thf_name,$eve_img,$event_detail->name,$event_detail->start,$event_detail->end,$event_detail->location,$event_detail->address2,$event_detail->city,$state->name,$country->name,$event_detail->postalcode,$reason,$userdata->username,$userdata->address,$userdata->city,$user_country->name,$user_state->name,$userdata->postalcode,$userdata->email,$userdata->number), $emailthoutput);
     $subject = $thtemplate->post_title;
     $message = '<DOCTYPE! html>
                        <html>
                            <head>
                                <title>

                                </title>
                            </head>
                            <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
                                <div class="container" style="margin:0 auto;max-width:1080px;">
                                    <div style="width:100%;background: #e4e4e4;padding-left: 15px;padding-top: 10px;padding-bottom: 8px;">
                                        <img src="'.site_url().'/wp-content/uploads/2020/01/new-logo.png" style="width: 200px;">
                                        </div>
                                        <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
                                    '.$th_cncl_Content.'
                              </div>
                            </body>
                        </html>
                    </DOCTYPE>';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Neighbur Inc.;<no-reply@snapd.com>' . "\r\n";
   // echo $message;
     wp_mail($to, $subject, $message, $headers);

}

   // die();


    header("Location: " . site_url() . '?page_id=485');
}

if(isset($_GET['success']) && $_GET['success'] != '') {

    $event_id = base64_decode($_GET['success']);

    $ch = curl_init(API_URL . 'events/' . $event_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: ' . $token
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $event = json_decode($response);
    $success = $event->event->name . ' ' . 'successfully updated !';
}
if(isset($_GET['cancelsave']) && $_GET['cancelsave'] != ''){

    $event_id = base64_decode($_GET['cancelsave']);

    $ch = curl_init(API_URL . 'events/' . $event_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: ' . $token
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $event = json_decode($response);
    $cancelsave = "No changes made to " . $event->event->name;
}

if(isset($_POST['btnSearch']) && $_POST['btnSearch'] != ''){
    $event_state = $_POST['hidden_event_state'];
    $searchKeyword = $_POST['searchKeyword'];

    $ch = curl_init(API_URL . '/events?user=' . $userdata->id . '&state=' . $event_state . '&sort=ASC&sortType=startDate&search=' . $searchKeyword);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: ' . $token
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $apirespons = json_decode($result);

} else {

    if (isset($_GET['state']) && $_GET['state'] != '') {
       // echo API_URL . '/events?user=' . $userdata->id . '&state=' . $_GET['state'] . '&sort=ASC&sortType=startDate'; exit;
    //echo $token; exit;
        $ch = curl_init(API_URL . '/events?user=' . $userdata->id . '&state=' . $_GET['state'] . '&sort=ASC&sortType=startDate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . $token
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        $apirespons = json_decode($result);
        $event_state = $_GET['state'];
    } else {
        $ch = curl_init(API_URL . '/events?user=' . $userdata->id . '&state=upcoming&sort=ASC&sortType=startDate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . $token
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        $apirespons = json_decode($result);
        // echo"<pre>"; print_r($apirespons); die();
        $event_state = 'upcoming';

    }

}

if(isset($_SESSION['event_edit_data'])) {
    unset($_SESSION['event_edit_data']);
}
if(isset($_SESSION["ticket_data"])) {
    unset ($_SESSION["ticket_data"]);
}

get_header(); ?>
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/css/manage_event.css">
<style>
@media only screen and (max-width: 900px) {
.mobile-flex{
    flex-basis: 100% !important;
}
.change-flex-direction{
    flex-direction: row !important;
    flex-wrap: wrap;
    margin-top: 1rem;
    margin-bottom: 1rem;
}
.change-jusitify{
    display: flex;
    justify-content: center;
}
}
</style>
<div id="main-content">
    <div class="outer-wrapper">
        <div class="container container-home">
            <div class="edit-event">
                <h3 class="h3-title">Select Event to Edit </h3>

                <div class="alert alert-success" style="display: none;" id="success_msg">
                    <p style="text-align: center;"><?php echo $success; ?></p>
                </div>

                <div class="alert alert-success" style="display: none;" id="cancelsave_msg">
                    <p style="text-align: center;"><?php echo $cancelsave ? $cancelsave : ''; ?></p>
                </div>

                <div class="alert alert-danger" style="display: none; background-color: #FF9494;" id="err">
                    <p style="text-align: center;"><?php echo $err; ?></p>
                </div>

                <?php if ($success != '') { ?>

                    <script>
                        jQuery('#success_msg').show();
                        setTimeout(function () {
                            jQuery('#success_msg').hide();
                        }, 5000);
                    </script>
                <?php } ?>
                <?php if ($cancelsave) {
                    if ($cancelsave != '') { ?>
                        <script>
                            jQuery('#cancelsave_msg').show();
                            setTimeout(function () {
                                jQuery('#cancelsave_msg').hide();
                            }, 5000);
                        </script>
                    <?php }
                } ?>
                <?php if ($err != '') { ?>
                    <script>
                        jQuery('#err').show();
                        setTimeout(function () {
                            jQuery('#err').hide();
                        }, 5000);
                    </script>
                <?php } ?>
                <div class="event-type">
                    <form method="get" class="event-btn" style="display: flex;
    flex-direction: row;
    flex-wrap: wrap;">
                        <label class="radio-chk"> <input type="radio"
                                                        name="edit-event" <?php echo (!isset($_GET['state']) || $_GET['state'] == 'upcoming') ? 'checked' : ''; ?> value="upcoming"
                                                        onclick="eventState('upcoming')"> <span
                                    class="checkmark1"> </span> Upcoming</label>
                        <label class="radio-chk"> <input type="radio"
                                                        name="edit-event" <?php echo (isset($_GET['state']) && $_GET['state'] == 'past') ? 'checked' : ''; ?> value="past"
                                                        onclick="eventState('past')"><span class="checkmark1"></span> Past</label>
                        <label class="radio-chk"> <input type="radio"
                                                        name="edit-event" <?php echo (isset($_GET['state']) && $_GET['state'] == 'cancelled') ? 'checked' : ''; ?> value="cancelled"
                                                        onclick="eventState('cancelled')"><span
                                    class="checkmark1"></span> Cancelled</label>
                    </form>
                    <form role="search" method="POST" class="edit-search" action="" id="searchForm" style="display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    width: revert;">
                            <span class="e-search">
                              <input type="hidden" name="hidden_event_state" value="<?php echo $event_state; ?>">
                              <input type="text" name="searchKeyword" placeholder="Search..."
                                     value="<?php echo isset($searchKeyword) ? $searchKeyword : ''; ?>" id="s">
                              <input type="submit" id="searchsubmit" name="btnSearch" value="Search">
                              <i class="fa fa-search" style="
                                  position: relative;
    left: -20px;
    top: -40px;"></i>
                            </span>
                    </form>
                    <?php if (count($apirespons->events) > 0) { ?>

                    <div id="myEvents">
                        <?php $i = 0;
                        foreach ($apirespons->events as $row) { ?>

                            <?php if (($event_state == 'cancelled' && $row->canceled == 1) || ($event_state == 'past' && $row->canceled != 1) || ($event_state == 'upcoming' && $row->canceled != 1)) {
                                $i++; ?>
                                <?php
                                /*
                                $ch = curl_init(API_URL . 'events/' . $row->id);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json',
                                    'Authorization: ' . $token
                                ));
                                $response = curl_exec($ch);
                                curl_close($ch);
                                $event = json_decode($response);
                                */
                                if ($row->files[0]->type == 'image') {
                                    $cat_im = 'https://storage.googleapis.com/' . $row->files[0]->bucket . '/' . $row->files[0]->filename;
                                } else {
                                    $cat_im = '';
                                }

                                if ($cat_im && $cat_im != '') {
                                    $event_image = $cat_im;
                                } else {
                                  /*
                                    $ch = curl_init(API_URL . '/files/' . $row->categorys[0]->image_id);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        'Content-Type: application/json',
                                        'Authorization: ' . $token
                                    ));
                                    $result167 = curl_exec($ch);
                                    curl_close($ch);
                                    $image_resp = json_decode($result167);
                                    */
                                    $event_image = site_url() . '/wp-content/uploads/2019/06/f1.jpg';
                                }
                                ?>
                                <div class="event-detail" style="
                                display: flex;
                                flex-flow: wrap;
                                ">
                                    <div class="upload-image mobile-flex" style="
    display: flex;
    flex-wrap: wrap;
    padding-bottom: 1rem;">
                                        <img src="<?php echo $event_image; ?>" >

                                    </div>
                                    <div class="event-details" style="flex: 1;
    display: flex;
    flex-wrap: wrap;">

                                        <div class="row mobile-flex" style="
                                        display: flex;    flex: 1;
    flex-direction: column;">
                                            <h3><?php echo $row->name; ?> </h3>
                                            <?php foreach ($row->event_dates as $edate)
                                            {
                                              echo '<b class="p-date">' . format_dates($edate->start_date, $edate->end_date) . '</b>';

                                            }

                                            $country = $wpdb->get_row("Select * from wp_countries where  id = $row->country_id");
                                            $state = $wpdb->get_row("select * from wp_states where id = $row->province_id");
                                            ?>
                                            <p><b><?php echo $row->location . '<br/>'; ?></b>
                                                <?php echo ($row->address2) ? $row->address2 . '<br/>' : ''; ?>
                                                <?php echo $row->city; ?>, <?php echo $state->state_code; ?>, <?php echo $country->name; ?> <br/>
                                                <?php echo ($row->postalcode) ? $row->postalcode : ''; ?>
											</p>

											<div style="clear: both;"><?php echo $row->lat.', '.$row->long; ?></div>

											<div class="three-btn" style="display: flex;
    width: fit-content;
    flex-wrap: wrap;
    justify-content: center;">

                                                <?php if (!isset($_GET['state']) || $_GET['state'] == 'upcoming' || $_GET['state'] == 'past') { ?>

                                                    <form action="<?php echo site_url(); ?>/edit-event?eventstate=<?php echo $event_state; ?>&event_id=<?php echo $row->id; ?>"
                                                          method="post" style="width:auto !important;">

                                                        <input type="hidden" name="edit"
                                                               value="<?php echo $row->id; ?>">
                                                        <input type="hidden" name="eventstate"
                                                               value="<?php echo $event_state; ?>">
                                                        <button type="submit" name="btnToEditPage" style="color: black !important;">Edit</button>
                                                        <!-- <button><a href="<?php // echo site_url(); ?>?page_id=917&edit=<?php //echo $row->id;?>&eventstate=<?php e//cho $event_state;?>">EDIT</a></button> -->
                                                    </form>

                                                <?php } ?>

                                                <button>
                                                    <a href="<?php echo site_url(); ?>/attendees-report/<?php echo $row->id; ?>">Attendees</a>
                                                </button>
                                                <button><a href="<?php echo site_url(); ?>/sales-report/<?php echo $row->id; ?>">Report</a>
                                                </button>
                                            </div>

                                        </div>
                                        <div class="event-detail-right mobile-flex change-flex-direction" style="
                                        display: flex;
                                        flex: 1;
    justify-content: center;
    align-items: center;
    flex-direction: column;">
                                            <h3>Event ID: <?php echo $row->id; ?></h3>
                                            <!-- <div class="clone-event"><a href="<?php echo site_url() ?>?page_id=304&copy=<?php echo base64_encode($row->id); ?>">
                                                    <span class="copy-event"><i class="fa fa-copy"></i></span>Clone Event</a>
                                            </div> -->

                                            <div class="copy-url"><p id="eveUrl<?php echo $row->id; ?>" style="display:none"><?php echo site_url(); ?>?page_id=354&event_id=<?php echo $row->id; ?></p>
                                                <a href="javascript:void(0)" onclick="copyEventurl('#eveUrl<?php echo $row->id; ?>','#copied_<?php echo $row->id; ?>')">
                                                    <span class="copy-event"><i class="fa fa-link"></i></span>Copy event URL</a></div>
                                            <div class="copied" id="copied_<?php echo $row->id; ?>">Url copied to clipboard</div>
                                            <!-- <div class="event_preview" id="<?php echo $row->id; ?>"><a
                                                        href="javascript:void(0)"><span class="copy-event"><i
                                                                class="fa fa-search"></i></span>Preview</div>
                                            </a>
                                        </div> -->

                                    </div>
                                    <div class="events_links change-jusitify">
                                        <a href="<?php echo site_url(); ?>/view-event/<?php echo $row->id; ?>"
                                           class="view_event">View Event </a>
                                        <?php if (!isset($_GET['state']) || $_GET['state'] == 'upcoming') { ?>

                                            <a href="#" class="cancel_event" id="cancel_event" data-eventurl="<?php echo site_url(); ?>/edit-event?eventstate=<?php echo $event_state; ?>&event_id=<?php echo $row->id; ?>"
                                               data-eventid=<?php echo $row->id; ?>>Cancel Event </a>
                                        <?php } ?>
                                    </div>
                                </div>

                            <?php } ?>

                        <?php } ?>


                        <?php if ($i > 2) { ?>
<!--                            <p class="load-more">-->
<!--                                <button class="load-event" id="load_more" data-total="--><?php //echo $i - 1; ?><!--">Load More-->
<!--                                </button>-->
<!--                            </p>-->
                        <?php } else if ($i == 0) { ?>

                            <p class="no-cevent" style="text-align: center; font-size: 20px;">No Events Found</p>
                        <?php } ?>

                        <?php } else { ?>

                            <p class="no-cevent" style="text-align: center; font-size: 20px;">No Events Found</p>
                        <?php } ?>

                    </div>
                </div>
                <!-- # outer-wrapper-->
            </div>
            <!-- #main content -->
        </div>
        <div class="modal" id="loadingModal" role="dialog">
            <div class="modal-dialog modal-lg" style="max-width: 220px !important;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="email-confomation">
                            <p class="mail-img" style="padding: 0px;"><img
                                        src="<?php echo site_url(); ?>/wp-content/uploads/loading.gif"></p>
                            <p id="modal_loader_text">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<div class="modal" id="Cancel_eveModal" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-body">
            <div class="email-confomation" id="cancel_evnt">
               <p class="wnat_to_cancel" id="modal_loader_text">
                   Are you sure you want to cancel the event? Did you know simple changes like date, time or description updates can be completed using the 
                   <a class="edit-link" dataeventid="" href="">EDIT</a> feature?
                   If you proceed to cancel, all ticket purchasers will receive a notification email. Please contact
                   <a href="mailto:support@neighbur.com">support@neighbur.com</a> if you would like to discuss your options.</p>
               <p><input type="text" name="reason" id="reason_text" class="reason" placeholder="Reason for Cancelling Event"></p>
               <span class="reason_err" id="reason_err">Please Enter Reason to Cancel Event</span>
            <p class="cancel_btn">
                <button class="next-btn cancel-btn" type="button" style="margin: 6px; padding-bottom: 8px;" onClick="$('#Cancel_eveModal').hide();">NO</button>
                <button class="next-btn" type="button" onclick="" id="yesCancel">Yes</button>
            </p>
            </div>
         </div>
      </div>
   </div>
</div>


        <div class="modal" id="previewModal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Preview</h4>
                        <button type="button" class="btn btn-default" onClick="jQuery('#previewModal').hide();">X
                        </button>
                    </div>
                    <div class="modal-body" id="modal_body_preview">
                    </div>
                </div>
            </div>
        </div>

        <?php get_footer(); ?>

        <script>

            jQuery(document).ready(function () {

                var limit = 2;

                jQuery('#myEvents div.event-detail:lt(' + limit + ')').show();

                jQuery(document).on('click', '#load_more', function () {

                    var total = jQuery(this).data('total');
                    limit = parseInt(limit) + parseInt(2);

                    jQuery('#myEvents div.event-detail:lt(' + limit + ')').show();

                    if (parseInt(limit) > parseInt(total)) {

                        jQuery('#load_more').hide();
                    }

                });
            });

            function eventState(state = '') {

                jQuery('#modal_loader_text').text('In progress….');
                jQuery('#loadingModal').show();
                setTimeout(function () {
                    jQuery('#loadingModal').hide();
                }, 3000);
                window.location.href = '<?php echo site_url()?>?page_id=485&state=' + state;
            }

            jQuery('#searchForm').submit(function () {

                jQuery('#modal_loader_text').text('Searching.....');
                jQuery('#loadingModal').show();
                setTimeout(function () {
                    jQuery('#loadingModal').hide();
                }, 3000);
            });

            jQuery(document).on('click', '#cancel_event', function (e) {
                e.preventDefault();
                var dataId = $(this).attr("data-eventid");
                var url = $(this).attr("data-eventurl");
                console.log(dataId);
	            $('#yesCancel').attr('onclick','cancel_eve('+dataId+')');
	            $('.edit-link').attr('dataeventid', dataId);
	             $('.edit-link').attr('href', url);
                jQuery('#Cancel_eveModal').show();
            });

            function cancel_eve(event_id){
                var reason_text = jQuery('#reason_text').val();
                if(reason_text != ""){
                    jQuery('#Cancel_eveModal').hide();
                jQuery('#modal_loader_text').text('Event cancellation in progress….');
                    jQuery('#loadingModal').show();

                    setTimeout(function () {
                        jQuery('#loadingModal').hide();
                    }, 5000);
                  //  console.log('cancelevent=' + event_id +'   reason=' + reason_text)
                    window.location.href = '<?php echo site_url()?>?page_id=485&cancelevent=' + event_id +'&reason=' + reason_text;
                }else{
                    jQuery("#reason_err").show();
                    jQuery(document).on('keyup', '#reason_text', function () {
                    jQuery('#reason_err').hide();
                    });
                     }
                }

            jQuery(document).on('click', '.event_preview', function () {
                var site_url = jQuery('#Site_Url').val();
                var event_id = jQuery(this).attr('id');
                //alert(event_id +" is id picked ");
                jQuery.ajax({
                    type: 'POST',
                    url: site_url + '/wp-content/themes/Divi Child/single_event_preview_ajax.php',
                    data: 'event_id=' + event_id,
                    success: function (html) {
                        //alert(html);
                        jQuery('#modal_body_preview').html(html);
                        jQuery('#previewModal').show();
                    }
                });
            });

            function copyEventurl(e,c) {
                var $temp = jQuery("<input>");
                jQuery("body").append($temp);
                $temp.val(jQuery(e).text()).select();
                document.execCommand("copy");
                $temp.remove();
                console.log(jQuery(e).text());
                jQuery(c).show().delay(3000).fadeOut(2000);

            }

        </script>
