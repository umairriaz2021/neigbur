<?php
session_start();
require('../../../../wp-config.php');

use Dompdf\Dompdf;
use Dompdf\Options;

require_once "../dompdf/autoload.inc.php";

$token   =  $_SESSION['Api_token'];

echo "Posted data _POST=\n";
echo print_r($_POST);

if (isset($_POST['promocode']) && $_POST['promocode']!='') {
    /* promocode handle code goes here */
}

if (isset($_POST['charity']) && $_POST['charity']==1) {
    /* charity handle code goes here */
}


/* if(isset($_POST['remembercard']) && $_POST['remembercard']==1){ */

$exp = explode('/', $_POST['cardExpiry']);
$pdata = array(
            'card'=>array('name'=> $_POST['cardOwnername'],'number'=> str_replace(' ', '', $_POST['cardNumber']), 'expiry_month'=> $exp[0], 'expiry_year'=> $exp[1], 'cvd'=> $_POST['cardCVC']),
            'billing'=>array('name'=>$_POST['cardOwnername'],'address_line1'=>$_SESSION['userdata']->address,'city'=>$_SESSION['userdata']->city,'postal_code'=>$_SESSION['userdata']->postalcode,'email_address'=>$_SESSION['userdata']->email,'phone_number'=>$_SESSION['userdata']->number)
            );

$payload = json_encode($pdata);

