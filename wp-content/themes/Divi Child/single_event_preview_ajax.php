<?php
require('../../../wp-config.php');

   global $wpdb;
   $token   =  $_SESSION['Api_token'];

   if(isset($_POST['event_id']) && $_POST['event_id'] != '') {

       $event_id = $_POST['event_id'];

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

         //  echo API_URL.'files/'.$metadata['file_id'];die;

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
     // echo "<pre>"; print_r($event_detail); print_r($file); die();

   ?>
   <link href='https://makitweb.com/demo/photo_gallery/simplelightbox-master/dist/simplelightbox.min.css' rel='stylesheet' type='text/css'>
   <script type="text/javascript" src="https://makitweb.com/demo/photo_gallery/simplelightbox-master/dist/simple-lightbox.js"></script>
   <script src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/custom.js"></script>
   <style>
   .container-home .event-detail {
    margin-bottom: 40px !important;
}
.single-evnt-detail p b {
    width: 100px !important;
    float: left;
}</style>
<div id="main-content">
   <div class="outer-wrapper ">
      <div class="container container-home">
         <div class="event-detail">
           <div class="upload-image">
              <?php if (isset($event_detail) && count($event_detail->files) > 0 && $event_detail->files[0]->type == 'image') { ?>
                  <img src="https://storage.googleapis.com/<?php echo $event_detail->files[0]->bucket ?>/<?php echo $event_detail->files[0]->filename; ?>" style="max-height: 250px;">
              <?php }else{  ?>
                  <img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg">
               <?php } ?>
			   <div class="image-gallery">
				<div>
					<?php if (!empty($token)) : ?>
					<form action="<?php echo site_url(); ?>/wp-content/themes/Divi Child/ajax/addupload.php?event_id=<?php echo $event_id; ?>" method="post" id="frm_image_upload" enctype="multipart/form-data">
					   <div class="">
						  <input type="file" name="gallery_images[]" id="event_gallery_images" accept=".jpg, .png, image/jpeg, image/png" style="display: none" multiple>
						  <!--button type="button" id="button_upload_image">Upload Images</button-->
						  <!--button type="button" onclick="$('#thefiles').next().find('.ff_fileupload_actions button.ff_fileupload_start_upload').click(); return false;">Upload all files</button-->
					   </div>
					</form>
					 <?php endif; ?>
					 <?php echo @$uploadmessage; ?>
				</div>

				 <?php if(isset($event_detail) && count($event_detail->photos) > 0) { ?>
                     <h3>Gallery</h3>
                     <?php foreach ($event_detail->photos as $row) { ?>
                           <a href="https://storage.googleapis.com/<?php echo $row->file->bucket ?>/<?php echo $row->file->filename; ?>">
                              <img src="https://storage.googleapis.com/<?php echo $row->file->bucket ?>/<?php echo $row->file->filename; ?>">
                           </a>
                     <?php } ?>
               <?php } ?>
			  </div>
            </div>

            <form class="event-details">
                 <?php if(count($event_detail->ticketTypes)>0){ ?>
					<?php if($event_detail->ticketTypes[0]->name ==''){ ?>
						<h2 class="f-tkt">FREE</h2>
					 <?php }else{ ?>
						<a href="<?php echo site_url()?>/get-tickets/<?php echo $event_id;?>"><button class="buy-ticket" type="button" name="btnSubmit">BUY TICKETS</button></a>
						<div class="ticket-range">
						<?php foreach ($event_detail->ticketTypes as $ticket) { ?>
								<p><?php echo $ticket->name ?> <?php echo '$'.$ticket->price ?></p>
						<?php } ?>
						</div>
					 <?php } ?>
				<?php } ?>
               <h3><?php echo isset($event_detail) ? $event_detail->name : ''; ?></h3>
               <?php
               foreach ($event_detail->event_dates as $edate)
               {
                 echo format_dates($edate->start_date, $edate->end_date) . "<br>";
               }
               if (isset($event_detail))
               {
                  $country = $wpdb->get_row("Select * from wp_countries where  id = $event_detail->country_id");
                  $state = $wpdb->get_row("select * from wp_states where id = $event_detail->province_id");
               }
               ?>
               <p>
			   <?php echo isset($event_detail) ? !empty($event_detail->address1) ? $event_detail->address1.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? !empty($event_detail->address2) ? $event_detail->address2.'<br>': ' ' : ' '; ?>
                     <?php echo isset($event_detail) ? $event_detail->city : '';?>, <?php echo isset($event_detail) ? $state->name : '';?>, <?php echo isset($event_detail) ? $country->name : '';?><br/>
                     <?php echo isset($event_detail) ? $event_detail->postalcode : '';?>
               </p>
               <div class="p-description">
                  <h3>Description</h3>
                  <p><?php echo isset($event_detail) ? $event_detail->description : ''; ?></p>
               </div>
               <div class="row">
                  <?php foreach ($event_detail->categorys as $category) { ?>
                     <div class="p-catg" style="margin-right:5px;"><?php echo $category->name; ?></div>
                  <?php } ?></div>

			   <div class="single-evnt-detail">
                     <?php
                     $cdet = "";
                     if(isset($event_detail)){
                     $cdet .= isset($metadata['org']) && $metadata['org'] != '' ? "<p><b>Organization:</b>".$metadata['org']."</p>" : "";
                     $cdet .= $event_detail->contact_name != '' && isset($metadata) && $metadata['exclude_name'] == 'off' ? "<p><b>Name:</b>".$event_detail->contact_name."</p>" : "";
                     $cdet .= $event_detail->contact_phone != '' && isset($metadata) && $metadata['exclude_phone'] == 'off' ? "<p><b>Phone:</b>".$event_detail->contact_phone."</p>" : "";
                     $cdet .= isset($metadata) && $metadata['extension'] != '' && $metadata['exclude_phone'] == 'off' ? "<p><b>Extension:</b>".$metadata['extension']."</p>" : "";
                     $cdet .= $event_detail->contact_url != '' ? "<p><b>Website:</b>".$event_detail->contact_url."</p>" : "";
                     $cdet .= $event_detail->contact_email != '' && isset($metadata) && $metadata['exclude_email'] == 'off' ? "<p><b>Email:</b>".$event_detail->contact_email."</p>" : "";
                    }
                    if ($cdet != "")
                    {
                      echo "<h3>Contact Details</h3>\n$cdet";
                    }
                    ?>
                  </div>

               <div class="attachemnts">
                 <?php if(isset($metadata['file_id']) || isset($metadata['logo_id'])){ ?>
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
                     <?php if(isset($file)) { ?>
                        <p class="up-attach">
                           <h3>Attachment</h3>
                          <p>Important imformation about this event:</p>
                           <a class="btn-downlaod" href="<?php echo isset($file) ? 'https://storage.cloud.google.com/' . $file->bucket . '/' . $file->filename : '#' ?>" target="_blank" style="background: #529cfb; color: #fff; padding: 1px 13px;">Download <i class="fa fa-download"></i> </a>
                           <p><span>Don’t have Adobe Reader – </span><a href="https://get.adobe.com/reader/" target="_blank">click here</a> to download.</p>
                           <!-- </button> -->
                        </p>
                     <?php } ?>
                  </div>
				  <?php } ?>

               <div class="<?php echo isset($file) && (isset($metadata['file_id']) || isset($metadata['logo_id'])) ? 'map-details' : 'map-inter' ; ?>">
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
   <?php }
   ?>
   <script>
   $(document).ready(function() {
      var gallery = $('.image-gallery a').simpleLightbox();
    });
   </script>
