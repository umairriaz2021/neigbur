<?php 
/*
Template Name: Event login Page
*/

  
    if(isset($_GET['verification']) && $_GET['verification'] == 'success') {
        
        $msg = 'Account verification successful! Please sign-in to continue';
    }

//if(isset($_GET['verification']) && $_GET['verification'] == 'xxxx') {

    if(isset($_GET['reset']) && $_GET['reset'] == 'success') {

        $msg = "Password has been reset successfully. Please <b>Sign in </b> to continue.";
    }

    if((isset($_GET['user']) && $_GET['user'] != '') && (isset($_GET['fname']) && $_GET['fname'] != '')) {

        if(isset($_SESSION['msg'])) {

            echo $_SESSION['msg'];die;
        }

        $email = base64_decode($_GET['user']);
        $fname = base64_decode($_GET['fname']);

        $data    = array(
            'email' => $email
        );

        $payload = json_encode($data);
        $ch      = curl_init(API_URL.'users/confirmEmail');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length:' . strlen($payload)
        ));

        $result = curl_exec($ch);
        curl_close($ch);
        $apirespons = json_decode($result);

        if($apirespons->success) {

           // $msg = 'Account verification successful! Please sign-in to continue';
            $to = $email;
            $emailtemplate   = get_post(1170);
            $emailoutput =  apply_filters( 'the_content', $emailtemplate->post_content );
            $signin_url = '<a style="color:#3274b6;font-weight:600;text-decoration:none;" href="'.site_url().'?page_id=187">Click here</a>';
            $support_url = '<a style="color:#3274b6;font-weight:600;text-decoration:none;" href="www.support.snapd.com" target="_blank">support.snapd.com</a>';
            $snapd_hub_section = '<a href="https://apps.apple.com/ca/app/snapd-hub/id1443366785" target="_blank"><img style="width:200px;margin-right:8px;" src="'.site_url().'/images/apple-store.jpg"></a> <a href="https://play.google.com/store/apps/details?id=com.snapd.communityhub&hl=en_CA" target="_blank"><img  style="width:200px;" src="'.site_url().'/images/google-pay.jpg"></a>';
            $user_name = ucfirst($fname);
            $emailContent= str_replace(array('[[user_name]]', '[[signin_url]]', '[[support_url]]', '[[snapd_hub_section]]'), array($user_name, $signin_url, $support_url, $snapd_hub_section), $emailoutput);
            $subject = $emailtemplate->post_title;

            $message .= '<DOCTYPE! html>
                                <html>
                                    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                                        <title>
                                            New Account Created
                                        </title>
                                    </head>
                                    <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
                                        <div class="container" style="margin:0 auto;max-width:1080px;">
                                    <div style="width:100%;background: #e4e4e4;padding-left: 15px;padding-top: 10px;padding-bottom: 8px;">
                                        <a href="#"><img src="'.site_url().'/wp-content/uploads/2020/01/new-logo.png" style="width: 200px;"></a>
                                        </div>
                                        <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
                                            '.$emailContent.'
                                      </div>
                                    </body>
                                </html>
                            </DOCTYPE>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Neighbur Inc.;<no-reply@snapd.com>' . "\r\n";
            wp_mail($to, $subject, $message, $headers);
            header("Location: ".site_url().'/sign-in?verification=success');
        } else {

            $form_errors = 'Your email verification failed. Please try again.';
        }

    }
//}


if(isset($_POST['SignInUser']) || isset($_COOKIE['user_cookie_sn_un'])) {
//	echo $_COOKIE['user_cookie_sn_un']; exit;
// var_dump($_POST['remember']); exit;
 	extract($_POST);
 	if(isset($_COOKIE['user_cookie_sn_un'])){
 	    $data=array(
    			'email' => $_COOKIE['user_cookie_sn_un'],
    			'password' => $_COOKIE['user_cookie_sn_pw']
    		);
    		$uemail= $_COOKIE['user_cookie_sn_un'];
            $psw= $_COOKIE['user_cookie_sn_pw'];
 	}
 	else{
    	$data = array(
    			'email' => $uemail,
    			'password' => $psw
    		);
 	}
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
		$_SESSION['Api_token']  =   $apirespons->token;
		$_SESSION['userdata']  =   $apirespons->user;
		$loginredirect = (isset($_SESSION['loginredirect']))?$_SESSION['loginredirect']:site_url().'?page_id=307';
		unset($_SESSION['loginredirect']);
		
        
        if(isset($_POST['SignInUser']) && $_POST['remember']=='on' ){ 
            setcookie("user_cookie_sn_un",$uemail,time()+31556926, "/" );
            setcookie("user_cookie_sn_pw",$psw,time()+31556926, "/" );
         }
		wp_redirect($loginredirect);
		exit;
	}else{
		if($apirespons->error == 'User has not confirmed email'){
		$form_errors = 'Previous sign-up authentication not completed. <a href="#">Please resend.</a>';
		}else{
		$form_errors = ucwords($apirespons->error);   
		}
		
	}
}	

get_header(); ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
    <style>

        .error {
            color: red; padding-bottom: 10px;font-size: smaller;
        }
        .login-form2 input {
            margin-bottom: 5px !important;
            margin-top: 20px !important;
        }
        .login-form2 input.error {
           border: 1px solid red !important;
        }

        .mo-openid-app-icons a,.mo-openid-app-icons p, .mo_image_id{
	        display:none !important;
        }

       /* div#logged_in_user {
            margin-bottom: 10px;
            display: none !important;
        }*/
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
									echo "<p style='color:red;font-size:16px;text-align:center;padding:10px'><b>".$form_errors."</b></p>";
								}
							?>

                             <?php
                             if(isset($msg)){
                                 echo "<p style='color:green;font-size:16px;text-align:center;padding:10px'><b>".$msg."</b></p>";
                             }
                             ?>

                             <?php
                             if(isset($_SESSION['msg'])){
                                 echo "<p style='color:green;font-size:16px;text-align:center;padding:10px'><b>".$_SESSION['msg']."</b></p>";
                                 unset($_SESSION['msg']);
                             }
                             ?>
                          </div>
                        <form class="rgt-form2" action="" id="LoginForm" method="post">
                            <div class="frow2">
                                <input type="email" id="user_login" placeholder="Email" name="uemail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required title="Please enter valid Email address">
                                <input type="password" placeholder="Enter Password" name="psw" required title="Please enter Password">
							</div>
                            <p class="remember">
                               <span class="chkbox">   <input type="checkbox" id="rememberme" checked="checked" name="remember"><span class="checkmark"></span> &nbsp;  &nbsp; Keep me signed in</span>
                                <button style="width: 100%;" type="submit" name="SignInUser" class="signupbtn">Sign in</button>
                            </p>
                            <div class="clearfix">
                                <p class="event-for"><a href="<?php echo site_url(); ?>?page_id=685">Forgot Password? </a></p>
                            </div>
                            <div class="or2" style="display:none !important;">OR </div>
                          <div class="login-social2">
                            <?php echo do_shortcode('[miniorange_social_login]'); ?>
						<div class="social-login-link" style="display:none !important;">	<a href="javascript:void(0);" onClick="moOpenIdLogin('facebook','true');"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/12/fb-login-1.png"></a>
                            <a href="javascript:void(0);" onClick="moOpenIdLogin('google','true');"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/12/g-login-1.png"></a></div>
                           </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- #outer-wrapper -->
    </div>


    <?php get_footer(); ?>

<script>

    $(function() {

        $("#LoginForm").validate({

            rules: {

                uemail: {
                    required: true,
                    email: true
                },
                psw: {
                    required: true
                },
            }

        });
    });
</script>
