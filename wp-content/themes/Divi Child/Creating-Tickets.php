<?php
/* Template Name: Create Tickets 2021 */

/*

Different session data:

$_SESSION['edit_event_data']  - Set after editing the first step of an existing event
$_SESSION['ticket_data']      - Set after completing the tickets step (this page) but pressing back to get back here
$_SESSION['event_data']       - The POST data from step 1 for the event (might need to be overriden by edit_event_data)


*/


if (!isset($_SESSION['Api_token']))
{
	wp_redirect(site_url().'?page_id=187');
	exit();
}

if (!isset($_GET['edit']) && $_GET['edit'] == '')
{
	unset($_SESSION['eventstate']);
}

global $wpdb;

if (isset($_POST['btnSubmit']))
{
  // coming from step 1
	if ($_FILES['fileToUpload']['name'] != '')
	{
		$type = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
		$base64 = explode('base64,', $_POST['event_image_base64']);
		$_POST['filetouploadname'][0] = array('name' => date('Ymd') . time() . rand(0, 9999) . '.' . $type, 'base64' => $base64[1],'type'=>$_FILES['fileToUpload']['type']);
	}

	if ($_FILES['logo_image']['name'] != '')
	{
		$type = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
		$_POST['logo_image'] = date('Ymd') . time() . rand(0, 9999) . '.' . $type;
		$_POST['logo_image_type'] = $_FILES['logo_image']['type'];
		$data = file_get_contents($_FILES['logo_image']['tmp_name']);
		$_POST['logo_image_base64'] =  base64_encode($data);
	}

	if ($_FILES['attach_image']['name'] != '')
	{
		$type = pathinfo($_FILES['attach_image']['name'], PATHINFO_EXTENSION);
		$data = file_get_contents($_FILES['attach_image']['tmp_name']);
		$_POST['attach_image'] = $_FILES['attach_image']['name'];
		$_POST['attach_image_base64'] = base64_encode($data);
	}
	$_SESSION['event_data'] = $_POST;
}
//$v3 = 'Select Neighbur Tix to complete your setup today!';
//$v2 = 'Did you know that Neighbur Tix provides a complete ticketing solution ?';
//$v = 'Neighbur TIX provides a complete ticketing solution from digital tickets to event marketing?';
//update_post_meta(364, 'tix_right_content1', $v2);
//update_post_meta(364, 'tix_right_content2', $v3);
if (isset($_POST['btnTicketSubmit']))
{
        $data = $_POST;
        
        unset($data['ticket_name'][0]);
        unset($data['ticket_details'][0]);
        unset($data['select_per'][0]);
        unset($data['tax_inclusion'][0]);
        unset($data['price_per_tkt'][0]);
        unset($data['no_of_tkt_available'][0]);
        unset($data['ticket_per_bundle'][0]);
        unset($data['bundles_available'][0]);
        unset($data['total_tickets'][0]);
        unset($data['tkt_order_limit'][0]);
        unset($data['tkt_start_date'][0]);
        unset($data['tkt_end_date'][0]);
        unset($data['release_start_date'][0]);
        unset($data['release_end_date'][0]);
        unset($data['code_name'][0]);
        unset($data['code_value'][0]);
        unset($data['ticket_type_dates']['num'][0]);
      
     
	$_SESSION['ticket_data'] = $data;
    
 
    
    
	if (isset($_GET['edit']) && $_GET['edit'])
	{
		header("Location: ".site_url().'?page_id=440&edit='.$_GET['edit']);  /* review and submit page */
	}
	else
	{
		header("Location: ".site_url().'?page_id=440');  /* review and submit page */
	}
  exit();
}


if (isset($_GET['edit']) && $_GET['edit'] != '')
{
	$token = $_SESSION['Api_token'];
	$event_id = $_GET['edit'];

	$ch = curl_init(API_URL.'ticketTypes?eventId='.$event_id);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: ' . $token));
	$tkt_response = curl_exec($ch);
	curl_close($ch);
	$tkt = json_decode($tkt_response);
 
	if ($tkt->success && !empty($tkt->ticketType))
	{
		$tickets = $tkt->ticketType;

		$ch = curl_init(API_URL.'events/'.$event_id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: ' . $token
      ));
		$response = curl_exec($ch);
		curl_close($ch);
		$event = json_decode($response)->event;
	//	var_dump($event);
	
	}

}

if (isset($_GET['clone']) && $_GET['clone'] != '')
{
	$token = $_SESSION['Api_token'];
	$event_id = $_GET['clone'];

	$ch = curl_init(API_URL.'ticketTypes?eventId='.$event_id);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	 'Content-Type: application/json',
	 'Authorization: ' . $token
  ));
	$tkt_response = curl_exec($ch);
	curl_close($ch);
	$tkt = json_decode($tkt_response);
	if ($tkt->success && !empty($tkt->ticketType))
	{
		$tickets = $tkt->ticketType;
	}
}

if (isset($_SESSION['event_data']))
{
	$country_id = $_SESSION['event_data']['country'];
	$currency = $country_id == '2' ? 'CAD' : 'USD';
}

if (isset($_SESSION['event_edit_data']))
{
	$country_id = $_SESSION['event_edit_data']['country_id'];
	$currency = $country_id == '2' ? 'CAD' : 'USD';
}

$event_state = $_SESSION['eventstate'];

$countries = $wpdb->get_results("Select * from wp_countries");
$fee = getConvenienceFees();

get_header();

$t = array();

// First set up default values
$t['%%FEES%%'] = json_encode($fee);
$t['%%UPDATE_OR_SUBMIT%%'] = "Submit";
$t['%%EDIT_OR_CREATE%%'] = "create-tkt-class";

$t['%%TIX_NO_CHECKED%%'] = "checked";
$t['%%TIX_DISABLED%%'] = "";
$t['%%TIX_YES_CHECKED%%'] = "";
$t['%%TIX_3RD_CHECKED%%'] = "";
$t['%%TICKET_SETUP_OPTIONS_DISPLAY%%'] = "none";

$t['%%COUNT_VALUE%%'] = 1;
$t['%%EVENT_STATE_VALUE%%'] = $event_state;
$max_val = 0;
foreach ($tickets as $tkt)
{
  $max_val += $tkt->max;
}
$t['%%CURRENT_TICKETS%%'] = $max_val;
$t['%%REPORT_LINK%%'] = '';

$t['%%TAX_NO_SELECTED%%'] = 'selected';
$t['%%TAX_YES_SELECTED%%'] = '';
$t['%%TAX_YES_DISPLAY%%'] = 'none';
$t['%%TAX_PRO_ID%%'] = '';
$t['%%TAX_ID_VAL%%'] = '';
$t['%%TAX_NAME_VAL%%'] = '';
$t['%%TAX_RATE_VAL%%'] = '';

