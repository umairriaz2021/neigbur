<?php
/**
 * Custom Functions file for current theme.
 *
 */

// disable admin bar at top of every page
show_admin_bar(false);

// IMPORT PARENT STYLE
function child_theme_enqueue_styles() {
    $parent_style = 'divi-style'; // This is 'divi-style' for the Divi theme.
    if(is_page_template('single-event.php') || is_page_template('header.php')){
    wp_enqueue_style('bootstrap-min-css','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
    }
    elseif(!is_page_template('single-event.php')){
        wp_enqueue_style('bootstrap-min-css',get_stylesheet_directory_uri().'/css/bootstrap.min.css');
    }
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    

    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
     if(is_page_template('single-event.php')){
    wp_enqueue_script('popper-js','https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',array(),false,true);
    wp_enqueue_script('bootstrap-js','https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',array(),false,true);
    wp_enqueue_script('uscript-js',get_stylesheet_directory_uri().'/js/uscript.js',array(),false,true);
    wp_localize_script('uscript-js','myajaxurl',admin_url( 'admin-ajax.php' ));
     }

}
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );

add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)
{
    if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
        $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
        $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));;
        header("Location: $location");
        die;
    }
}
/* Get Files By ID */
function getFileById($fid){
	$token   =  $_SESSION['Api_token'];
	$ch = curl_init(API_URL . '/files/' . $fid);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: ' . $token
	));
	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response);
}
/* Get Event By ID */
function getEventById($eid){
	$ch = curl_init(API_URL . 'events/' . $eid);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json'
	));
	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response);
}

/* function to get convenice fees */
function getConvenienceFees(){
	$ch      = curl_init(API_URL.'convenience-fees');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json'
	));
	$result = curl_exec($ch);
	curl_close($ch);
	$apirespons=json_decode($result);

	if($apirespons->success) {
		$fee = $apirespons->fees;
	}else{
		$fee =array();
	}
	return $fee;
}

function getticketTypes($event_id){
	$token   =  $_SESSION['Api_token'];
	$ch   = curl_init(API_URL . '/ticketTypes?eventId=' . $event_id);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: ' . $token
	));
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result);
}

/*
TT	: TP * Tax Rate
BT	: TP - D + TT
BCF	: F1a + ( TP * F1b )
BCF_Tax : BCF * 0.13
ST-1	: BT + BCF + BCF Tax
ECF	: ST-1 * F2
TCF	: BCF + ECF
ST-2	: TP + TCF
EF2_Tax : TCF * 0.13

CT	: BT +  (TT + EF2 tax) +TCF
OP	: TP
SC	: CT - TP
*/

function cartTickets($event_id){

	$fee = getConvenienceFees();
	$tickets = getticketTypes($event_id);

	/* echo "<pre>"; print_r($fee); die; */

	$finalticket = array();
	foreach($tickets->ticketType as $key=>$tkt){
		// echo "<br/>=====================================<br/>";
		// echo "Ticket name: ".$tkt->name;
		// echo "<br/>=====================================<br/>";
		$_TP = $tkt->price;
		$_D = 0.00;
		$event_tax=0.13;
		// echo "Ticket Price befor tax calculation : ".$_TP;
		// echo "<br/>";

		if(isset($tkt->tax_profile->tax_rate_aggregate)&& $tkt->tax_inclusion != 0){
			$_TT = $_TP * $tkt->tax_profile->tax_rate_aggregate;
			// echo "Ticket Tax rate : ".$tkt->tax_profile->tax_rate_aggregate; echo "<br/>";
		}else{
			$_TT = '0.00';
		}
		// echo "Ticket Tax : ".$_TT; echo "<br/>";

		if($tkt->tax_profile_id==''){
			$_TP = $tkt->price;
		}else{
			if($tkt->tax_inclusion == 0 || $tkt->tax_inclusion == 1)
				$_TP = $tkt->price;

			if($tkt->tax_inclusion == 2 )
				$_TP = $tkt->price -  $_TT;
		}
		// echo "Ticket Price : ".$_TP;
		// echo "<br/>";
		$_BT = $_TP - $_D + $_TT;

		foreach($fee as $frate){
			if(($tkt->price >= $frate->price_range_low)&& ($tkt->price <= $frate->price_range_high || $frate->price_range_high=='')){
				$_F1a = $frate->fee1a;
				$_F1b = $frate->fee1b;
				$_F2 = $frate->fee2;
			}
		}
		// echo "F1a : ".$_F1a; echo "<br/>";
		// echo "F1b : ".$_F1b; echo "<br/>";
		// echo "F2 : ".$_F2; echo "<br/>";

		// echo "Base total : ".$_BT; echo "<br/>";

		$_BCF = $_F1a + ($_F1b * $tkt->price);
		$_BCF =($_BCF>9.5)?9.5:$_BCF;
		// echo "Base convince fee : ".$_BCF."<br/>";

		$_BCF_Tax = $_BCF * $event_tax;
		// echo "Base convince fee TAX: ".$_BCF_Tax."<br/>";

		$_ST_1	= $_BT + $_BCF + $_BCF_Tax;
		// echo "Sub total 1 : ".$_ST_1."<br/>";

		$_ECF	= $_ST_1 * $_F2;
		// echo "Extended convenice fee : ".$_ECF."<br/>";

		$_TCF	= $_BCF + $_ECF;
		// echo "Total convenice fee : ".$_TCF."<br/>";

		$_ST_2	= $_TP + $_TCF;
		// echo "Sub total 2 : ".$_ST_2."<br/>";

		$_EF2_Tax = $_TCF * $event_tax;

		// echo "Extended fee 2 Tax : ".$_EF2_Tax."<br/>";

		// $Buyertotal = $_BT+((1-($tkt->fee_percentage/100))*($_TCF+$_EF2_Tax));
		$Buyertotal_TCF = $_TCF*(1-($tkt->fee_percentage/100));

		// echo "Buy page Total convenice fee : ".$Buyertotal_TCF."<br/>";

		$Buyertotal_Tax =$_TT + ($_EF2_Tax*(1-($tkt->fee_percentage/100)));
		// echo "Buy page Total TAX : ".$Buyertotal_Tax."<br/>";

		$tkt->TP = $_TP;
		$tkt->TCF = $Buyertotal_TCF;

		$tkt->Ttax = $Buyertotal_Tax;
		$finalticket[$key]= $tkt;
	}
	// die;
	 /*  echo "<pre>"; print_r($finalticket); die; */
	return $finalticket;

}

