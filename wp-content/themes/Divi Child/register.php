<?php
/*Template Name: Register Page*/
$error = '';
if (isset($_POST['signupbtn'])) {

    extract($_POST);
    $phone = '';

    if($uphone != '') {
        $ph = explode(' ', $uphone);
        $ph1 = trim($ph[0], '()');
        $ph2 = str_replace('-', '', $ph[1]);
        $phone = $ph1.$ph2;
    }
    $data    = array(
				'first' => $fname,
				'last' => $lname,
				'email' => $uemail,
				'number' => $phone,
				'age_range_id' => $age_range,
				'password' => $psw,
				'created' => date('Y-m-d h:i:s'),
				'updated' => date('Y-m-d h:i:s'),
                'country' => 'CA',
                'province'=> 'ON'
			);

    $payload = json_encode($data);
    $ch      = curl_init(API_URL.'users');
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

    if ($apirespons->success) {
        if(isset($apirespons->error)) {

            $apierror = $apirespons->error;

        } else {
            $processrespons = "Created successfully. Please check your email for verification !";
            unset($_SESSION['post_data']);

            $to = $uemail;

			$regemail   = get_post(1156);
			$regemailoutput =  apply_filters( 'the_content', $regemail->post_content );

			$user_name=ucfirst($apirespons->user->first);
			$Clikhere_url='<a style="color:#3274b6;font-weight:600;text-decoration:none;" href="'.site_url().'?page_id=187&user='.base64_encode($apirespons->user->email).'&fname='.base64_encode($apirespons->user->first).'">clicking here</a>';

			$regemailContent= str_replace(array('[[user_name]]','[[Clikhere_url]]'),array($user_name,$Clikhere_url),$regemailoutput);

            $subject = $regemail->post_title;
            $message = '<DOCTYPE! html>
                            <html>
                                <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                                    <title>
                                       Your Neighbur account requires verification
                                    </title>
                                </head>
                                <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
                                    <div class="container" style="margin:0 auto;max-width:1080px;">
                                    <div style="width:100%;background: #e4e4e4;padding-left: 15px;padding-top: 10px;padding-bottom: 8px;">
                                        <img src="'.site_url().'/wp-content/uploads/2020/01/new-logo.png" style="width: 200px;">
                                        </div>
                                        <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
										'.$regemailContent.'
                                    </div>
                                </body>
                            </html>
                        </DOCTYPE>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Neighbur Inc.;<no-reply@snapd.com>' . "\r\n";
            wp_mail($to, $subject, $message, $headers);
        }
    } else {

		$apierror = 'Email address already exists, please select a new one or return to sign-in and select Forgot Password to access your account.';

        $post_data = array(

            'fname' => $fname,
            'lname' => $lname,
            'email' => $uemail,
            'number' => $phone,
            'age_range_id' => $age_range,
            'psw' => $psw,
            'cpsw' => $_POST['cpsw']
        );

        $_SESSION['post_data'] = $post_data;
    }
}

get_header();
?>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>

<style>
    .error {
         color: red; padding-bottom: 10px;font-size: smaller;
     }
    .login-form input {
        margin-bottom: 3px !important;
        margin-top: 15px !important;
    }

    .login-form select {
        margin-bottom: 3px !important;
        margin-top: 15px !important;
    }
    .login-form input.error {
        border: 1px solid red !important;
    }

    .login-form select.error {
        border: 1px solid red !important;
    }

    #pswd_info {  box-shadow: 0px 0px 5px #b8bfc3;
           position: absolute;
        width: 230px;
        padding: 10px;
        background: #ffffff;
        font-size: .875em;
        border-radius: 3px;
        z-index: 99999;
        right: -240px;
        float: right;
        top: 215px;
    }
    form#RegisterUser {
        position: relative;
    }
    #pswd_info::before {
       content: "\25B2";
        position: absolute;
        top: 18px;
        left: -7%;
        font-size: 20px;
        line-height: 14px;
        color: #dedede;
        text-shadow: none;
        display: block;
        transform: rotate(-90deg);
    }
    .invalid {
        line-height:24px; font-size: 15px;
    }
    .invalid .fa-check, .valid .fa-times{
        display:none;
    }
    .valid .fa-check, .invalid .fa-times{
        display:inline-block;
    }
    .valid {
        line-height:24px; font-size: 15px;
    }
    #pswd_info {
        display:none;
    }
