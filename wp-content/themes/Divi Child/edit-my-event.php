    <?php
   /*
   Template Name: Edit Event
   */

   if(!isset($_SESSION['Api_token'])){
       wp_redirect( site_url().'?page_id=187' );
       exit;
   }
   global $wpdb;
   $token   =  $_SESSION['Api_token'];
   $user = $_SESSION['userdata'];

   // if(isset($_POST['edit']) && $_POST['edit'] != '') {

   //     if($_POST['eventstate'] == 'upcoming' || $_POST['eventstate'] == 'past') {

   //         $countries = $wpdb->get_results("Select * from wp_countries");
   //         $states = $wpdb->get_results("Select * from wp_states");

   //         $ch      = curl_init(API_URL.'events/'.$_POST['edit']);
   //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   //         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   //             'Content-Type: application/json',
   //             'Authorization: ' . $token
   //         ));
   //         $response = curl_exec($ch);
   //         curl_close($ch);
   //         $events = json_decode($response);
   //         $event_state = $_POST['eventstate'];
   //     } else {

   //         header("Location: ".site_url().'/manage-my-events');
   //         exit();
   //     }
   // }

   if(isset($_GET['event_id']) && $_GET['event_id'] != '') {

      if($_GET['eventstate'] == 'upcoming' || $_GET['eventstate'] == 'past') {

          $countries = $wpdb->get_results("Select * from wp_countries");
          $states = $wpdb->get_results("Select * from wp_states");

          $ch      = curl_init(API_URL.'events/'.$_GET['event_id']);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Authorization: ' . $token
          ));
          $response = curl_exec($ch);
          curl_close($ch);
          $events = json_decode($response);

          if($events->event->drupal_user_id != $user->id){

            header("Location: ".site_url().'/manage-my-events?action=invalid');
            exit();
          }
          $event_state = $_GET['eventstate'];
          $_SESSION['eventstate'] = $event_state;
      } else {

          header("Location: ".site_url().'/manage-my-events');
          exit();
      }
  }

   if($events->success) {

       $events = $events->event;

       $ch      = curl_init(API_URL.'ticketTypes?eventId='.$events->id);
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

       $metadata = unserialize($events->metadata);

           if(isset($metadata['file_id'])) {

               $ch   = curl_init(API_URL.'files/'.$metadata['file_id']);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/pdf',
                   'Authorization: ' . $token
               ));
               $result112 = curl_exec($ch);
               curl_close($ch);
               $fileresponse=json_decode($result112);

               if($fileresponse->success) {

                   $file = $fileresponse->file;
               }
           }

   //  echo "<pre>"; print_r($events);  print_r($metadata);  print_r($file); die;

   } else {

       header("Location: ".site_url().'/manage-my-events');
       exit();
   }
$categories = $wpdb->get_results("SELECT * FROM api_category order by title ASC");/*
   $ch      = curl_init(API_URL.'categories?sort=ASC&sortType=name');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Authorization: ' . $token
   ));
   $result = curl_exec($ch);
   curl_close($ch);
   $apirespons=json_decode($result);

   if($apirespons->success) {
       $categories = $apirespons->categories;
   } */

   get_header();

   if(isset($_POST['btnUpdate'])) {

       $data    = array(

           'description' => $_POST['description'],
           "drupal_user_id"=> $_SESSION['userdata']->id
       );

       if(isset($_POST['country'])) {

           $country_id = $_POST['country'];
           $country = $wpdb->get_row("Select * from wp_countries where id=$country_id");
           $data['country'] = $country->sortname;
           $data['country_id'] = $country->id;
       }

       if(isset($_POST['state'])) {

           $state_id = $_POST['state'];
           $state = $wpdb->get_row("Select * from wp_states where id=$state_id");
           $data['province'] =  $state->state_code;
           $data['province_id'] =  $state->id;
       }

       if(isset($_POST['contact_phone'])) {

           $phone = '';

           if($_POST['contact_phone'] != '') {
               $ph = explode(' ', $_POST['contact_phone']);
               $ph1 = trim($ph[0], '()');
               $ph2 = str_replace('-', '', $ph[1]);
               $phone = $ph1.$ph2;
           }

           $data['contact_phone'] = $phone;
       }

       if(isset($_POST['event_status_id'])) {

           $data['event_status_id'] = $_POST['event_status_id'];
       }

       if(isset($_POST['title'])) {

           $data['name'] = $_POST['title'];
       }

       if(isset($_POST['address1'])) {

           $data['location'] = $_POST['address1'];
       }

       if(isset($_POST['address2'])) {

           $data['address2'] = $_POST['address2'];
       }

       if(isset($_POST['city'])) {

           $data['city'] = $_POST['city'];
       }

       if(isset($_POST['postalcode'])) {

           $data['postalcode'] = $_POST['postalcode'];
       }

       if(isset($_POST['contact_name'])) {

           $data['contact_name'] = $_POST['contact_name'];
       }

       if(isset($_POST['email'])) {

           $data['contact_email'] = $_POST['email'];
       }

       if(isset($_POST['website_url'])) {

           $data['contact_url'] = $_POST['website_url'];
       }

       if(isset($_POST['category_id'])) {

           $data['categories'] = $_POST['category_id'];
       }

       if(isset($_POST['event_start_date'])) {

           $dateRanges=[];

           for($j=0; $j<count($_POST['event_start_date']); $j++) {

               //$start_date = str_replace('am', '', $_POST['event_start_date'][$j]);
               //$start_date = str_replace('pm', '', $start_date);
               //$end_date = str_replace('am', '', $_POST['event_end_date'][$j]);
               //$end_date = str_replace('pm', '', $end_date);

               $dateRanges[$j] = array(date('Y-m-d H:i', strtotime($_POST['event_start_date'][$j])), date('Y-m-d H:i', strtotime($_POST['event_end_date'][$j])));
           }

           $data['dateRanges'] = $dateRanges;
       }

       if(isset($_POST['remove_img']) && $_POST['remove_img'] != '' ){
           $data['r_img']    = $_POST['remove_img'];
           $data['r_img_id'] = $_POST['remove_img_id'];
       }

       if(isset($_POST['remove_logo']) && $_POST['remove_logo'] != '' ){
           $data['r_logo']    = $_POST['remove_logo'];
           $data['r_logo_id'] = $_POST['remove_logo_id'];
       }

       if(isset($_POST['remove_pdf']) && $_POST['remove_pdf'] != '' ){
           $data['r_pdf']    = $_POST['remove_pdf'];
           $data['r_pdf_id'] = $_POST['remove_pdf_id'];
       }

       if($_FILES['fileToUpload']['name'] != '') {
           // die("test");
           $type = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
           //$file = file_get_contents($_FILES['fileToUpload']['tmp_name']);
           $base64 = $_POST['event_image_base64'];
           $filetouploadname = date('Ymd').time().rand(0, 9999).'.'.$type;
           $data['filetouploadname'] = $filetouploadname;
           $data['filetouploadname_base64'] = $base64;
       }

       if($_FILES['logo_image']['name'] != '') {
           $type = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
           $file = file_get_contents($_FILES['logo_image']['tmp_name']);
           $base64 = base64_encode($file);
           $logo_image = date('Ymd').time().rand(0, 9999).'.'.$type;
           $data['logo_image'] = $logo_image;
           $data['logo_image_base64'] = $base64;
       }

       if(isset($_POST['filetouploadname'])) {
           $data['filetouploadname'] = $_POST['filetouploadname'];
           $data['filetouploadname_base64'] = $_POST['filetouploadname_base64'];
       }

       if(isset($_POST['logo_image'])) {
           $data['logo_image'] = $_POST['logo_image'];
           $data['logo_image_base64'] = $_POST['logo_image_base64'];
       }

       if(isset($_POST['attach_image'])) {
           $data['attach_image'] = $_POST['attach_image'];
           $data['attach_image_base64'] = $_POST['attach_image_base64'];
       }

       if($_FILES['attach_image']['name'] != '') {

           $type = pathinfo($_FILES['attach_image']['name'], PATHINFO_EXTENSION);
           $file = file_get_contents($_FILES['attach_image']['tmp_name']);
           // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
           $base64 = base64_encode($file);
           $attach_image = date('Ymd').time().rand(0, 9999).'.'.$type;
           $data['attach_image'] = $_FILES['attach_image']['name'];
           $data['attach_image_base64'] = $base64;
       }

       $org = isset($_POST['org']) ? $_POST['org'] : '' ;
       $extension = isset($_POST['extension']) ? $_POST['extension'] : '' ;
                  $eventmeta = array(
					'org' => $org,
					'exclude_name' => $_POST['exclude_name'],
					'exclude_phone' => $_POST['exclude_phone'],
					'extension' => $extension,
					'exclude_email' => $_POST['exclude_email']
					);
			$data['metadata'] = serialize($eventmeta);



       $_SESSION['event_edit_data'] = $data;
       

      // echo "<pre>"; print_r($_SESSION['event_edit_data']); die();
       $token   =  $_SESSION['Api_token'];
      //  if(isset($_SESSION['event_edit_data']['filetouploadname'])) {


      //      $idata = array(
      //         'name' => $_SESSION['event_edit_data']['filetouploadname'],
      //         "contentType"=> "image/png",
      //         "data"=> $_SESSION['event_edit_data']['filetouploadname_base64'],
      //         "franchise_id"=> 0,
      //         "event_id"=> $_POST['edit'],
      //         "type" => 'image'
      //       );


      //      $payload = json_encode($idata);
      //      $ch      = curl_init(API_URL.'files');
      //      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      //      curl_setopt($ch, CURLOPT_POST, true);
      //      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      //      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      //          'Content-Type: application/json',
      //          'Content-Length:' . strlen($payload),
      //          'Authorization: ' . $token
      //      ));

      //      $result41 = curl_exec($ch);
      //      curl_close($ch);
      //      $response = json_decode($result41);
      //      unset($_SESSION['event_edit_data']['filetouploadname']);
      //      unset($_SESSION['event_edit_data']['filetouploadname_base64']);
      //  }
      //  if(isset($_SESSION['event_edit_data']['logo_image'])) {

      //       $idata = array(
      //         'name' => $_SESSION['event_edit_data']['logo_image'],
      //         "contentType"=> "image/png",
      //         "data"=> $_SESSION['event_edit_data']['logo_image_base64'],
      //         "franchise_id"=> 0,
      //         "event_id"=> $_POST['edit'],
      //         "type" => 'logo'
      //       );


      //      $payload = json_encode($idata);
      //      $ch      = curl_init(API_URL.'files');
      //      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      //      curl_setopt($ch, CURLOPT_POST, true);
      //      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      //      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      //          'Content-Type: application/json',
      //          'Content-Length:' . strlen($payload),
      //          'Authorization: ' . $token

      //      ));

      //      $result51 = curl_exec($ch);
      //      curl_close($ch);
      //      $response = json_decode($result51);

      //      unset($_SESSION['event_edit_data']['logo_image']);
      //      unset($_SESSION['event_edit_data']['logo_image_base64']);
      //  }
      //  if(isset($_SESSION['event_edit_data']['attach_image'])) {

      //      $idata = array(

      //          'eventId' =>  $_POST['edit'],
      //          'name' => $_SESSION['event_edit_data']['logo_image'],
      //          'data' => $_SESSION['event_edit_data']['logo_image_base64'],
      //          'caption' => 'File'
      //      );

      //      $payload = json_encode($idata);
      //      $ch      = curl_init(API_URL.'uploads');
      //      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      //      curl_setopt($ch, CURLOPT_POST, true);
      //      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      //      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      //          'Content-Type: application/json',
      //          'Content-Length:' . strlen($payload),
      //          'Authorization: ' . $token

      //      ));

      //      $result51 = curl_exec($ch);
      //      curl_close($ch);
      //      $response = json_decode($result51);

      //      unset($_SESSION['event_edit_data']['attach_image']);
      //      unset($_SESSION['event_edit_data']['attach_image_base64']);
      //  }


      //  $payload = json_encode($_SESSION['event_edit_data']);
      //  $ch      = curl_init(API_URL.'events/'.$_POST['edit']);
      //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      //  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      //  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      //  curl_setopt($ch, CURLOPT_FAILONERROR, true);
      //  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      //      'Content-Type: application/json',
      //      'Content-Length:' . strlen($payload),
      //      'Authorization: ' . $token
      //  ));

      //  $result31 = curl_exec($ch);
      //  curl_close($ch);
      //  $editresponse = json_decode($result31);

      //  unset ($_SESSION["event_edit_data"]);

       header("Location: ".site_url()."/create-tickets/?edit=".$_GET['event_id']);
   }

   ?>
