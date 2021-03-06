<?php
/* Template Name: Create Tickets 2021 */

/*

Different session data:

$_SESSION['edit_event_data']  - Set after editing the first step of an existing event
$_SESSION['ticket_data']      - Set after completing the tickets step (this page) but pressing back to get back here
$_SESSION['event_data']       - The POST data from step 1 for the event (might need to be overriden by edit_event_data)


*/
function GetEmptyTicket($num){
    
}
// function GetEmptyTicket($num)
// {

// 	$j = num;

  
// 	$TEMPLATE = file_get_contents(get_stylesheet_directory().'/Creating-Tickets_template_ticket.html');
// 	$out = $TEMPLATE;
//     $j =1;
   

// 	$i=1;
// 	$check = array();
// 	foreach ($_SESSION['event_data']['event_start_date'] as $key=>$val)
// 	{
// 		$start_date = $_SESSION['event_data']['event_start_date'][$key];
// 		$end_date = $_SESSION['event_data']['event_end_date'][$key];
// 		if (date('Y-m-d', strtotime($start_date)) == date('Y-m-d', strtotime($end_date)))
// 		{
// 			$selctdatae = $start_date.' to '.date('h:i a', strtotime($end_date));
// 		}
// 		else
// 		{
// 			$selctdatae = $start_date.' to '.$end_date;
// 		}
// 		if ($i == 1)
// 			$checked = 'checked';
// 		else
// 			$checked = '';
// 		$tk['%%TICKET_DATES%%'] .= '<span class="chkbox"><input type="checkbox" value="' . $selctdatae . '" checked name="ticket_type_dates[' . $j . '][]"><span class="checkmark"></span><span class="select-datetype">' . $selctdatae . '</span></span>';
// 		$i++;
// 	} // end of each date

// 	foreach($tk as $key => $val)
// 	{
// 	  $out = str_replace($key, $val, $out);
// 	}
// 	return $out;
// }


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
$t['%%TICKETS%%'] .= GetEmptyTicket(1);	// now add the editable ticket
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
}

if (isset($_SESSION['ticket_data']))
    

{
   
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

  $t['%%TICKETS%%'] = "";
	$j=1;
	foreach ($_SESSION['ticket_data']['ticket_name'] as $key=>$val)
	{
		// %%TICKETS%%

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

//   $t['%%TICKETS%%'] = GetEmptyTicket(0);		// first add a "clean" template to use
// $t['%%TICKETS%%'] = str_replace('id="tkt_0"', 'id="tkt_0" style="display: none;"', $t['%%TICKETS%%']);	// hide it
// $t['%%TICKETS%%'] .= GetEmptyTicket(1);	// now add the editable ticket
// $t['%%ADD_NEW%%'] = '<p class="add-new" style="cursor:pointer;" id="add_more_tkt"><i class="fa fa-plus"></i> Add New Ticket Type</p>';
	$j=1;
  $tk = array();
	foreach ($tickets as $key=>$val)
	{
  
		 
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

		if (isset($_GET['clone']))
		{
			$start_date = $_SESSION['event_data']['event_start_date'][0];
			$end_date = $_SESSION['event_data']['event_end_date'][count($_SESSION['event_data']['event_start_date'])-1];
			$tk['%%CLONE_INPUT%%'] = '<input type="hidden" id="EVENTSTRATDATE" value="' . date('Y/m/d h:i a', strtotime($start_date)) .'"/>\n';
			$tk['%%CLONE_INPUT%%'] .= '<input type="hidden" id="EVENTENDDATE" value="' . date('Y/m/d h:i a', strtotime($end_date)) .'"/>\n';
		}

		$tk['%%TICKET_PROMO_DISABLED_CHECKED%%'] = !isset($val->ticketPromo)? "checked" : "";
		$tk['%%TICKET_PROMO_DOLLAR_CHECKED%%'] =  $val->ticketPromo->metric = 'dollar' ? "checked" : "";
		$tk['%%TICKET_PROMO_PERCENTAGE_CHECKED%%'] =  $val->ticketPromo->metric = 'percentage' ? "checked" : "";
		$tk['%%TICKET_PROMO_DISPLAY%%'] = isset($val->ticketPromo) ? 'block' : 'none';
		$tk['%%TICKET_PROMO_CODE_NAME%%'] = isset($val->ticketPromo) ? $val->ticketPromo->code : '';
		$tk['%%TICKET_PROMO_CODE_VAL%%'] = isset($val->ticketPromo) ? $val->ticketPromo->value : '';

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
	  		} // end of foreach dateRanges
	  	} //end of if (isset($_SESSION['event_edit_data']['dateRanges']))
	  } // end of if(isset($_GET['edit']))

    $j++;

	} // end of foreach tickets

	$t['%%ADD_NEW%%'] = '';
	if ((isset($event_state) && $event_state == 'upcoming') || isset($_GET['clone']))
	{
		$t['%%ADD_NEW%%'] = '<p class="add-new" style="cursor:pointer;" id="add_more_tkt"><i class="fa fa-plus"></i> Add New Ticket Type</p>';
	}
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
</script>