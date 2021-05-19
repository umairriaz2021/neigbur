<?php
/*
Template Name: Resetpassword Page
*/

$error = '';
$success = '';

if(isset($_POST['btnSubmit'])) {

    $data   = array('email' => $_SESSION['user_email'], 'recoverToken' => $_SESSION['recover_token'], 'password' => $_POST['pwd']);

    $payload = json_encode($data);
    $ch      = curl_init(API_URL.'users/resetPassword');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
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
        $error = 'Not a valid request !';
    } else {
       // $success = 'Password has been reset successfully. Please <a href="'.site_url().'/sign-in/">Sign in </a> to continue.';
        unset($_SESSION['user_email']);
        unset($_SESSION['recover_token']);
        header("Location: ".site_url().'/event-signin?reset=success');
    }

}

get_header(); ?>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
<style>
    .mo-openid-app-icons a,.mo-openid-app-icons p, .mo_image_id{
        display:none !important;
    }

    .error {
        color: red;
    }

    #pswd_info { box-shadow: 0px 0px 5px #b8bfc3;
        position: absolute;
        width: 230px;
        padding: 10px;
        background: #ffffff;
        font-size: .875em;
        border-radius: 3px;
        z-index: 99999;
        right: -240px;
        float: right;
        top: 182px;
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

    .error {
        color: red; padding-bottom: 10px;font-size: smaller;
    }
    .login-form2 input {
        margin-bottom: 5px !important;
        margin-top: 15px !important;
    }
    .login-form2 input.error {
        border: 1px solid red !important;
    }
</style>

<div id="main-content">
    <div class="outer-wrapper">
        <div class="container container-home">
            <h3 class="h3-title">Reset Password</h3>
            <div class="login-form2">
                <div class="form-inn">
                    <div class="clearfix">
                        <?php
                            if(isset($error)){
                                echo "<p style='color:red;font-size:16px;text-align:center;padding:10px'><b>".$error."</b></p>";
                            }
                        ?>

                        <?php
                            if(isset($success)){
                                echo "<p style='color:#28a745;font-size:16px;text-align:center;padding:10px'>".$success."</p>";
                            }
                            ?>
                    </div>
                    <form class="rgt-form2" id="page_form" action="" method="post">
                        <div class="frow2">
                            <input type="password" placeholder="Please Enter New password" name="pwd" id="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}" required title="Enter password.">

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
                        <div class="frow2">
                            <input type="password" placeholder="Please Enter Confirm password" name="cpwd" required title="Enter password confirmation.">
                        </div>
                        <p class="remember">
                            <button style="width: 100%;" type="submit" name="btnSubmit" class="signupbtn">Submit</button>
                        </p>
                        <div class="clearfix"></div>
                        <div class="clearfix"></div>
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

        $("#page_form").validate({

            rules: {
                pwd: {
                    required: true,
                    pwdcheck: true
                },

                cpwd: {
                    required: true,
                    equalTo: "#psw"
                }
            },

            messages: {
                cpwd: {
                    equalTo: "Please enter the same password as above"
                }
            }
        });
    });

    jQuery.validator.addMethod("pwdcheck", function(value, element) {
        return this.optional(element) || /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(value);
    }, "");
</script>