$t['%%TIX_LEFT_TITLE%%'] = get_post_meta(364, 'tix_left_title', TRUE);
$t['%%TIX_LEFT_CONTENT%%'] = get_post_meta(364, 'tix_left_content', TRUE);
$t['%%TIX_THIRD_PARTY_TITLE%%'] = get_post_meta(364, 'tix_third_party_title', TRUE);
$t['%%TIX_THIRD_HTTP_SELECTED%%'] = 'selected';  // TODO
$t['%%TIX_THIRD_HTTPS_SELECTED%%'] = '';
$t['%%TIX_THIRD_URL%%'] = '';  // TODO
$t['%%TIX_IMAGE%%'] = wp_get_attachment_url(get_post_meta(364, 'tix_image', TRUE));
$t['%%TIX_RIGHT_CONTENT1%%'] = get_post_meta(364, 'tix_right_content1', TRUE);
$t['%%TIX_RIGHT_CONTENT2%%'] = get_post_meta(364, 'tix_right_content2', TRUE);
$t['%%TIX_PHONE_NO%%'] = get_post_meta(364, 'tix_phone_no', TRUE);

$t['%%CHARITABLE_NO_SELECTED%%'] = 'selected';
$t['%%CHARITABLE_YES_SELECTED%%'] = '';
$t['%%CHARITABLE_DISPLAY%%'] = 'none';
$t['%%CHARITABLE_VAL%%'] = '';

$t['%%THIRD_PARTY_DISPLAY%%'] = 'none';

$t['%%TICKETS%%'] = GetEmptyTicket(0);		// first add a "clean" template to use
$t['%%TICKETS%%'] = str_replace('id="tkt_0"', 'id="tkt_0" style="display: none;"', $t['%%TICKETS%%']);	// hide it
$t['%%TICKETS%%'] .= GETEmptyTicket(1);	// now add the editable ticket
$t['%%ADD_NEW%%'] = '<p class="add-new" style="cursor:pointer;" id="add_more_tkt"><i class="fa fa-plus"></i> Add New Ticket Type</p>';

if(isset($_GET['edit']) && isset($_SESSION['eventstate']))
{
  $t['%%BACK_BUTTON%%'] = '<a href="/edit-event?eventstate=' . $_SESSION['eventstate'] . '&event_id=' . $_GET['edit'] . '" id="back_page" class="back-btn">BACK</a>';
}
else
{
  $t['%%BACK_BUTTON%%'] = '<a href="/create-event/" id="back_page" class="back-btn">BACK</a>';
}

$t['%%DISABLE_STUFF%%'] = (isset($event_state) && $event_state == 'past') ? '<script>$("input:radio").attr("disabled",true);
$("input:text").attr("disabled",true);
$("select").attr("disabled",true);
</script>' : '';

ob_start();
get_footer();
$t['%%FOOTER%%'] = ob_get_contents();
ob_end_clean();

if (isset($_GET['edit']))
{
	$t['%%UPDATE_OR_SUBMIT%%'] = "Update";
    $t['%%EDIT_OR_CREATE%%'] = "edit-tkt-class";
	
}

if (isset($_SESSION['ticket_data']))