$ch   = curl_init(API_URL . '/users/addCard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);

$response=json_decode($result);

/* }  */

$base_cost=$fees=$subtotal=$taxes=$total=$tqty=0;

/* creating ticket order order start */
if (isset($_POST['tickets']) && count($_POST['tickets'])>0) {
    foreach ($_POST['tickets'] as $tkt) {
        if ($tkt['tqty']>0) {
            $base_cost += ($tkt['tprice']*$tkt['tqty']);
            $fees     += ($tkt['tfee']*$tkt['tqty']);
            $subtotal  += ($tkt['tprice']*$tkt['tqty']);
            $taxes  += ($tkt['ttax']*$tkt['tqty']);
            $total	+= (($tkt['tprice']+$tkt['ttax']+$tkt['tfee'])*$tkt['tqty']);
            $tqty	+= ($tkt['tqty']);
        }
    }
}

$fields = array(
            "base_cost" => number_format($base_cost, 2),
            "fees"      => number_format($fees, 2),
            "subtotal"  => number_format($subtotal, 2),
            "taxes"     => number_format($taxes, 2),
            "total" 	=> number_format($total, 2),
            "quantity" 	=> $tkt['tqty'],
            "bambora_transaction_id" 	=> $_POST['bambora_transaction_id'],
            "drupal_user_id" 	=> $_SESSION['userdata']->id
            );

echo "Create ticket order of cart tickets\n";
echo $payload = json_encode($fields);

$ch   = curl_init(API_URL . '/orders/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);

$orderResponse=json_decode($result);

echo "\norderResponse:\n"; print_r($orderResponse);

/* creating ticket order order ends */


if ($orderResponse->success) {
    $th=[];
    $tc=-1;
    if (isset($_POST['tktholder']) && count($_POST['tktholder'])>0) {
        foreach ($_POST['tktholder'] as $tkid=>$tholder) {
            foreach ($tholder as $cust) {
                $tc++;    /* Ticket counter increement */
                $firstname=($cust['firstname']!='')?$cust['firstname']:$_SESSION['userdata']->first;
                $lastname=($cust['lastname']!='')?$cust['lastname']:$_SESSION['userdata']->last;
                $customeremail=($cust['customeremail']!='')?$cust['customeremail']:$_SESSION['userdata']->email;

                $th[$tc]['ticket_type_id']=$tkid;
                $th[$tc]['firstname']=$firstname;
                $th[$tc]['lastname']=$lastname;
                $th[$tc]['customeremail']=$customeremail;
            }
        }
    }
}

echo "\nth:\n"; print_r($th);

foreach ($orderResponse->tickets as $key=>$orderResTicket) {
    if ($orderResponse->tickets[$key]->ticket_type_id == $th[$key]['ticket_type_id']) {
        $fields = array(
                    "firstname"=> $th[$key]['firstname'],
                    "lastname"=> $th[$key]['lastname'],
                    "email"=> $th[$key]['customeremail'],
                    "event_id"=> $orderResponse->tickets[$key]->event_id,
                    "ticket_type_id"=> $th[$key]['ticket_type_id']
                );

        echo "\nfields:\n";
        print_r($fields);
        echo $payload = json_encode($fields);

        $ch   = curl_init(API_URL . '/tickets/'.$orderResponse->tickets[$key]->id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($payload),
        'Authorization: ' . $token
        ));
        $result = curl_exec($ch);
        curl_close($ch);

        $response=json_decode($result);

        echo "\nresponse:\n";
        print_r($response);
    }
}


/******************************************/
/********Neighbur - Ticket Purchase***********/
/******************************************/


/******************************/
/*****Recipient emails loop***********/
/******************************/

/* $Clikhere_url='<a style="color:#3274b6;font-weight:600;text-decoration:none;" href="'.site_url().'?page_id=187&user='.base64_encode($apirespons->user->email).'&fname='.base64_encode($apirespons->user->first).'">clicking here</a>'; */
$eventId = 0;
$to="";
if ($orderResponse->success)
{
    global $wpdb;
    $to = $_SESSION['userdata']->email;
    $user_name = ucfirst($_SESSION['userdata']->first.' '.$_SESSION['userdata']->last);

    $eventId = $orderResponse->order->event_id;
    $evntinfo = getEventById($orderResponse->order->event_id);
    $event = $evntinfo->event;
    $country = $wpdb->get_row("Select * from wp_countries where  id = $event->country_id");
    $state = $wpdb->get_row("select * from wp_states where id = $event->province_id");

    $eventaddress='';
    //$eventaddress.=$event->address1.'<br/>';
    $eventaddress.=$event->address2.'<br/>';
    $eventaddress.=$event->city.', ' . $state->state_code. ', ' .$country->name.'<br/>';
    $eventaddress.=$event->postalcode.'<br/>';

    $event_image_src='';

    if (isset($event) && count($event->files) > 0 && $event->files[0]->type == 'image') {
        $event_image_src="https://storage.googleapis.com/".$event->files[0]->bucket."/".$event->files[0]->filename;
    } else {
        $event_image_src = site_url().'/wp-content/uploads/2019/08/r1.jpg';
    }

    $eventdate='';
    foreach ($event->event_dates as $edate)
		{
			$estart = strtotime($edate->start_date);
			$eend = strtotime($edate->end_date);
			if (date('Y-m-d',$estart) == date('Y-m-d',$eend))
			{
        $eventdate .= '<b class="p-date">'.date('l F j, Y g:ia', $estart).' to '.date('g:ia', $eend).'</b><br/>';
			}
			else
			{
        $eventdate .= '<b class="p-date">'.date('l F j, Y g:ia', $estart).' to '.date('l F j, Y g:ia', $eend).'</b><br/>';
      }
    }
    echo "purchaser email emails\n";
    $ticPurchase   = get_post(2466);
    //$ticPurchase = str_replace('[[', '[', $ticPurchase);
    //$ticPurchase = str_replace(']]', ']', $ticPurchase);
    $ticPurchaseOutput =  apply_filters('the_content', $ticPurchase->post_content);
    $eventURL = site_url() . "/view-event/" . $eventId;
    $eventURL = "<a href='$eventURL'>$eventURL</a>";
    echo "1\n";
    $ticPurchaseemailContent= str_replace(
        array(
                            '[user_name]',
                            'https://webdev.snapd.com/wp-content/uploads/2020/11/sample_image-300x168.jpg',
                            '[event_name]',
                            '[event_time]',
                            '[event_venu]',
                            '[event_address]',
                            '[purchase_date]',
                            '[order_id]',
                            '[order_qnty]',
                            '[order_total]',
                            '[event_URL]'
                        ),
        array(
                            $user_name,
                            $event_image_src,
                            $event->name,
                            $eventdate,
                            $event->location,
                            $eventaddress,
                            date('l F j, Y g:ia', strtotime($orderResponse->order->create_date)),
                            $orderResponse->order->id,
                            $tqty,
                            $orderResponse->order->total,
                            $eventURL
                        ),
        $ticPurchaseOutput
    );
    echo "2\n";
    $subject = $ticPurchase->post_title;
    $message = '<DOCTYPE! html>
	                        <html>
	                            <head>
	                                <title>
	                                    ' . $subject . '
	                                </title>
	                            </head>
	                            <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
	                                <div class="container" style="margin:0 auto;max-width:1080px;">
	                                    <a href="#"><img src="'.site_url().'/wp-content/themes/Divi Child/img/neighbur_logo.png"></a>
	                                    <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
	                                    '.$ticPurchaseemailContent.'
	                              </div>
	                            </body>
	                        </html>
	                    </DOCTYPE>';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Neightbur Inc.;<no-reply@snapd.com>' . "\r\n";
    echo "3\n";

    //echo wp_mail($to, $subject, $message, $headers);

		file_get_contents('https://webdev.snapd.com/wp-content/themes/Divi%20Child/ajax/pdf_test.php?oid='. $orderResponse->order->id);
		$attachment = array('../ticketsqrcode/neighbur_tix_' . md5($orderResponse->order->id . '|H|10') . '.pdf');
    wp_mail($to, $subject, $message, $headers, $attachment);



    foreach ($orderResponse->tickets as $key=>$orderResTicket)
		{
        echo "recipient emails";

        if ($orderResponse->tickets[$key]->ticket_type_id == $th[$key]['ticket_type_id'])
				{
            $recipient_name =$th[$key]['firstname'].' '.$th[$key]['lastname'];
            $torecemail = $th[$key]['customeremail'];
            $qty = $th[$key]['qty'];

            $ticRecient   = get_post(2468);
            //$ticRecient = str_replace('[[', '[', $ticRecient);
            //$ticRecient = str_replace(']]', ']', $ticRecient);

            $ticRecientOutput =  apply_filters('the_content', $ticRecient->post_content);


            $ticRecientemailContent= str_replace(
                array(
                                    '[recipient_name]',
                                    '[user_name]',
                                    '[event_image_src]',
                                    '[event_name]',
                                    '[event_time]',
                                    '[event_venu]',
                                    '[event_address]',
                                    '[purchase_date]',
                                    '[order_id]',
                                    '[order_qnty]',
                                    '[order_total]',
                                    '[event_URL]'
                                ),
                array(
                                    $recipient_name,
                                    $user_name,
                                    $event_image_src,
                                    $event->name,
                                    $eventdate,
                                    $event->location,
                                    $eventaddress,
                                    date('D M, Y H:i A', strtotime($orderResponse->order->create_date)),
                                    $orderResponse->order->id,
                                    $qty,
                                    $orderResponse->order->total,
                                    $eventURL
                                ),
                $ticRecientOutput
            );

            $subject = $ticRecient->post_title;
            $message = '<DOCTYPE! html>
			                        <html>
			                            <head>
			                                <title>
			                                    ' . $subject . '
			                                </title>
			                            </head>
			                            <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
			                                <div class="container" style="margin:0 auto;max-width:1080px;">
			                                    <a href="#"><img src="'.site_url().'/wp-content/themes/Divi Child/img/neighbur_logo.png"></a>
			                                    <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
			                                    '.$ticRecientemailContent.'
			                              </div>
			                            </body>
			                        </html>
			                    </DOCTYPE>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Neighbur Inc.;<no-reply@snapd.com>' . "\r\n";
            if ($torecemail != $to) {
                echo wp_mail($torecemail, $subject, $message, $headers);
                // $attachment = array(WP_CONTENT_DIR . '/uploads/image.png');
                // wp_mail($torecemail, $subject, $message, $headers, $attachment);
            }
        }
    }
}
