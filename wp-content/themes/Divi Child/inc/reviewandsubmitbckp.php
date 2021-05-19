<?php  
/* echo "<pre>"; print_r($_SESSION); die; */
if(isset($_POST['btnFinalSubmit'])){
	
	
    if(isset($_SESSION['event_data'])) {    
            
        $country_id = $_SESSION['event_data']['country'];
        $state_id = $_SESSION['event_data']['state'];  
        $countries = $wpdb->get_row("Select * from wp_countries where id=$country_id");
        $states = $wpdb->get_row("Select * from wp_states where id=$state_id");
        
        $phone = '';

        if($_SESSION['event_data']['contact_phone'] != '') {

            $ph = explode(' ', $_SESSION['event_data']['contact_phone']);
            $ph1 = trim($ph[0], '()');
            $ph2 = str_replace('-', '', $ph[1]);
            $phone = $ph1.$ph2;
        }        
		$eventmeta = array(
					'org' => $_SESSION['event_data']['org'],
					'exclude_name' => $_SESSION['event_data']['exclude_name'],
					'exclude_phone' => $_SESSION['event_data']['exclude_phone'],
					'extension' => $_SESSION['event_data']['extension'],
					'exclude_email' => $_SESSION['event_data']['exclude_email']
					);
        $data    = array(

            'name' => stripslashes($_SESSION['event_data']['title']),
            'description' => stripslashes($_SESSION['event_data']['description']),           
            'location' => stripslashes($_SESSION['event_data']['address1']),
            /* 'address1' => $_SESSION['event_data']['address1'], */
            'address2' => stripslashes($_SESSION['event_data']['streetaddress2']),
            'city' => $_SESSION['event_data']['city'],
            /* 'lat' => $_SESSION['event_data']['lat'],
            'long' => $_SESSION['event_data']['long'], not need now as david told its auto calucalted using city and zip code */
            'province' => $states->state_code,
            'postalcode' => $_SESSION['event_data']['postalcode'],
            'country' => $countries->sortname,
            'contact_name' => stripslashes($_SESSION['event_data']['contact_name']),
            'contact_email' => stripslashes($_SESSION['event_data']['email']),
            'contact_phone' => $phone,
            'contact_url' => $_SESSION['event_data']['website_url'],
            'has_tickets' => (isset($_SESSION['ticket_data']['ticket_name']) && ($_SESSION['ticket_data']['ticket_name'][0]!='')) ? true : false,
            'country_id' => $countries->id,
            'province_id' => $states->id,
            "drupal_user_id"=> $_SESSION['userdata']->id,
            'extension'=> $_SESSION['event_data']['extension'],
            'org'=> $_SESSION['event_data']['org'],
            'exclude_phone' => $_SESSION['event_data']['exclude_phone'],
            'exclude_name' => $_SESSION['event_data']['exclude_name'],
            'exclude_email' => $_SESSION['event_data']['exclude_email'],
            'categories' => $_SESSION['event_data']['category_id']
        );  
       
      
        
        $dateRanges=[];

       for($j=0; $j<count($_SESSION['event_data']['event_start_date']); $j++) {
            // $start_date = str_replace('am', '', $_SESSION['event_data']['event_start_date'][$j]);
            // $start_date = str_replace('pm', '', $start_date);            
            // $end_date = str_replace('am', '', $_SESSION['event_data']['event_end_date'][$j]);
            // $end_date = str_replace('pm', '', $end_date);
			$start_date = date('Y-m-d H:i', strtotime('+5 hour',strtotime($_SESSION['event_data']['event_start_date'][$j])));
            $end_date = date('Y-m-d H:i', strtotime('+5 hour',strtotime($_SESSION['event_data']['event_end_date'][$j])));
            $dateRanges[$j] = array($start_date,$end_date);
			
			if($j==0){ $ticket_start_date = $start_date; }
			if($j==(count($_SESSION['event_data']['event_start_date'])-1)){ $ticket_end_date = $end_date; }
        }  
		 /*
		for($j=0; $j<count($_SESSION['event_data']['event_start_date']); $j++) {			
            $daterange = explode(' - ',$_SESSION['event_data']['event_start_date'][$j]);			
			$dateRanges[$j] = array(date('Y-m-d H:i',strtotime('+5 hour',strtotime($daterange[0]))), date('Y-m-d H:i',strtotime('+5 hour',strtotime($daterange[1]))));
        }*/

        $data['dateRanges'] = $dateRanges;        
        $payload = json_encode($data);       
        $token   =  $_SESSION['Api_token'];    

		echo "<pre>";
		 echo "Event create /event<br/>"; print_r($data); echo $payload; 

        $ch      = curl_init(API_URL.'events');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length:' . strlen($payload),
            'Authorization: ' . $token
        ));

        $result = curl_exec($ch);       
        curl_close($ch);
        $apirespons = json_decode($result);     
       print_r($apirespons);
        if($apirespons->success) {
			$eventfiles=array();
            if(isset($_SESSION['event_data']['filetouploadname']) && count($_SESSION['event_data']['filetouploadname'])> 0) {
                foreach($_SESSION['event_data']['filetouploadname'] as $row){

                    $idata = array(
                        'name' => $row['name'],
                        "contentType"=> $row['type'],
                        "data"=> $row['base64'],
                        "franchise_id"=> 0,
                        "event_id"=> $apirespons->event->id,
                        "type" => 'image'
                    );

                    $payload = json_encode($idata);
                    
					echo "<br/><br/>files create /files<br/>"; print_r($idata); echo $payload; 

                    $ch      = curl_init(API_URL.'files');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length:' . strlen($payload),
                        'Authorization: ' . $token
                    ));

                    $result = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($result);
					
					// echo "<pre>"; print_r($response); die();
					
					$eventmeta['image_id']=$response->file->id;
                }
               
            }

            if(isset($_SESSION['event_data']['logo_image'])) {

                $idata = array(
                    'name' => $_SESSION['event_data']['logo_image'],
                    "contentType"=> $_SESSION['event_data']['logo_image_type'],
                    "data"=> $_SESSION['event_data']['logo_image_base64'],
                    "franchise_id"=> 0,
                    "event_id"=> $apirespons->event->id,
                    "type" => 'logo'
                );


                $payload = json_encode($idata);

echo "<br/><br/>files create /files<br/>"; 
print_r($idata); echo $payload; 
                $ch      = curl_init(API_URL.'files');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length:' . strlen($payload),
                    'Authorization: ' . $token

                ));

                $result = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($result);
				$eventmeta['logo_id']=$response->file->id;
				/* echo "<pre>"; print_r($response);  */
            }

            if(isset($_SESSION['event_data']['attach_image'])) {

                $img_data = array(
    
                    'name' => $_SESSION['event_data']['attach_image'],
                    'contentType' => 'application/pdf',
                    'data' => $_SESSION['event_data']['attach_image_base64'],
					"franchise_id"=> 0,
                    "event_id"=> $apirespons->event->id,
                    "type" => 'Pdf file'
                ); 
                
                $payload = json_encode($img_data);
				
				echo "<br/><br/>files create /files<br/>"; print_r($img_data); echo $payload; 
				
                $ch      = curl_init(API_URL.'files');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length:' . strlen($payload),
                    'Authorization: ' . $token
                ));
    
                $result7 = curl_exec($ch);
                curl_close($ch);
                $fileresponse = json_decode($result7);
                $file = $fileresponse->file;   
                echo "<pre>"; print_r($file);  
				$eventmeta['file_id']=$fileresponse->file->id;
				
            }
			/* echo "<pre>"; print_r($eventmeta); */
			if(count($eventmeta)>0) {
				
				$meta_data = array('metadata' => serialize($eventmeta));
				$payload = json_encode($meta_data);
				
				/* echo "<br/>edit event payload";
				echo $payload; */
				
				$token   =  $_SESSION['Api_token'];
			
			  echo "<br/><br/>Event update meta_data /events<br/>"; print_r($meta_data); echo $payload; 
			  
				$ch      = curl_init(API_URL.'events/'.$apirespons->event->id);
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
			
				$result12 = curl_exec($ch);
				curl_close($ch);
				$editresponse = json_decode($result12); 
			   print_r($editresponse);
			}  

            if(isset($_SESSION['ticket_data'])) {
                
                $tickets = $_SESSION['ticket_data'];
                echo "<br/><br/>Ticket data to store.<br/>"; print_r($tickets); 
                if($tickets['tkt_tax'] == 'yes') {
					$taxrat = $tickets['tax_rate']/100;
                    $tpdata = array(
                        'name' => $tickets['tax_name'],
                        'tax_id'=> $tickets['tax_id'],
                        'country_id' => $tickets['country'],
                        'tax_rate_aggregate' => $taxrat
                    );

                    $payload = json_encode($tpdata);
                  
					echo "<br/><br/>taxProfiles create /taxProfiles<br/>"; print_r($tpdata); echo $payload; 
				  
                    $ch      = curl_init(API_URL.'taxProfiles');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length:' . strlen($payload),
                        'Authorization: ' . $token

                    ));

                    $result22 = curl_exec($ch);
                    curl_close($ch);
                    $taxProfileResponse = json_decode($result22);
                    if($taxProfileResponse->success) {

                        $taxProfile = $taxProfileResponse->taxProfile;
                    }
                }

                if($_SESSION['ticket_data']['tkt_setup']=='Yes Tix'){										for($i=0; $i<$_SESSION['ticket_data']['count']; $i++) {

						$n = $i;
						$number = $n+1;

						$tdata = array(

							'name' =>  stripslashes($tickets['ticket_name'][$i]),
							'note' => stripslashes($tickets['ticket_details'][$i]),
							'event_id' => $apirespons->event->id,
							'currency_code' => 'USD',
							'event_dates' => array('start_date'=>$start_date,'end_date'=>$val)
						);

						if($tickets['no_of_tkt_available'][$i]!=0) {
							$tdata['max'] = $tickets['no_of_tkt_available'][$i];
						}else{
							$tdata['max'] = $tickets['ticket_per_bundle'][$i] * $tickets['bundles_available'][$i] ;
						}
						
						if(isset($taxProfile)) {
							$tdata['tax_profile_id'] = $taxProfile->id;
						}

						
						if($tickets['radio_tkt_start_time_'.$number] != 'Match Event') {
							$tdata['start'] = date('Y-m-d H:i',strtotime($tickets['tkt_start_date'][$i]));
						}else{
							$tdata['start'] = $ticket_start_date;
						}
						

						if($tickets['radio_tkt_end_time_'.$number] != 'Match Event') {
							$tdata['end'] = date('Y-m-d H:i', strtotime($tickets['tkt_end_date'][$i]));
						}else{
							$tdata['end'] = $ticket_end_date;
						}

						if($tickets['radio_release_time_'.$number] != 'Immediately') {
							$tdata['release'] = date('Y-m-d H:i', strtotime($tickets['release_start_date'][$i]));
						}else{
							$tdata['release'] = date('Y-m-d H:i');
						}

						if($tickets['radio_expiration_time_'.$number] != 'None') {
							$tdata['expiration_date'] = date('Y-m-d H:i', strtotime($tickets['release_end_date'][$i]));
						}else{
							$tdata['expiration_date'] = $ticket_end_date;
						}
						

						if($tickets['price_radio_'.$number] == 'Paid') {
							$tdata['paid_yn'] = true;
							$tdata['price'] = $tickets['price_per_tkt'][$i];
							
						} else {
							$tdata['paid_yn'] = false;
						}
						$tdata['fee_percentage'] = $tickets['select_per'][$i];
						$tdata['tax_inclusion'] = $tickets['tax_inclusion'][$i];

						if($tickets['radio_tkt_type_'.$number] != 'Single Tickets') {
							$tdata['bundled_yn'] = true;
							$tdata['bundle_size'] = $tickets['bundles_available'][$i];
							//$tdata['order_limit'] = $tickets['total_tickets'][$i];
						} else {
							$tdata['bundled_yn'] = false;
							//$tdata['order_limit'] = $tickets['tkt_order_limit'][$i];
						}
						
						if($tickets['radio_tkt_limit_'.$number] != 'no') {
							$tdata['order_limit'] = $tickets['tkt_order_limit'][$i];
						} else {
							$tdata['order_limit'] = $tickets['no_of_tkt_available'][$i];
						}
				    
                        //$tdata['event_dates'] = $eventdates;
                
              
						
						
						$payload = json_encode($tdata);
					  
					  echo "<br/><br/>ticketTypes create /ticketTypes<br/>"; print_r($tdata); echo $payload; 
					  
						$ch      = curl_init(API_URL.'ticketTypes');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLINFO_HEADER_OUT, true);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Content-Length:' . strlen($payload),
							'Authorization: ' . $token

						));

						$result = curl_exec($ch);
						curl_close($ch);
						$response = json_decode($result);
						echo "<pre>"; print_r($response);
						
						if($tickets['radio_promo_code_'.$number] != 'disabled') {
							$pdata=array(
											  "name"=> $tickets['code_name'][$i],
											  "code"=> $tickets['code_name'][$i],
											  "metric"=> $tickets['radio_promo_code_'.$number],
											  "value"=> $tickets['code_value'][$i],
											  "ticket_type_id" => $response->ticketType->id);
							echo $payloadpromo = json_encode($pdata);
							
							$ch      = curl_init(API_URL.'ticketPromos');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLINFO_HEADER_OUT, true);
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadpromo);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(
								'Content-Type: application/json',
								'Content-Length:' . strlen($payloadpromo),
								'Authorization: ' . $token

							));
							$result = curl_exec($ch);
							curl_close($ch);
							$response = json_decode($result);
							echo "<pre>"; print_r($response);
						}
					}				}

            }	
			
            unset ($_SESSION["event_data"]);
            unset ($_SESSION["ticket_data"]);
			
			?>
			<br/><br/><br/>
			<a href="<?php echo site_url().'?page_id=664&event_id='.$apirespons->event->id; ?>"><h2>Click here to redirect success page</h2></a>
			<?php
			die();
            header("Location: ".site_url().'?page_id=664&event_id='.$apirespons->event->id);
        }else{
			echo "<pre>"; print_r($apirespons); die;
			
		}
    }

    if(isset($_SESSION['event_edit_data'])) {
        
        echo "<pre>"; print_r($_SESSION['event_edit_data']);

        $token   =  $_SESSION['Api_token'];

        if(isset($_SESSION['event_edit_data']['filetouploadname'])){
            
            if(isset($_SESSION['event_edit_data']['r_img']) && $_SESSION['event_edit_data']['r_img'] != ''){
                delete_file($_SESSION['event_edit_data']['r_img_id']);
            }else{
                echo "<script>alert('no instruction to remove file');</script>";
            }

            $idata = array(
            'name' => $_SESSION['event_edit_data']['filetouploadname'],
            "contentType"=> "image/png",
            "data"=> $_SESSION['event_edit_data']['filetouploadname_base64'],
            "franchise_id"=> 0,
            "event_id"=> $_GET['edit'],
            "type" => 'image'
        );

            echo "<br><br>requested event image<br><pre>"; print_r($idata);
            $payload = json_encode($idata);
            $ch      = curl_init(API_URL.'files');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length:' . strlen($payload),
                'Authorization: ' . $token
            ));

            $result41 = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result41);      
            echo "<br><br>event image response<br>"; print_r($response);
            unset($_SESSION['event_edit_data']['filetouploadname']);
            unset($_SESSION['event_edit_data']['filetouploadname_base64']);
        }

        if(isset($_SESSION['event_edit_data']['logo_image'])) {
            
            if(isset($_SESSION['event_edit_data']['r_logo']) && $_SESSION['event_edit_data']['r_logo'] != ''){
                delete_file($_SESSION['event_edit_data']['r_logo_id']);
            }else{
                echo "<script>alert('no instruction to remove file');</script>";
            }
            
            $idata = array(
                'name' => $_SESSION['event_edit_data']['logo_image'],
                "contentType"=> "image/png",
                "data"=> $_SESSION['event_edit_data']['logo_image_base64'],
                "franchise_id"=> 0,
                "event_id"=> $_GET['edit'],
                "type" => 'logo'
            );
            echo "<br><br>requested logo image<br><pre>"; print_r($idata);
            
            $payload = json_encode($idata);
            $ch      = curl_init(API_URL.'files');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length:' . strlen($payload),
                'Authorization: ' . $token

            ));

            $result51 = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result51);
            echo "<br><br>event logo response<br><pre>"; print_r($response);
            unset($_SESSION['event_edit_data']['logo_image']);
            unset($_SESSION['event_edit_data']['logo_image_base64']);
            
        }

        if(isset($_SESSION['event_edit_data']['attach_image'])) {
            
            if(isset($_SESSION['event_edit_data']['r_pdf']) && $_SESSION['event_edit_data']['r_pdf'] != ''){
                delete_file($_SESSION['event_edit_data']['r_pdf_id']);
            }else{
                echo "<script>alert('no instruction to remove file');</script>";
            }
            
            $idata = array(

                'eventId' =>  $_GET['edit'],
                'name' => $_SESSION['event_edit_data']['attach_image'],
                'data' => $_SESSION['event_edit_data']['attach_image_base64'],
                'caption' => 'File'
            );
            echo "<br><br>requested pdf image<br><pre>"; print_r($idata);
            
            $payload = json_encode($idata);
            $ch      = curl_init(API_URL.'files');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length:' . strlen($payload),
                'Authorization: ' . $token

            ));

            $result51 = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result51);
            echo "<br><br>event pdf response<br><pre>"; print_r($response);
            
            unset($_SESSION['event_edit_data']['attach_image']);
            unset($_SESSION['event_edit_data']['attach_image_base64']);
        }      

        $payload = json_encode($_SESSION['event_edit_data']);        
        $ch      = curl_init(API_URL.'events/'.$_GET['edit']);
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

        $result31 = curl_exec($ch);
        curl_close($ch);
        $editresponse = json_decode($result31);           
        $event_id = $_GET['edit'];

        unset ($_SESSION["event_edit_data"]);   
        unset ($_SESSION["ticket_data"]);
      //  header("Location: ".site_url().'/manage-my-events?success='.base64_encode($event_id));?>
        <a href="<?php echo site_url().'/manage-my-events?success='.base64_encode($event_id); ?>"><h2>Manage event page</h2></a>
        
        <?php
        die();
    }    
}

function delete_file($id){
     global $token;
       $ch = curl_init(API_URL.'files/'.$id);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLINFO_HEADER_OUT, true);
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
       curl_setopt($ch, CURLOPT_FAILONERROR, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Authorization: ' . $token
       ));
       $result = curl_exec($ch);
       $del_response = json_decode($result);
       echo "<br>Deleted File response <pre>"; print_r($del_response);
   }
?>