<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->
   <link rel="stylesheet" href="/wp-content/themes/Divi Child/datepicker/css/jquery.datetimepicker.min.css">
   <script src="/wp-content/themes/Divi Child/datepicker/js/moment.js"></script>
   <script src="/wp-content/themes/Divi Child/datepicker/js/jquery.datetimepicker.full.js"></script>
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/selec2css/select2.css">
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/css/cropper.css">
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/css/edit_event.css">
<script src="<?php echo site_url()?>/wp-content/themes/Divi Child/js/select2.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
<script src="<?php echo site_url()?>/wp-content/themes/Divi Child/js/jquery.fileupload.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
    <?php if(isset($_REQUEST['eventstate']) && $_REQUEST['eventstate']=='past'){ ?>
   .event-details input,.event-details select{
   border: 0px !important;
    cursor : default;
   }
   select:hover{
       background: none!important;
   }
   <?php } ?>

</style>
<?php //echo "<pre>"; print_r($events); die; ?>
<div id="main-content">
   <div class="outer-wrapper ">
      <div class="container container-home">
         <h3 class="h3-title">Edit Your Event</h3>
           <ul class="progressbar">
            <li class="active">Page Design</li>
            <li class="">Ticket Details</li>
            <li>Options & Update</li>
         </ul>
         <?php if(isset($_SESSION['event_edit_data'])){
         $session_metadata = unserialize($_SESSION['event_edit_data']['metadata']);
         ?>
        <form  class="edit_event" id="event_form" method="post" action="#" enctype="multipart/form-data">
            <input type="hidden" name="edit" id="get_event_id" value="<?php echo isset($events) ? $events->id : '';?>">
            <input type="hidden" name="eventstate" value="<?php echo $_REQUEST['eventstate'];?>">
            <div class="event-type">
               <?php if(isset($_SESSION['event_edit_data']['dateRanges'])) { ?>
               <span class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="1" <?php echo (count($_SESSION['event_edit_data']['dateRanges']) == '1') ? 'checked' : '';?> id="check_single"><span class="checkmark1"> </span> <label class="radio-chk" for="check_single">Single day Event</label></span>
               <span class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="2" <?php echo (count($_SESSION['event_edit_data']['dateRanges']) > '1') ? 'checked' : '';?> id="check_multi"><span class="checkmark1"> </span> <label class="radio-chk" for="check_multi">Multi-day event</label></span>
               <?php } else { ?>
               <span class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="1" <?php echo (count($events->event_dates) == '1') ? 'checked' : '';?> id="check_single"><span class="checkmark1"> </span> <label class="radio-chk" for="check_single">Single day Event</label></span>
               <span class="radio-chk">  <input class="check-radio" type="radio" name="event_status_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="2" <?php echo (count($events->event_dates) > '1') ? 'checked' : '';?> id="check_multi"><span class="checkmark1"> </span> <label class="radio-chk" for="check_multi">Multi-day event</label></span>
               <?php }?>
               <span id="event_message">This event will start and end on same date</span>
               <span class="event-preview">
             <!--  <a href="javascript:void(0);" onClick="jQuery('#previewModal').show();"> PREVIEW <i class="fa fa-eye"></i></a>-->
               </span> <br/>
               <span>
               </span><br/>
               <?php if(isset($_SESSION['event_edit_data']['dateRanges'])) {
                $dates = $_SESSION['event_edit_data']['dateRanges'];
                foreach($dates as $i => $date) {
                  if($i == 0) {  ?>
               <div class="event-dates">
                  <label style="cursor: pointer;" class="start-date" for="single_start_date">Start <input type="text" id="single_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo date('M d, Y h:i a', strtotime($date[0]));?>"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <label style="cursor: pointer;" class="start-date" for="single_end_date">End <input type="text" id="single_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo date('M d, Y h:i a', strtotime($date[1]));?>"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
               </div>
               <?php } else { ?>
               <div class="add_more_div" id="multi_div_<?php echo $i;?>">
                  <label style="cursor: pointer;" class="start-date" for="multi_start_date_<?php echo $i;?>">Start<input type="text" class="start_datepicker multi_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="multi_start_date_<?php echo $i;?>" name="event_start_date[]" data-number="<?php echo $i;?>" value="<?php echo date('M d, Y h:i a', strtotime($date[0]));?>" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <label style="cursor: pointer;" class="start-date" for="multi_end_date_<?php echo $i;?>">End<input type="text" class="start_datepicker multi_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="multi_end_date_<?php echo $i;?>" name="event_end_date[]" data-number="<?php echo $i;?>" value="<?php echo date('M d, Y h:i a', strtotime($date[1]));?>" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <span class="remove-date" style="cursor:pointer;" data-id="<?php echo $i;?>"> - </span>
               </div>
               <?php }
                  }
                  } else {
                      for($i=0; $i<count($events->event_dates); $i++) {

                      if($i == 0) { ?>
               <div class="event-dates">
                  <label style="cursor: pointer;" class="start-date" for="single_start_date">Start <input type="text" id="single_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->start_date))?>"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <label style="cursor: pointer;" class="start-date" for="single_end_date">End <input type="text" id="single_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->end_date))?>"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
               </div>
               <?php } else { ?>
               <div class="add_more_div" id="multi_div_<?php echo $i;?>">
                  <label style="cursor: pointer;" class="start-date" for="multi_start_date_<?php echo $i;?>">Start<input type="text" class="start_datepicker multi_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="multi_start_date_<?php echo $i;?>" name="event_start_date[]" data-number="<?php echo $i;?>" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->start_date))?>" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <label style="cursor: pointer;" class="start-date" for="multi_end_date_<?php echo $i;?>">End<input type="text" class="start_datepicker multi_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="multi_end_date_<?php echo $i;?>" name="event_end_date[]" data-number="<?php echo $i;?>" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->end_date))?>" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <?php if(isset($event_state) && $event_state != 'past'){ ?><span class="remove-date" style="cursor:pointer;" data-id="<?php echo $i;?>"> - </span><?php } ?>
               </div>
               <?php }
                  } }?>
               <?php if(isset($event_state) && $event_state != 'past'){ ?><a href="#" style="display: <?php echo isset($_SESSION['event_edit_data']['dateRanges']) && count($_SESSION['event_edit_data']['dateRanges']) > '1' ? '' : 'none';?>;" id="add_more" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>><span> + </span>Add a new date</a><?php } ?>
            </div>
            <hr/>
            <p><b>All required fields marked with (*).</b> <b style="float: right;">Event ID: <?php echo $events->id; ?> </b></p>
            <div class="event-detail editEventDetail">

               <div class="upload-image">
                    <h3 style="font-size: 17px;">Event Image*</h3>
                  <?php if(isset($_SESSION['event_edit_data']['filetouploadname'])){ ?>
                  <div id="dropZone">
                     <h1>Drag & Drop event image here...</h1>
                     <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                     <input type="file" class="event_imgzone" id="fileupload" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="fileToUpload">
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:25%;">File size should be less than 1 Mb.<br/>
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:25%;">File type should be png/jpg/jpeg only.<br/>
                        </p>
                     </span>
                  </div>
                  <div id="files" class="image-area">
                     <div class="pre-image" id="fileshide">
                     <img src="<?php echo 'data:image/'.pathinfo($_SESSION['event_edit_data']['filetouploadname'], PATHINFO_EXTENSION).';base64,'.$_SESSION['event_edit_data']['filetouploadname_base64']; ?>" onClick="$('#fileupload').click()">
                     <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                     <span class="remove-img" id="remove-img" style="cursor: pointer;">-</span>
                     <?php } ?>
                     </div>
                  </div>
                  <input type="hidden" name="remove_img" id="check_remove_img" value="<?php echo isset($_SESSION['event_edit_data']['r_img']) ? $_SESSION['event_edit_data']['r_img'] : "" ; ?>">
                  <input type="hidden" name="remove_img_id" value="<?php echo $_SESSION['event_edit_data']['r_img_id']; ?>">
                  <input type="hidden" name="filetouploadname" value="<?php echo $_SESSION['event_edit_data']['filetouploadname']; ?>">
                  <input type="hidden" name="filetouploadname_base64" value="<?php echo $_SESSION['event_edit_data']['filetouploadname_base64']; ?>">
                  <script>
                  jQuery("#dropZone").find("h1").hide();
                  jQuery("#dropZone").find("button").hide();
                  jQuery("#dropZone").css({"background-image": "none"});
                  $('#fileupload').on('change', function() {
                    $('[name="filetouploadname"]').remove();
                    $('[name="filetouploadname_base64"]').remove();
                  });
                  </script>
                  <?php } ?>

                  <?php if(isset($events) && count($events->files) > 0 && $events->files[0]->type == 'image' && (!isset($_SESSION['event_edit_data']['filetouploadname']))) { ?>
                  <div id="dropZone">
                     <h1>Drag & Drop event image here...</h1>
                     <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                     <input type="file" class="event_imgzone" id="fileupload" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="fileToUpload">
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:25%;">File size should be less than 1 Mb.<br/>
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:25%;">File type should be png/jpg/jpeg only.<br/>
                        </p>
                     </span>
                  </div>
                  <div id="files" class="image-area">

                     <?php
                     $removeimgid = $events->files[0]->id;
                     ?>
                     <div class="pre-image" id="fileshide">
                     <img src="https://storage.googleapis.com/<?php echo $events->files[0]->bucket?>/<?php echo $events->files[0]->filename;?>" onClick="$('#fileupload').click()">
                     <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                     <span class="remove-img" id="remove-img" style="cursor: pointer;">-</span>
                     <?php } ?>
                     </div>


                  </div>
                  <script>
                  jQuery("#dropZone").find("h1").hide();
                  jQuery("#dropZone").find("button").hide();
                  jQuery("#dropZone").css({"background-image": "none"});
                  </script>
                  <input type="hidden" name="remove_img" id="check_remove_img" value="">
                  <input type="hidden" name="remove_img_id" value="<?php echo $removeimgid; ?>">
                  <script>
                  jQuery("#dropZone").find("h1").hide();
                  jQuery("#dropZone").find("button").hide();
                  jQuery("#dropZone").css({"background-image": "none"});
                  </script>
                  <?php }
                  if(!isset($events->files[0]) && (!isset($_SESSION['event_edit_data']['filetouploadname']))){ ?>
                  <div id="files" class="image-area" onClick="$('#fileupload').click()"></div>
                  <div id="dropZone" style="text-align: center;">
                     <h1>Drag&Drop your event image here...</h1>
                     <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                     <input type="file" id="fileupload" class="event_imgzone" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="fileToUpload" required title="Please upload Event image">
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:25%;">File size should be less than 300 kb.<br/>
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:25%;">File type should be png/jpg/jpeg only.<br/>
                        </p>
                     </span>

                  </div>
                  <?php } ?>
                  <!--
                     <input type="file" name="fileToUpload" <?php //echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="fileToUpload" style="position: relative;z-index: 999999999999;bottom: 209px;opacity: 0; height: 220px; margin-top: -20px;" onchange="getUploadImageUrl(this, 'fileToUpload_prev');">
                     -->
               </div>
               <div class="event-details">
                  <div>
                  </div>
                  <input type="hidden" id="start" value="0">
                  <input type="hidden" id="end" value="0">
                  <h3 style="font-size: 17px;">Venue Information*</h3>
                  <div>
                     <input style="width:99%;" type="text" focusID="ID_TO_FOCUSTO" id="title" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="title" placeholder="Event Title*" required title="Please enter an Event title" value="<?php echo isset($_SESSION['event_edit_data']['name']) ? stripslashes($_SESSION['event_edit_data']['name']) : $events->name;?>">
                  </div>
                  <input type="hidden" value="<?php echo isset($events) ? count($events->event_dates) : '0';?>" id="count" name="count">
                  <div class="evnt-dates">
                     <?php if(isset($_SESSION['event_edit_data']['dateRanges'])) {
                $dates = $_SESSION['event_edit_data']['dateRanges'];
                foreach($dates as $i => $date) {
                            $start_date = $date[0];
                            $end_date = $date[1];
                            if(date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date))) {
                                $end_date = 'to '.date('h:i a', strtotime($end_date));
                            } else {
                                $end_date = 'to '.date('M d, Y h:i a', strtotime($end_date));
                            }
                            if($i == 0) { ?>
                     <p><span id="span_start_date"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="span_end_date"><?php echo $end_date;?></span></p>
                     <?php } else { ?>
                     <p class="multi_span" id="p_<?php echo $i;?>"><span id="span_start_date_<?php echo $i;?>"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="span_end_date_<?php echo $i;?>"><?php echo $end_date;?></span></p>
                     <?php }
                        }
                        } else { for($i=0; $i<count($events->event_dates); $i++) {

                            $start_date = $events->event_dates[$i]->start_date;
                            $end_date = $events->event_dates[$i]->end_date;

                            if(date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date))) {

                                $end_date = 'to '.date('h:i a', strtotime($end_date));
                            } else {

                                $end_date = 'to '.date('M d, Y h:i a', strtotime($end_date));
                            }

                            if($i == 0) { ?>
                     <p><span id="span_start_date"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="span_end_date"><?php echo $end_date;?></span></p>
                     <?php } else { ?>
                     <p class="multi_span" id="p_<?php echo $i;?>"><span id="span_start_date_<?php echo $i;?>"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="span_end_date_<?php echo $i;?>"><?php echo $end_date;?></span></p>
                     <?php }
                        } }?>
                  </div>
                  <div>
                     <input style="width:99%;" type="text" name="address1" placeholder="Venue*" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="venue" title="Please enter the Venue name" required value="<?php echo isset($_SESSION['event_edit_data']['address1']) ? stripslashes($_SESSION['event_edit_data']['address1']) : $events->location;?>">
                  </div>
                  <div>
                     <input style="width:99%;"  type="text" name="address2" onFocus="geolocate_event()" id="address" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> placeholder="Address" title="Enter Address"  value="<?php echo isset($_SESSION['event_edit_data']['address2']) ? stripslashes($_SESSION['event_edit_data']['address2']) : $events->address2;?>">
                  </div>
                  <div class="div_country">
                     <select class="Country" name="country" id="country" autocomplete="off" required title="Please select a country" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="">
                        <option value="">Country*</option>
                        <?php foreach($countries as $row){
                        if($row->id == '2'){?>
                        <option value="<?php echo $row->id;?>" <?php echo isset($_SESSION['event_edit_data']['country_id']) ? ($_SESSION['event_edit_data']['country_id'] == $row->id) ? 'selected' : '' : (isset($events) && $events->country_id == $row->id) ? 'selected' : '' ;?>><?php echo $row->name;?></option>
                        <?php } }?>
                     </select>
                  </div>
                  <div class="div_states">
                     <?php if(isset($_SESSION['event_edit_data']['province_id'])) {
                        //$country_id = $events->country_id;
                       // $states = $wpdb->get_results("Select * from wp_states where country_id = $country_id");

                       ?>
                     <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="">
                        <option value="">Province*</option>
                        <?php foreach($states as $row) {
                        if($row->id >= "2" && $row->id <= "14"){ ?>
                        <option value="<?php echo $row->id?>" <?php echo ($_SESSION['event_edit_data']['province_id'] == $row->id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                        <?php } }?>
                     </select>
                     <?php } else { ?>
                     <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="">
                        <option value="">Province*</option>
                        <?php foreach($states as $row) {
                        if($row->id >= "2" && $row->id <= "14"){ ?>
                        <option value="<?php echo $row->id?>" <?php echo ($events->province_id == $row->id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                        <?php } }?>
                     </select>
                     <?php }?>
                  </div>
                  <div class="class_city">
                     <input type="text" name="city" id="city" placeholder="City *" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> title="Please enter a city" required value="<?php echo isset($_SESSION['event_edit_data']['city']) ? stripslashes($_SESSION['event_edit_data']['city']) : $events->city;?>" style="">
                  </div>
                  <div class="class_zip">
                     <input type="text" name="postalcode" id="postalcode" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> placeholder="Postal Code*" required title="Please enter a valid postal code" value="<?php echo isset($_SESSION['event_edit_data']['postalcode']) ? $_SESSION['event_edit_data']['postalcode'] : $events->postalcode;?>" style="">
                  </div>
                  <div class="div_description">
                     <p>
                     <h3 style="font-size: 17px;">Description*</h3>
                     </p>
                     <textarea rows="4" placeholder="Description" id="description" name="description" required title="Please enter a description of the event"><?php echo isset($_SESSION['event_edit_data']['description']) ? stripslashes($_SESSION['event_edit_data']['description']) : $events->description;?></textarea>
                  </div>
                  <?php if(count($_SESSION['event_edit_data']['categories']) > 0){
                     $cats = $_SESSION['event_edit_data']['categories'];
                     } else {

                     $cats = array_column($events->categorys, 'id');
                     }?>
                  <p>
                  <h3 style="font-size: 17px;">Categories*</h3>
                  (select one or more)</p>
                  <div>
                     <select name="category_id[]" id="category1_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>  multiple="multiple" data-placeholder="Select Category" required title="Please select a Category" style="">



                        <option value="">Category *</option>

                        <?php foreach($categories as $row) {
                           if($row->api_cat_id != 17) { ?>

                        <option value="<?php echo $row->api_cat_id?>" <?php echo in_array($row->api_cat_id, $cats) ? 'selected' : '';?>><?php echo $row->title;?></option>
                        <?php }?>
                        <?php }?>
                     </select>
                  </div>


                  <span class="multiple"></span>

                  <h3 style="font-size: 17px;">Contact Details</h3>
                  <input style="width:99%; " type="text" placeholder="Organization" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="org" value="<?php echo ($session_metadata['org'] == "") && ($metadata['org'] == "") ? "" : $session_metadata['org'] != "" ? $session_metadata['org'] : $metadata['org'] != "" ? stripslashes($session_metadata['org']) : "" ;?>">
                  <input style="width:99%; " type="text" id="website_url" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> placeholder="Website URL" name="website_url" value="<?php echo isset($_SESSION['event_edit_data']['contact_url']) ? stripslashes($_SESSION['event_edit_data']['contact_url']) : $events->contact_email;?>">
                  <p class="exclude">
                     <input type="text" placeholder="Full Name*" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="contact_name" id="contact_name" class="exclude_input" required title="Please enter First and Last name" value="<?php echo isset($events) ? $events->contact_name : '';?>" style="">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="contact_name_check" name="exclude_name" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> <?php echo $session_metadata['exclude_name'] == 'on' ? 'checked' : '';?> >
                     <span class="checkmark"></span><label class="chkbox" for="contact_name_check">Exclude Name from public listing </label></span>
                     <input type="hidden" name="exclude_name" value="<?php echo $session_metadata['exclude_name'] == 'on' ? 'on' : 'off' ?>">
                  </p>

                  <p class="exclude">
                     <input type="text" placeholder="(XXX) XXX-XXXX*" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="contact_phone" class="exclude_input" id="contact_phone" required title="Please enter valid Phone number including area code" value="<?php echo isset($_SESSION['event_edit_data']['contact_phone']) ? $_SESSION['event_edit_data']['contact_phone'] : $events->contact_phone;?>" style="">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox"  id="contact_phone_check"  name="exclude_phone" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> <?php echo $session_metadata['exclude_phone'] == 'on' ? 'checked' : '';?> >
                     <span class="checkmark"></span><label class="chkbox" for="contact_phone_check">Exclude Phone from public listing</label></span>
                     <input type="hidden" name="exclude_phone" value="<?php echo $session_metadata['exclude_phone'] == 'on' ? 'on' : 'off' ?>">
                  </p>

                  <p class="exclude">
                     <input type="text" placeholder="Extension" name="extension" value="<?php echo ($session_metadata['extension'] == "") && ($metadata['extension'] == "") ? "" : $session_metadata['extension'] != "" ? stripslashes($session_metadata['extension']) : $metadata['extension'] != "" ? $session_metadata['extension'] : "" ; ?>" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="" class="exclude_input">
					 <!--
                     <span class="chkbox">Exclude Extension from public listing
                     <input class="tix-tkt" type="checkbox" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>>
                     <span class="checkmark"></span> </span>
					 -->
                  </p>

                  <p class="exclude">
                     <input type="email" placeholder="Email*" name="email" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="email" required class="exclude_input" title="Please enter valid Email address" value="<?php echo isset($_SESSION['event_edit_data']['contact_email']) ? stripslashes($_SESSION['event_edit_data']['contact_email']) : $events->contact_email;?>" style="">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="email_check" name="exclude_email" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> <?php echo $session_metadata['exclude_email'] == 'on' ? 'checked' : '';?> >
                     <span class="checkmark"></span><label class="chkbox" for="email_check">Exclude Email from public listing</label></span>
                     <input type="hidden" name="exclude_email value="<?php echo $session_metadata['exclude_email'] == 'on' ? 'on' : 'off' ?>">
                  </p>

                  <div class="attachemnts">
                    <div class="logo-details">
                     <h3>Logo</h3>

                                <?php if(isset($_SESSION['event_edit_data']['logo_image'])) { ?>
                                <div id="logodropZone" class="logo-zone">
                                <input type="file" id="logo_image"  <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="logo_image" class="icon_droparea">
                                <h1 id="logozoneh1">Drag & Drop a single logo here</h1>
                                <button class="btn btn-eventimg Logo_btn" id="btnicon" onClick="$('#logo_image').click()">Browse File</button>
                                <div id="logo_files">
                                   <div id="logohide">
                                    <img class="uploaded-img" src="<?php echo 'data:image/'.pathinfo($_SESSION['event_edit_data']['logo_image'], PATHINFO_EXTENSION).';base64,'.$_SESSION['event_edit_data']['logo_image_base64']; ?>" onClick="$('#logo_image').click()">
                                         <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                                        <span class="remove-img" id="remove-logo" style="cursor: pointer;">-</span>
                                         <?php }?>
                                    </div>
                                </div>
                                <input type="hidden" name="remove_logo" id="check_remove_logo" value="<?php echo $_SESSION['event_edit_data']['r_logo']; ?>">
                                <input type="hidden" name="remove_logo_id" value="<?php echo $_SESSION['event_edit_data']['r_logo_id']; ?>">
                                <input type="hidden" name="logo_image" value="<?php echo $_SESSION['event_edit_data']['logo_image']; ?>">
                                <input type="hidden" name="logo_image_base64" value="<?php echo $_SESSION['event_edit_data']['logo_image_base64']; ?>">


                                    <span style="display: none;" id="file_err">
                                     <p style="color: #ff0000;margin-top:11%;">
                                         File size should be less than 300 kb.<br/>
                                     </p>
                                    </span>
                                    <span style="display: none;" id="file_type_err">
                                     <p style="color: #ff0000;margin-top:11%;">
                                         File type should be png/jpg/jpeg only.<br/>
                                     </p>
                                    </span>
                                    <script>
                                           jQuery("#logodropZone h1").hide();
                                           jQuery("#logodropZone").css({"padding-top": "0px"});
                                           jQuery("#btnicon").css({"display":"none"});
                                           $('#logo_image').on('change', function() {
                                           $('[name="logo_image"]').remove();
                                           $('[name="logo_image_base64"]').remove();
                                           });
                                    </script>
                                 </div>
                                <?php } ?>


                                <?php if(isset($events) && count($events->files) > 0 && (!isset($_SESSION['event_edit_data']['logo_image']))) {
                                     $i=0;
                                     foreach($events->files as $row) {
                                     if($row->type == 'logo') {
                                     $i++;
                                     ?>
                                <div id="logodropZone" class="logo-zone">
                                <input type="file" id="logo_image"  <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="logo_image" class="icon_droparea">
                                <h1 id="logozoneh1">Drag & Drop a single logo here</h1>
                                <button class="btn btn-eventimg Logo_btn" id="btnicon" onClick="$('#logo_image').click()">Browse File</button>
                                <div id="logo_files">
                                   <div id="logohide">
                                    <img class="uploaded-img" src="https://storage.googleapis.com/<?php echo $row->bucket?>/<?php echo $row->filename;?>" onClick="$('#logo_image').click()">
                                         <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                                        <span class="remove-img" id="remove-logo" style="cursor: pointer;">-</span>
                                         <?php }?>
                                    </div>
                                </div>
                                <input type="hidden" name="remove_logo" id="check_remove_logo" value="">
                                <input type="hidden" name="remove_logo_id" value="<?php echo $row->id; ?>">


                                    <span style="display: none;" id="file_err">
                                     <p style="color: #ff0000;margin-top:11%;">
                                         File size should be less than 300 kb.<br/>
                                     </p>
                                    </span>
                                    <span style="display: none;" id="file_type_err">
                                     <p style="color: #ff0000;margin-top:11%;">
                                         File type should be png/jpg/jpeg only.<br/>
                                     </p>
                                    </span>
                                    <script>
                                           jQuery("#logodropZone h1").hide();
                                           jQuery("#logodropZone").css({"padding-top": "0px"});
                                           jQuery("#btnicon").hide();
                                    </script>
                                 </div>
                                <?php } } if($i == 0){  ?>
                                    <div id="logodropZone" class="logo-zone">
                                     <h1>Drag & Drop a single logo here</h1>
                                     <div id="logo_files"></div>
                                         <input type="file" id="logo_image"  <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="logo_image" class="icon_droparea">
                                         <button class="btn btn-eventimg Logo_btn" id="btnicon" onClick="$('#logo_image').click()">Browse File</button>
                                     <div style="clear:both"></div>
                                         <span style="display: none;" id="file_err">
                                          <p style="color: #ff0000;margin-top:11%;">
                                            File size should be less than 300 kb.<br/>
                                          </p>
                                         </span>
                                         <span style="display: none;" id="file_type_err">
                                          <p style="color: #ff0000;margin-top:11%;">
                                            File type should be png/jpg/jpeg only.<br/>
                                          </p>
                                         </span>


                            </div>
                                 <?php } } ?>

                        <div class="up-attach">
                            <h3 style="font-size:17px;">Attachment</h3>
                           <span>Include a single PDF for all instructions, waivers, map etc...</span>
                           <div>
                             <?php if(isset($_SESSION['event_edit_data']['attach_image'])){ ?>
                              <div id="pdfdropZone" class="pdf-zone">
                                 <h1 id="dd_text_pdf">Drag & Drop a single PDF here</h1>
                                 <div id="choose_pdf_div"></div>
                                 <button id="btnpdf" class="btn btn-eventimg PDF-btn">Browse File</button>
                                 <input type="file" id="pdf_image" name="attach_image" class="pdf_input" value="<?php echo isset($file) ? 'https://storage.cloud.google.com/'.$file->bucket.'/'.$file->filename : ''?>">
                                 <div id="pdf_files"></div>


                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;"><?php echo $_SESSION['event_edit_data']['attach_image']; ?>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File size should be less than 10 mb.<br/>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File type should be pdf only.<br/>
                                    </p>
                                 </span>
                            <input type="hidden" name="remove_pdf" id="check_remove_pdf" value="<?php echo $_SESSION['event_edit_data']['r_pdf'] ? $_SESSION['event_edit_data']['r_pdf'] : "" ; ?>">
                            <input type="hidden" name="remove_pdf_id" value="<?php echo $_SESSION['event_edit_data']['r_pdf_id']; ?>">
                            <input type="hidden" name="attach_image" value="<?php echo $_SESSION['event_edit_data']['attach_image']; ?>">
                            <input type="hidden" name="attach_image_base64" value="<?php echo $_SESSION['event_edit_data']['attach_image_base64']; ?>">

                             <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                             <span class="remove-file" id="remove-pdf" style="cursor: pointer;">-</span>
                             <?php } ?>

                               <script>  //jQuery("#pdf_image").siblings("#file_name p").text('');
                                           jQuery("#pdf_image").siblings("#file_name").css({"margin-top":"0%"});
                                           jQuery("#pdf_image").siblings("#file_name").show();
                                           jQuery("#dd_text_pdf").hide();
                                           jQuery("#pdfdropZone").css({"background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')","border": "2px dashed #ffffff","padding-top": "50px"});
                                           jQuery("#btnpdf").hide();

                                           </script>

                              </div>
                             <?php } ?>

                             <?php if(isset($events) && count($events->files) > 0 && (!isset($_SESSION['event_edit_data']['attach_image']))) {
                                 $i=0;
                                 foreach($events->files as $file) {
                                   if($file->type == 'Pdf file')  {
                                       $i++;?>
                              <div id="pdfdropZone" class="pdf-zone">
                                 <h1 id="dd_text_pdf">Drag & Drop a single PDF here</h1>
                                 <div id="choose_pdf_div"></div>
                                 <button id="btnpdf" class="btn btn-eventimg PDF-btn">Browse File</button>
                                 <input type="file" id="pdf_image" name="attach_image" class="pdf_input" value="<?php echo isset($file) ? 'https://storage.cloud.google.com/'.$file->bucket.'/'.$file->filename : ''?>">
                                 <div id="pdf_files"></div>


                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;"><?php echo $file->filename; ?>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File size should be less than 10 mb.<br/>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File type should be pdf only.<br/>
                                    </p>
                                 </span>
                            <input type="hidden" name="remove_pdf" id="check_remove_pdf" value="">
                            <input type="hidden" name="remove_pdf_id" value="<?php echo $row->id; ?>">
                             <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                             <span class="remove-file" id="remove-pdf" style="cursor: pointer;">-</span>
                             <?php } ?>

                               <script>  //jQuery("#pdf_image").siblings("#file_name p").text('');
                                           jQuery("#pdf_image").siblings("#file_name").css({"margin-top":"0%"});
                                           jQuery("#pdf_image").siblings("#file_name").show();
                                           jQuery("#dd_text_pdf").hide();
                                           jQuery("#pdfdropZone").css({"background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')","border": "2px dashed #ffffff","padding-top": "50px"});
                                           jQuery("#btnpdf").hide();

                                           </script>

                              </div>
                                     <?php } }
                                     if($i == 0){
                                     ?>
                                     <div style="clear:both"></div>

                              <div id="pdfdropZone" class="pdf-zone">
                                 <h1 id="dd_text_pdf">Drag & Drop a single PDF here.</h1><div id="choose_pdf_div"></div>
                                 <button id="btnpdf" class="btn btn-eventimg PDF-btn">Browse File</button>
                                 <input type="file" id="pdf_image" name="attach_image" class="pdf_input">
                                 <div id="pdf_files"></div>
                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;">
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File size should be less than 10 mb.<br/>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File type should be pdf only.<br/>
                                    </p>
                                 </span>
                              </div>
                                     <?php } } ?>





                           </div>
                        </div>
                     </div>
                     <div class="map-details">
                        <h3>Map Details</h3>
                  <?php
                  $mapsrc= (isset($events)&& isset($events->long)) ?"https://webdev.snapd.com/map.php?lat=".$events->lat."&lng=".$events->long :"https://webdev.snapd.com/map.php?lat=56.1304&lng=-106.346771";
                  ?>
                  <iframe class="mapIframe" src="<?php echo $mapsrc; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                     </div>
                  </div>
               </div>
             <button class="next-btn" name="btnUpdate">NEXT</button>
            <!-- <a href="<?php //echo site_url()?>/manage-my-events"><button class="next-btn cancel-btn" type="button" style="margin: 6px; padding-bottom: 8px;">CANCEL</button></a> -->
            <button onClick="jQuery(this).css('width','350px');jQuery(this).text('Cancelling event changes...');setTimeout(function(){ window.location.href='<?php echo site_url()?>/manage-my-events'; }, 2000);" class="next-btn cancel-btn" type="button" style="margin: 6px; padding-bottom: 8px;">CANCEL</button>
         </form>


         <?php } ?>
         <?php if(!isset($_SESSION['event_edit_data'])){ ?>
         <form  class="edit_event" id="event_form" method="post" action="#" enctype="multipart/form-data">
            <input type="hidden" name="edit" value="<?php echo isset($events) ? $events->id : '';?>">
            <input type="hidden" name="eventstate" value="<?php echo $_REQUEST['eventstate'];?>">
            <div class="event-type">
               <?php if(isset($events)) { ?>
               <span class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="1" <?php echo (count($events->event_dates) == '1') ? 'checked' : '';?> id="check_single"><span class="checkmark1"> </span><label class="radio-chk" for="check_single"> Single day Event</label></span>
               <span class="radio-chk">  <input class="check-radio" type="radio" name="event_status_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="2" <?php echo (count($events->event_dates) > '1') ? 'checked' : '';?> id="check_multi"><span class="checkmark1"> </span><label class="radio-chk" for="check_multi"> Multi-day event</label></span>
               <?php } else { ?>
               <span class="radio-chk"> <input class="check-radio" type="radio" name="event_status_id" value="1" checked id="check_single"><span class="checkmark1"> </span><label class="radio-chk" for="check_single"> Single day Event</label></span>
               <span class="radio-chk">  <input class="check-radio" type="radio" name="event_status_id" value="2" id="check_multi"><span class="checkmark1"> </span><label class="radio-chk" for="check_multi"> Multi-day event</label></span>
               <?php }?>
               <span id="event_message">This event will start and end on same date</span>
               <span class="event-preview">
               <!--<a href="javascript:void(0);" onClick="jQuery('#previewModal').show();"> PREVIEW <i class="fa fa-eye"></i></a>-->
               </span> <br/>
               <span>
               </span><br/>
               <?php if(isset($events)) {
                  for($i=0; $i<count($events->event_dates); $i++) {

                      if($i == 0) { ?>
               <div class="event-dates">
                  <label style="cursor: pointer;" class="start-date" for="single_start_date">Start <input type="text" id="single_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->start_date))?>"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <label style="cursor: pointer;" class="start-date" for="single_end_date">End <input type="text" id="single_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->end_date))?>"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
               </div>
               <?php } else { ?>
               <div class="add_more_div" id="multi_div_<?php echo $i;?>">
                  <label style="cursor: pointer;" class="start-date" for="multi_start_date_<?php echo $i;?>">Start<input type="text" class="start_datepicker multi_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="multi_start_date_<?php echo $i;?>" name="event_start_date[]" data-number="<?php echo $i;?>" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->start_date))?>" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <label style="cursor: pointer;" class="start-date" for="multi_end_date_<?php echo $i;?>">End<input type="text" class="start_datepicker multi_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="multi_end_date_<?php echo $i;?>" name="event_end_date[]" data-number="<?php echo $i;?>" value="<?php echo date('M d, Y h:i a', strtotime($events->event_dates[$i]->end_date))?>" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><?php echo (isset($event_state) && $event_state != 'past') ? '<small style="font-size: 12px !important;font-weight: normal;">Select to change</small>' : '';?></label>
                  <?php if(isset($event_state) && $event_state != 'past'){ ?><span class="remove-date" style="cursor:pointer;" data-id="<?php echo $i;?>"> - </span><?php } ?>
               </div>
               <?php }
                  }
                  } else { ?>
               <div class="event-dates">
                  <label style="cursor: pointer;" class="start-date" for="single_start_date">Start <input type="text" id="single_start_date" class="start_datepicker single_start_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
                  <label style="cursor: pointer;" class="start-date" for="single_end_date">End <input type="text" id="single_end_date" class="end_datepicker single_end_date" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;" value="NOT SET"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>
               </div>
               <?php }?>
               <?php if(isset($event_state) && $event_state != 'past'){ ?><a href="#" style="display: <?php echo isset($events) && count($events->event_dates) > '1' ? '' : 'none';?>;" id="add_more" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>><span> + </span>Add a new date</a><?php } ?>
            </div>
            <hr/>
            <p><b>All required fields marked with (*).</b> <b style="float: right;">Event ID: <?php echo $events->id; ?> </b></p>
            <div class="event-detail editEventDetail">

               <div class="upload-image">
                    <h3 style="font-size: 17px;">Event Image*</h3>
                  <?php if(isset($events) && count($events->files) > 0 && $events->files[0]->type == 'image') { ?>
                  <div id="dropZone">
                     <h1>Drag & Drop event image here...</h1>
                     <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                     <input type="file" class="event_imgzone" id="fileupload" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="fileToUpload">
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:25%;">File size should be less than 1 Mb.<br/>
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:25%;">File type should be png/jpg/jpeg only.<br/>
                        </p>
                     </span>
                  </div>
                  <div id="files" class="image-area">
                     <?php
                     $removeimgid = $events->files[0]->id;
                     ?>
                     <div class="pre-image" id="fileshide">
                     <img src="https://storage.googleapis.com/<?php echo $events->files[0]->bucket?>/<?php echo $events->files[0]->filename;?>" onClick="$('#fileupload').click()">
                     <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                     <span class="remove-img" id="remove-img" style="cursor: pointer;">-</span>
                     <?php } ?>
                     </div>

                  </div>
                  <script>
                  jQuery("#dropZone").find("h1").hide();
                  jQuery("#dropZone").find("button").hide();
                  jQuery("#dropZone").css({"background-image": "none"});
                  </script>
                  <input type="hidden" name="remove_img" id="check_remove_img" value="">
                  <input type="hidden" name="remove_img_id" value="<?php echo $removeimgid; ?>">
                  <?php } else { ?>
                  <div id="files" class="image-area" onClick="$('#fileupload').click()"></div>
                  <div id="dropZone" style="text-align: center;">
                     <h1>Drag&Drop your event image here...</h1>
                     <button style="font-size: 16px;" class="btn btn-eventimg">Browse File</button>
                     <input type="file" id="fileupload" class="event_imgzone" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="fileToUpload" required title="Please upload Event image">
                     <span style="display: none;" id="file_err">
                        <p style="color: #ff0000;margin-top:25%;">File size should be less than 300 kb.<br/>
                        </p>
                     </span>
                     <span style="display: none;" id="file_type_err">
                        <p style="color: #ff0000;margin-top:25%;">File type should be png/jpg/jpeg only.<br/>
                        </p>
                     </span>

                  </div>
                  <?php } ?>
                  <!--
                     <input type="file" name="fileToUpload" <?php //echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="fileToUpload" style="position: relative;z-index: 999999999999;bottom: 209px;opacity: 0; height: 220px; margin-top: -20px;" onchange="getUploadImageUrl(this, 'fileToUpload_prev');">
                     -->
               </div>
               <div class="event-details">
                  <div>
                  </div>
                  <input type="hidden" id="start" value="0">
                  <input type="hidden" id="end" value="0">
                  <h3 style="font-size: 17px;">Venue Information*</h3>
                  <div>
                     <input style="width:99%;" type="text" focusID="ID_TO_FOCUSTO" id="title" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="title" placeholder="Event Title*" required title="Please enter an Event title" value="<?php echo isset($events) ? $events->name : '';?>">
                  </div>
                  <input type="hidden" value="<?php echo isset($events) ? count($events->event_dates) : '0';?>" id="count" name="count">
                  <div class="evnt-dates">
                     <?php if(isset($events)) {
                        for($i=0; $i<count($events->event_dates); $i++) {

                            $start_date = $events->event_dates[$i]->start_date;
                            $end_date = $events->event_dates[$i]->end_date;

                            if(date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date))) {

                                $end_date = 'to '.date('h:i a', strtotime($end_date));
                            } else {

                                $end_date = 'to '.date('M d, Y h:i a', strtotime($end_date));
                            }

                            if($i == 0) { ?>
                     <p><span id="span_start_date"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="span_end_date"><?php echo $end_date;?></span></p>
                     <?php } else { ?>
                     <p class="multi_span" id="p_<?php echo $i;?>"><span id="span_start_date_<?php echo $i;?>"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="span_end_date_<?php echo $i;?>"><?php echo $end_date;?></span></p>
                     <?php }
                        }
                        } else { ?>
                     <p><span id="span_start_date">NOT SET</span>&nbsp<span id="span_end_date">NOT SET</span></p>
                     <?php }?>
                  </div>
                  <div>
                     <input style="width:99%;" type="text" name="address1" placeholder="Venue*" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="venue" title="Please enter the Venue name" required value="<?php echo ($events) ? $events->location : '';?>">
                  </div>
                  <div>
                     <input style="width:99%;"  type="text" name="address2" onFocus="geolocate_event()" id="address" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> placeholder="Address" title="Enter Address"  value="<?php echo ($events) ? $events->address2 : '';?>">
                  </div>
                  <div class="div_country">
                     <select class="Country" name="country" id="country" autocomplete="off" required title="Please select a country" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="">
                        <option value="">Country*</option>
                        <?php foreach($countries as $row){
                        if($row->id == '2' || $row->id == '3'){?>
                        <option value="<?php echo $row->id;?>" <?php echo (isset($events) && $events->country_id == $row->id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                        <?php } }?>
                     </select>
                  </div>
                  <div class="div_states">
                     <?php if(isset($events)) {
                        //$country_id = $events->country_id;
                        //$states = $wpdb->get_results("Select * from wp_states where country_id = $country_id"); ?>
                     <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="">
                        <option value="">Province*</option>
                        <?php foreach($states as $row) {
                        if($row->id >= "2" && $row->id <= "65"){ ?>
                        <option value="<?php echo $row->id?>" <?php echo ($events->province_id == $row->id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                        <?php } }?>
                     </select>
                     <?php } else { ?>
                     <select class="State" id="state" name="state" autocomplete="off" title="Please select a province" required <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="">
                        <option value="">Province*</option>
                        <?php foreach($states as $row) {
                        if($row->id >= "2" && $row->id <= "65"){ ?>
                        <option value="<?php echo $row->id?>"><?php echo $row->name;?></option>
                        <?php } }?>
                     </select>
                     <?php }?>
                  </div>
                  <div class="class_city">
                     <input type="text" name="city" id="city" placeholder="City *" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> title="Please enter a city" required value="<?php echo isset($events) ? $events->city : '';?>" style="">
                  </div>
                  <div class="class_zip">
                     <input type="text" name="postalcode" id="postalcode" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> placeholder="Postal Code*" required title="Please enter a valid postal code" value="<?php echo isset($events) ? $events->postalcode : '';?>" style="">
                  </div>
                  <div class="div_description">
                     <p>
                     <h3 style="font-size: 17px;">Description*</h3>
                     </p>
                     <textarea rows="4" placeholder="Description" id="description" name="description" required title="Please enter a description of the event"><?php echo isset($events) ? $events->description : '';?></textarea>
                  </div>
                  <?php if(isset($events) && count($events->categorys) > 0){
                     $cats = array_column($events->categorys, 'id');
                     } else {

                     $cats = [];
                     }?>
                  <p>
                  <h3 style="font-size: 17px;">Categories*</h3>
                  (select one or more)</p>
                  <div>
                     <select name="category_id[]" id="category1_id" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>  multiple="multiple" data-placeholder="Select Category" required title="Please select a Category" style="">
                        <option value="">Category *</option>
                        <?php foreach($categories as $row) {
                           if($row->api_cat_id != 17) { ?>
                        <option value="<?php echo $row->api_cat_id?>" <?php echo in_array($row->api_cat_id, $cats) ? 'selected' : '';?>><?php echo $row->title;?></option>
                        <?php }?>
                        <?php }?>
                     </select>
                  </div>


                  <span class="multiple"></span>

                  <h3 style="font-size: 17px;">Contact Details</h3>
                  <input style="width:99%; " type="text" placeholder="Organization" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="org" value="<?php echo isset($metadata) && $metadata['org'] != '' ? $metadata['org'] : '';?>">
                  <input style="width:99%; " type="text" id="website_url" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> placeholder="Website URL" name="website_url" value="<?php echo isset($events) ? $events->contact_url : '';?>">
                  <p class="exclude">
                     <input type="text" placeholder="Full Name*" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="contact_name" id="contact_name" class="exclude_input" required title="Please enter First and Last name" value="<?php echo isset($events) ? $events->contact_name : '';?>" style="">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="contact_name_check" name="exclude_name" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> <?php echo $metadata['exclude_name'] == 'on' ? 'checked' : '';?> >
                     <span class="checkmark"></span><label class="chkbox" for="contact_name_check">Exclude Name from public listing</label></span>
                     <input type="hidden" name="exclude_name" value="<?php echo $metadata['exclude_name'] == 'on' ? 'on' : 'off' ?>">
                  </p>

                  <p class="exclude">
                     <input type="text" placeholder="(XXX) XXX-XXXX*" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="contact_phone" class="exclude_input" id="contact_phone" required title="Please enter valid Phone number including area code" value="<?php echo isset($events) ? $events->contact_phone : '';?>" style="">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox"  id="contact_phone_check"  name="exclude_phone" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> <?php echo $metadata['exclude_phone'] == 'on' ? 'checked' : '';?> >
                     <span class="checkmark"></span><label class="chkbox" for="contact_phone_check">Exclude Phone from public listing</label></span>
                     <input type="hidden" name="exclude_phone" value="<?php echo $metadata['exclude_phone'] == 'on' ? 'on' : 'off' ?>">
                  </p>

                  <p class="exclude">
                     <input type="text" placeholder="Extension" name="extension" value="<?php echo isset($metadata) ? $metadata['extension'] : '';?>" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> style="" class="exclude_input">
					 <!--
                     <span class="chkbox">Exclude Extension from public listing
                     <input class="tix-tkt" type="checkbox" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>>
                     <span class="checkmark"></span> </span>
					 -->
                  </p>

                  <p class="exclude">
                     <input type="email" placeholder="Email*" name="email" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> id="email" required class="exclude_input" title="Please enter valid Email address" value="<?php echo isset($events) ? $events->contact_email : '';?>" style="">
                     <span class="chkbox"><input class="tix-tkt" type="checkbox" id="email_check" name="exclude_email" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> <?php echo $metadata['exclude_email'] == 'on' ? 'checked' : '';?> >
                     <span class="checkmark"></span><label class="chkbox" for="email_check">Exclude Email from public listing</label></span>
                     <input type="hidden" name="exclude_email" value="<?php echo $metadata['exclude_email'] == 'on' ? 'on' : 'off' ?>">
                  </p>

                  <div class="attachemnts">
                    <div class="logo-details">
                     <h3>Logo</h3>


                                     <?php if(isset($events) && count($events->files) > 0) {
                                     $i=0;
                                     foreach($events->files as $row) {
                                     if($row->type == 'logo') {
                                     $i++;
                                     ?>
                                <div id="logodropZone" class="logo-zone">
                                <input type="file" id="logo_image"  <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="logo_image" class="icon_droparea">
                                <h1 id="logozoneh1">Drag & Drop a single logo here</h1>
                                <button class="btn btn-eventimg Logo_btn" id="btnicon" onClick="$('#logo_image').click()">Browse File</button>
                                <div id="logo_files">
                                   <div id="logohide">
                                    <img class="uploaded-img" src="https://storage.googleapis.com/<?php echo $row->bucket?>/<?php echo $row->filename;?>" onClick="$('#logo_image').click()">
                                         <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                                        <span class="remove-img" id="remove-logo" style="cursor: pointer;">-</span>
                                         <?php }?>
                                    </div>
                                </div>
                                <input type="hidden" name="remove_logo" id="check_remove_logo" value="" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>>
                                <input type="hidden" name="remove_logo_id" value="<?php echo $row->id; ?>">


                                    <span style="display: none;" id="file_err">
                                     <p style="color: #ff0000;margin-top:11%;">
                                         File size should be less than 300 kb.<br/>
                                     </p>
                                    </span>
                                    <span style="display: none;" id="file_type_err">
                                     <p style="color: #ff0000;margin-top:11%;">
                                         File type should be png/jpg/jpeg only.<br/>
                                     </p>
                                    </span>
                                    <script>
                                            jQuery("#logodropZone h1").hide();
                                           jQuery("#logodropZone").css({"padding-top": "0px"});
                                           jQuery("#btnicon").hide();
                                    </script>
                                 </div>
                                <?php } } if($i == 0){  ?>
                                    <div id="logodropZone" class="logo-zone">
                                     <h1>Drag & Drop a single logo here</h1>
                                     <div id="logo_files"></div>
                                         <input type="file" id="logo_image"  <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> name="logo_image" class="icon_droparea">
                                         <button class="btn btn-eventimg Logo_btn" id="btnicon" onClick="$('#logo_image').click()">Browse File</button>
                                     <div style="clear:both"></div>
                                         <span style="display: none;" id="file_err">
                                          <p style="color: #ff0000;margin-top:11%;">
                                            File size should be less than 300 kb.<br/>
                                          </p>
                                         </span>
                                         <span style="display: none;" id="file_type_err">
                                          <p style="color: #ff0000;margin-top:11%;">
                                            File type should be png/jpg/jpeg only.<br/>
                                          </p>
                                         </span>


                            </div>
                                 <?php } } ?>

                        <div class="up-attach">
                            <h3 style="font-size:17px;">Attachment</h3>

                           <div>
                             <?php
                             if($event_state == 'upcoming'){
                             if(isset($events) && count($events->files) > 0) {
                                 $i=0;
                                 foreach($events->files as $file) {
                                   if($file->type == 'Pdf file')  {
                                       $i++;?>
                              <!--  <span>Include a single PDF for all instructions, waivers, map etc...</span>-->
                              <div id="pdfdropZone" class="pdf-zone">
                                 <h1 id="dd_text_pdf">Drag & Drop a single PDF here</h1>
                                 <div id="choose_pdf_div"></div>
                                 <button id="btnpdf" class="btn btn-eventimg PDF-btn">Browse File</button>
                                 <input type="file" id="pdf_image" name="attach_image" class="pdf_input" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?> value="<?php echo isset($file) ? 'https://storage.cloud.google.com/'.$file->bucket.'/'.$file->filename : ''?>">
                                 <div id="pdf_files"></div>


                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;"><?php echo $file->filename; ?>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File size should be less than 10 mb.<br/>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File type should be pdf only.<br/>
                                    </p>
                                 </span>
                            <input type="hidden" name="remove_pdf" id="check_remove_pdf" value="">
                            <input type="hidden" name="remove_pdf_id" value="<?php echo $row->id; ?>">
                             <?php if(isset($event_state) && $event_state == 'upcoming') { ?>
                             <span class="remove-file" id="remove-pdf" style="cursor: pointer;">-</span>
                             <?php } ?>

                               <script>  //jQuery("#pdf_image").siblings("#file_name p").text('');
                                           jQuery("#pdf_image").siblings("#file_name").css({"margin-top":"0%"});
                                           jQuery("#pdf_image").siblings("#file_name").show();
                                           jQuery("#dd_text_pdf").hide();
                                           jQuery("#pdfdropZone").css({"background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')","border": "2px dashed #ffffff","padding-top": "50px"});
                                           jQuery("#btnpdf").hide();

                                           </script>

                              </div>
                                     <?php } }
                                     if($i == 0){
                                     ?>

                          <!--  <span>Include a single PDF for all instructions, waivers, map etc...</span>-->
                                     <div style="clear:both"></div>

                              <div id="pdfdropZone" class="pdf-zone">
                                 <h1 id="dd_text_pdf">Drag & Drop a single PDF here.</h1><div id="choose_pdf_div"></div>
                                 <button id="btnpdf" class="btn btn-eventimg PDF-btn">Browse File</button>
                                 <input type="file" id="pdf_image" name="attach_image" class="pdf_input" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>>
                                 <div id="pdf_files"></div>
                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;">
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File size should be less than 10 mb.<br/>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File type should be pdf only.<br/>
                                    </p>
                                 </span>
                              </div>
                                     <?php } } }else if($event_state != 'upcoming' && isset($file)){
                                         $i=0;
                                 foreach($events->files as $file) {
                                   if($file->type == 'Pdf file')  {
                                       $i++;?>
                                <p class="up-attach">

                          <span>Important imformation about this event:</span>
                           <a class="btn-downlaod" href="<?php echo isset($file) ? 'https://storage.cloud.google.com/' . $file->bucket . '/' . $file->filename : '#' ?>" target="_blank" style="background: #529cfb; color: #fff; padding: 1px 13px;">Download <i class="fa fa-download"></i> </a>
                           <p><span>Don’t have Adobe Reader –</span><a href="https://get.adobe.com/reader/" target="_blank">click here</a> to download.</p>
                           <!-- </button> -->
                        </p>
                    <?php } } }else{ ?>
                              <div id="pdfdropZone" class="pdf-zone">
                                 <h1 id="dd_text_pdf">Drag & Drop a single PDF here.</h1><div id="choose_pdf_div"></div>
                                 <button id="btnpdf" class="btn btn-eventimg PDF-btn">Browse File</button>
                                 <input type="file" id="pdf_image" name="attach_image" class="pdf_input" <?php echo (isset($event_state) && $event_state == 'past') ? 'disabled' : '';?>>
                                 <div id="pdf_files"></div>
                                 <span style="display: none;" id="file_name">
                                    <p style="color: #ff6600;margin-top:10%;">
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File size should be less than 10 mb.<br/>
                                    </p>
                                 </span>
                                 <span style="display: none;" id="file_type_err">
                                    <p style="color: #ff0000;margin-top:11%;">
                                       File type should be pdf only.<br/>
                                    </p>
                                 </span>
                              </div>
                              <?php } ?>
                           </div>
                        </div>




                     </div>
                     <div class="map-details">
                        <h3>Map Details</h3>
                  <?php
                  $mapsrc= (isset($events)&& isset($events->long)) ?"https://webdev.snapd.com/map.php?lat=".$events->lat."&lng=".$events->long :"https://webdev.snapd.com/map.php?lat=56.1304&lng=-106.346771";
                  ?>
                  <iframe class="mapIframe" src="<?php echo $mapsrc; ?>" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                     </div>
                  </div>
               </div>
            
             <button class="next-btn" name="btnUpdate">NEXT</button>
            <!-- <a href="<?php //echo site_url()?>/manage-my-events"><button class="next-btn cancel-btn" type="button" style="margin: 6px; padding-bottom: 8px;">CANCEL</button></a> -->
            <button onClick="jQuery(this).css('width','350px');jQuery(this).text('Cancelling event changes...');setTimeout(function(){ window.location.href='<?php echo site_url()?>/manage-my-events'; }, 2000);" class="next-btn cancel-btn" type="button" style="margin: 6px; padding-bottom: 8px;">CANCEL</button>
         </form>
         <?php } ?>





         <!--<div class="next-btn"><a href="<?php// echo site_url(); ?>/create-tickets/?edit=<?php //echo $_GET['event_id']; ?>">NEXT</a></div>-->
         <div class="help-btn"><i class="fa fa-question"></i> Need Help? <a href="#">Visit our support site for answers</a></div>
         </div>
      </div>
      <!-- #container -->
   </div>
   <!-- #outer-wrapper -->
