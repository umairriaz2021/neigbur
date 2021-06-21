<?php

require('../../../../wp-config.php');

//set it to writable location, a place for ticketsqrcode generated PNG files
$PNG_TEMP_DIR = '../ticketsqrcode/';
include "../phpqrcode/qrlib.php";
//ofcourse we need rights to create ticketsqrcode dir
if (!file_exists($PNG_TEMP_DIR))  mkdir($PNG_TEMP_DIR);

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

use Dompdf\Dompdf;
use Dompdf\Options;
require_once "../dompdf/autoload.inc.php";

$orderid = "34";
if (isset($_GET['oid']))
	$orderid = $_GET['oid'];

// get the order info
$ch = curl_init(API_URL . 'orders/' . $orderid);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjozNjAsImlhdCI6MTYwNjkzNTYwNX0.ubpAKAfh76Ly4IJ4A2QFLh_ossHMo0rnIxiYZVCQMvM'
));
$response = curl_exec($ch);
curl_close($ch);
$res = json_decode($response);

$order = $res->ticketOrder;

$event_id = $order->event_id;

// get the full event info
$ch = curl_init(API_URL . 'events/' . $event_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json'
));
$response = curl_exec($ch);
curl_close($ch);
$res = json_decode($response);
$event = $res->event;


$event_location = '';
$event_location .= $order->event->location . '<br/>';
$event_location .= $order->event->address2 . '<br/>';
$event_location .= $order->event->city . ', '.$event->province->province_code . ' ' . $event->country->country_name . '<br/>';
$event_location .= $order->event->postalcode .'<br/>';

$event_name = $order->event->name;

$event_image = '';
if (isset($event) && count($event->files) > 0 && $event->files[0]->type == 'image')
{
		$event_image = "https://storage.googleapis.com/" . $event->files[0]->bucket . "/" . $event->files[0]->filename;
}
else
{
		$event_image = site_url().'/wp-content/uploads/2019/08/r1.jpg';
}

$order_id = $order->id;
$ticket_purchase_date = date('l F j, Y', strtotime($order->create_date)) . ' at ' . date('g:ia', strtotime($order->create_date)); //'Wednesday November 23, 2020 at 11:23am';
$ticket_purchaser = $order->user->first . ' ' . $order->user->last;

$ticket_name = array();
$ticket_price = array();
$ticket_id = array();
$ticket_holder = array();
$ticket_time = array();
$ticket_email = array();
$tickets = 0;

// get all the ticket data
for ($toi = 0; $toi < count($order->ticket_order_item); $toi++)
{
	$typename = $order->ticket_order_item[$toi]->ticket_type->name;
	$typeprice = 'Free';
	if ($order->ticket_order_item[$toi]->ticket_type->price > 0)
	{
		$typeprice = '$' . $order->ticket_order_item[$toi]->ticket_type->price;
	}
	$estart = strtotime($order->ticket_order_item[$toi]->ticket_type->start);
	$eend = strtotime($order->ticket_order_item[$toi]->ticket_type->end);
	//date('D M, Y H:i A', strtotime($orderResponse->order->create_date))
	if (date('Y-m-d',$estart) == date('Y-m-d',$eend))
	{
		$typetime = date('l F j, Y', $estart) . '<br/>' . date('g:ia', $estart) . ' to ' . date('g:ia', $eend);
	}
	else
	{
		$typetime = date('l F j, Y g:ia',$estart) . '<br/>to<br/>' . date('l F j, Y g:ia', $eend);
	}

	for ($toit = 0; $toit < count($order->ticket_order_item[$toi]->tickets); $toit++)
	{
		$ticket_name[] = $typename;
		$ticket_price[] = $typeprice;
		$uuid = $order->ticket_order_item[$toi]->tickets[$toit]->uuid;
		$ticket_id[] = $uuid;

		// generate QR Code
		$filename = $PNG_TEMP_DIR.'ticket'.md5($uuid.'|H|10').'.png';
		QRcode::png($uuid, $filename, 'H', 10, 2);

		$ticket_holder[] = $order->ticket_order_item[$toi]->tickets[$toit]->firstname . ' ' . $order->ticket_order_item[$toi]->tickets[$toit]->lastname;
		$ticket_email[] = $order->ticket_order_item[$toi]->tickets[$toit]->email;
		$ticket_time[] = $typetime;
		$tickets++;
	}
}

$contentPage1 = get_post(2547);
$contentPage1 =  apply_filters( 'the_content', $contentPage1->post_content );

$contentPage2 = get_post(2549);
$contentPage2 =  apply_filters( 'the_content', $contentPage2->post_content );

$contentTicket = get_post(2539);
$contentTicket =  apply_filters( 'the_content', $contentTicket->post_content );

$contentTickets = array();

