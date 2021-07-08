<?php
/*
   Template Name: Single Event
   */

if (isset($_SESSION['userdata'])) {
  $userdata = $_SESSION['userdata'];
}

global $wpdb;
$token   =  $_SESSION['Api_token'];
$url = $_SERVER['REQUEST_URI'];
$event = explode('/', $url);
$event_id = $event[3];


// echo "<pre>"; print_r($token); echo "</pre>";
$third_party_url = $event_details->third_party_url;

if (isset($event_id) && $event_id != '') {

  //$event_id = $_GET['event_id'];

  $ch   = curl_init(API_URL . '/events/' . $event_id);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $token
  ));
  $result = curl_exec($ch);
  curl_close($ch);
  $apirespons = json_decode($result);
  //   echo "<pre>";
  //   print_r($result);
  //   echo "</pre>";die;
  if ($apirespons->success) {

    $event_detail = $apirespons->event;
    $metadata = unserialize($event_detail->metadata);

    if (isset($metadata['file_id'])) {

      //echo API_URL.'files/'.$metadata['file_id']; die;

      $ch   = curl_init(API_URL . 'files/' . $metadata['file_id']);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/pdf',
        'Authorization: ' . $token
      ));
      $result1 = curl_exec($ch);
      curl_close($ch);
      $fileresponse = json_decode($result1);

      if ($fileresponse->success) {

        $file = $fileresponse->file;
      }
    }
  }
}
//echo "<pre>"; print_r($event_detail); die();
get_header(); ?>
<link href='<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/simpleLightbox.css' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/simpleLightbox.js"></script>
<div id="main-content">
  <link rel="stylesheet" href="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/fancy-file-uploader/fancy_fileupload.css" type="text/css" media="all" />
  <script type="text/javascript" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/fancy-file-uploader/jquery.ui.widget.js"></script>
  <script type="text/javascript" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/fancy-file-uploader/jquery.fileupload.js"></script>
  <script type="text/javascript" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/fancy-file-uploader/jquery.iframe-transport.js"></script>
  <script type="text/javascript" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/fancy-file-uploader/jquery.fancy-fileupload.js"></script>
  <style>
    .ff_fileupload_wrap .ff_fileupload_dropzone {
      padding: 0 26px 40px 0px !important;
      height: 125px;
      background-size: 80px;
      font-size: 20px;
      text-align: right;
      background-position: left 25px top 10px;
    }

    .ff_fileupload_wrap table.ff_fileupload_uploads td.ff_fileupload_summary .ff_fileupload_filename {
      max-width: 340px !important;
      font-weight: bold;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .map-inter {
      float: left;
      width: 60%;
      margin-left: 0%;
    }

    .topbtns {
      width: 40px;
      float: right;
      padding: 5px 10px 0px 10px;
      cursor: pointer;
    }

    .ticket-range {
      top: 110px !important;
      display: none;
    }

    .closeh4 {
      float: right;
      padding-top: 7px;
    }

    .upper a {
      color: black;
      float: right;
      margin-left: 20px;
    }

    .up-logo img {
      margin-top: 10px;
      max-width: 80%;
    }

    .image-gallery a img {
      width: 144px;
      margin: 10px 2px 0 2px !important;
    }

    #button_upload_image {
      padding: 6px 12px;
      margin-top: 10px;
      border: 2px solid #3b93fc;
      border-radius: 4px;
      font-size: 16px;
      margin-bottom: 12px;
      position: absolute;
      top: 322px;
      left: 156px;
    }

    .shareit {
      display: none;
      float: right;
      position: relative;
      top: -17px;
    }

    .gallery_title {
      margin-top: 10px;
    }

    .single-evnt-detail p b {
      width: 100px !important;
      float: left;
    }

    .gallery_t {
      font-size: 17px;
      font-weight: bold;
      color: #333333;
    }

    .detali_t {
      float: right;
    }

    .img-wrap {
      position: relative;
      float: left;
      width: 144px;
      height: 100px;
      margin: 10px 2px 0 2px !important;
    }

    .img-edit {
      position: absolute;
      top: 14px;
      right: 2px;
      z-index: 100;
      background: #f56d3a;
      border: 0;
      color: #fff;
      padding: 1px 5px 1px 5px;
    }
  </style>
  <?php


  if (count($event_detail->ticketTypes) > 0) {
    foreach ($event_detail->ticketTypes as $key => $tickets) {

      $ticktype = (array) $tickets;
      $ticktype = count($ticktype);
      //print_r(count($type));die;

    }
  ?>
    <style>
      .topbtns {
        width: 40px;
        float: right;
        padding: 10px 10px 0px 10px;
        cursor: pointer;
        position: relative;
        right: 144px;
      }
    </style>
  <?php }
  if (isset($event_detail)) {

    $country = $wpdb->get_row("Select * from wp_countries where  id = $event_detail->country_id");
    $state = $wpdb->get_row("select * from wp_states where id = $event_detail->province_id");
  } ?>


  <div id="main-content">
    <div class="outer-wrapper ">
      <div class="container container-home">
        <?php // echo site_url().parse_url($_SERVER['HTTP_REFERER'],PHP_URL_PATH); 
        ?>
        <div class="upper">

        </div>

        <div class="event-detail">
          <div class="upload-image">
            <?php if (isset($event_detail) && count($event_detail->files) > 0 && $event_detail->files[0]->type == 'image') { ?>
              <?php $eve_img = "https://storage.googleapis.com/" . $event_detail->files[0]->bucket . "/" . $event_detail->files[0]->filename; ?>
              <img src="<?php echo $eve_img; ?>" style="max-height: 250px;">
            <?php } else {  ?>
              <img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
            <?php } ?>
            <div class="image-gallery">
              <div>
                <?php if (!empty($token)) : ?>
                  <!--input type="file" name="gallery1" onChange="newuploadmethod(this);"-->
                  <form action="<?php echo site_url(); ?>/wp-content/themes/Divi Child/ajax/addupload.php?event_id=<?php echo $event_id; ?>" method="post" id="frm_image_upload" enctype="multipart/form-data">
                    <div class="">
                      <!--
						  <input type="file" name="gallery_images[]" id="event_gallery_images" accept=".jpg, .png, image/jpeg, image/png" style="display: none" multiple>
						  <button type="button" onclick="$('.ff_fileupload_dropzone').click()" id="button_upload_image">Browse Photos</button>
						  -->
                      <div class="ff_fileupload_wrap">
                        <div class="ff_fileupload_dropzone_wrap"><button class="ff_fileupload_dropzone" type="button" aria-label="Browse, drag-and-drop, or paste files to upload">Drag &amp; drop photos to gallery....</button>
                          <div class="ff_fileupload_dropzone_tools"></div>
                        </div>
                        <table class="ff_fileupload_uploads"></table>
                      </div>
                      <input type="file" id="newmethodfile" onChange="newuploadmethod(this);" accept=".jpg, .png, image/jpeg, image/png" style="border: none; position: absolute;top: 258px;left: 0;z-index: 2;opacity: 0;cursor: pointer;height: 127px;width: 100%;">
                      <button type="button" onclick="$('.ff_fileupload_dropzone').click();$('#newmethodfile').click();" id="button_upload_image">Browse Photos</button>
                    </div>
                  </form>

                <?php endif; ?>
                <?php echo @$uploadmessage; ?>

              </div>
              <div class="gallery_title"><span class="gallery_t">Gallery</span><span class="detali_t">Select photo for details</span></div>

              <?php if (isset($event_detail) && count($event_detail->photos) > 0) { ?>

                <?php foreach ($event_detail->photos as $row) { ?>
                  <div class="img-wrap">
                    <a title="<?php echo $row->caption; ?>" href="https://storage.googleapis.com/<?php echo $row->file->bucket ?>/<?php echo $row->file->filename; ?>">
                      <img src="https://storage.googleapis.com/<?php echo $row->file->bucket ?>/<?php echo $row->file->filename; ?>">
                    </a>
                    <?php
                    if (isset($userdata) && $row->file->drupal_user_id == $userdata->id) {
                      echo <<<HTML
                             <button onclick="location.href='/my-uploads/'" class="btn img-edit"><i class="fa fa-edit" style="color: #fff; font-size: 14px; top: 2px;"></i></button>
HTML;
                    }
                    ?>
                  </div>

              <?php }
              } else {
                echo "Currently no photos at this time.";
              } ?>
            </div>
          </div>

          <?php
          $third_party_url = $event_detail->third_party_url;
          $idss = $_GET['tid'];

          //echo "<pre>"; print_r($event_detail); "</pre>"; die;

          if ($event_detail->ticketTypes[0]->id == $idss) {
            $ticket_price = $event_detail->ticketTypes[0]->price;
            $ticket_type = $event_detail->ticketTypes[0]->paid_yn;
            $third_party_url = $event_detail->third_party_url;
            //$ticket_date =$event_detail->ticketTypes[0]->start;
            $ticket_date = date('Y-m-d', strtotime($_GET['abc']));
          } elseif ($event_detail->ticketTypes[1]->id == $idss) {
            $ticket_price = $event_detail->ticketTypes[1]->price;
            $third_party_url = $event_detail->third_party_url;
            $ticket_type = $event_detail->ticketTypes[1]->paid_yn;
            $ticket_date = $event_detail->ticketTypes[1]->start;
          }


          ?>

          <form class="event-details">
            <?php if (count($event_detail->ticketTypes) > 0) { /* echo "<pre>"; print_r($event_detail->ticketTypes); die; */ ?>
              <?php if ($event_detail->ticketTypes[0]->name == '') { ?>
                <h2 class="f-tkt">FREE</h2>
                <?php } else {

                if (count($event_detail->ticketTypes) == 1) {
                  if ((strtotime($event_detail->ticketTypes[0]->start) >= strtotime(date('Y-m-d'))) && (strtotime($event_detail->ticketTypes[0]->end) <= strtotime(date('Y-m-d')))) {
                ?>





                    <a href="<?php echo site_url() ?>/get-tickets/<?php echo $event_id; ?>"><button class="buy-ticket" type="button" name="btnSubmit">GET TICKETS</button></a>


                  <?php
                  } else if (strtotime("now") < strtotime($event_detail->ticketTypes[0]->release)) {
                  ?>
                    <a href="#"><button class="buy-ticket-available" type="button" name="btnSubmit">AVAILABLE SOON</button></a>
                    <p>TICKETS GO ON SALE
                      <br /><?php echo date('M d, Y', strtotime($event_detail->ticketTypes[0]->release)); ?>
                      <br>@<?php echo date('h:i', strtotime($event_detail->ticketTypes[0]->release)); ?>
                      <br>Current time: <?php echo date('M d, Y h:i', strtotime("now")); ?>
                    </p>
                <?php
                  }
                }

                ?>

                <!--Yahan condition lagani hai-->

                <?php if (isset($idss)) : ?>
                  <?php if ($ticket_price > 0) : ?>
                    <?php $sendUrl =  site_url() . '/get-tickets/' . $event_id; ?>

                    <a href="<?php echo site_url() ?>/get-tickets/<?php echo $event_id; ?>/?tid=<?php echo $idss; ?>/&abc=<?php echo $ticket_date; ?>"><button class="buy-ticket" value="<?php echo $ticket_price; ?>" type="button" name="btnSubmit">GET TICKETS</button></a>
                  <?php elseif ($ticket_price == 0) : ?>
                    <a href="<?php echo site_url() ?>/get-tickets/<?php echo $event_id; ?>/?abc=<?php echo $event_detail->start; ?>"><button class="buy-ticket" type="button" name="btnSubmit">FREE TICKETS</button></a>
                  <?php elseif (!empty($third_party_url)) : ?>
                    <a href="<?php echo $third_party_url; ?>"><button class="buy-ticket" type="button" name="btnSubmit">DETAILS</button>
                    </a>

                  <?php endif; ?>

                <?php else : ?>
                  <?php $third_party_url = 1; ?>
                  <?php if ($event_detail->ticketTypes[0]->price > 0) : ?>
                    <a href="<?php echo site_url() ?>/get-tickets/<?php echo $event_id; ?>"><button class="buy-ticket" type="button" name="btnSubmit" value="buybtn">GET TICKETS</button></a>
                  <?php elseif ($event_detail->ticketTypes[0]->price == 0) : ?>
                    <a href="<?php echo site_url() ?>/get-tickets/<?php echo $event_id; ?>"><button class="buy-ticket" type="button" name="btnSubmit" value="freebtn">FREE TICKETS</button></a>
                  <?php elseif ($third_party_url) : ?>
                    <a href="<?php echo $third_party_url; ?>"><button class="buy-ticket" type="button" name="btnSubmit">DETAILS</button>
                    </a>
                  <?php endif; ?>
                <?php endif; ?>
                <div class="ticket-range">
                  <?php foreach ($event_detail->ticketTypes as $ticket) { ?>
                    <p><?php echo $ticket->name ?> <?php echo '$' . $ticket->price ?></p>
                  <?php } ?>
                </div>
              <?php } ?>
            <?php } ?>

            <?php
            $emailtemplate   = get_post(2094);
            $emailoutput =  apply_filters('the_content', $emailtemplate->post_content);
            $emailContent = "";
            //$emailContent = str_replace(array('[[eve_image]]','[[start_d]]','[[start_d]]','[[eve_name]]','[[address_1]]','[[address_2]]','[[city]]','[[province]]','[[country]]','[[pin_code]]'), array($eve_img,$event_detail->start,$event_detail->end,$event_detail->name,$event_detail->location,$event_detail->address2,$event_detail->city,$state->state_code,$country->name,$event_detail->postalcode), $emailoutput);


            ?>
            <button type="button" class="btn btn-primary invisible" data-toggle="modal" id="gmailModalId" data-target="#exampleModal">
              Launch demo modal
            </button>






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
            <h3><?php echo isset($event_detail) ? $event_detail->name : ''; ?><img class="topbtns  a2a_dd" <?php if (!empty($event_detail->third_party_url)) : ?>style="margin-right:100px;" <?php endif; ?> id="share_btn" href="https://www.addtoany.com/share" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/sharepng.png" alt="uplaod images">
              <?php if (!empty($event_detail->third_party_url)) : ?>
                <a href="<?php echo $event_detail->third_party_url; ?>"><button class="buy-ticket" type="button" name="btnSubmit">DETAILS</button>
                </a>
              <?php endif; ?>
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
                  body: 'This Neighbur event has been recommended for you... \n \n' + tempd3 + '\n \n ' + tempd1 + ' to ' + tempd2 + ' \n \nThe Event Venue \n ' + tempd4 + '\n ' + tempd6 + ', ' + tempd5 + '\n ' + tempd9 + ' \n \n You can view this event anytime at https://webdev.snapd.com/view-event/' + tempd11 + '/'
                };
              </script>
            </h3>

            <?php

            $idss = $_GET['tid'];


            if ($event_detail->ticketTypes[0]->id == $idss) {
              $ticket_price = $event_detail->ticketTypes[0]->price;
              $ticket_type = $event_detail->ticketTypes[0]->paid_yn;
              //$ticket_date =$event_detail->ticketTypes[0]->start;
              $dates = date("F j", strtotime($_GET['abc']));
              $time = date("g:i a", strtotime($_GET['abc']));
              $ticket_dates = $dates . ' 2021 ' . $time;
            } elseif ($event_detail->ticketTypes[1]->id == $idss) {
              $ticket_price = $event_detail->ticketTypes[1]->price;
              $ticket_type = $event_detail->ticketTypes[1]->paid_yn;
              //$ticket_date =$event_detail->ticketTypes[1]->start;

            }

            ?>



            <?php

            if (!empty($idss)) {
              $d = date('l', strtotime($ticket_dates));
              $d2 = date('F j, Y', strtotime($ticket_dates));
              echo '<b class="p-date"><span>' . $d . '</span> <span>' . $d2 . '</span></b>';
            } else {

              foreach ($event_detail->event_dates as $edate) {





                //       $d = date('l', strtotime($edate->start_date));

                //       $d2 = date('F j, Y', strtotime($edate->start_date));
                //       echo '<b class="p-date"><span>'.$d2.'</span> <span>'.$d2.'</span></b>';

                //   }else{
                $sdate = $edate->start_date;

                echo '<b class="p-date">' . format_dates($edate->start_date, $edate->end_date) . '</b>';
              }
            }
            ?>

            <p>
              <?php echo isset($event_detail) ? !empty($event_detail->location) ? $event_detail->location . '<br>' : ' ' : ' '; ?>
              <?php echo isset($event_detail) ? !empty($event_detail->address2) ? $event_detail->address2 . '<br>' : ' ' : ' '; ?>
              <?php echo isset($event_detail) ? $event_detail->city : ''; ?>, <?php echo isset($event_detail) ? $state->state_code : ''; ?>, <?php echo isset($event_detail) ? $country->name : ''; ?><br />
              <?php echo isset($event_detail) ? $event_detail->postalcode : ''; ?>
            </p>
            <div class="p-description">
              <h3>Description</h3>
              <p><?php echo isset($event_detail) ? $event_detail->description : ''; ?></p>

            </div>
            <div class="row">
              <?php foreach ($event_detail->categorys as $category) { ?>
                <div class="p-catg" style="margin-right:5px;"><?php echo $category->name; ?></div>
              <?php } ?>
            </div>

            <?php
            $contactDetails = "";
            if (isset($event_detail)) {
              $contactDetails .= isset($metadata['org']) && $metadata['org'] != '' ? "<p><b>Organization:</b>" . $metadata['org'] . "</p>" : "";
              $contactDetails .= $event_detail->contact_name != '' && isset($metadata) && $metadata['exclude_name'] == 'off' ? "<p><b>Name:</b>" . $event_detail->contact_name . "</p>" : "";
              $contactDetails .= $event_detail->contact_phone != '' && isset($metadata) && $metadata['exclude_phone'] == 'off' ? "<p><b>Phone:</b>" . $event_detail->contact_phone . "</p>" : "";
              $contactDetails .= isset($metadata) && $metadata['extension'] != '' && $metadata['exclude_phone'] == 'off' ? "<p><b>Extension:</b>" . $metadata['extension'] . "</p>" : "";
              $contactDetails .= $event_detail->contact_url != '' ? "<p><b>Website:</b><a href='" . $event_detail->contact_url . "' target='_new'>" . $event_detail->contact_url . "</a></p>" : "";
              $contactDetails .= $event_detail->contact_email != '' && isset($metadata) && $metadata['exclude_email'] == 'off' ? "<p><b>Email:</b><a href='mailto:" . $event_detail->contact_email . "'>" . $event_detail->contact_email . "</a></p>" : "";
            }
            if ($contactDetails != "") {
              echo <<<HTML
			              <div class="single-evnt-detail contant">
                     <h3>Contact Details</h3>
                     $contactDetails
                  </div>
HTML;
            }
            ?>
            <div class="attachemnts">
              <?php if (isset($metadata['file_id']) || isset($metadata['logo_id'])) { ?>
                <div class="logo-details">
                  <!-- <h3>Logo</h3> -->
                  <p class="up-logo">
                    <?php
                    foreach ($event_detail->files as $row) {
                      if ($row->type == 'logo') { ?>
                        <img src="https://storage.googleapis.com/<?php echo $row->bucket ?>/<?php echo $row->filename; ?>">
                    <?php
                      }
                    }
                    ?>
                  </p>
                  <?php if (isset($file)) { ?>
                    <p class="up-attach">
                    <h3>Attachment</h3>
                    <p>Important imformation about this event:</p>
                    <a class="btn-downlaod" href="<?php echo isset($file) ? 'https://storage.cloud.google.com/' . $file->bucket . '/' . $file->filename : '#' ?>" target="_blank" style="background: #529cfb; color: #fff; padding: 1px 13px;">Download <i class="fa fa-download"></i> </a>
                    <p><span>Don’t have Adobe Reader –</span><a href="https://get.adobe.com/reader/" target="_blank">click here</a> to download.</p>
                    <!-- </button> -->
                    </p>
                  <?php } ?>
                </div>
              <?php } ?>

              <div class="<?php echo isset($file) && (isset($metadata['file_id']) || isset($metadata['logo_id'])) ? 'map-details' : 'map-inter'; ?>">
                <h3>Map Details</h3>
                <iframe class="mapIframe" src="https://webdev.snapd.com/map.php?lat=<?php echo $event_detail->lat ?>&lng=<?php echo $event_detail->long ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
              </div>
            </div>
          </form>
        </div>
      </div>
      <!-- #container -->
    </div>
    <!-- #outer-wrapper -->
  </div>
  <!-- #main content -->
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="my_form_data" method="POST">
          <div class="modal-body">


            <label for="rec_email"><strong>Recipent Email</strong></label>
            <input type="text" name="email_id" id="email_id" class="form-control rounded-0" placeholder="Enter recipent email">


            <label for="subject_name" class="mt-3"><strong>Subject Name</strong></label>
            <input type="text" name="subject_name" value="Neighbur Event For You" id="subject_name" class="form-control rounded-0" placeholder="Enter Subject">

            <label for="body" class="mt-3"><strong>Your Message</strong></label>

            <?php wp_editor('
         <center><strong>This Neighbur event has been recommended to you...</strong></center>
       
         <i>' . $event_detail->start . ' to ' . $event_detail->end . '</i>
         
         <i>' . ucwords($event_detail->name) . '</i>
      
         <i>' . ucwords($event_detail->location) . '</i>
        
         ' . ucwords($event_detail->address2) . '<br>
        
         ' . $event_detail->postalcode . '       
        
         <a href="https://webdev.snapd.com/view-event/' . $event_id . '/">View Details</a>
         ', 'email_body'); ?>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="close_btn" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="send_email">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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

  <div class="modal" id="captionModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="width: fit-content !important;">
        <div class="modal-body">
          <div class="">
            <img id="captionImage" />
          </div>
          <div class="">
            <input style="width:100%;" type="text" placeholder="Enter Caption for selection" id="captionText" />
          </div>
        </div>
        <div class="modal-footer" style="justify-content: space-around;">
          <button onclick="onSubmitImage()" style="
    box-sizing: border-box;
    margin: 0;
    line-height: inherit;
    overflow: visible;
    text-transform: none;
    font-family: inherit;
    cursor: pointer;
    -webkit-appearance: button;
    padding: 8px 18px;
    margin-top: 10px;
    background: #F56D3A;
    border: 0;
    color: #fff;
    border-radius: 2px;
    font-size: 15px;
    margin-bottom: 12px;">Submit</button>
          <button onclick="closeModal()" style="
    box-sizing: border-box;
    margin: 0;
    line-height: inherit;
    overflow: visible;
    text-transform: none;
    font-family: inherit;
    cursor: pointer;
    -webkit-appearance: button;
    padding: 8px 18px;
    margin-top: 10px;
    background: #fff;
    border: 0;
    color: #F56D3A;
    border-radius: 2px;
    font-size: 15px;
    margin-bottom: 12px;">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- #main content -->
  <?php get_footer(); ?>

  <script type='text/javascript'>
    function onSubmitImage() {
      var images = new File([$('#captionImage').attr('src')], `Gallary` + new Date());
      var str = $("#captionText").val();

      var $modal = jQuery("#captionModal");
      $modal.modal("hide");

      /* alert(input.files[0]); return; */
      jQuery('#modal_loader_text').text('In progress...');
      jQuery('#loadingModal').show();

      var form = new FormData();
      form.append("caption", str);
      form.append("type", "logo");
      form.append("eventId", "<?php echo $event_id; ?>");
      form.append("Authorization", "<?php echo $_SESSION['Api_token']; ?>");
      form.append("file", images, "file");

      var settings = {
        "url": "/wp-content/themes/Divi Child/upload_proxy.php",
        "method": "POST",
        "timeout": 0,
        "headers": {
          "Authorization": "<?php echo $_SESSION['Api_token']; ?>"
        },
        "cache": false,
        "contentType": false,
        "processData": false,
        "data": form
      };

      $.ajax(settings).done(function(response) {
          console.log("Photo uploaded: " + response);
          window.location.href = window.location.href;
        })
        .fail(function(xhr, status, error) {
          // error handling
          console.log(status);
          jQuery('#loadingModal').hide();
          alert("There was an error uploading this file. please try again with a different file.");
          //window.location.href=window.location.href;
        });

    }

    $(document).ready(function() {
      var gallery = $('.image-gallery a').simpleLightbox();
    });

    function closeModal() {
      var $modal = jQuery("#captionModal");
      $modal.modal("hide");
    }

    function newuploadmethod(input) {
      var $modal = jQuery("#captionModal");
      $modal.modal("show");

      var image = document.getElementById('captionImage');
      image.src = URL.createObjectURL(input.files[0]);
    }

    $('.ff_fileupload_wrap, #newmethodfile').on('dragover dragenter', function(e) {
      e.stopPropagation();
      e.preventDefault();
      console.log('dragenter');
      $('.ff_fileupload_wrap .ff_fileupload_dropzone').css('border', '3px dashed #ee7c13');
    });

    $('.ff_fileupload_wrap, #newmethodfile').on('dragleave', function(e) {
      e.stopPropagation();
      e.preventDefault();
      console.log('dragenter');
      $('.ff_fileupload_wrap .ff_fileupload_dropzone').css('border', '2px dashed gray');
    });
  </script>
  <?php //if(isset($_SESSION['event_data'])) { print_r($_SESSION['event_data']); } 
  ?>