</div>



<!-- cropper modal start -->
<div class="modal fade img_crop" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
      <h5 class="modal-title" id="modalLabel">Crop the image</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
     </div>
     <div class="modal-body">
      <div class="img-container align-middle" style="min-height:250px">
        <img id="imageforcrop" src="">
      </div>
     </div>
     <div class="modal-footer">
         <div class="instructions"><b>1.</b> Use corner and midpoint grips to resize crop area <br/>
         <b> 2.</b> Select crop area to reposition   <br/>
         <b>3.</b> Drag on photo for new crop area</div>

    <!--
      <div class="btn-group">
          <button type="button" class="btn btn-primary cropOptions" data-method="zoom" data-option="0.1" title="Zoom In">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
              <span class="fa fa-search-plus"></span>
            </span>
          </button>
          <button type="button" class="btn btn-primary cropOptions" data-method="zoom" data-option="-0.1" title="Zoom Out">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
              <span class="fa fa-search-minus"></span>
            </span>
          </button>
        </div>

        <div class="btn-group">
          <button type="button" class="btn btn-primary cropOptions" data-method="move" data-option="-10" data-second-option="0" title="Move Left">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(-10, 0)">
              <span class="fa fa-arrow-left"></span>
            </span>
          </button>
          <button type="button" class="btn btn-primary cropOptions" data-method="move" data-option="10" data-second-option="0" title="Move Right">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(10, 0)">
              <span class="fa fa-arrow-right"></span>
            </span>
          </button>
          <button type="button" class="btn btn-primary cropOptions" data-method="move" data-option="0" data-second-option="-10" title="Move Up">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(0, -10)">
              <span class="fa fa-arrow-up"></span>
            </span>
          </button>
          <button type="button" class="btn btn-primary cropOptions" data-method="move" data-option="0" data-second-option="10" title="Move Down">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(0, 10)">
              <span class="fa fa-arrow-down"></span>
            </span>
          </button>
        </div>
      <div class="btn-group docs-buttons">
          <button type="button" class="btn btn-primary cropOptions" data-method="rotate" data-option="-45"  title="Rotate Left">
            <span class="fa fa-undo"></span>
          </button>
          <button type="button" class="btn btn-primary cropOptions" data-method="rotate" data-option="45" title="Rotate Right">
            <span class="fa fa-repeat"></span>
          </button>
        </div>
