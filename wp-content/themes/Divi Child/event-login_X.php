<?php 
/*
Template Name: Event login Page
*/

if(isset($_POST['SignInUser'])) {
	
 	extract($_POST);
	$data = array(
			'email' => $uemail,
			'password' => $psw
		);	 
	$payload = json_encode($data);

	$ch = curl_init(API_URL.'users/login');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($payload))
	);
	$result = curl_exec($ch);
	curl_close($ch);
	
	$apirespons=json_decode($result);
	
	/* echo "<pre>".API_URL; print_r($apirespons); die;  */

	if($apirespons->success){
		$form_errors=array();
		if (filter_var($uemail, FILTER_VALIDATE_EMAIL)) { //Invalid Email
			$user = get_user_by('email',$uemail);
		} else {
			$user = get_user_by('login',$uemail);
		}
		if(!$user) {		
			$form_errors['userErr']='Invalid username';	
		} 	
		if(!wp_check_password($psw, $user->user_pass, $user->ID)) {	
			$form_errors['passErr']='Incorrect password';	
		} 	
				
		if(empty($form_errors)) { 	
			wp_setcookie($user->data->user_login, $user->data->user_pass, true);	
			wp_set_current_user($user->ID, $user->data->user_login);

			$_SESSION['Api_token']  =   $apirespons->token;
			wp_redirect(site_url().'/my-account'); 
			exit;			
		}
	}else{
		/* $errorres = explode(' - ',$apirespons->error);
		$form_errors = json_decode($errorres[1]); */
		$form_errors = $apirespons->error;

	}
}	
/* 
 if(is_user_logged_in()){
	$mylink = $wpdb->get_row( "SELECT * FROM wp_users WHERE ID = '".$_SESSION['uid']."'" );
	echo $ustatus = $mylink->user_status;
	if($ustatus==1){
		$user_id = get_current_user_id();
		wp_redirect(site_url()); exit;
	}else{
		wp_redirect(site_url()); exit;	
	}
}  */
get_header(); ?>
<style>
.mo-openid-app-icons a,.mo-openid-app-icons p, .mo_image_id{
	display:none !important;
}
</style>
	
	<div id="main-content">  
        <div class="outer-wrapper">
            <div class="container container-home">
                 <h3 class="h3-title">Create Your Next Big Event</h3>
                <div class="login-form2">
                    
                    <div class="form-inn">
                         <div class="clearfix">
                                <p class="event-login">  Sign In <span> or</span> <a href="<?php echo site_url(); ?>/sign-up/"> Sign Up </a></p>
								<?php 
									if(isset($form_errors)){
										// echo "<p style='color:red;font-size:16px;text-align:center;padding:10px'><b>".$form_errors."</b></p>";
										foreach($form_errors as $key=>$val){
											echo "<p style='color:red;font-size:16px;text-align:center;padding:10px'><b>".$val."</b></p>";
										}
									}
								?>
                          </div>
                        <form class="rgt-form2" action="" method="post">
                            <div class="frow2">
                                <input type="email" placeholder="Email" name="uemail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
                                <input type="password" placeholder="Enter Password" name="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
							</div>
                            <p class="remember">
                               <span class="chkbox">   <input type="checkbox" checked="checked" name="remember"><span class="checkmark"></span> &nbsp;  &nbsp; Keep me signed in</span>
                                <button style="width: 100%;" type="submit" name="SignInUser" class="signupbtn">Sign in</button>
                            </p>
                            <div class="clearfix">
                                <p class="event-for"><a href="<?php echo site_url(); ?>?page_id=685">Forgot Password? </a></p>
                            </div>
                            <div class="or2">OR </div>
                          <div class="login-social2">
                            <?php echo do_shortcode('[miniorange_social_login]'); ?>
							 <a href="javascript:void();" onClick="moOpenIdLogin('facebook','true');"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/fb.jpg"></a>
                            <a href="javascript:void();" onClick="moOpenIdLogin('google','false');"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/goo.jpg"></a>
                           </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- #outer-wrapper -->
    </div>


    <?php get_footer(); ?>