</style>
    <div id="main-content">
        <div class="outer-wrapper ">
            <div class="container container-home">
                 <h3 class="h3-title">Create Your Next Big Event</h3>
                  <div class="login-form3">
                 <div class="clearfix">
                                <p class="event-login"> <a href="<?php echo site_url(); ?>/event-signin/">Sign In </a> <span>or </span> Sign Up</p>
                          </div>

            <?php 	if (isset($processrespons)) { ?>
                <script>
                    $(function(){
                        $('#myModal').show();
                    });
                </script>
                   <!-- <div class="email-confomation" style="margin:20px auto;">
                        <h3>Check your email</h3>
                        <p>A verification email has been sent to you.You must click on the link to activate your account before you can sign-in.</p>
                        <p class="mail-img"><img src="<?php /* echo site_url(); */?>/wp-content/uploads/2019/08/chk-mail.png"></p>
                        <p class="thx-btn"><a href="<?php /*echo site_url(); */?>/sign-in">OK, THANKS</a></p>
                    </div>-->
            <?php 	} else {

                ?>
                        <div class="login-form">

                            <form action="" method="post" id="RegisterUser">
								<?php
									/* if (isset($form_errors) && count($form_errors) > 0) {
										foreach ($form_errors as $key => $val) {
											echo "<p style='color:red;font-size:16px'><b>" . $val . "</b></p>";
										}
									} */

									if($apierror != '') {
                                        echo "<p style='color: red;font-size: 16px;background: #fff;text-align: center;padding: 16px;line-height: 22px;border-radius: 4px;margin-bottom: 6px;'><b class='alreday-exit'>" . $apierror . "</b></p>";
                                    }
								?>
                                    <p style="margin-left:5px;">Please complete all required fields below</p>
                                    <div class="frow">
                                        <input type="text" placeholder="First Name" name="fname" pattern="[A-Za-z ]{1,30}" title="Please enter First Name" required oninvalid="scroll_to_validator(this)" value="<?php echo isset($_SESSION['post_data']) ? $_SESSION['post_data']['fname'] : '';?>">
                                        <input type="text" placeholder="Last Name" name="lname" pattern="[A-Za-z ]{1,30}" title="Please enter Last Name" required oninvalid="scroll_to_validator(this)" value="<?php echo isset($_SESSION['post_data']) ? $_SESSION['post_data']['lname'] : '';?>">
                                    </div>
                                    <div class="frow">
                                        <input type="password" placeholder="Enter Password" name="psw" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Please enter Password" required oninvalid="scroll_to_validator(this)" value="<?php echo isset($_SESSION['post_data']) ? $_SESSION['post_data']['psw'] : '';?>">
									</div>
									<div id="pswd_info">
										<ul>
											<li id="length" class="invalid"><i class="fa fa-check" aria-hidden="true"></i><i class="fa fa-times" aria-hidden="true"></i> Be at least <strong>8 characters</strong></li>
											<li id="letter" class="invalid"><i class="fa fa-check" aria-hidden="true"></i><i class="fa fa-times" aria-hidden="true"></i> Include a <strong>lowercase letter</strong></li>
											<li id="capital" class="invalid"><i class="fa fa-check" aria-hidden="true"></i><i class="fa fa-times" aria-hidden="true"></i> Include an <strong>uppercase letter</strong></li>
											<li id="number" class="invalid"><i class="fa fa-check" aria-hidden="true"></i><i class="fa fa-times" aria-hidden="true"></i> Include a <strong>number</strong></li>
											<li id="space" class="invalid"><i class="fa fa-check" aria-hidden="true"></i><i class="fa fa-times" aria-hidden="true"></i> No <strong>space allowed</strong></li>
											<li id="comnpass" class="invalid"><i class="fa fa-check" aria-hidden="true"></i><i class="fa fa-times" aria-hidden="true"></i> Not include a <strong>commonly used phrase</strong></li>
										</ul>
									</div>
									<div class="frow">
                                        <input type="password" placeholder="Confirm Password" name="cpsw"  title="Please confirm Password" required oninvalid="scroll_to_validator(this)" value="<?php echo isset($_SESSION['post_data']) ? $_SESSION['post_data']['cpsw'] : '';?>">
                                    </div>
                                    <div class="frow">
                                        <input type="email" placeholder="Email" name="uemail" title="Please enter valid Email address" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="<?php echo isset($_SESSION['post_data']) ? $_SESSION['post_data']['email'] : '';?>" required>
                                        <input type="text" placeholder="(XXX) XXX-XXXX" name="uphone" title="Please enter valid Phone number" id="uphone" value="<?php echo isset($_SESSION['post_data']) ? $_SESSION['post_data']['number'] : '';?>" required>
                                    </div>
                                    <div class="frow">
                                        <select name="age_range" required title="Please select Age Range">
                                            <option value="">Age Range</option>
                                            <option value="1" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '1') ? 'selected' : ''?>>13-17</option>
                                            <option value="2" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '2') ? 'selected' : ''?>>18-29</option>
                                            <option value="3" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '3') ? 'selected' : ''?>>30-39</option>
                                            <option value="4" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '4') ? 'selected' : ''?>>40-49</option>
                                            <option value="5" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '5') ? 'selected' : ''?>>50-59</option>
                                            <option value="6" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '6') ? 'selected' : ''?>>60-69</option>
                                            <option value="7" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '7') ? 'selected' : ''?>>70-79</option>
                                            <option value="8" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '8') ? 'selected' : ''?>>80-89</option>
                                            <option value="9" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '9') ? 'selected' : ''?>>90-99</option>
                                            <option value="10" <?php echo (isset($_SESSION['post_data']) && $_SESSION['post_data']['age_range_id'] == '10') ? 'selected' : ''?>>Over 100 (Congratulations)</option>
                                        </select>
                                    </div>
                                    <p class="agree">
                                        <span class="chkbox" style="font-weight: normal;">
                                        <input type="checkbox" name="remember" required title="Please acknowledge Terms & Prvacy"> <span class="checkmark"></span>  &nbsp; By creating an account you agree to our <a href="javascript:void(0);" style="color:dodgerblue" onClick="jQuery('#myTermModal').show();">Terms & Privacy</a>. </span> </p>
                                    <div class="clearfix">
                                        <button type="submit" name="signupbtn" class="signupbtn">Sign Up</button>
                                    </div>
                            </form>
                        </div>
                        <?php
					}
					?>
            </div></div>
        </div>
        <!-- #content-area -->
        <!--Email Confirmation  -->
    </div>