-->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-primary" id="crop">Crop</button>
     </div>
   </div>
  </div>
</div>
<!-- cropper modal ends -->

<!-- #main content -->
<?php /* ?>
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
                        <div class="upload-image">
                           <?php if(isset($events) && count($events->photos) > 0 && $events->photos[0]->caption == 'image') { ?>
                           <img id="modal_image" src="https://storage.googleapis.com/<?php echo $events->photos[0]->file->bucket?>/<?php echo $events->photos[0]->file->filename;?>" alt="uplaod images" style="max-height: 250px;">
                           <?php } else { ?>
                           <img id="modal_image" src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
                           <?php }?>

                        </div>
                        <form class="event-details">
                           <h3 id="modal_event_title"><?php echo isset($events) ? $events->name : ''?></h3>
                           <span class="p-date">
                              <?php if(isset($events)) {
                                 for($i=0; $i<count($events->event_dates); $i++) {

                                     $start_date = $events->event_dates[$i]->start_date;
                                     $end_date = $events->event_dates[$i]->end_date;

                                     if(date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date))) {

                                         $end_date = 'to '.date('h:i a', strtotime($end_date));
                                     } else {

                                         $end_date = 'to '.date('M d, Y h:i a', strtotime($end_date));
                                     }

                                     if($i == 0) { ?>
                              <p><span id="prev_start_date"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="prev_end_date"><?php echo $end_date;?></span></p>
                              <?php } else { ?>
                              <p class="multi_span" id="p_<?php echo $i;?>"><span id="prev_start_date_<?php echo $i;?>"><?php echo date('M d, Y h:i a', strtotime($start_date))?></span> <span id="prev_start_date_<?php echo $i;?>"><?php echo $end_date;?></span></p>
                              <?php }
                                 }
                                 } else { ?>
                              <p><span id="prev_start_date">NOT SET</span>&nbsp;<span id="prev_start_date">NOT SET</span></p>
                              <?php }?>
                           </span>
                           <?php
                              if(isset($events)) {
                                   $country = $wpdb->get_row("Select * from wp_countries where  id = $events->country_id");
                                   $state = $wpdb->get_row("select * from wp_states where id = $events->province_id");
                              }
                              ?>
                           <p>
                              <span id="modal_venue"><?php echo isset($events) ? $events->location : '';?></span>, <br/>
                              <span id="modal_address"><?php echo isset($events) ? $events->address2 : '';?></span><br/>
                              <span id="modal_city"><?php echo isset($events) ? $events->city : '';?></span>,
                              <span id="modal_country"><?php echo isset($events) ? $country->name : '';?></span><br/>
                              <span id="modal_province"><?php echo isset($events) ? $state->name : '';?></span><br/>
                              <span id="modal_zip"><?php echo isset($events) ? $events->postalcode : '';?></span>
                           </p>
                           <div class="p-description">
                              <h3>Description</h3>
                              <p id="modal_description"><?php echo isset($events) ? $events->description : '';?></p>
                              <!-- <div class="exp-more"> Read More <span> <img src="http://webdev.snapd.com/wp-content/uploads/2019/09/down-arrow.png"></span></div>-->
                           </div>
                  <div id="modal_catg"><?php foreach($events->categorys as $category){ ?>
                  <div class="p-catg" style="margin-right:5px;"><?php echo $category->name; ?></div>
                  <?php }?></div>

                           <h2 class="f-tkt">This is a free Event</h2>
                           <div class="attachemnts">
                              <div class="logo-details">
                                 <h3>Contact Details</h3>
                                 <p><b>Name:</b> <span id="modal_name"><?php echo isset($events) ? $events->contact_name : '';?></span></p>
                                 <p><b>Phone:</b> <span id="modal_phone"><?php echo isset($events) ? $events->contact_phone : '';?></span></p>
                                 <p><b>Website: </b><a href="#"><span id="modal_website"><?php echo isset($events) ? $events->contact_url : '';?></span></a></p>
                                 <p><b>Email: </b><a href="mailto:"><span id="modal_email"><?php echo isset($events) ? $events->contact_email : '';?></span></a></p>
                                 <p class="up-logo">
                                    <?php if(isset($events) && count($events->photos) > 0) {
                                       $i=0;
                                       foreach($events->photos as $row) {

                                           if($row->caption == 'logo') { ?>
                                    <img id="modal_logo" src="https://storage.googleapis.com/<?php echo $row->file->bucket?>/<?php echo $row->file->filename;?>">
                                    <?php $i++;}
                                       } }?>
                                 </p>
                                 <?php if(isset($file)) { ?>
                                 <p class="up-attach">
                                 <h3>Attachment</h3>
                                 <!-- <span>Include a single PDF for all instructions, waivers, map etc...</span> -->
                                 <a class="btn-downlaod" href="<?php echo isset($file) ? 'https://storage.cloud.google.com/'.$file->bucket.'/'.$file->filename : '#'?>" target="_blank" style="background: #529cfb; color: #fff; padding: 1px 13px;">Download</a>
                                 </p>
                                 <?php }?>
                              </div>
                              <div class="map-details">
                                 <h3>Map Details</h3>
                                 <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d184551.80858184173!2d-79.51814199446795!3d43.718403811497105!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4cb90d7c63ba5%3A0x323555502ab4c477!2sToronto%2C%20ON%2C%20Canada!5e0!3m2!1sen!2sin!4v1568367410679!5m2!1sen!2sin" width="250" height="290" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                              </div>
                           </div>
                        </form>
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
<?php */ ?>


