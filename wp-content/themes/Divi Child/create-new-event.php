<?php
/*
   Template Name: Create new Event
   */

if (!isset($_SESSION['Api_token'])) {
   wp_redirect(site_url() . '?page_id=187');
   exit;
}
global $wpdb;
$token   =  $_SESSION['Api_token'];
// echo "<pre>";
// print_r($token);
// echo "</pre>";die;
//echo $token;
if (isset($_GET['copy']) && $_GET['copy'] != '') {

   $event_id = base64_decode($_GET['copy']);

   $ch      = curl_init(API_URL . 'events/' . $event_id);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: ' . $token
   ));
   $response = curl_exec($ch);
   curl_close($ch);
   $events = json_decode($response);

   if ($events->success) {

      $events = $events->event;

      $ch      = curl_init(API_URL . 'ticketTypes?eventId=' . $event_id);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'Content-Type: application/json',
         'Authorization: ' . $token
      ));
      $tkt_response = curl_exec($ch);
      curl_close($ch);
      $tkt = json_decode($tkt_response);

      if ($tkt->success && !empty($tkt->ticketType)) {

         $tickets = $tkt->ticketType;
      }

      $metadata = unserialize($events->metadata);
   }
}

$countries = $wpdb->get_results("Select * from wp_countries");
$states = $wpdb->get_results("Select * from wp_states");
$categories = $wpdb->get_results("SELECT * FROM api_category order by title ASC");

get_header();

function get_base64($path)
{
   $type = pathinfo($path, PATHINFO_EXTENSION);
   $data = file_get_contents($path);
   $base64 = base64_encode($data);
   echo $base64;
}



?>

<link rel="stylesheet" href="/wp-content/themes/Divi Child/datepicker/css/jquery.datetimepicker.min.css">
<script src="/wp-content/themes/Divi Child/datepicker/js/moment.js"></script>
<script src="/wp-content/themes/Divi Child/datepicker/js/jquery.datetimepicker.full.js"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.css" />
<link rel="stylesheet" href="<?php echo site_url() ?>/wp-content/themes/Divi Child/selec2css/select2.css">
<link rel="stylesheet" href="<?php echo site_url() ?>/wp-content/themes/Divi Child/css/cropper.css">
<link rel="stylesheet" href="<?php echo site_url() ?>/wp-content/themes/Divi Child/css/createevent.css">


<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.js"></script>
<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/select2.js"></script>
<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/jquery.ui.widget.js"></script>
<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/jquery.iframe-transport.js"></script>
<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/jquery.fileupload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>

<style>
   .HighlightArea {
      height: 250px;
      background-color: transparent !important;
   }

   .HighlightArea>#dropZone {
      border: 3px dashed #ee7c13 !important;
   }

   @media only screen and (max-width: 900px) {
      .mobile-event-type {
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         align-self: center;
         text-align: center;
      }

      .mobile-event-dates {
         float: none !important;
         margin-right: 0 !important;
         margin-bottom: 0 !important;
         margin-top: 0 !important;
         margin-left: 0 !important;
         display: flex;
         justify-content: end;
         flex-direction: column;
         flex-wrap: nowrap;
      }

      .mobile-event-dates>label {
         margin-bottom: 1rem;
      }

      .mobile-event-detail {
         flex-direction: column;
         display: flex;
         justify-content: center !important;
      }

      .mobile-upload-image {
         display: flex;
         flex-direction: column;
         width: 100%;
         justify-content: center;
         align-items: center;
         margin-bottom: 2rem;
         text-align: center;
      }

      .mobile-event-details {
         display: flex;
         width: 100% !important;
         align-items: end;
         padding-left: 0px !important;
         flex-wrap: wrap;
      }

      .mobile-attachemnts {
         display: flex;
         flex-direction: column;
         flex-wrap: wrap;
         justify-content: center;
         align-items: center;
      }

      .mobile-logo-details {
         width: 100% !important;
         display: flex;
         flex-flow: column
      }

      .mobile-map-details {
         display: flex;
         flex-flow: column;
         width: 100% !important;
         margin-left: 0%;
      }

      .make-center {
         display: flex;
         justify-content: center;
         align-self: center;
         margin-left: 0rem !important;
      }
   }
