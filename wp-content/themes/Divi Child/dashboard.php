<?php 
/*
Template Name: Event dashboard Page
*/

if(!isset($_SESSION['Api_token'])){
    wp_redirect( site_url().'?page_id=187' );
    exit;
}

$msg = '';
$err = '';

if(isset($_GET['action']) && $_GET['action'] == 'eventsuccess') {

    $msg = 'Event created successfully !';
}

if(isset($_SESSION['event_data'])) {

    unset($_SESSION['event_data']);
}

if(isset($_SESSION["ticket_data"])) {

    unset ($_SESSION["ticket_data"]);
}

get_header(); ?>

    <div id="main-content">   
        <div class="outer-wrapper ">
            <div class="container container-home">
            <?php if($msg != '') { ?>

                <p style="text-align: center; color: green; font-size: 20px;"><b><?php echo $msg;?></b></p>
            <?php }?>
            <?php if(isset($_GET['canceled'])) { ?>

                <p style="text-align: center; color: green; font-size: 20px;"><b>Event cancellation successful.</b></p>
            <?php }?>
                 <h3 class="h3-title">Create Your Next Big Event</h3>               
                <div class="login-form2">
                    <div class="event-dashboard">
                     <p class="cne"><a href="<?php echo site_url(); ?>/create-event/">Create New Event</a></p>  
					 <p><a href="<?php echo site_url(); ?>/manage-my-events/">Manage My Events</a></p>
                   </div>
                </div>
                <div class="help-btn evt-board"><i class="fa fa-question"></i> Need Help! <a target="_blank" href="https://support.neighbur.com/portal/home">Visit our support site for answers</a></div>
            </div>
        </div>
        <!-- #outer-wrapper -->
    </div>


    <?php get_footer(); ?>