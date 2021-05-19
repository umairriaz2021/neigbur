<?php
session_start();
require('../../../../wp-config.php');

$token   =  $_SESSION['Api_token'];

$ch      = curl_init(API_URL.'uploads?sort=DESC&sortType=id&userId='.$_POST['uid'].'&range='.$_POST['range'].'&offset='.$_POST['offset'].'&limit=10');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);



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
				<!--p class="photo-name"><?php //echo $pic->file->filename;?></p-->
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
		<?php 
		$offset=$_POST['offset']+11;
		$ch      = curl_init(API_URL.'uploads?sort=DESC&sortType=id&userId='.$_POST['uid'].'&range='.$_POST['range'].'&offset='.$offset.'&limit=10');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   'Content-Type: application/json',
		   'Authorization: ' . $token
		));
		$result = curl_exec($ch);
		curl_close($ch);
		$nextapirespons=json_decode($result);
		/* echo "<pre>"; print_r($nextapirespons); die; */
		if(count($nextapirespons->photos)>0){ ?>
			<p class="load-more"><button class="load-event" onClick="loaduploads(this,<?php echo $_POST['uid'] ?>,<?php echo $_POST['range'] ?>,<?php echo $_POST['offset']+10; ?>)">Load More</button></p>
		<?php } ?>
<?php }else{ ?>
 <h3 class="h3-title">No more photos founds in your library </h3>
 <?php } ?>