</style>
<div id="main-content">

   <div class="outer-wrapper ">
      <div class="container container-home">
         <h3 class="h3-title">Submit Your Next Big Event</h3>
         <ul class="progressbar desktop-visible">
            <li class="active">Page Design</li>
            <li>Ticket Options</li>
            <li>Options & Submit</li>
         </ul>
         <p class="d-none  desktop-visible d-lg-block" style="float:left;width:100%;text-align:center;margin-bottom:10px;font-size: 15px;">Complete each required section. Select NEXT to proceed.</p>
         <hr />

         <form autocomplete="off" class="create-event" id="event_form" method="post" action="<?php echo site_url(); ?>/create-tickets<?php echo isset($events) ? '?clone=' . $event_id : ''; ?>" enctype="multipart/form-data" class="box">

            <div class="event-type mobile-event-type">
               <?php if (isset($_SESSION['event_data'])) { ?>
                  <label class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" value="1" <?php echo (count($_SESSION['event_data']['event_start_date']) == '1') ? 'checked' : ''; ?> id="check_single"><span class="checkmark1"> </span> Single day Event</label>
                  <label class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" value="2" <?php echo (count($_SESSION['event_data']['event_start_date']) > '1') ? 'checked' : ''; ?> id="check_multi"><span class="checkmark1"> </span> Multi-day event</label>
               <?php } else { ?>
                  <label class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" value="1" checked id="check_single"><span class="checkmark1"> </span> Single day Event</label>
                  <label class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" value="2" id="check_multi"><span class="checkmark1"> </span> Multi-day event</label>
               <?php } ?>
               <div id="event_message">This event will start and end on the same date</div>
               <br />
               <!-- <input type="hidden" name="event_status_id" id="event_status_id" value="1">-->
               <?php if (isset($events)) { ?>
                  <div class="event-dates mobile-event-dates">
                     <label style="cursor: pointer;" class="start-date col-lg-3 mb-2 pb-2" for="single_start_date">Start <input type="text" required id="single_start_date" class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                     <label style="cursor: pointer;" class="start-date col-lg-3 mb-2 pb-2" for="single_end_date">End <input type="text" required id="single_end_date" class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                  </div>
                  <a href="#" style="display: none" id="add_more"><span> + </span>Add a new date</a>
            </div>
            <hr />
            <p><b>All required fields marked with (*).</b> <b style="float: right;">Event ID: <?php echo $events->id; ?> </b></p>
            <div class="event-detail mobile-event-detail">
               <!--  <div class="upload-image">
                   <h3>Event Image*</h3>
                <div class="img-upload-outer" id="DragAReahight" onmouseout="HighlightArea(this.id, false)" ondragleave="    HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
            <?php if (isset($events) && count($events->photos) > 0 && $events->photos[0]->caption == 'image') { ?>
                  <img id="fileToUpload_prev" src="https://storage.googleapis.com/<?php echo $events->photos[0]->file->bucket ?>/<?php echo $events->photos[0]->file->filename; ?>" alt="uplaod images" style="max-height: 250px;"><a href="<?php echo site_url() ?>?page_id=917&edit=<?php echo $events->id; ?>&delimage=<?php echo $events->photos[0]->id; ?>"><span class="remove-img" style="cursor: pointer;">-</span></a>
                  <?php } else { ?>
                  <img id="fileToUpload_prev" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/eventImages.jpg" alt="uplaod images">
                  <?php } ?>
              <input type="hidden" id="event_image_base64" name="event_image_base64"/>
                  <div id="files" onClick="jQuery('#fileupload').click()"></div>
                  <div id="dropZone" style="text-align: center;">
                     <h1>Drag & Drop event image here...</h1>
                     <button style="font-size: 16px;">Choose File</button><span>  No file chosen</span>
                     <input type="file" id="fileupload" name="fileToUpload" style="border:none;" value="<?php echo isset($events) && count($events->photos) > 0 && $events->photos[0]->caption == 'image' ? 'https://storage.googleapis.com/' . $events->photos[0]->file->bucket . '/' . $events->photos[0]->file->filename : ''; ?>" required>
                  </div> </div>
                  <span style="display: none;" id="file_succ">
                     <p style="color: #999;">Click image to replace or Drag & Drop
                     </p>
                  </span>
                  <span style="display: none;" id="file_err">
                     <p style="color: #ff0000;">Image size should be less than 10 Mb.<br/>
                        Image type should be jpg/jpeg/png.
                     </p>
                  </span>
                  <p>Select an image related to your event to display in search results. Use a high resolution image of 1280 x 720</p>

               </div>-->
               <div class="upload-image mobile-upload-image">
                  <h3>Event Image*</h3>
                  <div class="img-upload-outer" id="DragAReahight" onmouseout="HighlightArea(this.id, false)" ondragleave="    HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
                     <input type="file" id="fileupload" name="fileToUpload" value="https://storage.googleapis.com/<?php echo $events->files[0]->bucket ?>/<?php echo $events->files[0]->filename; ?>" style="border: none; position: absolute;top: 50px;left: 0;z-index: 2;opacity: 0;cursor: pointer;height: 254px;width: 100%;" <?php echo isset($events) && isset($events->files[0])  ? '' : 'required'; ?> title="Please Upload Event Image.">
                     <div id="dropZone" style="text-align: center;">
                        <h1>Drag & Drop event image here...</h1>
                        <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                        <!--<span>  No file chosen</span>-->
                     </div>
                     <?php
                     $path = 'https://storage.googleapis.com/' . $events->files[0]->bucket . '/' . $events->files[0]->filename;
                     $type = pathinfo($path, PATHINFO_EXTENSION);
                     $data = file_get_contents($path);
                     $base64 = base64_encode($data);
                     ?>
                     <input type="hidden" id="event_image_base64" name="event_image_base64" value="<?php echo 'data:image/' . $type . ';base64,' . $base64; ?>" />
                     <input type="hidden" class="for_clone" name="filetouploadname[0][name]" value="<?php echo $events->files[0]->filename; ?>" />
                     <input type="hidden" class="for_clone" name="filetouploadname[0][base64]" value="<?php echo $base64; ?>" />
                     <input type="hidden" class="for_clone" name="filetouploadname[0][type]" value="<?php echo 'image/' . $type; ?>" />

                     <div id="files">
                        <?php if (isset($events) && $events->files[0]->type == 'image') { ?>
                           <img class="uploaded-img myid" src="https://storage.googleapis.com/<?php echo $events->files[0]->bucket ?>/<?php echo $events->files[0]->filename; ?>" />
                           <span class="remove-img" style="cursor: pointer;">-</span>
                        <?php } ?>
                     </div>
                     <span style="display: none;" id="file_succ">
                        <p style="color: #999;">Click image to replace or Drag & Drop
                        </p>
                     </span>
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:0%;">File size should be less than 10 MB.<br />
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:0%;">Image type should be jpg/jpeg/png.<br />
                        </p>
                     </span>
                  </div>
                  <p class="img-resolution">Select an image related to your event to display in search results. Use a high resolution image of 1280 x 720</p>
               </div>
               <div class="event-details mobile-event-details">
                  <input type="hidden" id="start" value="0">
                  <input type="hidden" id="end" value="0">
                  <h3>Venue Information*</h3>

                  <div>
                     <input style="width:99%;" type="text" focusID="ID_TO_FOCUSTO" id="title" name="title" placeholder="Event Title*" required title="Please enter an Event title" value="<?php echo isset($events) ? $events->name : ''; ?>">
                  </div>
                  <input type="hidden" value="<?php echo isset($events) ? count($events->event_dates) : '0'; ?>" id="count" name="count">
                  <div class="evnt-dates">
                     <p><span id="span_start_date">Please select event date.</span>&nbsp;<span id="span_end_date"></span></p>
                  </div>
                  <div>
                     <input style="width:99%;" type="text" name="address1" placeholder="Venue*" id="venue" title="Please enter the Venue name" required value="<?php echo ($events) ? $events->location : ''; ?>" autocomplete="off">
                  </div>
                  <div>
                     <input style="width:99%;" type="text" onFocus="geolocate_event()" name="streetaddress2" id="streetaddress2" placeholder="Address*" title="Please enter Address" required value="<?php echo ($events) ? $events->address2 : ''; ?>" autocomplete="off">
                  </div>
                  <div class="div_country">
                     <select class="Country" name="country" id="country" autocomplete="off" required title="Please select a Country">
                        <option value="">Country*</option>
                        <?php foreach ($countries as $row) { ?>
                           <?php if ($row->id == '2') { ?>
                              <option value="<?php echo $row->id; ?>" <?php echo (isset($events) && $events->country_id == $row->id) ? 'selected' : ''; ?>><?php echo $row->name; ?></option>
                           <?php } ?>
                        <?php } ?>
                     </select>
                  </div>
                  <div class="div_states">
                     <?php if (isset($events)) { ?>
                        <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required>
                           <option value="">Province*</option>
                           <?php foreach ($states as $row) {
                              if ($row->id >= "2" && $row->id <= "14") { ?>
                                 <option value="<?php echo $row->id ?>" <?php echo ($events->province_id == $row->id) ? 'selected' : ''; ?>><?php echo $row->name; ?></option>
                           <?php }
                           } ?>
                        </select>
                     <?php } else { ?>
                        <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required>
                           <option value="">Province*</option>
                           <?php foreach ($states as $row) {
                              if ($row->id >= "2" && $row->id <= "14") { ?>
                                 <option value="<?php echo $row->id ?>"><?php echo $row->name; ?></option>
                           <?php }
                           } ?>
                        </select>
                     <?php } ?>
                  </div>
                  <div class="class_city">
                     <input type="text" name="city" id="city" placeholder="City *" title="Please enter a city" required value="<?php echo isset($events) ? $events->city : ''; ?>">
                  </div>
                  <div class="class_zip">
                     <input type="text" name="postalcode" id="postalcode" placeholder="Postal Code*" required title="Please enter a valid postal code" value="<?php echo isset($events) ? $events->postalcode : ''; ?>">
                  </div>
                  <h3>Description*</h3>
                  <div class="div_description">
                     <textarea rows="4" placeholder="Description" id="description" name="description" required title="Please enter a description of the event"><?php echo isset($events) ? $events->description : ''; ?></textarea>
                  </div>
                  <?php if (isset($events) && count($events->categorys) > 0) {
                     $cats = array_column($events->categorys, 'id');
                  } else {

                     $cats = [];
                  } ?>
                  <h3>Categories* <small> (select one or more)</small></h3>
                  <div>
                     <select name="category_id[]" id="category1_id" multiple="multiple" data-placeholder="Select Category" required title="Please select a category">
                        <option value="">Category *</option>
                        <?php foreach ($categories as $row) {
                           if ($row->api_cat_id != 17) { ?>
                              <option value="<?php echo $row->api_cat_id ?>" <?php echo in_array($row->api_cat_id, $cats) ? 'selected' : ''; ?>><?php echo $row->title; ?></option>
                           <?php } ?>
                        <?php } ?>
                     </select>
                  </div>
                  <!--   <span class="multiple"><b>You can select multiple categories</b></span>-->
                  <span class="multiple"></span>
                  <!-- client asked me to show this in preview mode
                  <p class="get_ticket">
                     <span class="chkbox"> <input class="tix-tkt" id="tix-tkt" type="checkbox" <?php echo isset($tickets) ? 'checked' : ''; ?> name="has_tickets">
                     <span class="checkmark"></span> I would like to create tickets for this event using snapd TIX or a thrid party provider</span>
                  </p>
                  <?php // if(isset($tickets)) { 
                  ?>
                  <p class="tkt-active"><i class="fa fa-check-circle" aria-hidden="true"></i> Tickets Active</p>
                  <input type="button" style="cursor:pointer;" name="create-tkt" value="Modify Tickets" class="modify-tkt" onclick="modifyTkt(<?php echo $events->id; ?>)">
                  <?php // } else { 
                  ?>
                  <div style="display: none;" id="div_add_ticket">
                     <p class="not-compt"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Ticket Setup not Completed</p>
                <a class="add-tkt" href="<?php echo site_url() ?>/create-tickets">Add Tickets</a>
                  </div> -->
                  <?php // } 
                  ?>
                  <h3>Contact Details</h3>
                  <input style="width:99%;" type="text" placeholder="Organization" name="org" value="<?php echo isset($metadata) ? $metadata['org'] : ''; ?>" autocomplete="nope">
                  <input style="width:99%;" type="text" id="website_url" placeholder="Website URL" name="website_url" value="<?php echo isset($events) ? $events->contact_url : ''; ?>" autocomplete="off">
                  <p class="exclude">
                     <input type="text" placeholder="Full Name*" name="contact_name" id="contact_name" class="exclude_input" required title="Please enter First and Last name" value="<?php echo isset($events) ? $events->contact_name : ''; ?>" autocomplete="off">
                     <label class="chkbox">Exclude Name from public listing
                        <input class="tix-tkt" type="checkbox" id="contact_name_check" name="exclude_name" <?php echo $metadata['exclude_name'] == 'on' ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> </label>
                  </p>
                  <input type="hidden" id="exclude_name_id" name="exclude_name" value="<?php echo $metadata['exclude_name'] == 'on' ? 'on' : 'off'; ?>">
                  <p class="exclude">
                     <input type="text" placeholder="(XXX) XXX-XXXX*" name="contact_phone" class="exclude_input" id="contact_phone" required title="Please enter valid Phone number including area code" value="<?php echo isset($events) ? $events->contact_phone : '' ?>" autocomplete="off">
                     <label class="chkbox">Exclude Phone from public listing
                        <input class="tix-tkt" type="checkbox" id="contact_phone_check" name="exclude_phone" <?php echo $metadata['exclude_phone'] == 'on' ? 'checked' : ''; ?>>
                        <span class="checkmark"></span></label>
                  </p>
                  <input type="hidden" id="exclude_phone_id" name="exclude_phone" value="<?php echo $metadata['exclude_phone'] == 'on' ? 'on' : 'off'; ?>">

                  <p class="exclude">
                     <input type="text" placeholder="Extension" id="extension" name="extension" value="<?php echo isset($metadata) ? $metadata['extension'] : ''; ?>" class="exclude_input" autocomplete="off">
                     <!--<span class="chkbox">Exclude Extension from public listing
                     <input class="tix-tkt" type="checkbox" name="exclude_extension">
                     <span class="checkmark"></span> </span>-->
                  </p>

                  <p class="exclude">
                     <input type="email" placeholder="Email*" name="email" id="email" required class="exclude_input" title="Please enter valid Email address" value="<?php echo isset($events) ? $events->contact_email : ''; ?>" autocomplete="off">
                     <label class="chkbox">Exclude Email from public listing
                        <input class="tix-tkt" type="checkbox" id="email_check" name="exclude_email" <?php echo $metadata['exclude_email'] == 'on' ? 'checked' : ''; ?> autocomplete="off">
                        <span class="checkmark"></span></label>
                  </p>
                  <input type="hidden" id="exclude_email_id" name="exclude_email" value="<?php echo $metadata['exclude_email'] == 'on' ? 'on' : 'off'; ?>">
                  <!-- <div class="attachemnts">
                     <div class="logo-details">
                        <div class="up-logo">
                           <h3>Logo</h3>
                           <div class="pre_logo">
                              <?php if (isset($events) && count($events->photos) > 0) {
                                 $i = 0;
                                 foreach ($events->photos as $row) {

                                    if ($row->caption == 'logo') { ?>
                              <img id="logo_image_prev" src="https://storage.googleapis.com/<?php echo $row->file->bucket ?>/<?php echo $row->file->filename; ?>">
                              <a href="<?php echo site_url() ?>?page_id=917&edit=<?php echo $events->id; ?>&delimage=<?php echo $row->id; ?>"><span class="remove-img" style="cursor: pointer;">-</span></a>
                              <?php $i++;
                                    }
                                 }

                                 if ($i <= 0) { ?>
                              <img id="logo_image_prev" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/logo.jpg" alt="uplaod images">
                              <?php }
                              } else { ?>
                              <img id="logo_image_prev" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/logo.jpg" alt="uplaod images">
                              <?php } ?>
                              <span style="display: none;">
                                 <p style="color: #ff0000;">Logo size should be less than 72 kb.<br/>
                                    Logo type should be jpg/jpeg/png.
                                 </p>
                              </span>
                              <input type="file" id="logo_image" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : ''; ?> name="logo_image" onchange="getUploadImageUrl(this, 'logo_image_prev');" style="position: relative;z-index: 999999999999;width: 230px;opacity: 0; height: 150px; margin-top: -150px;">
                           </div>
                        </div>
                        <div class="up-attach">
                           <h3>Attachment</h3>
                           <span>Include a single PDF for all instructions, waivers, map etc...</span>
                           <div>
                              <img src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/img/pdfupload.jpg" alt="uplaod images" id="attach_image_prev">
                              <p style="display: none;" id="pdfname"></p>
                              <span style="display: none;">
                                 <p style="color: #ff0000;">Attachment size should be less than 72 kb.<br/>
                                    Attachment type should be Pdf.
                                 </p>
                              </span>
                              <input type="file" name="attach_image" style="position: relative;z-index: 999999999999;width: 240px;opacity: 0;height: 200px; margin-top: -200px;" onchange="getUploadPdfUrl(this, 'attach_image_prev');">
                           </div>
                        </div>
                     </div>
                     <div class="map-details">
                        <h3>Map Details</h3>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d184551.80858184173!2d-79.51814199446795!3d43.718403811497105!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4cb90d7c63ba5%3A0x323555502ab4c477!2sToronto%2C%20ON%2C%20Canada!5e0!3m2!1sen!2sin!4v1568367410679!5m2!1sen!2sin" width="100%" height="290" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                     </div>
                  </div> -->
                  <div class="attachemnts mobile-attachemnts">
                     <div class="logo-details mobile-logo-details">
                        <h3>Logo</h3>
                        <div style="clear:both"></div>
                        <div id="logodropZone" style="position: relative;" onmouseout="HighlightArea(this.id, false)" ondragleave="    HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
                           <h1>Drag & Drop logo here</h1>
                           <br />
                           <button type="button" style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                           <!--<span>  No file chosen</span>-->
                           <?php if (isset($events) && count($events->files) > 0) {
                              $i = 0;
                              foreach ($events->files as $file) {

                                 if ($file->type == 'logo') {
                                    $i++;
                                    $path = 'https://storage.googleapis.com/' . $file->bucket . '/' . $file->filename;
                           ?>
                                    <input type="hidden" name="logo_image" value="<?php echo $file->filename; ?>">
                                    <input type="hidden" id="logo_image_type" name="logo_image_type" value="<?php echo $file->type; ?>">
                                    <input type="hidden" id="logo_image_base64" name="logo_image_base64" value="<?php get_base64($path); ?>">
                                    <input type="file" id="logo_image" name="logo_image" style="border: none;position: absolute;width: 100%;height:100%;top:0;left:0;z-index:2;opacity:0;cursor: pointer;" onchange="getUploadImageUrl(this, 'logo_image_prev');">
                              <?php }
                              } ?>

                           <?php  } ?>
                           <div id="logo_files">
                              <?php if (isset($events) && count($events->files) > 0) {
                                 $i = 0;
                                 foreach ($events->files as $file) {

                                    if ($file->type == 'logo') {
                                       $i++;
                              ?>
                                       <img class="uploaded-img" src="https://storage.googleapis.com/<?php echo $file->bucket ?>/<?php echo $file->filename; ?>" />
                                       <span class="remove-img" style="cursor: pointer;">-</span>
                              <?php }
                                 }
                              } ?>

                           </div>


                           <span style="display: none;" id="file_succ">
                              <!--  <p style="color: #999;">Click logo to replace or Drag & Drop
                     </p>-->
                           </span>
                           <span style="display: none;" id="file_err">
                              <p style="color: #ff0000;margin-top:11%;">File size should be less than 300 KB.<br />
                              </p>
                           </span>
                           <span style="display: none;" id="file_type_err">
                              <p style="color: #ff0000;margin-top:11%;">Image type should be jpg/jpeg/png.<br />
                              </p>
                           </span>
                        </div>

                        <div class="up-attach">
                           <h3>Attachment</h3>
                           <span>Include a single PDF for all instructions, waivers, map etc...</span>
                           <div>

                              <div style="clear:both"></div>





                              <div id="pdfdropZone" style="position: relative;" onmouseout="HighlightArea(this.id, false)" ondragleave="    HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
                                 <h1 id="DDText1">Drag & Drop a single PDF here</h1><i class="fal fa-file-pdf"></i>
                                 <button type="button" id="choose_pdf_div" style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                                 <!--<span>  No file chosen</span>-->
                                 <input type="file" id="pdf_image" name="attach_image" style="border: none;position: absolute;width: 100%;height:100%;top:0;left:0;z-index:2;opacity:0;cursor: pointer;" value="">
                                 <?php if (isset($events) && count($events->files) > 0) {
                                    $i = 0;
                                    foreach ($events->files as $file) {
                                       if ($file->type == 'Pdf file') {
                                          $i++;
                                          $path = 'https://storage.googleapis.com/' . $file->bucket . '/' . $file->filename;
                                          $filename = $file->filename;
                                 ?>
                                          <input type="hidden" name="attach_image" value="<?php echo $filename; ?>">
                                          <input type="hidden" name="attach_image_base64" value="<?php get_base64($path); ?>">
                                 <?php }
                                    }
                                 } ?>
                                 <div id="pdf_files"></div>
                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;">
                                       <?php echo isset($filename) ? $filename : ''; ?></p>
                                 </span>
                                 <?php if (isset($events) && count($events->files) > 0) {
                                    foreach ($events->files as $file) {
                                       if ($file->type == 'Pdf file') { ?>
                                          <script>
                                             isset($_SESSION['event_data']);
                                             jQuery(" #file_name").show();
                                             jQuery("#DDText1").hide();
                                             jQuery("#pdfdropZone").css({
                                                "background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')",
                                                "border": "2px dashed #ffffff"
                                             });
                                             jQuery("#choose_pdf_div").hide();
                                             jQuery(jQuery.parseHTML('<span class="remove-img" style="cursor: pointer;">-</span>')).appendTo('#pdfdropZone');
                                          </script><?php }
                                             }
                                          } ?>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">File size should be less than 10 mb.<br />
                                       <!--File type should be pdf only.-->
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">File type should be pdf only.<br />
                                       <!--File type should be pdf only.-->
                                    </p>
                                 </span>
                              </div>


                           </div>
                        </div>
                        <!--    <p class="adober">Adobe Reader Required.</p>
                           <a href="#"> Download here </a>-->

                     </div>
                     <div class="map-details mobile-map-details col-lg-6">
                        <h3>Map Details</h3>
                        <?php
                        $mapsrc = (isset($events) && isset($events->long)) ? "https://webdev.snapd.com/map.php?lat=" . $events->lat . "&lng=" . $events->long : "https://webdev.snapd.com/map.php?lat=56.1304&lng=-106.346771";
                        ?>
                        <input type="hidden" id="mapLat" name="lat" value="<?php echo $events->lat; ?>">
                        <input type="hidden" id="mapLong" name="long" value="<?php echo $events->long; ?>">
                        <iframe class="mapIframe" src="<?php echo $mapsrc; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                     </div>
                  </div>
               </div>
               <button class="next-btn make-center" type="submit" name="btnSubmit">NEXT</button>
               <a href="<?php echo site_url() ?>/event-dashboard"><button class="next-btn make-center" type="button" style="margin: 6px; padding-bottom: 8px;">CANCEL</button></a>
            <?php } else { ?>
               <?php if (isset($_SESSION['event_data'])) {
                     for ($i = 0; $i < count($_SESSION['event_data']['event_start_date']); $i++) {

                        if ($i == 0) { ?>
                        <div class="event-dates mobile-event-dates">
                           <label style="cursor: pointer;" class="start-date col-lg-3 mb-2 pb-2" for="single_start_date">Start <input type="text" id="single_start_date" required class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo $_SESSION['event_data']['event_start_date'][$i]; ?>"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                           <label style="cursor: pointer;" class="start-date col-lg-3 mb-2 pb-2" for="single_end_date">End <input type="text" id="single_end_date" required class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo $_SESSION['event_data']['event_end_date'][$i]; ?>"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                        </div>
                     <?php } else { ?>
                        <div class="add_more_div event-dates mobile-event-dates" id="multi_div_<?php echo $i; ?>">
                           <label style="cursor: pointer;" class="start-date" for="multi_start_date_<?php echo $i; ?>">Start<input type="text" required class="start_datepicker multi_start_date" id="multi_start_date_<?php echo $i; ?>" name="event_start_date[]" data-number="<?php echo $i; ?>" value="<?php echo $_SESSION['event_data']['event_start_date'][$i]; ?>" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                           <label style="cursor: pointer;" class="start-date" for="multi_end_date_<?php echo $i; ?>">End<input type="text" required class="start_datepicker multi_end_date" id="multi_end_date_<?php echo $i; ?>" name="event_end_date[]" data-number="<?php echo $i; ?>" value="<?php echo $_SESSION['event_data']['event_end_date'][$i]; ?>" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                           <span class="remove-date" style="cursor:pointer;" data-id="<?php echo $i; ?>"> - </span>
                        </div>
                  <?php }
                     }
                  } else { ?>
                  <div class="event-dates mobile-event-dates">
                     <label style="cursor: pointer;" class="start-date col-lg-3 mb-2 pb-2" for="single_start_date">Start <input type="text" required id="single_start_date" class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                     <label style="cursor: pointer;" class="start-date col-lg-3 mb-2 pb-2" for="single_end_date">End <input type="text" required id="single_end_date" class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                     <!--
                  <label style="cursor: pointer;" class="start-date" for="single_start_date">Start
                 <div class="single_start_date_2"> </div>
                 <input type="hidden" required id="single_start_date" class="start_datepicker " name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET">
                 <small style="font-size: 12px !important; font-weight: normal;">Select to change</small>
              </label>
              <label style="cursor: pointer;" class="start-date" for="single_end_date">End
                  <div class="single_end_date_2"> </div>
                 <input type="hidden" required id="single_end_date" class="end_datepicker" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET">
                 <small style="font-size: 12px !important; font-weight: normal;">Select to change</small>
              </label>

              <label style="cursor: pointer;width:350px" class="start-date" for="single_start_date">Start and End Date
               <input type="text" required id="single_start_date" class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET">
               <small style="font-size: 12px !important; font-weight: normal;">Select to change</small>
               </label>
               -->
                  </div>
               <?php } ?>
               <!-- <span class="remove-date" style="cursor:pointer" data-id="p_single"> - </span>-->
               <a href="#" style="display: <?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['event_status_id'] == '2' ? '' : 'none'; ?>;" id="add_more"><span> + </span>Add a new date</a>
            </div>
            <hr />
            <p><b>All required fields marked with (*).</b></p>
            <div class="event-detail mobile-event-detail">
               <div class="upload-image mobile-upload-image">
                  <h3>Event Image*</h3>
                  <div class="img-upload-outer" id="DragAReahight" onmouseout="HighlightArea(this.id, false)" ondragleave="HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
                     <input type="file" id="fileupload" name="fileToUpload" value="<?php echo isset($_SESSION['event_data']['event_image_base64']) && $_SESSION['event_data']['event_image_base64'] != '' ? $_SESSION['event_data']['event_image_base64'] : 'required'; ?>" style="border: none; position: absolute;top: 50px;left: 0;z-index: 2;opacity: 0;cursor: pointer;height: 254px;width: 100%;" <?php echo isset($_SESSION['event_data']['event_image_base64']) && $_SESSION['event_data']['event_image_base64'] != '' ? '' : 'required'; ?> title="Please Upload Event Image.">
                     <div id="dropZone" style="text-align: center;">
                        <h1>Drag & Drop event image here...</h1>
                        <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                        <!--<span>  No file chosen</span>-->
                     </div>
                     <input type="hidden" id="event_image_base64" name="event_image_base64" value="<?php echo isset($_SESSION['event_data']['event_image_base64']) ? $_SESSION['event_data']['event_image_base64'] : ''; ?>" />
                     <input type="hidden" class="for_clone" name="filetouploadname[0][name]" value="<?php echo $_SESSION['event_data']['filetouploadname'][0]['name'] ?>">
                     <input type="hidden" class="for_clone" name="filetouploadname[0][base64]" value="<?php echo $_SESSION['event_data']['filetouploadname'][0]['base64']; ?>">
                     <input type="hidden" class="for_clone" name="filetouploadname[0][type]" value="<?php echo $_SESSION['event_data']['filetouploadname'][0]['type']; ?>">
                     <div id="files">
                        <?php if (isset($_SESSION['event_data'])) { ?>
                           <img class="uploaded-img" src="<?php echo $_SESSION['event_data']['event_image_base64'] ?>" />
                           <span class="remove-img" style="cursor: pointer;">-</span>
                        <?php } ?>
                     </div>
                     <span style="display: none;" id="file_succ">
                        <p style="color: #999;">Click image to replace or Drag & Drop
                        </p>
                     </span>
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:0%;">File size should be less than 10 MB.<br />
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:0%;">Image type should be jpg/jpeg/png.<br />
                        </p>
                     </span>
                  </div>
                  <p class="img-resolution">Select an image related to your event to display in search results. Use a high resolution image of 1280 x 720</p>
               </div>

               <div class="event-details mobile-event-details">
                  <input type="hidden" id="start" value="0">
                  <input type="hidden" id="end" value="0">
                  <h3>Venue Information*</h3>
                  <div>
                     <input style="width:99%;" type="text" focusID="ID_TO_FOCUSTO" id="title" name="title" placeholder="Event Title*" required title="Please enter an Event title" value="<?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['title']) : ''; ?>">
                  </div>
                  <input type="hidden" value="<?php echo isset($_SESSION['event_data']) ? $_SESSION['event_data']['count'] : '0'; ?>" id="count" name="count">
                  <div class="evnt-dates">
                     <?php if (isset($_SESSION['event_data'])) {
                        for ($i = 0; $i < count($_SESSION['event_data']['event_start_date']); $i++) {

                           $start_date = $_SESSION['event_data']['event_start_date'][$i];
                           $start_date1 = str_replace(array('am', 'pm'), '', $start_date);
                           $end_date = $_SESSION['event_data']['event_end_date'][$i];
                           $end_date1 = str_replace(array('am', 'pm'), '', $end_date);

                           if (date('Y-m-d', strtotime($start_date1)) == date('Y-m-d', strtotime($end_date1))) {

                              $end_date = 'to ' . date('h:i a', strtotime($end_date1));
                           } else {

                              $end_date = 'to ' . $end_date;
                           }

                           if ($i == 0) { ?>
                              <p><span id="span_start_date"><?php echo $_SESSION['event_data']['event_start_date'][$i]; ?></span>&nbsp;<span id="span_end_date"><?php echo $end_date; ?></span></p>
                           <?php } else { ?>
                              <p class="multi_span" id="p_<?php echo $i; ?>"><span id="span_start_date_<?php echo $i; ?>"><?php echo $_SESSION['event_data']['event_start_date'][$i]; ?></span>&nbsp;<span id="span_end_date_<?php echo $i; ?>"><?php echo $end_date; ?></span></p>
                        <?php }
                        }
                     } else { ?>
                        <p><span id="span_start_date">Please select event date.</span>&nbsp;<span id="span_end_date"></span></p>
                     <?php } ?>
                  </div>
                  <div>
                     <input style="width:99%;" type="text" name="address1" placeholder="Venue*" id="venue" title="Please enter the venue name" required value="<?php echo (isset($_SESSION['event_data'])) ? stripslashes($_SESSION['event_data']['address1']) : ''; ?>" autocomplete="off">
                  </div>
                  <div>
                     <input style="width:99%;" type="text" onFocus="geolocate_event()" name="streetaddress2" id="streetaddress2" placeholder="Address*" title="Please enter the address" autocomplete="none" required value="<?php echo (isset($_SESSION['event_data'])) ? stripslashes($_SESSION['event_data']['streetaddress2']) : ''; ?>">
                  </div>
                  <div class="div_country">
                     <select class="Country" name="country" id="country" autocomplete="off" required title="Please select a country">
                        <option value="">Country*</option>
                        <?php
                        echo "<option value='2' selected>Canada</option>";
                        /*
                        foreach($countries as $row){ ?>
                        <?php if($row->id == '2'){ ?>
                        <option value="<?php echo $row->id;?>" <?php echo (isset($_SESSION['event_data']) && $_SESSION['event_data']['country'] == $row->id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                        <?php } ?>
                        <?php }
*/
                        ?>
                     </select>
                  </div>
                  <div class="div_states">
                     <?php if (isset($_SESSION['event_data'])) { ?>
                        <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required>
                           <option value="">Province*</option>
                           <?php foreach ($states as $row) {
                              if ($row->id >= "2" && $row->id <= "14") { ?>
                                 <option value="<?php echo $row->id ?>" <?php echo ($_SESSION['event_data']['state'] == $row->id) ? 'selected' : ''; ?>><?php echo $row->name; ?></option>
                           <?php }
                           } ?>
                        </select>
                     <?php } else { ?>
                        <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required>
                           <option value="">Province*</option>
                           <?php foreach ($states as $row) {
                              if ($row->id >= "2" && $row->id <= "14") { ?>
                                 <option value="<?php echo $row->id ?>"><?php echo $row->name; ?></option>
                           <?php }
                           } ?>
                        </select>
                     <?php } ?>
                  </div>
                  <div class="div_city">
                     <input type="text" name="city" id="city" placeholder="City*" title="Please enter a city" value="<?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['city']) : ''; ?>" required>
                  </div>
                  <div class="div_zip">
                     <input type="text" name="postalcode" id="postalcode" placeholder="Postal Code*" title="Please enter a valid postal code" value="<?php echo isset($_SESSION['event_data']) ? $_SESSION['event_data']['postalcode'] : ''; ?>" required>
                  </div>
                  <h3>Description*</h3>
                  <div class="div_description">

                     <textarea rows="4" placeholder="Description" id="description" name="description" title="Please enter a description of the event" required><?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['description']) : ''; ?></textarea>
                  </div>
                  <h3>Categories*<small> (select one or more)</small></h3>
                  <div class="div_catg">
                     <select name="category_id[]" id="category1_id" multiple="multiple" data-placeholder="Select Category" required title="Please select a category">
                        <option value="">Category *</option>
                        <?php foreach ($categories as $row) {
                           if ($row->api_cat_id != 17) { ?>
                              <option value="<?php echo $row->api_cat_id ?>" <?php echo isset($_SESSION['event_data']) && in_array($row->api_cat_id, $_SESSION['event_data']['category_id']) ? 'selected' : ''; ?>><?php echo $row->title; ?></option>
                           <?php } ?>
                        <?php } ?>
                     </select>
                  </div>
                  <span class="multiple"></span>
                  <!-- client asked me to show this in preview mode
                  <p class="get_ticket">
                     <span class="chkbox"> <input class="tix-tkt" id="tix-tkt" type="checkbox" name="has_tickets" <?php echo isset($_SESSION['ticket_data']) ? 'checked' : ''; ?>>
                     <span class="checkmark"></span> I would like to create tickets for this event using snapd TIX or a thrid party provider</span>
                  </p>
                  <?php //if(isset($_SESSION['ticket_data'])) { 
                  ?>
                  <p class="tkt-active"><i class="fa fa-check-circle" aria-hidden="true"></i> Tickets Active</p>
                  <input type="button" name="create-tkt" value="Modify Tickets" class="modify-tkt" onclick="modify_tickets()" style="cursor: pointer;">
                  <?php //} else { 
                  ?>
                  <div style="display: none;" id="div_add_ticket">
                     <p class="not-compt"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Ticket Setup not Completed</p>

                <input type="submit" name="btnAddTicket" value="Add Tickets" class="add-tkt" style="cursor:pointer;" formaction="<?php echo site_url() ?>/create-tickets">


                  </div>
                  <? php // } 
                  ?> -->
                  <h3>Contact Details</h3>
                  <div>
                     <input style="width:99%;" type="text" placeholder="Organization" name="org" value="<?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['org']) : ''; ?>" autocomplete="nope">
                  </div>
                  <div>
                     <input style="width:99%;" type="text" placeholder="Website URL" id="website_url" name="website_url" value="<?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['website_url']) : ''; ?>">
                  </div>
                  <p class="exclude w-100">
                     <input type="text" placeholder="Full Name*" name="contact_name" id="contact_name" class="exclude_input" required title="Please enter First" value="<?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['contact_name']) : $_SESSION['userdata']->first . ' ' . $_SESSION['userdata']->last; ?>">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="contact_name_check" name="exclude_name" <?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['exclude_name'] == 'on' ? 'checked' : ''; ?>>
                        <span class="checkmark"></span> <label class="chkbox" for="contact_name_check">Exclude Name from public listing</label></span>
                  </p>
                  <input type="hidden" name="exclude_name" value="<?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['exclude_name'] == 'on' ? 'on' : 'off'; ?>">

                  <p class="exclude">
                     <input type="text" placeholder="(XXX) XXX-XXXX*" name="contact_phone" class="exclude_input" id="contact_phone" required title="Please enter valid Phone number" value="<?php echo isset($_SESSION['event_data']) ? $_SESSION['event_data']['contact_phone'] : $_SESSION['userdata']->number; ?>">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="contact_phone_check" name="exclude_phone" <?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['exclude_phone'] == 'on' ? 'checked' : ''; ?>>
                        <span class="checkmark"></span><label class="chkbox" for="contact_phone_check">Exclude Phone from public listing</label></span>
                  </p>
                  <input type="hidden" name="exclude_phone" value="<?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['exclude_phone'] == 'on' ? 'on' : 'off'; ?>">

                  <p class="exclude">
                     <input type="text" placeholder="Extension" name="extension" id="extension" value="<?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['extension'] != '' ? stripslashes($_SESSION['event_data']['extension']) : ''; ?>" class="exclude_input" autocomplete="nope">
                     <!-- <span class="chkbox">Exclude Extension from public listing
                     <input class="tix-tkt" type="checkbox" name="exclude_extension">
                     <span class="checkmark"></span> </span>-->
                  </p>

                  <p class="exclude">
                     <input type="email" placeholder="Email*" name="email" id="email" class="exclude_input" required title="Please enter valid Email address" value="<?php echo isset($_SESSION['event_data']) ? stripslashes($_SESSION['event_data']['email']) : $_SESSION['userdata']->email; ?>">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="email_check" name="exclude_email" <?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['exclude_email'] == 'on' ? 'checked' : ''; ?>>
                        <span class="checkmark"></span><label class="chkbox" for="email_check">Exclude Email from public listing</label></span>
                  </p>
                  <input type="hidden" name="exclude_email" value="<?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['exclude_email'] == 'on' ? 'on' : 'off'; ?>">

                  <div class="attachemnts mobile-attachemnts">
                     <div class="logo-details mobile-logo-details">
                        <h3>Logo</h3>
                        <div style="clear:both"></div>
                        <div id="logodropZone" style="position: relative;" onmouseout="HighlightArea(this.id, false)" ondragleave="    HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
                           <h1>Drag & Drop logo here</h1>
                           <br />
                           <button type="button" style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                           <!--<span>  No file chosen</span>-->
                           <input type="file" id="logo_image" name="logo_image" style="border: none;position: absolute;width: 100%;height:100%;top:0;left:0;z-index:2;opacity:0;cursor: pointer;" onchange="getUploadImageUrl(this, 'logo_image_prev');">
                           <?php if (isset($_SESSION['event_data']['logo_image']) && $_SESSION['event_data']['logo_image'] != '') { ?>
                              <input type="hidden" id="logo_image_base64" name="logo_image_base64" value="<?php echo $_SESSION['event_data']['logo_image_base64'] ?>">
                              <input type="hidden" name="logo_image" value="<?php echo $_SESSION['event_data']['logo_image'] ?>">
                              <input type="hidden" id="logo_image_type" name="logo_image_type" value="<?php echo $_SESSION['event_data']['logo_image_type'] ?>">
                           <?php } ?>
                           <div id="logo_files">
                              <?php if (isset($_SESSION['event_data']['logo_image_base64']) && $_SESSION['event_data']['logo_image_base64'] != '') { ?>
                                 <img class="uploaded-img" src="data:<?php echo $_SESSION['event_data']['logo_image_type'] ?>;base64,<?php echo $_SESSION['event_data']['logo_image_base64'] ?>" />
                                 <span class="remove-img" style="cursor: pointer;">-</span>
                              <?php } ?>

                           </div>


                           <span style="display: none;" id="file_succ">
                              <!--  <p style="color: #999;">Click logo to replace or Drag & Drop
                     </p>-->
                           </span>
                           <span style="display: none;" id="file_err">
                              <p style="color: #ff0000;margin-top:11%;">File size should be less than 300 KB.<br />
                              </p>
                           </span>
                           <span style="display: none;" id="file_type_err">
                              <p style="color: #ff0000;margin-top:11%;">Image type should be jpg/jpeg/png.<br />
                              </p>
                           </span>
                        </div>

                        <div class="up-attach">
                           <h3>Attachment</h3>
                           <span>Include a single PDF for all instructions, waivers, map etc...</span>
                           <div>

                              <div style="clear:both"></div>

                              <?php if (isset($_SESSION['event_data']['attach_image']) && $_SESSION['event_data']['attach_image'] != '') { ?>
                                 <input type="hidden" name="attach_image" value="<?php echo $_SESSION['event_data']['attach_image']; ?>">
                                 <input type="hidden" name="attach_image_base64" value="<?php echo $_SESSION['event_data']['attach_image_base64']; ?>">
                              <?php } ?>

                              <div id="pdfdropZone" style="position: relative;" onmouseout="HighlightArea(this.id, false)" ondragleave="    HighlightArea(this.id, false)" ondragenter="HighlightArea(this.id, true)" ondrop="HighlightArea(this.id, false)">
                                 <h1 id="DDText1">Drag & Drop a single PDF here</h1><i class="fal fa-file-pdf"></i>
                                 <button type="button" id="choose_pdf_div" style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                                 <!--<span>  No file chosen</span>-->
                                 <input type="file" id="pdf_image" name="attach_image" style="border: none;position: absolute;width: 100%;height:100%;top:0;left:0;z-index:2;opacity:0;cursor: pointer;" value="<?php echo $session_pdf; ?>">
                                 <div id="pdf_files"></div>
                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;"><?php echo isset($_SESSION['event_data']) && $_SESSION['event_data']['attach_image'] != '' ? $_SESSION['event_data']['attach_image'] : ''; ?></p>
                                 </span>
                                 <?php if (isset($_SESSION['event_data']) && $_SESSION['event_data']['attach_image'] != '') {
                                 ?> <script>
                                       jQuery("#pdf_image").siblings("#file_name").show();
                                       jQuery("#DDText1").hide();
                                       jQuery("#pdfdropZone").css({
                                          "background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')",
                                          "border": "2px dashed #ffffff"
                                       });
                                       jQuery("#choose_pdf_div").hide();
                                       jQuery(jQuery.parseHTML('<span class="remove-img" style="cursor: pointer;">-</span>')).appendTo('#pdfdropZone');
                                    </script>
                                 <?php
                                 } ?>

                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">File size should be less than 10 mb.<br />
                                       <!--File type should be pdf only.-->
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">File type should be pdf only.<br />
                                       <!--File type should be pdf only.-->
                                    </p>
                                 </span>
                              </div>

                           </div>
                        </div>
                        <!--    <p class="adober">Adobe Reader Required.</p>
                           <a href="#"> Download here </a>-->

                     </div>
                     <div class="map-details mobile-map-details">
                        <h3>Map Details</h3>
                        <?php
                        $mapsrc = (isset($_SESSION['event_data']['lat']) && isset($_SESSION['event_data']['long'])) ? "https://webdev.snapd.com/map.php?lat=" . $_SESSION['event_data']['lat'] . "&lng=" . $_SESSION['event_data']['long'] : "https://webdev.snapd.com/map.php?lat=56.1304&lng=-106.346771";
                        ?>
                        <iframe class="mapIframe" src="<?php echo $mapsrc; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                        <input type="hidden" id="mapLat" name="lat" value="">
                        <input type="hidden" id="mapLong" name="long" value="">
                     </div>
                  </div>
               </div>
               <?php if (isset($_SESSION['event_data'])) { ?>
                  <button style="margin-left: 2.5rem;" class="next-btn make-center" type="submit" name="btnSubmit">NEXT</button>
               <?php } else { ?>
                  <button style="margin-left: 2.5rem;" class="next-btn make-center" type="submit" name="btnSubmit">NEXT</button>
               <?php } ?>
               <!--a href="<?php echo site_url() ?>/event-dashboard"-->
               <button onClick="jQuery(this).css('width','350px');jQuery(this).text('Cancelling new event...');setTimeout(function(){ window.location.href='<?php echo site_url() ?>/event-dashboard'; }, 2000);" class="next-btn cancel-btn make-center" type="button" style="margin: 6px; padding-bottom: 8px;">CANCEL</button>
               <!--/a-->
            <?php } ?>

         </form>
         <div class="help-btn"><i class="fa fa-question"></i> Need Help? <a href="#">Visit our support site for answers</a></div>
      </div>
   </div>
