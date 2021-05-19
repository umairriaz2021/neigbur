<?php
require_once( ABSPATH.'wp-load.php' );
if($_REQUEST['param'] == "get_email_data"){
    //print_r($_REQUEST);die;
  $email = sanitize_text_field($_REQUEST['email']);
  $subject = sanitize_text_field($_REQUEST['subject']);
  $email_body = $_REQUEST['e_body'];
   

  $to = $email;
  $subject = $subject;
  $body = $email_body;

  //$body = $email_body;
  //$body = "<p><h2>Hello World</h2></p>";

  
  $headers = array('Content-Type: text/html; charset=UTF-8');
  if (wp_mail($to, $subject, $email_body,$headers)) {
      echo json_encode(array('status'=>200,'message'=>'Message Sent Successfully'));
      
    }
     
    else{
      echo json_encode(array('status'=>400,'message'=>'Something Wrong Please Check All Fields'));
    }
   
}