<div class="modal" id="loadingeditModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="width: 220px !important;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="email-confomation">
                    <p class="mail-img" style="padding: 0px;"><img src="<?php echo site_url(); ?>/wp-content/uploads/loading.gif"></p>
               <p id="edit_modal_loader_text"></p>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- progress modal ends-->

<!-- delet confirmation modal -->
				 <div class="modal" id="confirmdelete" role="dialog" style="display:none">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-body">
								<div class="email-confomation">
									<p id="confirmtext" style="padding: 0px;"></p>
									<p>
									<button onClick="" id="yesdelete" type="button" class="modalconfirm">Yes</button>
									<button onClick="jQuery('#confirmdelete').hide();" type="button" class="modaldenied">No</button>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>

<?php get_footer(); ?>
<script>
    jQuery(document).on('change', '#contact_phone_check', function() {
     if($(this).prop("checked") == true)
{
   $('input[name="exclude_phone"]:hidden').val("on");
}else
{
   $('input[name="exclude_phone"]:hidden').val("off");
}
});


jQuery(document).on('change', '#contact_name_check', function() {
     if($(this).prop("checked") == true)
{
   $('input[name="exclude_name"]:hidden').val("on");
}else
{
   $('input[name="exclude_name"]:hidden').val("off");
}
});


jQuery(document).on('change', '#email_check', function() {
     if($(this).prop("checked") == true)
{
   $('input[name="exclude_email"]:hidden').val("on");
}else
{
   $('input[name="exclude_email"]:hidden').val("off");
}
});
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="<?php echo site_url()?>/wp-content/themes/Divi Child/js/editevent.js"></script>
<script src="<?php echo site_url()?>/wp-content/themes/Divi Child/js/cropper.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw&libraries=places&callback=initAutocomplete" async defer></script>
<?php// if(isset($_SESSION['event_edit_data'])){ echo "Seession:<pre>"; print_r($_SESSION['event_edit_data']); } ?>
<?php// echo "<br>event data:<pre>"; print_r($events); ?>