</div>
</div>
<!-- #main content -->

<!-- cropper modal start -->
<div class="modal fade img_crop" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
   <div class="modal-dialog myModal modal-dialog-centered " role="document">
      <div class="modal-content">
         <div class="modal-header d-none d-lg-block">
            <h5 class="modal-title h3  ">Crop Image Before Upload</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body">

            <div class="row">
               <div class="col-md-12">
                  <div class="img-container" style="object-fit: contain; width: 100% !important;
    height: 100% !important;">
                     <img id="imageforcrop" class="img-responsive" src="" style="object-fit: contain; width: 100% !important;
    height: 100% !important;">
                  </div>
               </div>

            </div>

         </div>
         <div class=" modal-footer">
            <div class="d-none d-lg-block"><b>1.</b> Use corner and midpoint grips to resize crop area <br />
               <b> 2.</b> Select crop area to reposition <br />
               <b>3.</b> Drag on photo for new crop area
            </div>
            <div class="d-flex justify-content-center justify-content-md-start ">
               <button type="button" id="crop" class="btn btn-primary mr-2" style="margin-right:1rem;">Crop</button>
               <button type="button" class="ml-2 btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- cropper modal ends -->
<!-- progress modal start-->
<div class="modal" id="loadingModal" role="dialog">
   <div class="modal-dialog modal-dialog-centered modal-lg" style="width: 220px !important;">
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
<!-- progress modal ends-->
<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/cropper.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?php echo site_url() ?>/wp-content/themes/Divi Child/js/customImage.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw&libraries=places&callback=initAutocomplete" async defer></script>
<script>
   jQuery.validator.addMethod("notset", function(value, element) {

      return value != 'NOT SET';
   }, "Please select date");

   jQuery.validator.addMethod("postalcode", function(value, element) {

      if (jQuery('#country').val() == '2') {
         return this.optional(element) || /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/.test(value);
      } else {
         return true;
      }
   }, "Postalcode is invalid");
   /*
   jQuery(document).on('click', '#tix-tkt', function () {
       if(jQuery(this).is(':checked')) {
           jQuery('#div_add_ticket').show();
       } else {
           jQuery('#div_add_ticket').hide();
       }
   });

    jQuery(document).ready(function(){
         jQuery("input[name=btnAddTicket]").click(function(){
             jQuery("#event_form").find("input").attr("required",false);
             jQuery("#event_form").find("select").attr("required",false);
             jQuery("#event_form").find("textarea").attr("required",false);
             jQuery( "#postalcode" ).rules( "remove" );
             jQuery( "#single_start_date" ).rules( "remove" );
             jQuery( "#single_end_date" ).rules( "remove" );
             jQuery( "#category1_id" ).rules( "remove" );
          jQuery("input[name=btnAddTicket]").click();
        });
   });


   function modify_tickets() {
       window.location.href = "<?php echo site_url() ?>/create-tickets";
   }

   function modifyTkt(event_id) {
       window.location.href = "<?php echo site_url() ?>/create-tickets&edit="+event_id;
   }    */
