<?php
//get Beanstream Gateway
require_once 'Gateway.php';
//init api settings (beanstream dashboard > administration > account settings > order settings)
//$merchant_id = '225813712'; //INSERT MERCHANT ID (must be a 9 digit string)
//$api_key = '568c8703A4B24b839917fEE66dc4a405'; //INSERT API ACCESS PASSCODE
$merchant_id = '300207489'; //INSERT MERCHANT ID (must be a 9 digit string)
$api_key = '3DF2EE77B4b142cBb95a11565243026b'; //INSERT API ACCESS PASSCODE


$api_version = 'v1'; //default
$platform = 'api'; //default (or use 'tls12-api' for the TLS 1.2-Only endpoint)
//init new Beanstream Gateway object
$beanstream = new \Beanstream\Gateway($merchant_id, $api_key, $platform, $api_version);

$order_number = time();
$amount = $_POST['totalamount'];
$exp=explode('/',$_POST['cardExpiry']);

$payment_data = array(
        'order_number' => $order_number,
        'amount' => $amount,
        'payment_method' => 'card',
        'card' => array(
            'name' => $_POST['cardOwnername'],
            'number' => str_replace(' ','',$_POST['cardNumber']),
            'expiry_month' => $exp[0],
            'expiry_year' => $exp[1],
            'cvd' => $_POST['cardCVC']
        )/* ,
	    'billing' => array(
	        'name' => 'Mr. John Doe',
	        'email_address' => 'johndoe@email.com',
	        'phone_number' => '1234567890',
	        'address_line1' => 'Main St.',
	        'city' => 'Anytown',
	        'province' => 'BC',
	        'postal_code' => 'V8J9I5',
	        'country' => 'CA'
		),
	    'shipping' => array(
	        'name' => 'Shipping Name',
	        'email_address' => 'email@email.com',
	        'phone_number' => '1234567890',
	        'address_line1' => '789-123 Shipping St.',
	        'city' => 'Shippingsville',
	        'province' => 'BC',
	        'postal_code' => 'V8J9I5',
	        'country' => 'CA'
		) */
);

$complete = TRUE;


$response=[];
try
{
	$result = $beanstream->payments()->makeCardPayment($payment_data, $complete);
	/* is_null($result)?:print_r($result); */
	$response['success']=true;
	$response['message']=$result['message'];
	$response['data']=$result;
}
catch (\Beanstream\Exception $e)
{
   $result = (array) $e;
	$response['success']=false;
	$response['message']=$result["\0*\0" . '_message'];
	$response['data']=$result;
   if ($e->_code == 221 || $e->_code == 7)
   {
      $response['message'] = "The transaction was declined. Please check the info and try again.";
   }
   else if ($e->_code == 314)
   {
      $response['message'] = "Missing or invalid payment information. Please check and try again.";
   }
   else
   {
      $response['message'] = "An error occurred while processing your transaction. Please try again.";
   }

}

echo json_encode($response);
?>