{	
    global $currency;
    $current_tick_all = $_SESSION['ticket_data']['current_ticket_allocation'];
    //print_r($current_tick_all);die;
	// use $SESSION['ticket_data'] when we are pressing BACK button, just repopulate with all the selected values
	$t['%%COUNT_VALUE%%'] = $_SESSION['ticket_data']['count'];
	$t['%%EVENT_STATE_VALUE%%'] = '';
	$t['%%CURRENT_TICKETS%%'] = $_SESSION['ticket_data']['current_ticket_allocation'];
	$t['%%REPORT_LINK%%'] = '';
	if (isset($_GET['edit']) && $_GET['edit'])
	{
		$t['%%REPORT_LINK%%'] = '<a href="https://webdev.snapd.com/sales-report/' . $_GET['edit'] .'" target="_new"><i class="fa fa-search"></i> Current Ticket Report</a>';
	}
	$t['%%TAX_NO_SELECTED%%'] = ($_SESSION['ticket_data']['tkt_tax'] == "no") ? 'selected' : '';
	$t['%%TAX_YES_SELECTED%%'] = ($_SESSION['ticket_data']['tkt_tax'] == "yes") ? 'selected' : '';
	$t['%%TAX_YES_DISPLAY%%'] = ($_SESSION['ticket_data']['tkt_tax'] == "yes") ? '' : 'none';
	$t['%%TAX_ID_VAL%%'] = $_SESSION['ticket_data']['tax_id'];
	$t['%%TAX_NAME_VAL%%'] = $_SESSION['ticket_data']['tax_name'];
	$t['%%TAX_RATE_VAL%%'] = $_SESSION['ticket_data']['tax_rate'];
	
	
 $t['%%TIX_NO_CHECKED%%'] = ($_SESSION['ticket_data']['tkt_setup']) == "No"  ? 'checked' : '';
  $t['%%TIX_DISABLED%%'] = (isset($event_state) && $event_state == 'past') ? 'disabled' : '';
  $t['%%TIX_YES_CHECKED%%'] = ($_SESSION['ticket_data']['tkt_setup']) == "Yes Tix" ? 'checked' : '';
  $t['%%TIX_3RD_CHECKED%%'] = ($_SESSION['ticket_data']['tkt_setup']) == "Yes 3rd party" ? 'checked' : '';
  $t['%%TICKET_SETUP_OPTIONS_DISPLAY%%'] =isset($_SESSION['ticket_data']) && $_SESSION['ticket_data']['tkt_setup'] == 'Yes Tix' ? 'block' : 'none';
   $t['%%CHARITABLE_NO_SELECTED%%'] = ($_SESSION['ticket_data']['charitablereceipt'] == "no") ? 'selected' : '';
  $t['%%CHARITABLE_YES_SELECTED%%'] = ($_SESSION['ticket_data']['charitablereceipt'] == "yes") ? 'selected' : '';
  $t['%%CHARITABLE_DISPLAY%%'] = ($_SESSION['ticket_data']['charitablereceipt'] == "yes") ? '' : 'none';
  $t['%%CHARITABLE_VAL%%'] = $_SESSION['ticket_data']['charitableregistration'];
  
  $t['%%TICKETS%%'] = "";
  $t['%%TICKETS%%'] = GetEmptyTicket(0);		// first add a "clean" template to use
  $t['%%TICKETS%%'] = str_replace('id="tkt_0"', 'id="tkt_0" style="display: none;"', $t['%%TICKETS%%']);	// hide it
	$j=1;
	foreach ($_SESSION['ticket_data']['ticket_name'] as $key=>$val)
	{
		// %%TICKETS%%
	$TEMPLATE = file_get_contents(__DIR__.'/Creating-Tickets_template_ticket.html');
	$out = $TEMPLATE;
	 $t['%%TICKETID%%'] = $_SESSION['ticket_data']['ticket_id'][$j];
		// %%TICKETS%%
		$t['%%TICKETNUM%%'] = $j;
		$t['%%TICKET_NAME%%'] = $_SESSION['ticket_data']['ticket_name'][$j];
		$t['%%TOTAL_SOLD%%'] = $_SESSION['ticket_data']['ticket_allocation'][$j] == ''  ? '0' : $_SESSION['ticket_data']['ticket_allocation'][$j];
		$t['%%TICKET_MAX%%'] =($_SESSION['ticket_data']['radio_tkt_type_'.$j]) == "Bundled Tickets" ? $_SESSION['ticket_data']['total_tickets'][$j] : $_SESSION['ticket_data']['no_of_tkt_available'][$j];
		$t['%%FEE_0_SELECTED%%'] = (isset($_SESSION['ticket_data']['select_per'][$j]) && $_SESSION['ticket_data']['select_per'][$j] == '0') ? 'selected' : '';
		$t['%%FEE_25_SELECTED%%'] =(isset($_SESSION['ticket_data']['select_per'][$j]) && $_SESSION['ticket_data']['select_per'][$j] == '25') ? 'selected' : '';
		$t['%%FEE_50_SELECTED%%'] = (isset($_SESSION['ticket_data']['select_per'][$j]) && $_SESSION['ticket_data']['select_per'][$j] == '50') ? 'selected' : '';
		$t['%%FEE_75_SELECTED%%'] = (isset($_SESSION['ticket_data']['select_per'][$j]) && $_SESSION['ticket_data']['select_per'][$j] == '75') ? 'selected' : '';
		$t['%%FEE_100_SELECTED%%'] = (isset($_SESSION['ticket_data']['select_per'][$j]) && $_SESSION['ticket_data']['select_per'][$j] == '100') ? 'selected' : '';
		$t['%%TAX_INCLUSION_0_SELECTED%%'] = (isset($_SESSION['ticket_data']['tax_inclusion'][$j]) && $_SESSION['ticket_data']['tax_inclusion'][$j] == '0') ? 'selected' : '';
		$t['%%TAX_INCLUSION_1_SELECTED%%'] = (isset($_SESSION['ticket_data']['tax_inclusion'][$j]) && $_SESSION['ticket_data']['tax_inclusion'][$j] == '1') ? 'selected' : '';
		$t['%%TAX_INCLUSION_2_SELECTED%%'] = (isset($_SESSION['ticket_data']['tax_inclusion'][$j]) && $_SESSION['ticket_data']['tax_inclusion'][$j] == '2') ? 'selected' : '';
		$t['%%TICKET_DETAILS%%'] = $_SESSION['ticket_data']['ticket_details'][$j];
		$t['%%TICKET_SINGLE_CHECKED%%'] =  ($_SESSION['ticket_data']['radio_tkt_type_'.$j]) == "Single Tickets" ? 'checked' : '' ;
		$t['%%TICKET_BUNDLE_CHECKED%%'] =  ($_SESSION['ticket_data']['radio_tkt_type_'.$j]) == "Bundled Tickets"  ? 'checked' : '';
		$t['%%TICKET_PAID_CHECKED%%'] = ($_SESSION['ticket_data']['price_radio_'.$j]) == "Paid" ? 'checked' : '';
		$t['%%TICKET_FREE_CHECKED%%'] = ($_SESSION['ticket_data']['price_radio_'.$j] != "Paid") ? 'checked' : '';
		$t['%%CURRENCY%%'] = $currency;
		$t['%%TICKET_PRICE_DISPLAY%%'] = ($_SESSION['ticket_data']['price_radio_'.$j]) == "Paid"? '' : 'none;';
		$t['%%TICKET_PRICE_VAL%%'] = number_format($_SESSION['ticket_data']['price_per_tkt'][$j], 2);
		$t['%%TICKET_PRICE_DISABLED%%'] = (isset($event_state) && $event_state == 'past') ? 'disabled' : '';
		$t['%%TICKET_QTY_DISPLAY%%'] = ($_SESSION['ticket_data']['radio_tkt_type_'.$j]) == "Bundled Tickets" ? 'none' : '';
		$t['%%TICKET_QTY_MIN%%'] = $_SESSION['ticket_data']['ticket_allocation'][$j] == ''  ? '0' :  $_SESSION['ticket_data']['ticket_allocation'][$j];
		$t['%%TICKET_QTY_VAL%%'] = $_SESSION['ticket_data']['no_of_tkt_available'][$j];
		$t['%%TICKET_QTY_DISABLED%%'] = (isset($event_state) && $event_state == 'past') ? 'disabled' : '';
		$t['%%TICKET_PRICE_SCRIPT%%'] = "";
		
			if (($_SESSION['ticket_data']['price_radio_'.$j]) == "Paid")
		{
			$tk['%%TICKET_PRICE_SCRIPT%%'] = "<script>jQuery('#price_per_tkt_" . $j . "').val(0);jQuery('#price_per_tkt_" . $j . "').attr('readonly',true);</script>";
		} 
		
		
		$t['%%TICKET_BUNDLE_DISPLAY%%'] = ($_SESSION['ticket_data']['radio_tkt_type_'.$j])=="Bundled Tickets" ? '' : 'none';
		$t['%%TICKETS_PER_BUNDLE_VAL%%'] = ($_SESSION['ticket_data']['radio_tkt_type_'.$j]) ?  ($_SESSION['ticket_data']['ticket_per_bundle'][
		    $j]) : 0;
		$t['%%TICKET_BUNDLES_AVAILABLE_VAL%%'] = ($_SESSION['ticket_data']['radio_tkt_type_'.$j]) ? $_SESSION['ticket_data']['bundles_available'][
		    $j] : 0;
		$t['%%TICKET_TOTAL_TICKETS_VAL%%'] = ($_SESSION['ticket_data']['radio_tkt_type_'.$j]) ? $_SESSION['ticket_data']['total_tickets'][$j] : 0;
		
		
		$t['%%TICKET_MATCH_EVENT_CHECKED%%'] = $_SESSION['ticket_data']['radio_tkt_start_time_'.$j] == "Match Event" ? 'checked' : '';
		$t['%%TICKET_START_TIME_CHECKED%%'] =$_SESSION['ticket_data']['radio_tkt_start_time_'.$j] != "Match Event" ? 'checked' : '';
		$t['%%TICKET_START_DISPLAY%%'] = $_SESSION['ticket_data']['radio_tkt_start_time_'.$j] == "Match Event" ? 'none' : 'block';
		$t['%%TICKET_START_VAL%%'] = $_SESSION['ticket_data']['tkt_start_date'] [$j]? date('M d, Y h:i a', strtotime($_SESSION['ticket_data']['tkt_start_date'] [$j])) :  'NOT SET';
		$t['%%TICKET_END_MATCH_CHECKED%%'] = $_SESSION['ticket_data']['radio_tkt_end_time_'.$j] == "Match Event" ? 'checked' : '';
		$t['%%TICKET_END_NEW_CHECKED%%'] = $_SESSION['ticket_data']['radio_tkt_end_time_'.$j] != "Match Event" ? 'checked' : '';
		$t['%%TICKET_END_DISPLAY%%'] = $_SESSION['ticket_data']['radio_tkt_end_time_'.$j] == "Match Event" ? 'none' : 'block';
		$t['%%TICKET_END_VAL%%'] =$_SESSION['ticket_data']['tkt_end_date'][$j]? date('M d, Y h:i a', strtotime($_SESSION['ticket_data']['tkt_end_date'][$j])) :  'NOT SET';
		$t['%%TICKET_LIMIT_NO_CHECKED%%'] = $_SESSION['ticket_data']['radio_tkt_limit_'.$j] == "no" ? 'checked' : '' ;
		$t['%%TICKET_LIMIT_YES_CHECKED%%'] =$_SESSION['ticket_data']['radio_tkt_limit_'.$j] == "yes" ? 'checked' : '' ;
		$t['%%TICKET_LIMIT_VAL%%'] = ($_SESSION['ticket_data']['tkt_order_limit'][$j]) ? $_SESSION['ticket_data']['tkt_order_limit'][$j] : '';
		$t['%%TICKET_LIMIT_DISPLAY%%'] = $_SESSION['ticket_data']['radio_tkt_limit_'.$j] == "no"  ? 'none' : 'block' ;
		
		
		$immediately = true;
		//$date = new DateTime($val->release);
		//$now = new DateTime();

		//if ($date > $now)
		//{
			//$immediately = false;
	//	}
		$t['%%TICKET_RELEASE_IMMEDIATELY_CHECKED%%'] = ($immediately) ? 'checked':'';
		$t['%%TICKET_RELEASE_SCHEDULED_CHECKED%%'] =($_SESSION['ticket_data']['radio_release_time_'.$j]) !="Immediately" ? 'checked':'';
		$t['%%TICKET_RELEASE_START_DISPLAY%%'] = ($_SESSION['ticket_data']['radio_release_time_'.$j]) =="Immediately"  ? 'none' : '';
		$t['%%TICKET_RELEASE_START_VAL%%'] = $_SESSION['ticket_data']['release_start_date'][$j] ?date('M d, Y h:i a', strtotime($_SESSION['ticket_data']['release_start_date'][$j])) :  'NOT SET';
		     // TODO: use value
		$t['%%TICKET_EXPIRE_NONE_CHECKED%%'] = ($_SESSION['ticket_data']['radio_expiration_time_'.$j]) == "None" ? 'checked':'';
		$t['%%TICKET_EXPIRE_SCHEDULED_CHECKED%%'] =($_SESSION['ticket_data']['radio_expiration_time_'.$j]) != "None" ? 'checked':'';
		$t['%%TICKET_EXPIRE_START_DISPLAY%%'] = $_SESSION['ticket_data']['radio_expiration_time_'.$j] == "None"  ? 'none' : 'block';
		$t['%%TICKET_EXPIRE_START_VAL%%'] = $_SESSION['ticket_data']['release_end_date'][$j] ?date('M d, Y h:i a', strtotime($_SESSION['ticket_data']['release_end_date'][$j])) :  'NOT SET';
			$start_date = $_SESSION['event_data']['event_start_date'][0];
			$end_date = $_SESSION['event_data']['event_end_date'][count($_SESSION['event_data']['event_start_date'])-1];
			$t['%%CLONE_INPUT%%'] = '<input type="hidden" id="EVENTSTRATDATE" value="' . date('Y/m/d h:i a', strtotime($start_date)) .'"/>';
			$t['%%CLONE_INPUT%%'] .= '<input type="hidden" id="EVENTENDDATE" value="' . date('Y/m/d h:i a', strtotime($end_date)) .'"/>';

		
		$t['%%TICKET_PROMO_DISABLED_CHECKED%%'] = ($_SESSION['ticket_data']['radio_promo_code_'.$j]) == "disabled"? "checked" : "";
		$t['%%TICKET_PROMO_DOLLAR_CHECKED%%'] = $_SESSION['ticket_data']['radio_promo_code_'.$j] == 'dollar' ? "checked" : "";
		$t['%%TICKET_PROMO_PERCENTAGE_CHECKED%%'] =  $_SESSION['ticket_data']['radio_promo_code_'.$j] == 'percentage' ? "checked" : "";
		$t['%%TICKET_PROMO_DISPLAY%%'] = (($_SESSION['ticket_data']['radio_promo_code_'.$j] == "dollar") ||  ($_SESSION['ticket_data']['radio_promo_code_'.$j] == "percentage") ) ? 'block' : 'none';
		$t['%%TICKET_PROMO_CODE_NAME%%'] = isset($_SESSION['ticket_data']['radio_promo_code_'.$j]) ? $_SESSION['ticket_data']['code_name'][$j] : '';
		$t['%%TICKET_PROMO_CODE_VAL%%'] = isset($_SESSION['ticket_data']['radio_promo_code_'.$j]) ? $_SESSION['ticket_data']['code_value'][$j] : '';
		
		
		
		
		$t['%%TICKET_DATES%%'] = "";

		///if (isset($_GET['clone']))
		//{
			$i=1;
			$check = array();
		//	print_r($_SESSION['event_data']);die;
			foreach ($_SESSION['event_data']['event_start_date'] as $key=>$val)
			{
				$start_date = $_SESSION['event_data']['event_start_date'][$key];
				$end_date = $_SESSION['event_data']['event_end_date'][$key];
				if (date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date)))
				{
					$selctdatae = $start_date.' '.'to'.' '.date('h:i a', strtotime($end_date));
				}
				else
				{
					$selctdatae = $start_date.' '.'to'.' '.$end_date;
				}
				for ($m=0;$m<count($_SESSION['event_data']['event_start_date']);$m++)
				{
					if ($_SESSION['ticket_data']['ticket_type_dates'][$j][$m] == $selctdatae)
					{
						$check[$i] ="true";
					}
				}
				$checked = ($check[$i] == "true") ? 'checked' : '';
				$t['%%TICKET_DATES%%'] .= '<span class="chkbox">&nbsp;<input type="checkbox" value="' . $selctdatae . '" ' . $checked . ' name="ticket_type_dates[' . $j . '][]">&nbsp;<span class="checkmark"></span>&nbsp;' . $selctdatae . '&nbsp;</span>';
				
				$i++;
			} // end of each date
	//	} // end of if clone
		
	/*	$checked = ($_SESSION['ticket_data']['checked_'.$j] == "true") ? 'checked' : '';
		
				$t['%%TICKET_DATES%%'] = '<span class="chkbox"><input type="checkbox" value="' . $_SESSION['ticket_data']['ticket_type_dates']['num'][$j] . '" '.$checked.' name="ticket_type_dates['.$j.'][]"><span class="checkmark"></span>&nbsp;&nbsp;' . $_SESSION['ticket_data']['ticket_type_dates']['num'][$j] . '</span>';   */

		
	foreach($t as $key => $val)
	{
	  $out = str_replace($key, $val, $out);
	  //$tk['%%TICKETS%%'] = $out;
	}
	//echo  $tk['%%TICKETS%%'];
     //echo out;
   
	$t['%%TICKETS%%'] .= $out;
		$j++;
	}

  $t['%%THIRD_PARTY_DISPLAY%%'] = isset($_SESSION['ticket_data']) && $_SESSION['ticket_data']['tkt_setup'] == 'Yes 3rd party' ? 'block' : 'none';

}
elseif (isset($tickets))

