<script type='text/javascript' src='https://webdev.snapd.com/wp-includes/js/jquery/jquery.js?ver=1.12.4-wp'></script>
<?php 
session_start();
require('../../../../wp-config.php');

$token   =  $_SESSION['Api_token'];

if (isset($_FILES['gallery_images'])){
	 /* echo "<pre>"; print_r($_FILES); die; */
	$uploadmessage="";
	for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
        
		$validextensions = array("jpeg","jpg", "png"); // Extensions which are allowed.
        $filename             = explode('.', basename($_FILES['gallery_images']['name'][$i])); // Explode file name from dot(.)
        $file_extension  = end($filename); 
        $j               = $j + 1; // Increment the number of uploaded images according to the files in array.
        if(($_FILES["gallery_images"]["size"][$i] <= 1000000000) && in_array($file_extension, $validextensions)) {
            
			 $data       = file_get_contents($_FILES['gallery_images']['tmp_name'][$i]);
			 
						 
?>
<script>
	var blob = dataURItoBlob(<?php echo $_FILES['gallery_images']['tmp_name'][$i]; ?>);
	var form = new FormData();
		form.append("name", "<?php echo substr(str_replace(" ","_",$filename[0]),0,10).date('ymd').time(); ?>");
		form.append("eventId", "<?php echo $_GET['event_id']; ?>");
		form.append("caption", "<?php echo $filename[0]; ?>");
		form.append("file", input.files[0], "file");

		var settings = {
		  "url": "https://35.203.116.207/uploads/form",
		  "method": "POST",
		  "timeout": 0,
		  "headers": {
			"Authorization": "<?php echo $_SESSION['Api_token']; ?>",
			"Accept": "application/vnd.pagerduty+json"
		  },
		  "datatype": 'jsonp',
		  "processData": false,
		  "mimeType": "multipart/form-data",
		  "contentType": false,
		  "data": form
		};

		$.ajax(settings).done(function (response) {
		  console.log(response);
		});
</script>
<?php	
$result = array("success" => true);		
	echo json_encode($result, JSON_UNESCAPED_SLASHES);		 
			 
			 /* $fields = array(
				"eventId"   => $_GET['event_id'],
				"name"      => substr(str_replace(" ","_",$filename[0]),0,10).date('ymd').time(),
				"caption"    => $filename[0],
				"contentType" => 'image/'.$file_extension,
				"data"      => base64_encode($data),
				);          
          

           $payload = json_encode($fields);	     
         
			 $ch   = curl_init(API_URL . '/uploads/');
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			 curl_setopt($ch,CURLOPT_POSTFIELDS,$payload);
			 curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization: ' . $token
			 ));
			 $result = curl_exec($ch);
			 curl_close($ch);

			 $response=json_decode($result);
			
			
			if ($response->success) {
				$result = array("success" => true);
				echo json_encode($result, JSON_UNESCAPED_SLASHES);
				exit();
            } else { 
				$result = array("success" => false,"error" =>"Please try later!","errorcode" => "invalid_file_ext");
				echo json_encode($result, JSON_UNESCAPED_SLASHES);
				exit();
            } */
        } else { 
			$result = array("success" => false,"error" =>"Invalid file Size or Type","errorcode" => "invalid_file_ext");
			echo json_encode($result, JSON_UNESCAPED_SLASHES);
			exit();
        }
    }
	exit;
}
?>