<style>
.modal-open {
  overflow: hidden;
}
.modal {
    background: #00000054;
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 2000000;
  display: none;
  overflow: hidden;
  outline: 0;
}

.modal-open .modal {
  overflow-x: hidden;
  overflow-y: auto;
}

.modal-dialog {
  position: relative;
  width: auto;
  margin: 0.5rem;
  pointer-events: none;
}

.modal.fade .modal-dialog {
  transition: -webkit-transform 0.3s ease-out;
  transition: transform 0.3s ease-out;
  transition: transform 0.3s ease-out, -webkit-transform 0.3s ease-out;
  -webkit-transform: translate(0, -25%);
  transform: translate(0, -25%);
}
.btn-default {
    color: #fefefe;
    background-color: #333;
    border-color: #000;
    padding: 4px 11px;
    font-size: 18px;
    border-radius: 30px;
    border: 0;
}
@media screen and (prefers-reduced-motion: reduce) {
  .modal.fade .modal-dialog {
    transition: none;
  }
}

.modal.show .modal-dialog {
  -webkit-transform: translate(0, 0);
  transform: translate(0, 0);
}

.modal-dialog-centered {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-align: center;
  align-items: center;
  min-height: calc(100% - (0.5rem * 2));
}

.modal-content {
  position: relative;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-direction: column;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 0.3rem;
  outline: 0;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 1040;
  background-color: #000;
}