{
  $t['%%TIX_NO_CHECKED%%'] = !empty($tickets[0]->name) ? '' : 'checked';
  $t['%%TIX_DISABLED%%'] = (isset($event_state) && $event_state == 'past') ? 'disabled' : '';
  $t['%%TIX_YES_CHECKED%%'] = !empty($tickets[0]->name) ? 'checked' : '';
  $t['%%TIX_3RD_CHECKED%%'] = '';
  $t['%%TICKET_SETUP_OPTIONS_DISPLAY%%'] = $t['%%TIX_YES_CHECKED%%'] == 'checked' ? 'block' : 'none';

	$t['%%COUNT_VALUE%%'] = count($tickets);
	$t['%%EVENT_STATE_VALUE%%'] = $event_state;
	$max_val = 0;
	foreach ($tickets as $tkt)
	{
		$max_val += $tkt->max;
	}
	$t['%%CURRENT_TICKETS%%'] = $max_val;
	$t['%%REPORT_LINK%%'] = '';
	if (isset($_GET['edit']) && $_GET['edit'])
	{
		$t['%%REPORT_LINK%%'] = '<a href="https://webdev.snapd.com/sales-report/' . $_GET['edit'] .'" target="_new"><i class="fa fa-search"></i> Current Ticket Report</a>';
	}
	$t['%%TAX_NO_SELECTED%%'] = (isset($tickets[0]->tax_profile) && $tickets[0]->tax_profile != '') ? '' : 'selected';
	$t['%%TAX_YES_SELECTED%%'] = (isset($tickets[0]->tax_profile) && $tickets[0]->tax_profile != '') ? 'selected' : '';
	$t['%%TAX_YES_DISPLAY%%'] = (isset($tickets[0]->tax_profile) && $tickets[0]->tax_profile != '') ? '' : 'none';
	$t['%%TAX_PRO_ID%%'] = isset($tickets[0]->tax_profile) ? $tickets[0]->tax_profile->id : '';
	$t['%%TAX_ID_VAL%%'] = isset($tickets[0]->tax_profile) ? $tickets[0]->tax_profile->tax_id : '';
	$t['%%TAX_NAME_VAL%%'] = isset($tickets[0]->tax_profile) ? $tickets[0]->tax_profile->name : '';
	$t['%%TAX_RATE_VAL%%'] = isset($tickets[0]->tax_profile) ? $tickets[0]->tax_profile->tax_rate_aggregate : '';

  $t['%%CHARITABLE_NO_SELECTED%%'] = 'selected';
  $t['%%CHARITABLE_YES_SELECTED%%'] = '';
  $t['%%CHARITABLE_DISPLAY%%'] = 'none';
  $t['%%CHARITABLE_VAL%%'] = '';

  $t['%%TICKETS%%'] = "";
  
   $t['%%TICKETS%%'] = GetEmptyTicket(0);		// first add a "clean" template to use
$t['%%TICKETS%%'] = str_replace('id="tkt_0"', 'id="tkt_0" style="display: none;"', $t['%%TICKETS%%']);	// hide it
//$t['%%TICKETS%%'] .= GETEmptyTicket(1);	// now add the editable ticket
//$t['%%ADD_NEW%%'] = '<p class="add-new" style="cursor:pointer;" id="add_more_tkt"><i class="fa fa-plus"></i> Add New Ticket Type</p>';

	$j=1;
  $tk = array();
	foreach ($tickets as $key=>$val)
	{	
	    
	$TEMPLATE = file_get_contents(__DIR__.'/Creating-Tickets_template_ticket.html');
	$out = $TEMPLATE;
	//echo $out;
	 $tk['%%TICKETID%%'] = $val->id;
		// %%TICKETS%%
		$tk['%%TICKETNUM%%'] = $j;
		$tk['%%TICKET_NAME%%'] = $val->name;
		$tk['%%TOTAL_SOLD%%'] = $val->ticket_allocation == ''  ? '0' : $val->ticket_allocation;
		$tk['%%TICKET_MAX%%'] = $val->max;
		$tk['%%FEE_0_SELECTED%%'] = (isset($val->fee_percentage) && $val->fee_percentage == '0') ? 'selected' : '';
		$tk['%%FEE_25_SELECTED%%'] = (isset($val->fee_percentage) && $val->fee_percentage == '25') ? 'selected' : '';
		$tk['%%FEE_50_SELECTED%%'] = (isset($val->fee_percentage) && $val->fee_percentage == '50') ? 'selected' : '';
		$tk['%%FEE_75_SELECTED%%'] = (isset($val->fee_percentage) && $val->fee_percentage == '75') ? 'selected' : '';
		$tk['%%FEE_100_SELECTED%%'] = (isset($val->fee_percentage) && $val->fee_percentage == '100') ? 'selected' : '';
		$tk['%%TAX_INCLUSION_0_SELECTED%%'] = (isset($val->tax_inclusion) && $val->tax_inclusion == '0') ? 'selected' : '';
		$tk['%%TAX_INCLUSION_1_SELECTED%%'] = (isset($val->tax_inclusion) && $val->tax_inclusion == '1') ? 'selected' : '';
		$tk['%%TAX_INCLUSION_2_SELECTED%%'] = (isset($val->tax_inclusion) && $val->tax_inclusion == '2') ? 'selected' : '';
		$tk['%%TICKET_DETAILS%%'] = $val->note;
		$tk['%%TICKET_SINGLE_CHECKED%%'] = ($val->bundled_yn) ? '' : 'checked' ;
		$tk['%%TICKET_BUNDLE_CHECKED%%'] = ($val->bundled_yn) ? 'checked' : '';
		$tk['%%TICKET_PAID_CHECKED%%'] = ($val->paid_yn) ? 'checked' : '';
		$tk['%%TICKET_FREE_CHECKED%%'] = ($val->paid_yn) ? '' : 'checked';
		$tk['%%CURRENCY%%'] = $currency;
		$tk['%%TICKET_PRICE_DISPLAY%%'] = ($val->paid_yn) ? '' : 'none;';
		$tk['%%TICKET_PRICE_VAL%%'] = number_format($val->price, 2);
		$tk['%%TICKET_PRICE_DISABLED%%'] = (isset($event_state) && $event_state == 'past') ? 'disabled' : '';
		$tk['%%TICKET_QTY_DISPLAY%%'] = ($val->bundled_yn) ? 'none' : '';
		$tk['%%TICKET_QTY_MIN%%'] = $val->ticket_allocation == ''  ? '0' : $val->ticket_allocation;
		$tk['%%TICKET_QTY_VAL%%'] = ($val->bundled_yn) ? 0 : $val->max;
		$tk['%%TICKET_QTY_DISABLED%%'] = (isset($event_state) && $event_state == 'past') ? 'disabled' : '';
		$tk['%%TICKET_PRICE_SCRIPT%%'] = "";
		
		if ($val->paid_yn == 0)
		{
			$tk['%%TICKET_PRICE_SCRIPT%%'] = "<script>jQuery('#price_per_tkt_" . $j . "').val(0);jQuery('#price_per_tkt_" . $j . "').attr('readonly',true);</script>";
		}
		$tk['%%TICKET_BUNDLE_DISPLAY%%'] = ($val->bundled_yn) ? '' : 'none';
		$tk['%%TICKETS_PER_BUNDLE_VAL%%'] = ($val->bundled_yn) ? $val->order_limit/$val->bundle_size : 0;
		$tk['%%TICKET_BUNDLES_AVAILABLE_VAL%%'] = ($val->bundled_yn) ? $val->bundle_size : 0;
		$tk['%%TICKET_TOTAL_TICKETS_VAL%%'] = ($val->bundled_yn) ? $val->max : 0;

		if (isset($_GET['edit']))
		{
			$tk['%%EDIT_INPUT%%'] = '<input type="hidden" name="id[]" value="' . $val->id . '">';
		}

		$tk['%%TICKET_ID%%'] = $val->id;
		$tk['%%TICKET_MATCH_EVENT_CHECKED%%'] = $val->start == $val->event->start ? 'checked' : '';
		$tk['%%TICKET_START_TIME_CHECKED%%'] = $val->start == $val->event->start ? '' : 'checked';
		$tk['%%TICKET_START_DISPLAY%%'] = $val->start == $val->event->start ? 'none' : 'block';
		$tk['%%TICKET_START_VAL%%'] = $val->start ? date('M d, Y h:i a', strtotime($val->start)) :  'NOT SET';
		$tk['%%TICKET_END_MATCH_CHECKED%%'] = $val->end == $val->event->end ? 'checked' : '';
		$tk['%%TICKET_END_NEW_CHECKED%%'] = $val->end == $val->event->end ? '' : 'checked';
		$tk['%%TICKET_END_DISPLAY%%'] = $val->end == $val->event->end ? 'none' : 'block';
		$tk['%%TICKET_END_VAL%%'] = $val->end ? date('M d, Y h:i a', strtotime($val->end)) :  'NOT SET';
		$tk['%%TICKET_LIMIT_NO_CHECKED%%'] = $val->order_limit == $val->max ? 'checked' : '' ;
		$tk['%%TICKET_LIMIT_YES_CHECKED%%'] = $val->order_limit == $val->max ? '' : 'checked' ;
		$tk['%%TICKET_LIMIT_VAL%%'] = ($val->order_limit) ? $val->order_limit : '';
		$tk['%%TICKET_LIMIT_DISPLAY%%'] = $val->order_limit == $val->max  ? 'none' : 'block' ;

		$immediately = true;
		$date = new DateTime($val->release);
		$now = new DateTime();

		if ($date > $now)
		{
			$immediately = false;
		}
		$tk['%%TICKET_RELEASE_IMMEDIATELY_CHECKED%%'] = ($immediately) ? 'checked':'';
		$tk['%%TICKET_RELEASE_SCHEDULED_CHECKED%%'] = ($val->release) ? '':'checked';
		$tk['%%TICKET_RELEASE_START_DISPLAY%%'] = ($immediately) ? 'none' : '';
		$tk['%%TICKET_RELEASE_START_VAL%%'] = date('M d, Y h:i a');      // TODO: use value
		$tk['%%TICKET_EXPIRE_NONE_CHECKED%%'] = $val->event->end == $val->expiration_date ? 'checked' : '';
		$tk['%%TICKET_EXPIRE_SCHEDULED_CHECKED%%'] = $val->event->end == $val->expiration_date ? '' : 'checked';
		$tk['%%TICKET_EXPIRE_START_DISPLAY%%'] = $val->event->end == $val->expiration_date ? 'none' : 'block';
		$tk['%%TICKET_EXPIRE_START_VAL%%'] = $val->event->end == $val->expiration_date ? 'NOT SET' :  date('M d, Y h:i a', strtotime($val->expiration_date));

	//	if (isset($_GET['clone']))
	//	{
			$start_date = $_SESSION['event_data']['event_start_date'][0];
			$end_date = $_SESSION['event_data']['event_end_date'][count($_SESSION['event_data']['event_start_date'])-1];
			$tk['%%CLONE_INPUT%%'] = '<input type="hidden" id="EVENTSTRATDATE" value="' . date('Y/m/d h:i a', strtotime($start_date)) .'"/>';
			$tk['%%CLONE_INPUT%%'] .= '<input type="hidden" id="EVENTENDDATE" value="' . date('Y/m/d h:i a', strtotime($end_date)) .'"/>';

	//	}

		$tk['%%TICKET_PROMO_DISABLED_CHECKED%%'] = !isset($val->ticketPromo)? "checked" : "";
		$tk['%%TICKET_PROMO_DOLLAR_CHECKED%%'] =  $val->ticketPromo->metric == 'dollar' ? "checked" : "";
		$tk['%%TICKET_PROMO_PERCENTAGE_CHECKED%%'] =  $val->ticketPromo->metric == 'percentage' ? "checked" : "";
		$tk['%%TICKET_PROMO_DISPLAY%%'] = isset($val->ticketPromo) ? 'block' : 'none';
		$tk['%%TICKET_PROMO_CODE_NAME%%'] = isset($val->ticketPromo) ? $val->ticketPromo->code : '';
		$tk['%%TICKET_PROMO_CODE_VAL%%'] = isset($val->ticketPromo) ? $val->ticketPromo->value : '';
			 $tk['%%PROMO_ID%%'] = $val->ticketPromo->id;
 //foreach($tk as $key => $val)
	//{
//	  $out = str_replace($key, $val, $out);
	//}
	//echo $out;
		$tk['%%TICKET_DATES%%'] = "";

		if (isset($_GET['clone']))
		{
			$i=1;
			$check = array();
			print_r($_SESSION['event_data']);die;
			foreach ($_SESSION['event_data']['event_start_date'] as $key=>$val)
			{
				$start_date = $_SESSION['event_data']['event_start_date'][$key];
				$end_date = $_SESSION['event_data']['event_end_date'][$key];
				if (date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date)))
				{
					$selctdatae = $start_date.' '.'to'.' '.date('h:i a', strtotime($end_date));
				}
				else
				{
					$selctdatae = $start_date.' '.'to'.' '.$end_date;
				}
				for ($m=0;$m<count($_SESSION['event_data']['event_start_date']);$m++)
				{
					if ($_SESSION['ticket_data']['ticket_type_dates'][$j][$m] == $selctdatae)
					{
						$check[$i] ="true";
					}
				}
				$checked = ($check[$i] == "true") ? 'checked' : '';
				$tk['%%TICKET_DATES%%'] .= '<span class="chkbox">\n<input type="checkbox" value="' . $selctdatae . '" ' . $checked . ' name="ticket_type_dates[' . $j . '][]">\n<span class="checkmark"></span>\n' . $selctdatae . '\n</span>';
				
				$i++;
			} // end of each date
		} // end of if clone
		
		
	  if (isset($_GET['edit']))
	  {
	  	if (isset($_SESSION['event_edit_data']['dateRanges']))
	  	{
	  		$a=1;
	  		$check = array();
	  		foreach ($_SESSION['event_edit_data']['dateRanges'] as $key=>$v)
	  		{
	  			$start_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][$key][0]));
	  			$end_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][$key][1]));
	  			if (date("Y-m-d", strtotime($start_date)) == date("Y-m-d", strtotime($end_date)))
	  			{
	  				$selctdatae = $start_date." to ".date("h:i a", strtotime($end_date));
	  			}
	  			else
	  			{
	  				$selctdatae = $start_date." to ".$end_date;
	  			}

	  			foreach ($val->event_dates as $ind=>$tkt_dates)
	  			{
	  				$t_start_date = date('M d, Y h:i a', strtotime($tkt_dates->start_date));
	  				$t_end_date = date('M d, Y h:i a', strtotime($tkt_dates->end_date));
	  				if (date("Y-m-d", strtotime($t_start_date)) == date("Y-m-d", strtotime($t_end_date)))
	  				{
	  					$t_selctdatae = $t_start_date." to ".date("h:i a", strtotime($t_end_date));
	  				}
	  				else
	  				{
	  					$t_selctdatae = $t_start_date." to ".$t_end_date;
	  				}
	  				if ($t_selctdatae == $selctdatae)
	  				{
	  					$check[$a] = "true";
	  				}

	  				/*
				<?php $start_date = $_SESSION['event_edit_data']['event_start_date'][0]; ?>
				<?php $end_date = $_SESSION['event_edit_data']['event_end_date'][count($_SESSION['event_edit_data']['event_start_date'])-1]; ?>
				<input type="hidden" id="EVENTSTRATDATE" value="<?php echo date('Y/m/d h:i a', strtotime($start_date)) ?>"/>
				<input type="hidden" id="EVENTENDDATE" value="<?php echo date('Y/m/d h:i a', strtotime($end_date)) ?>"/>
	  				  */
	  			}  // end of foreach date
	  				$checked = ($check[$a] == "true") ? 'checked' : '';
				$tk['%%TICKET_DATES%%'] .= '<span class="chkbox">  <input type="checkbox" value="' . $selctdatae . '" ' . $checked . ' name="ticket_type_dates[' . $a . '][]"> <span class="checkmark"></span>  ' . $selctdatae . '  </span>';
				$a++;
				
			//custom	
			$tk['%%CLONE_INPUT%%'] = '<input type="hidden" id="EVENTSTRATDATE" value="' . date('Y/m/d h:i a', strtotime($start_date)) .'"/>';
			$tk['%%CLONE_INPUT%%'] .= '<input type="hidden" id="EVENTENDDATE" value="' . date('Y/m/d h:i a', strtotime($end_date)) .'"/>';
	  		//custom
	  		
	  		} // end of foreach dateRanges
	  	} //end of if (isset($_SESSION['event_edit_data']['dateRanges']))
	  		  

	  } // end of if(isset($_GET['edit']))
	 

	foreach($tk as $key => $val)
	{
	  $out = str_replace($key, $val, $out);
	  //$tk['%%TICKETS%%'] = $out;
	}
	//echo  $tk['%%TICKETS%%'];
     //echo out;
   
	$t['%%TICKETS%%'] .= $out;
    $j++;

	} // end of foreach tickets