function format_dates($start, $end)
{
  $res = "";

	$estart = strtotime($start);
	$eend = strtotime($end);

	if (date('Y-m-d',$estart) == date('Y-m-d',$eend))
	{
		$res = date('l F j, Y', $estart) . '<br/>' . date('g:ia', $estart) . ' to ' . date('g:ia', $eend);
	}
	else
	{
		$res = date('l F j, Y g:ia',$estart) . '<br/>to ' . date('l F j, Y g:ia', $eend);
	}

  return $res;
}

function get_time_in_prov($provCode)
{
  $dt = new DateTime('America/Toronto');

  if ($provCode == 'BC')
  {
    // Pacific
    $dt = new DateTime('America/Vancouver');
  }
  else if ($provCode == 'AB' || $provCode == 'SK' || $provCode == 'NT' || $provCode == 'YK')
  {
    // Mountain
    $dt = new DateTime('America/Edmonton');
  }
  else if ($provCode == 'MB')
  {
    // Central
    $dt = new DateTime('America/Winnipeg');
  }
  else if ($provCode == 'ON' || $provCode == 'QC' || $provCode == 'NU')
  {
    // Eastern
    $dt = new DateTime('America/Toronto');
  }
  else if ($provCode == 'NB' || $provCode == 'NS' || $provCode == 'PE')
  {
    // Atlantic
    $dt = new DateTime('America/Halifax');
  }
  else if ($provCode == 'NL')
  {
    // Newfoundland
    $dt = new DateTime('America/St_Johns');
  }

  return strtotime($dt->format('M d, Y H:i:s'));
}

function share_meta_tags() {
	global $wpdb;
	$token   =  $_SESSION['Api_token'];
	$url = $_SERVER['REQUEST_URI'];
	$event = explode('/', $url);
	$event_id = $event[2];
	$siteUrl = site_url().$url;
	if (isset($event_id) && $event_id != '') {

	   $ch   = curl_init(API_URL . '/events/' . $event_id);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  'Content-Type: application/json',
		  'Authorization: ' . $token
	   ));
	   $result = curl_exec($ch);
	   curl_close($ch);
	   $apirespons = json_decode($result);

	   if ($apirespons->success) {

		  $event_detail = $apirespons->event;
	   }
	}
	$title = isset($event_detail) ? $event_detail->name : '';
	$description = isset($event_detail) ? $event_detail->description : '';
	$image ="https://storage.googleapis.com/".$event_detail->files[0]->bucket."/".$event_detail->files[0]->filename;
	echo '<meta property="og:type" content="article" />';
	echo '<meta property="og:title" content="'.$title.'"/>';
	echo '<meta property="og:image" content="'.$image.'"/>';
	echo '<meta property="og:description" content="'.$description.'"/>';
	echo '<meta property="og:url" content="'.$siteUrl.'"/>';
}
add_action('wp_head', 'share_meta_tags');

function get_email_form_data(){
global $wpdb;

require_once get_stylesheet_directory().'/inc/ajaxcode.php';

wp_die();
    
}

add_action('wp_ajax_mylibrary','get_email_form_data');

?>
