<?php 
/* Template Name: custom redirect page */ 


$email = $_GET['email'];
$password = $_GET['password'];
$redirect = $_GET['redirect'];

// https://localhost/neighbur-web/redirect-page/?email=4krutikparikh@gmail.com&password=Krutik1998&redirect=create-event


if(($email !== "") && ($password !== "") && ($redirect !== "") ) {
    $curl = curl_init();

    $data    = array(
        'email' => $email,
        'password' => $password
    );

    $payload = json_encode($data);

    curl_setopt_array($curl, array(
      CURLOPT_URL => API_URL.'users/login',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $payload,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);

    $apiresponse = json_decode($response);

    $ShowError = 'Redirecting User...'.$email;

    if($apiresponse->success){
		$_SESSION['Api_token']  =   $apiresponse->token;
		$_SESSION['userdata']  =   $apiresponse->user;		
        
        if(isset($_POST['SignInUser']) && $_POST['remember']=='on' ){ 
            setcookie("user_cookie_sn_un",$email,time()+31556926, "/" );
            setcookie("user_cookie_sn_pw",$password,time()+31556926, "/" );
         }
         wp_redirect(site_url().'/'.$redirect);

		exit;
	}else{
		if($apiresponse->error == 'User has not confirmed email'){
		$ShowError = 'Previous sign-up authentication not completed. <a href="#">Please resend.</a>';
		}else{
		$ShowError = $apiresponse->error;   
		}
		
	}
}
?>

    <div id="main-content">   
        <div class="outer-wrapper ">
            <div class="container container-home">
                <?php echo $ShowError?>
                <?php echo $password?>

            </div>
        </div>
    </div>


    <?php  ?>