//$t['%%TICKETS%%'] = $out;
	$t['%%ADD_NEW%%'] = '';
	//if ((isset($event_state) && $event_state == 'upcoming') || isset($_GET['clone']))
	//{
		$t['%%ADD_NEW%%'] = '<p class="add-new" style="cursor:pointer;" id="add_more_tkt"><i class="fa fa-plus"></i> Add New Ticket Type</p>';
//	}
}

//echo "\n\n\n<!--\n";
//echo "Loading template...\n";
$TEMPLATE = file_get_contents(__DIR__.'/Creating-Tickets_template.html');
if ($TEMPLATE == false)
{
	//echo "Could not load file!\n";
}
$out = $TEMPLATE;
//echo "Template is " . strlen($out) . " bytes.\n";
foreach($t as $key => $val)
{
//	if ($key != '%%FOOTER%%')
//		echo $key . " => " . $val . "\n";
//	else
//		echo $key . " => (long footer html)\n";
  $out = str_replace($key, $val, $out);
}
//echo "Template is now " . strlen($out) . " bytes.\n";
//echo "\n\n\n-->\n";

echo $out;



echo "\n\n\n<!--\n";
echo "\n\n\$tickets\n";

if (isset($tickets))
{
	print_r($tickets);
}
echo "\n\n\$_SESSION['edit_event_data']\n";
if (isset($_SESSION['edit_event_data']))
{
	print_r($_SESSION['edit_event_data']);
}
echo "\n\n\$_SESSION['ticket_data']\n";
if (isset($_SESSION['ticket_data']))
{
	print_r($_SESSION['ticket_data']);
}
echo "\n\n\$_SESSION['event_data']\n";
if (isset($_SESSION['event_data']))
{
	print_r($_SESSION['event_data']);
}

