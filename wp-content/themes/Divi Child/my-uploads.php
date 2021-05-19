<?php
/*
Template Name: My Uploads
*/


if(!isset($_SESSION['Api_token'])){
   wp_redirect( site_url().'?page_id=187' );
   exit;
}
global $wpdb;
$token   =  $_SESSION['Api_token'];
$user = $_SESSION['userdata'];



/* fecthing user all uploads here start */
$range = (isset($_GET['state']))?$_GET['state']:3;
/* echo API_URL.'uploads?sort=DESC&sortType=id&userId='.$user->id.'&range='.$range.'&offset=0&limit=10'; */
$ch      = curl_init(API_URL.'uploads?sort=DESC&sortType=id&userId='.$user->id.'&range='.$range.'&offset=0&limit=1000');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apiresponsfull=json_decode($result);


$ch      = curl_init(API_URL.'uploads?sort=DESC&sortType=id&userId='.$user->id.'&range='.$range.'&offset=0&limit=10');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);

/* echo "<pre>"; print_r($apirespons); echo $apirespons->success; die;  */

/* fecthing user all uploads here end */

get_header(); ?>

<link href='<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/simpleLightbox.css' rel='stylesheet' type='text/css'>

<style>
span.pi{cursor:pointer;}
</style>
<div id="main-content">
<div class="outer-wrapper">
   <div class="container container-home">
      <div class="edit-event">
         <?php if(count($apirespons->photos)>0){ ?>
         <h3 class="h3-title"><?php echo count($apiresponsfull->photos) ?> photos in your library </h3>
         <div class="event-type">
            <h5>Add caption, edit thumbnail, remove photo</h5>
			<form method="get" class="event-btn">
				<label class="radio-chk"> <input type="radio" name="photo-event" <?php echo (isset($_GET['state']) && $_GET['state'] == "1") ? 'checked' : '';?> value="1" onclick="uploadState(1)"> <span class="checkmark1"> </span> Last Week</label>
				<label class="radio-chk"> <input type="radio" name="photo-event" <?php echo (isset($_GET['state']) && $_GET['state'] == "0") ? 'checked' : '';?> value="0" onclick="uploadState(0)"><span class="checkmark1"></span> Last Month</label>
				<label class="radio-chk"> <input type="radio" name="photo-event" <?php echo (!isset($_GET['state']) || $_GET['state'] == "3") ? 'checked' : '';?> value="3" onclick="uploadState(3)"><span class="checkmark1"></span> All</label>
			</form>
         </div>
		 <?php  }else{ ?>
		 <h3 class="h3-title">No photos founds in your library </h3>
         <?php } ?>
         <?php

            if(count($apirespons->photos)>0){
                $photos = $apirespons->photos;
		    	foreach($photos as $pic){

					$ch   = curl_init(API_URL . '/events/' . $pic->event_id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						  'Content-Type: application/json',
						  'Authorization: ' . $token
					   ));
				   $result = curl_exec($ch);
				   curl_close($ch);
				   $eventapirespons = json_decode($result);

			?>
				 <div class="event-detail" id="updivid_<?php echo $pic->id;?>">
					<div class="upload-image">
						<a title="<?php echo $pic->caption; ?>" href="https://storage.googleapis.com/<?php echo $pic->file->bucket?>/<?php echo $pic->file->filename;?>">
							<img src="https://storage.googleapis.com/<?php echo $pic->file->bucket?>/<?php echo $pic->file->filename;?>"/>
						</a>
					</div>
					<div class="event-details photo-library">
						<p class="photo-name">
							<?php echo $eventapirespons->event->name;?>
							<span class="go-to">
							  <a href="<?php echo site_url(); ?>/view-event/<?php echo $pic->event_id;?>">Go to event</a>
						   </span>
						</p>
						<p class="modify-position">
							<!--<span class="pi"><i class="fa fa-edit"></i></span> Modify thumbnail position -->
							<?php echo date('M d, Y', strtotime($pic->file->create_date));?>
						</p>
						<p  class="modify-position">
							<span class="pi" onClick="jQuery('#EditCapupid_<?php echo $pic->id;?>').show();">
								<i class="fa fa-edit"></i>
							</span>
							<span class="captxt"><?php echo $pic->caption; ?><span>
						</p>
					   <p  class="modify-position">
						<span class="pi" onClick="jQuery('#loadingModalupid_<?php echo $pic->id;?>').show();">
							<i class="fa fa-trash"></i>
						</span> <span>Remove photo from event</span>
						</p>

					</div>
				 </div>
				 <!-- delet confirmation modal -->
				 <div class="modal" id="loadingModalupid_<?php echo $pic->id;?>" role="dialog" style="display:none">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-body">
								<div class="email-confomation">
									<p style="padding: 0px;">Are you sure you want to remove photo from event?</p>
									<p>
									<button onClick="confirmDelUpld(<?php echo $pic->id;?>)" type="button" class="load-event">Yes</button>
									<button onClick="jQuery('#loadingModalupid_<?php echo $pic->id;?>').hide();" type="button" class="cancel-btn">No</button>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- edit caption modal -->
				 <div class="modal" id="EditCapupid_<?php echo $pic->id;?>" role="dialog" style="display:none">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-body">
								<div class="email-confomation">
									<p style="padding: 0px;">Add new caption.</p>
									<p style="padding: 2px;">
										<input class="new-capt" type="text" id="newCap_<?php echo $pic->id;?>" value="<?php echo $pic->caption; ?>">
									</p>
									<p>
									<button class="load-event" onClick="UpCapChangeUpld(<?php echo $pic->id;?>)" type="button">Update</button>
									<button class="cancel-btn" onClick="jQuery('#EditCapupid_<?php echo $pic->id;?>').hide();" type="button">Cancel</button>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
         <?php } ?>
				<?php if(count($apiresponsfull->photos)>10){ ?>
					<p class="load-more"><button class="load-event" onClick="loaduploads(this,<?php echo $user->id ?>,<?php echo $range ?>,10)">Load More</button></p>
				<?php } ?>
     <?php  } ?>

      </div>
   </div>
   <!-- # outer-wrapper-->
</div>
<!-- #main content -->
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
<script type="text/javascript" src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/simpleLightbox.js"></script>
<script src="<?php echo site_url(); ?>/wp-content/themes/Divi Child/js/myupload.js"></script>
<script type='text/javascript'>
   $(document).ready(function() {
		$('.upload-image a').simpleLightbox();
  });
</script>
<?php get_footer(); ?>