for ($i=0; $i < $tickets; $i++)
{
	$qr_url = site_url() . "/wp-content/themes/Divi Child/ticketsqrcode/";
	$qr_url .= 'ticket' . md5($ticket_id[$i].'|H|10') . '.png';

	$contentTickets[] = str_replace(
		array(
			'[event_name]',
			'[ticket_name]',
			'[ticket_price]',
			'[ticket_id]',
			'[order_id]',
			'[ticket_purchase_date]',
			'[ticket_purchaser]',
			'[ticket_holder]',
			'[ticket_time]',
			'[event_location]',
		//	'https://webdev.snapd.com/wp-content/uploads/2020/11/ticket_qr_sample-300x300.png',
		//	'https://webdev.snapd.com/wp-content/uploads/2020/11/sample_image-300x168.jpg'
		'[qr_code]',
		'[event_image]'
		),
		array(
			$event_name,
			$ticket_name[$i],
			$ticket_price[$i],
			$ticket_id[$i],
			$order_id,
			$ticket_purchase_date,
			$ticket_purchaser,
			$ticket_holder[$i],
			$ticket_time[$i],
			$event_location,
			$qr_url,
			$event_image
		),
		$contentTicket);
}

$contentTickets[] = "";
$contentTickets[] = "";
$contentTickets[] = "";

$contentPage1 = str_replace(
		array(
			'[event_name]',
			'[order_id]',
			'[ticket_time]',
			'[ticket_purchase_date]',
			'[ticket_purchaser]',
			'[event_location]'
		),
		array(
			$event_name,
			$order_id,
			$ticket_time[0],
			$ticket_purchase_date,
			$ticket_purchaser,
			$event_location
		),
		$contentPage1);

$contentPage1 = str_replace(
		array(
			'[ticket1]',
			'[ticket2]'
		),
		array(
			$contentTickets[0],
			$contentTickets[1]
		),
		$contentPage1);

if ($tickets == 1)
{
	$contentPage1 = str_replace('<td id="ticket2" style="vertical-align: top; width: 49%; border: 1px', '<td id="ticket2" style="vertical-align: top; width: 49%; border: 0px', $contentPage1);
}

$processed = 2;
$left = $tickets - 2;
$extraPages = '';
while ($left > 0)
{
	$page = str_replace(
			array(
				'[ticket1]',
				'[ticket2]',
				'[ticket3]',
				'[ticket4]'
			),
			array(
				$contentTickets[$processed++],
				$contentTickets[$processed++],
				$contentTickets[$processed++],
				$contentTickets[$processed++]
			),
			$contentPage2);
	if ($left < 2)
	{
		$page = str_replace('<td id="ticket2" style="vertical-align: top; width: 49%; border: 1px', '<td id="ticket2" style="vertical-align: top; width: 49%; border: 0px', $page);
	}
	if ($left < 3)
	{
		$page = str_replace('<td id="ticket3" style="vertical-align: top; width: 49%; border: 1px', '<td id="ticket3" style="vertical-align: top; width: 49%; border: 0px', $page);
		$page = str_replace('background-size: 50px', 'background-size: 0px', $page);
	}
	if ($left < 4)
		$page = str_replace('<td id="ticket4" style="vertical-align: top; width: 49%; border: 1px', '<td id="ticket4" style="vertical-align: top; width: 49%; border: 0px', $page);
	$extraPages .= '
			<div class="container">
					<img src="'.site_url().'/wp-content/themes/Divi Child/img/neighbur_logo.png" style="max-height:50px;"><br><br>
					'. $page . '
			</div>';
	$left -= 4;
}



$content = '<DOCTYPE! html>
	                        <html>
	                            <head>
	                                <title>
	                                    Neighbur Tickets
	                                </title>
																	<style>
																	body {
																		font-family: DejaVu Sans;
																		font-size: 14px;
																		color:black;
																	}
																	.container {
    																page-break-after: always;
																		margin:0 auto;
																		max-width:1080px;
																		width: 100%;
																		height: 100%;
																	}
																	.container:last-child {
    																page-break-after: avoid;
																	}
																	</style>
	                            </head>
	                            <body>
	                                <div class="container">
	                                    <img src="'.site_url().'/wp-content/themes/Divi Child/img/neighbur_logo.png" style="max-height:50px;">
	                                    <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
	                                    '. $contentPage1 . '
	                              	</div>
																	' . $extraPages . '
	                            </body>
	                        </html>
	                    </DOCTYPE>';


//set_include_path(get_include_path() . PATH_SEPARATOR . "/wp-content/themes/Divi Chil/dompdf");

if (isset($_GET['html']))
{
	echo $content;
}
else
{
	$options = new Options();
	$options->set('isRemoteEnabled', TRUE);
	$dompdf = new Dompdf($options);
	$dompdf->setPaper('A4', 'portrait');
	$context = stream_context_create([
		'ssl' => [
			'verify_peer' => FALSE,
			'verify_peer_name' => FALSE,
			'allow_self_signed'=> TRUE
		]
	]);
	$dompdf->setHttpContext($context);

	$dompdf->loadHtml($content);
	$dompdf->render();

	// DISPLAY IN BROWSER
	//$dompdf->stream("neighbur_tix.pdf", array("Attachment" => false));

	// DOWNLOAD IN BROWSER
	//$dompdf->stream("neighbur_tix.pdf");

	// SAVE TO FILE ON SERVER
	$output = $dompdf->output();
	$filename = $PNG_TEMP_DIR.'neighbur_tix_'.md5($orderid.'|H|10').'.pdf';
	file_put_contents($filename, $output);
}



?>