echo "\n\n\n-->\n";


function GetEmptyTicket($num)
{
	global $currency;
	$j = num;

	$TEMPLATE = file_get_contents(__DIR__.'/Creating-Tickets_template_ticket.html');
	$out = $TEMPLATE;

	$tk['%%TICKETNUM%%'] = $num;
	$tk['%%TICKET_NAME%%'] = 'Ticket Name';
	$tk['%%TOTAL_SOLD%%'] = 0;
	$tk['%%TICKET_MAX%%'] = 0;
	$tk['%%FEE_0_SELECTED%%'] = 'selected';
	$tk['%%FEE_25_SELECTED%%'] = '';
	$tk['%%FEE_50_SELECTED%%'] = '';
	$tk['%%FEE_75_SELECTED%%'] = '';
	$tk['%%FEE_100_SELECTED%%'] = '';
	$tk['%%TAX_INCLUSION_0_SELECTED%%'] = 'selected';
	$tk['%%TAX_INCLUSION_1_SELECTED%%'] = '';
	$tk['%%TAX_INCLUSION_2_SELECTED%%'] = '';
	$tk['%%TICKET_DETAILS%%'] = '';
	$tk['%%TICKET_SINGLE_CHECKED%%'] = 'checked';
	$tk['%%TICKET_BUNDLE_CHECKED%%'] = '';
	$tk['%%TICKET_PAID_CHECKED%%'] = 'checked';
	$tk['%%TICKET_FREE_CHECKED%%'] = '';
	$tk['%%CURRENCY%%'] = $currency;
	$tk['%%TICKET_PRICE_DISPLAY%%'] = '';
	$tk['%%TICKET_PRICE_VAL%%'] = '';
	$tk['%%TICKET_PRICE_DISABLED%%'] = '';
	$tk['%%TICKET_QTY_DISPLAY%%'] = '';
	$tk['%%TICKET_QTY_MIN%%'] = 0;
	$tk['%%TICKET_QTY_VAL%%'] = 0;
	$tk['%%TICKET_QTY_DISABLED%%'] = '';
	$tk['%%TICKET_PRICE_SCRIPT%%'] = "";
	$tk['%%TICKET_BUNDLE_DISPLAY%%'] = 'none';
	$tk['%%TICKETS_PER_BUNDLE_VAL%%'] = 0;
	$tk['%%TICKET_BUNDLES_AVAILABLE_VAL%%'] = 0;
	$tk['%%TICKET_TOTAL_TICKETS_VAL%%'] = 0;
	$tk['%%EDIT_INPUT%%'] = '';
	$tk['%%TICKET_ID%%'] = 0;
	$tk['%%TICKET_MATCH_EVENT_CHECKED%%'] = 'checked';
	$tk['%%TICKET_START_TIME_CHECKED%%'] = '';
	$tk['%%TICKET_START_DISPLAY%%'] = 'none';
	$tk['%%TICKET_START_VAL%%'] = 'NOT SET';
	$tk['%%TICKET_END_MATCH_CHECKED%%'] = 'checked';
	$tk['%%TICKET_END_NEW_CHECKED%%'] = '';
	$tk['%%TICKET_END_DISPLAY%%'] = 'none';
	$tk['%%TICKET_END_VAL%%'] = 'NOT SET';
	$tk['%%TICKET_LIMIT_NO_CHECKED%%'] = 'checked';
	$tk['%%TICKET_LIMIT_YES_CHECKED%%'] = '';
	$tk['%%TICKET_LIMIT_VAL%%'] = '';
	$tk['%%TICKET_LIMIT_DISPLAY%%'] = 'none';

	$tk['%%TICKET_RELEASE_IMMEDIATELY_CHECKED%%'] = 'checked';
	$tk['%%TICKET_RELEASE_SCHEDULED_CHECKED%%'] = '';
	$tk['%%TICKET_RELEASE_START_DISPLAY%%'] = 'none';
	$tk['%%TICKET_RELEASE_START_VAL%%'] = 'NOT SET';
	$tk['%%TICKET_EXPIRE_NONE_CHECKED%%'] = 'checked';
	$tk['%%TICKET_EXPIRE_SCHEDULED_CHECKED%%'] = '';
	$tk['%%TICKET_EXPIRE_START_DISPLAY%%'] = 'none';
	$tk['%%TICKET_EXPIRE_START_VAL%%'] = 'NOT SET';

	$tk['%%CLONE_INPUT%%'] = '';

	//if (isset($_GET['clone']))
	{
		$start_date = $_SESSION['event_data']['event_start_date'][0];
		$end_date = $_SESSION['event_data']['event_end_date'][count($_SESSION['event_data']['event_start_date'])-1];
		$tk['%%CLONE_INPUT%%'] = '<input type="hidden" id="EVENTSTRATDATE" value="' . date('Y/m/d h:i a', strtotime($start_date)) .'"/>';
		$tk['%%CLONE_INPUT%%'] .= '<input type="hidden" id="EVENTENDDATE" value="' . date('Y/m/d h:i a', strtotime(	$end_date)) .'"/>';
	}

	$tk['%%TICKET_PROMO_DISABLED_CHECKED%%'] = "checked";
	$tk['%%TICKET_PROMO_DOLLAR_CHECKED%%'] = "";
	$tk['%%TICKET_PROMO_PERCENTAGE_CHECKED%%'] = "";
	$tk['%%TICKET_PROMO_DISPLAY%%'] = 'none';
	$tk['%%TICKET_PROMO_CODE_NAME%%'] = '';
	$tk['%%TICKET_PROMO_CODE_VAL%%'] = '';

	$tk['%%TICKET_DATES%%'] = "";

	$i=1;
	$check = array();
	foreach ($_SESSION['event_data']['event_start_date'] as $key=>$val)
	{
		$start_date = $_SESSION['event_data']['event_start_date'][$key];
		$end_date = $_SESSION['event_data']['event_end_date'][$key];
		if (date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date)))
		{
			$selctdatae = $start_date.' to '.date('h:i a', strtotime($end_date));
		}
		else
		{
			$selctdatae = $start_date.' to '.$end_date;
		}
		if ($i == 1)
			$checked = 'checked';
		else
			$checked = '';
		$tk['%%TICKET_DATES%%'] .= '<span class="chkbox"><input type="checkbox" value="' . $selctdatae . '" checked name="ticket_type_dates[' . $j . '][]"><span class="checkmark"></span><span class="select-datetype">' . $selctdatae . '</span></span>';
		$i++;
	} // end of each date

