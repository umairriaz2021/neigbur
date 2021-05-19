<?php
/*
Template Name: Forgot Page
*/

$error = '';
$success = '';
$code_page = '';

if(isset($_POST['btnSubmit'])) {

    $data   = array('email' => $_POST['uemail']);

    $payload = json_encode($data);
    $ch      = curl_init(API_URL.'users/recoverPassword');
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

    if(!$apirespons->success) {
        $error = 'The email address is not registered with an account.';
    } else {

        $_SESSION['recover_token'] =  $apirespons->recoveryToken;
        $_SESSION['user_email'] =  $_POST['uemail'];
        $code = rand ( 1000 , 9999 );
        $to = $_POST['uemail'];
        $emailtemplate   = get_post(1173);
        $emailoutput =  apply_filters('the_content', $emailtemplate->post_content );
        $emailContent= str_replace(array('[[code]]'), array($code), $emailoutput);
        $subject = $emailtemplate->post_title;
        $message = '<DOCTYPE! html>
                        <html>
                            <head>
                                <title>
                                    Forgot Password
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
        $success = 'A code has been sent to your registered email.';
        $code_page = 'yes';
    }

}

if(isset($_POST['btnSubmitCode'])) {

    $enteredcode = $_POST['code'];
    $code = $_POST['hidden_code'];
    $user_id = $_POST['hidden_user_id'];

    if($enteredcode == $code) {

        wp_redirect(site_url().'?page_id=705');
    } else {
        $error = 'Invalid Code.';
        $code_page = 'yes';
    }
}

get_header(); ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="/wp-content/themes/Divi Child/datepicker/css/jquery.datetimepicker.min.css">
    <script src="/wp-content/themes/Divi Child/datepicker/js/moment.js"></script>
    <script src="/wp-content/themes/Divi Child/datepicker/js/jquery.datetimepicker.full.js"></script>
    <style>
        .mo-openid-app-icons a,.mo-openid-app-icons p, .mo_image_id{
            display:none !important;
        }
        .error {
            color: red;
        }
        #code {
       background-image: linear-gradient(to left, black 75%, rgba(255, 255, 255, 0) 0%);
    background-position: bottom;
    background-size: 23px 2px;
    background-repeat: repeat-x;
    background-position-x: 41px;
    width: 92px;
    min-width: 83px;
    font-size: 32px;
    border:0px solid !important;
}
    </style>

    <div id="main-content">
        <div class="outer-wrapper">
            <?php if($code_page == '') { ?>
                <div class="container container-home">
                    <h3 class="h3-title">Forgot Password</h3>
                    <div class="login-form2">
                        <div class="form-inn">
                            <div class="clearfix">
                                <?php
                                    if($error != ''){
                                        echo "<p style='color:red;font-size:16px;text-align:center;padding:10px'><b>".$error."</b></p>";
                                    }
                                ?>
                            </div>
                            <form class="rgt-form2" id="page_form" action="" method="post">
                                <div class="frow2">
                                    <input type="email" placeholder="Please Enter Email" name="uemail" required>
                                </div>
                                <p class="remember">
                                    <button style="width: 100%;" type="submit" name="btnSubmit" class="signupbtn">NEXT</button>
                                </p>
                                <div class="clearfix"></div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } else { ?>

                <div class="container container-home">
                    <h3 class="h3-title">Enter Code</h3>
                    <div class="login-form2">
                        <div class="form-inn">
                            <div class="clearfix">
                                <?php
                                    if($success != ''){
                                        echo "<p style='color:#28a745;font-size:16px;text-align:center;padding:10px'><b>".$success."</b></p>";
                                    }

                                    if($error != ''){
                                        echo "<p style='color:red;font-size:16px;text-align:center;padding:10px'><b>".$error."</b></p>";
                                    }

                                ?>
                            </div>
                            <form class="rgt-form2" id="code_form" action="" method="post">
                                <div class="frow2">
                                    <input id="code" type="text" name="code" maxlength="4" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"  onKeyPress="if(this.value.length==4) return false;"/>
                                    <input type="hidden" name="hidden_code" id="hidden_code" value="<?php echo (isset($code)) ? $code : ''?>">
                                    <input type="hidden" name="recover_token" id="hidden_code" value="<?php echo (isset($apirespons->recoveryToken)) ? $apirespons->recoveryToken : ''?>">
                                    <input type="hidden" name="hidden_user_id" value="<?php echo (isset($user)) ? $user->ID : ''?>">
                                    <input type="hidden" name="hidden_user_email" value="<?php echo isset($_POST['uemail']) ? $_POST['uemail'] : '';?>">
                                </div>
                                <p class="remember">
                                    <button style="width: 100%;" type="submit" name="btnSubmitCode" class="signupbtn">VERIFY</button>
                                </p>
                                <div class="clearfix"></div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }?>

        </div>
        <!-- #outer-wrapper -->
    </div>

<?php get_footer(); ?>
<script>

    $(function() {

        setTimeout(function(){ $('#hidden_code').val(''); }, 2880000); // 30 minutes

        $("#page_form").validate({

            rules: {

                uemail: {
                    required: true
                }
            }
        });

        $("#code_form").validate({

            rules: {

                code: {
                    required: true
                }
            }
        });
    });





</script>