</script>
<style>
   .modal-footer {
      display: block;
   }

   .instructions {
      float: left;
      width: 71%;
      padding-left: 15px;
      line-height: 20px;
      font-size: 15px;
   }
</style>
<script>
   jQuery(document).on('change', '#contact_phone_check', function() {
      if ($(this).prop("checked") == true) {
         $('input[name="exclude_phone"]:hidden').val("on");
      } else {
         $('input[name="exclude_phone"]:hidden').val("off");
      }
   });


   jQuery(document).on('change', '#contact_name_check', function() {
      if ($(this).prop("checked") == true) {
         $('input[name="exclude_name"]:hidden').val("on");
      } else {
         $('input[name="exclude_name"]:hidden').val("off");
      }
   });


   jQuery(document).on('change', '#email_check', function() {
      if ($(this).prop("checked") == true) {
         $('input[name="exclude_email"]:hidden').val("on");
      } else {
         $('input[name="exclude_email"]:hidden').val("off");
      }
   });

   function get_pdf_session() {
      jQuery(input).siblings("#file_name").show();
      jQuery("#DDText1").hide();
      jQuery("#pdfdropZone").css({
         "background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')",
         "border": "2px dashed #ffffff"
      });
      jQuery("#choose_pdf_div").hide();
   }


   // If theres no activity for 5 seconds do something
   var activityTimeout = setTimeout(inActive, 600000);

   function resetActive() {
      jQuery(document.body).attr('class', 'active');
      clearTimeout(activityTimeout);
      activityTimeout = setTimeout(inActive, 600000);
   }

   // No activity do something.
   function inActive() {
      jQuery(document.body).attr('class', 'inactive');
      alert('Your event creation has timed out.');
      jQuery(window).focus(function() {
         window.location.href = "https://webdev.snapd.com/event-dashboard/";
      });
   }

   // Check for mousemove, could add other events here such as checking for key presses ect.
   jQuery(document).bind('mousemove', function() {
      resetActive()
   });
</script>
<?php get_footer(); ?>
<?php // if(isset($_SESSION['event_data'])){ echo "<pre>"; print_r($_SESSION['event_data']); } 
?>
<?php // if(isset($events)){ echo "<pre>"; print_r($events); print_r($metadata); } 
?>
<?php // if(isset($tickets)){ echo "<pre>"; print_r($tickets); } 
?>