.modal-backdrop.fade {
  opacity: 0;
}

.modal-backdrop.show {
  opacity: 0.5;
}

.modal-header {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-align: start;
  align-items: flex-start;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
  border-top-left-radius: 0.3rem;
  border-top-right-radius: 0.3rem;background: #333333;
}

.modal-header .close {
  padding: 1rem;
  margin: -1rem -1rem -1rem auto;
}

.modal-title {
 margin-bottom: 0;
    line-height: 1.5;
    padding-bottom: 0;
    font-size: 26px;
    font-weight: bold;
    color: #fff !important;
}
.modal-dialog.modal-lg {
    margin-top: 10%;
}
h4.modal-title {
    width: 100%;
}
.modal-body {
  position: relative;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  padding: 1.3rem;
  max-height: 600px;
overflow-x: hidden;;
}

.modal-footer {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-align: center;
  align-items: center;
  -ms-flex-pack: end;
  justify-content: flex-end;
  padding: 1rem;
  border-top: 1px solid #e9ecef;
}

.modal-footer > :not(:first-child) {
  margin-left: .25rem;
}

.modal-footer > :not(:last-child) {
  margin-right: .25rem;
}

.modal-scrollbar-measure {
  position: absolute;
  top: -9999px;
  width: 50px;
  height: 50px;
  overflow: scroll;
}

@media (min-width: 576px) {
  .modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
  }
  .modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
  }
  .modal-sm {
    max-width: 300px;
  }
}

@media (min-width: 992px) {
  .modal-lg {
    max-width: 800px;
  }
}

</style>
<div class="modal" id="myTermModal" role="dialog">
	<div class="modal-dialog modal-lg">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Terms & Privacy</h4> <button type="button" class="btn btn-default" onClick="jQuery('#myTermModal').hide();">X</button>
		</div>
		<div class="modal-body">
		  <?php
                $args = array('page_id' => 3);
                $loop = new WP_Query( $args );
                while($loop->have_posts()) { $loop->the_post();
                the_content(); }
            ?>
		</div>

	  </div>
	</div>
</div>

<div class="modal" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Check your email</h3>
            </div>
            <div class="modal-body">
                <div class="email-confomation" style="margin:20px auto;">
                    <p>A verification email has been sent to you. You must click on the link to activate your account before you can sign-in.</p>
                    <p class="mail-img"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/chk-mail.png"></p>
                    <p class="thx-btn"><a href="<?php echo site_url(); ?>/sign-in/">OK, THANKS</a></p>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="submitModal" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 400px !important;">
        <div class="modal-content" style="width: 400px !important;">
            <div class="modal-body">
                <div class="email-confomation" style="margin:20px auto; width: 350px !important;">
                    <p>Account creation in progress…</p>
                    <p class="mail-img"><img src="<?php echo site_url(); ?>/wp-content/uploads/loading.gif"></p>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
get_footer();
?>

<script>

    function scroll_to_validator(input)
    {
        input.focus();
    }
    $(function() {

        $('#uphone').mask('(000) 000-0000');

        $("#RegisterUser").validate({

            rules: {

                uemail: {
                    required: true,
                    email: true
                },
                lname: {
                    required: true,
                    lettersonly: true
                },
                fname: {
                    required: true,
                    lettersonly: true
                },
                uphone: {
                    required: true
                },
                psw: {
                    required: true,
                    pwdcheck: true,
                    noSpace: true
                },

                cpsw: {
                    required: true,
                    equalTo: "#psw"
                }
            },

            messages: {
                cpsw: {
                    equalTo: "Please enter the same password as above"
                }
            }

        });
    });

    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-z]+$/i.test(value);
    }, "Letters only please");

    jQuery.validator.addMethod("pwdcheck", function(value, element) {
        return this.optional(element) || /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(value);
    }, "");

    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value.indexOf(" ") < 0;
    }, "No space allowed please.");

    $('#RegisterUser').submit(function () {

       if($('#RegisterUser').valid()) {

           $('#submitModal').show();
            setTimeout(function(){ $('#submitModal').hide(); }, 3000);
       }
    });

</script>