//custom
 if (isset($_GET['edit']))
	  {
	  	if (isset($_SESSION['event_edit_data']['dateRanges']))
	  	{
	  		$a=1;
	  		$check = array();
	  		foreach ($_SESSION['event_edit_data']['dateRanges'] as $key=>$v)
	  		{
	  		   // $start_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][$key][0]));
	  			$start_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][0][0]));
	  			$end_date = date('M d, Y h:i a', strtotime($_SESSION["event_edit_data"]["dateRanges"][$key][1]));
	  			if (date("Y-m-d", strtotime($start_date)) == date("Y-m-d", strtotime($end_date)))
	  			{
	  				$selctdatae = $start_date." to ".date("h:i a", strtotime($end_date));
	  			}
	  			else
	  			{
	  				$selctdatae = $start_date." to ".$end_date;
	  			}

	  			foreach ($val->event_dates as $ind=>$tkt_dates)
	  			{
	  				$t_start_date = date('M d, Y h:i a', strtotime($tkt_dates->start_date));
	  				$t_end_date = date('M d, Y h:i a', strtotime($tkt_dates->end_date));
	  				if (date("Y-m-d", strtotime($t_start_date)) == date("Y-m-d", strtotime($t_end_date)))
	  				{
	  					$t_selctdatae = $t_start_date." to ".date("h:i a", strtotime($t_end_date));
	  				}
	  				else
	  				{
	  					$t_selctdatae = $t_start_date." to ".$t_end_date;
	  				}
	  				if ($t_selctdatae == $selctdatae)
	  				{
	  					$check[$a] = "true";
	  				}

	  				/*
				<?php $start_date = $_SESSION['event_edit_data']['event_start_date'][0]; ?>
				<?php $end_date = $_SESSION['event_edit_data']['event_end_date'][count($_SESSION['event_edit_data']['event_start_date'])-1]; ?>
				<input type="hidden" id="EVENTSTRATDATE" value="<?php echo date('Y/m/d h:i a', strtotime($start_date)) ?>"/>
				<input type="hidden" id="EVENTENDDATE" value="<?php echo date('Y/m/d h:i a', strtotime($end_date)) ?>"/>
	  				  */
	  			}  // end of foreach date
	  				$checked = ($check[$a] == "true") ? 'checked' : '';
				$tk['%%TICKET_DATES%%'] .= '<span class="chkbox">  <input type="checkbox" value="' . $selctdatae . '" ' . $checked . ' name="ticket_type_dates[' . $a . '][]"> <span class="checkmark"></span>  ' . $selctdatae . '  </span>';
				$a++;
				
					$tk['%%CLONE_INPUT%%'] = '<input type="hidden" id="EVENTSTRATDATE" value="' . date('Y/m/d h:i a', strtotime($start_date)) .'"/>';
			$tk['%%CLONE_INPUT%%'] .= '<input type="hidden" id="EVENTENDDATE" value="' . date('Y/m/d h:i a', strtotime($end_date)) .'"/>';
	  			
	  		} // end of foreach dateRanges
	  	} //end of if (isset($_SESSION['event_edit_data']['dateRanges']))
	  		  

	  } // end of if(isset($_GET['edit']))

//custom
	foreach($tk as $key => $val)
	{
	  $out = str_replace($key, $val, $out);
	}
	return $out;
}


?>


<script>

   jQuery('#tba_1').keyup( function(e){
var max_chars = jQuery("#tba_1").val();
jQuery("#tkt__order_limit_1").attr("max",max_chars);
});

jQuery('#nota_1').keyup( function(e){
var max_chars = $("#nota_1").val();
jQuery("#tkt_order_limit_1").attr("max",max_chars);
});
//jQuery("#tkt_0").prependTo("#div_ticket");
jQuery(".tkt-details").prependTo("#div_ticket");



</script>