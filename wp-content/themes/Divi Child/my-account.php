<?php
/*
Template Name: My Account Page
*/

/* api user login check */
/* echo "<pre>"; print_r($_SESSION); */
$pwd_error = '';
$displayMsg = '';

if(isset($_SESSION['user_email']) && isset($_SESSION['social_user_id']) && isset($_SESSION['social_app_name'])) {


    $ldata    = array(
        'email'     =>  $_SESSION['user_email'],
        'social_user_id'  =>  $_SESSION['social_user_id'],
        'social_app_name' =>  $_SESSION['social_app_name']
    );

    $payload = json_encode($ldata);
    $ch      = curl_init(API_URL.'users/loginSocial');
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
    $loginresponse = json_decode($result);

    if($loginresponse->newUser) {

        $data    = array(
            'email' => $_SESSION['user_email']
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

        $to = $_SESSION['user_email'];
        $emailtemplate   = get_post(1172);
        $emailoutput =  apply_filters('the_content', $emailtemplate->post_content );
        $subject = $emailtemplate->post_title;
        $message = '<DOCTYPE! html>
                        <html>
                            <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                                <title>
                                    Single Sign on Activations
                                </title>
                            </head>
                            <body style="font-family:sans-serif;font-size:14px;color:black;font-size:16px;">
                            <div class="container" style="margin:0 auto;max-width:1080px;">
                            <div style="width:100%;background: #e4e4e4;padding-left: 15px;padding-top: 10px;padding-bottom: 8px;">
                                <img src="'.site_url().'/wp-content/uploads/2020/01/new-logo.png" style="width: 200px;">
                                </div>
                                <hr style="padding:3px 0px;border:0px;background-color:#80808021;">
                                    '.$emailoutput.'
                              </div>
                            </body>
                        </html>
                    </DOCTYPE>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Neighbur Inc.;<no-reply@snapd.com>' . "\r\n";
        wp_mail($to, $subject, $message, $headers);

        $displayMsg = 'Account created successfully. Please take a moment to fill in your account details.';
    }

    if($loginresponse->success)
    {

        $_SESSION['userdata']  =   $loginresponse->user;
        $_SESSION['Api_token']  =   $loginresponse->token;

        if ($displayMsg == '')
        {
          $displayMsg = 'Logged in successfully.';
        }
    }
}

if(isset($_SESSION['userdata'])){
	$userdata = $_SESSION['userdata'];
}else{
	wp_redirect( site_url().'?page_id=187' );
	exit;
}

$token   =  $_SESSION['Api_token'];

if(isset($_POST['perInfoUpdate'])) {

    extract($_POST);

    $uphone = '';

    if($phone != '') {
        $ph = explode(' ', $phone);
        $ph1 = trim($ph[0], '()');
        $ph2 = str_replace('-', '', $ph[1]);
        $uphone = $ph1.$ph2;
    }

    $data    = array(
        'first'     =>  $first,
        'last'      =>  $last,
        'name'      =>  $first.' '.$last,
        'number'     =>  $uphone,
        'address'     =>  $address,
        'country_id'     =>  $country_id,
        'province_id'     =>  $province_id,
        'city'     =>  $city,
        'postalcode'     =>  $postalcode,
        'age_range_id' =>  $age_range
    );

    $payload = json_encode($data);
	/* echo $payload; */
    $userID = $_POST['hidden_user_id'];

    $ch      = curl_init(API_URL.'users/'.$userID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($payload),
        'Authorization: ' . $token
    ));

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
    }
    curl_close($ch);

    $displayMsg = 'Account updated.';
}

if(isset($_POST['pwdInfoUpdate'])) {

    $user = $_SESSION['userdata'];

    $pdata  = array(
        'password'   =>  $_POST['new_pwd'],
        'old_password'   =>  $_POST['old_pwd']
    );

    $payload = json_encode($pdata);
    $ch      = curl_init(API_URL.'users/'.$user->id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($payload),
        'Authorization: ' . $token
    ));

    $result = curl_exec($ch);
    $response = json_decode($result);

    //echo '<pre>';print_r($response);die;

    if(!$response->success) {
        $pwd_error = $response->error;
    }
}

if(isset($_POST['notificationUpdate'])){

    $user = $_SESSION['userdata'];

    if(count($_POST['notifications']) > 0) {

        $ndata = array(
            'notifications' => $_POST['notifications']
        );
    } else {

        $ndata = array(
            'notifications' => ['0']
        );
    }

    $payload = json_encode($ndata);
    $ch      = curl_init(API_URL.'users/'.$user->id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($payload),
        'Authorization: ' . $token
    ));

    $result = curl_exec($ch);
    $response = json_decode($result);

}

$ch   = curl_init(API_URL.'users/'.$userdata->id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);

if($apirespons->success){
	$userdata = $_SESSION['userdata']  =   $apirespons->user;
	/*if(isset($userdata) && ($userdata->country_id != '')) {

        $province  =   $wpdb->get_results("Select * from wp_states where country_id = $userdata->country_id");
    }*/
}

$countries = $wpdb->get_results("Select * from wp_countries");
$states = $wpdb->get_results("Select * from wp_states");
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
        display:block;
    }
</style>
    <div id="main-content">
        <div class="outer-wrapper ">
            <div class="container container-home">
                <div class="account-outer">
<?php
                if ($displayMsg != '')
                  echo '<div class="message-box" style="color:white;">&nbsp;&nbsp;&nbsp;' . $displayMsg . '</div>';
?>

					<div class="login-form account-info" id="perInfoDefaultDiv" style="display:<?php echo ($userdata->city == '' || $userdata->address == '') ? 'none' : '';?>">
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Personal Information</h3> <span style="cursor:pointer" class="edit" onclick="openPerInfoEditView()"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span>
                            </div>
                            <div class="tab-row1">
                                <strong><?=$userdata->first?> <?=$userdata->last?> </strong>

                                <?php if(isset($userdata) && $userdata->country_id != '') {

                                    $country_name = $wpdb->get_row("Select * from wp_countries where id = $userdata->country_id");
                                }?>
                                <p><?php echo $userdata->address;?>, <?php echo $userdata->city;?>, <?php echo (isset($country_name)) ? $country_name->name : '';?>, <?php echo $userdata->postalcode;?></p>
                            </div>
                            <div class="tab-row1">
                                <strong>Mobile Number </strong>
                                <p>
                                    <?php echo $userdata->number;?>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Email </strong>
                                <p>
                                    <?php echo $userdata->email;?>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Age </strong>
                                <p>
                                    <?php echo ($userdata->age_range->id == '1' ? '13-17' : '')?>
                                    <?php echo ($userdata->age_range->id == '2' ? '18-29' : '')?>
                                    <?php echo ($userdata->age_range->id == '3' ? '30-39' : '')?>
                                    <?php echo ($userdata->age_range->id == '4' ? '40-49' : '')?>
                                    <?php echo ($userdata->age_range->id == '5' ? '50-59' : '')?>
                                    <?php echo ($userdata->age_range->id == '6' ? '60-69' : '')?>
                                    <?php echo ($userdata->age_range->id == '7' ? '70-79' : '')?>
                                    <?php echo ($userdata->age_range->id == '8' ? '80-89' : '')?>
                                    <?php echo ($userdata->age_range->id == '9' ? '90-99' : '')?>
                                    <?php echo ($userdata->age_range->id == '10' ? 'Over 100' : '')?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="login-form account-info" id="PerInfo" style="display:<?php echo ($userdata->city == '' || $userdata->address == '') ? '' : 'none';?>">
					    <form action="" method="post" id="PerInfoForm">
                        <!--   Personal info edit start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Personal Information</h3>
								<?php //echo "<pre>"; print_r($userdata); echo "</pre>"; ?>
							</div>
                            <p><b>All required fields marked with (*).</b></p>
                            <div class="tab-row1">
                                <input type="text" name="first" value="<?=$userdata->first?>" required title="Please enter First Name*" placeholder="First Name">
                                <input type="text" name="last" value="<?=$userdata->last?>" required title="Please enter Last Name*" placeholder="Last Name">
                                <input type="hidden" value="<?php echo $userdata->id;?>" name="hidden_user_id">
                                <input type="text" name="address" value="<?php echo $userdata->address;?>" placeholder="Street Address">
                               <!-- <select name="country_id">
                                    <option value="">Select Country</option>
                                    <option value="2">Canada</option>
                                    <option value="1">United State</option>
                                </select>-->

                                <select name="country_id" id="country" autocomplete="off">
                                    <option value="">Select Country</option>
                                    <?php foreach($countries as $row){ ?>
                                        <option value="<?php echo $row->id;?>" <?php echo ($row->id == $userdata->country_id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                                    <?php }?>
                                </select>


                                <select id="state" name="province_id" autocomplete="off">
                                    <option value="">Select Province/State</option>
                                    <?php foreach($states as $row) { ?>

                                        <option value="<?php echo $row->id;?>" <?php echo ($row->id == $userdata->province_id) ? 'selected' : '';?>><?php echo $row->name;?></option>
                                    <?php }?>
                                </select>

                                <input name="city" type="text" value="<?php echo $userdata->city;?>" placeholder="Enter City">
                                <input name="postalcode" pattern="[A-Za-z][0-9][A-Za-z] [0-9][A-Za-z][0-9]" type="text" value="<?php echo $userdata->postalcode;?>" placeholder="Postal/ZIP Code">
                            </div>
                            <div class="tab-row1">
                                <strong>Mobile Number*</strong>
                                <p>
                                    <input type="text" name="phone" id="phone"  value="<?php echo $userdata->number;?>" required placeholder="(XXX) XXX-XXXX" title="Please enter valid Phone number">
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Email </strong>
                                <p>
                                    <input type="text" name="email"  value="<?php echo $userdata->email;?>" readonly required title="Enter email address.">
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Age*</strong>
                                <p>
                                    <select name="age_range" required title="Please select Age Range">
                                        <option value="">Age Range</option>
                                        <option value="1" <?php echo ($userdata->age_range->id == '1' ? 'selected' : '')?>>13-17</option>
                                        <option value="2" <?php echo ($userdata->age_range->id == '2' ? 'selected' : '')?>>18-29</option>
                                        <option value="3" <?php echo ($userdata->age_range->id == '3' ? 'selected' : '')?>>30-39</option>
                                        <option value="4" <?php echo ($userdata->age_range->id == '4' ? 'selected' : '')?>>40-49</option>
                                        <option value="5" <?php echo ($userdata->age_range->id == '5' ? 'selected' : '')?>>50-59</option>
                                        <option value="6" <?php echo ($userdata->age_range->id == '6' ? 'selected' : '')?>>60-69</option>
                                        <option value="7" <?php echo ($userdata->age_range->id == '7' ? 'selected' : '')?>>70-79</option>
                                        <option value="8" <?php echo ($userdata->age_range->id == '8' ? 'selected' : '')?>>80-89</option>
                                        <option value="9" <?php echo ($userdata->age_range->id == '9' ? 'selected' : '')?>>90-99</option>
                                        <option value="10" <?php echo ($userdata->age_range->id == '10' ? 'selected' : '')?>>Over 100 (Congratulations)</option>
                                    </select>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <p>
                                    <input type="submit" name="perInfoUpdate" value="UPDATE" class="save-value">
                                    <!-- input onClick="closePerInfoEditView()" type="button" value="CANCEL" class="cancel-value" -->
                                    <input onClick="location.href = '/';" type="button" value="CANCEL" class="cancel-value">
                                </p>
                            </div>
                        </div>
						</form>
                    </div>

                    <div class="login-form account-info" id="perPassDefaultDiv">
                        <!--   password info start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Password</h3><span style="cursor:pointer" class="edit" onClick="openPerPassEditView()"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
                            <div class="tab-row1">
                                <?php if($pwd_error != '') { ?>
                                    <p style="color: #ff0000;"><b><?php echo $pwd_error;?></b></p>
                                <?php } else { ?>
                                    <strong>Password has been set </strong>
                                <?php }?>
                                <p>Choose a Strong, unique password atleast 8 characters long.</p>
                            </div>
                        </div>
                    </div>
                    <!--   password info end --->
                    <form action="" method="post" id="PwdInfoForm">
                        <div class="login-form account-info" id="PerPass" style="display:none">
                            <!--   password edit start --->
                            <div class="tab-1">
                                <div class="tab-header">
                                    <h3> Password</h3>
                                </div>
                                <div class="tab-row1">
                                   <!-- <strong>Password has been set </strong>-->
                                    <p>Choose a Strong, unique password atleast 8 characters long.</p>
                                    <p>Please complete all required fields below</p>
                                    <p>
                                        <input type="password" name="old_pwd" id="old_pwd" placeholder="Old Password*" required title="Please enter Old Password">
                                    </p>
                                   <!-- <span id="span_old_pwd"><b>Must contain at least one number and one uppercase and lowercase letter and at least 8 or more characters.</b></span>-->
                                    <p>
                                        <input type="password" name="new_pwd" id="psw" placeholder="New Password*" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Please enter Password" required>
                                    </p>
                                    <!--<span id="span_new_pwd"><b>Must contain at least one number and one uppercase and lowercase letter and at least 8 or more characters.</b></span>-->
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
                                    <p>
                                        <input type="password" name="cpwd" id="cpwd" placeholder="Confirm Password*" required title="Please confirm Password">
                                    </p>
                                    <!--<span id="span_cpwd"><b>Must contain at least one number and one uppercase and lowercase letter and at least 8 or more characters.</b></span>-->
                                </div>
                                <div class="tab-row1">
                                    <p>
                                        <input type="submit" name="pwdInfoUpdate" value="UPDATE" class="save-value">
                                        <input onClick="closePerPassEditView()" type="button" value="CANCEL" class="cancel-value">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--   password edit end --->
                    <div class="login-form account-info" id="perPaymentDefaultDiv">
                        <!--   Payments Methods start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Payout Method</h3>&nbsp;(<small>for ticket based events</small>)<span style="cursor:pointer" class="edit" onClick="openPerPaymentEditView();"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
                            <div class="tab-row1">
                                <strong>Paypal j***a@snapd.com (Primary) </strong>

                            </div>
                        </div>
                    </div>
                    <!--   Payments Methods end --->
                    <div class="login-form account-info" id="PerPaymentDiv" style="display:none">
                        <!--   Payments edit start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Payout Method </h3>&nbsp;(<small>for ticket based events</small>)</div>
                            <div class="tab-row1">
                                <p>
                                    <input type="text" value="" placeholder="Paypal j***a@snapd.com (Primary)">
                                </p>
                             <!--   <p>Add New + </p>-->
                            </div>
                            <div class="tab-row1">
                                <p>
                                    <!--<input onClick="jQuery('#PerPaymentDiv').hide();" type="button" value="UPDATE" class="save-value">-->
                                    <input type="button" value="UPDATE" class="save-value">
                                    <input onClick="closePerPaymentEditView()" type="button" value="CANCEL" class="cancel-value">
                                </p>
                            </div>
                        </div>
                    </div>
                    <!--   Payments edit end --->

                    <?php

                        $notification=[];

                       if(isset($apirespons->user->notifications)) {

                           foreach ($apirespons->user->notifications as $row){

                               $notification[] = $row->id;
                           }
                       }
                    ?>

                    <div class="login-form account-info" id="notificationDefaultView">
                        <!--   Notifications  start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Notifications</h3><span style="cursor: pointer;" class="edit" onClick="openNotificationEditView()"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
                            <div class="tab-row1">
                                <p>
                                    <span class="chkbox">
                                        <input type="checkbox" value="1" <?php echo in_array('1', $notification) ? 'checked' : '';?> disabled> <span class="checkmark"></span>Allow FAV event notifications 3 days prior
                                    </span>
                                </p>
                                <p>
                                    <span class="chkbox">
                                        <input type="checkbox" value="2" <?php echo in_array('2', $notification) ? 'checked' : '';?> disabled><span class="checkmark"></span> Allow news event notifications based on my experience profile
                                    </span>
                                </p>
                                <p>
                                    <span class="chkbox">
                                        <input type="checkbox" value="3" <?php echo in_array('3', $notification) ? 'checked' : '';?> disabled><span class="checkmark"></span> Allow notifications when I am near an event based on my experience profile
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="" method="post" id="notificationsform">
                        <div class="login-form account-info" style="display: none;" id="notificationEditView">
                            <!--   Notifications  start --->
                            <div class="tab-1">
                                <div class="tab-header">
                                    <h3> Notifications</h3><span style="display: none;" class="edit"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
                                <div class="tab-row1">
                                    <p>
                                         <span class="chkbox">
                                             <input type="checkbox" name="notifications[]" value="1" <?php echo in_array('1', $notification) ? 'checked' : '';?>> <span class="checkmark"></span>Allow FAV event notifications 3 days prior
                                         </span>
                                    </p>
                                    <p>
                                         <span class="chkbox">
                                             <input type="checkbox" name="notifications[]" value="2" <?php echo in_array('2', $notification) ? 'checked' : '';?>><span class="checkmark"></span> Allow news event notifications based on my experience profile
                                         </span>
                                    </p>
                                    <p>
                                         <span class="chkbox">
                                             <input type="checkbox" name="notifications[]" value="3" <?php echo in_array('3', $notification) ? 'checked' : '';?>><span class="checkmark"></span> Allow notifications when I am near an event based on my experience profile
                                         </span>
                                    </p>
                                </div>
                            </div>
                            <div class="tab-row1">
                                <p>
                                    <input type="submit" name="notificationUpdate" value="UPDATE" class="save-value">
                                    <input onClick="closeNotificationEditView()" type="button" value="CANCEL" class="cancel-value">
                                </p>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
        <!-- #content-area -->

    </div>
    <!-- #End Main -->

    <?php get_footer(); ?>

<script>

    $(function () {
        $('#phone').mask('(000) 000-0000');

        $('#PwdInfoForm').validate({

            rules: {

                old_pwd: {
                    required: true
                },
                new_pwd: {
                    required: true,
                    pwdcheck: true,
                    noSpace: true
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

        $('#PerInfoForm').validate();
    });

    jQuery.validator.addMethod("pwdcheck", function(value, element) {
        //$('#span_old_pwd').css('display', 'none');
        return this.optional(element) || /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(value);
    }, "");

    jQuery.validator.addMethod("pwdcheck1", function(value, element) {
        $('#span_new_pwd').css('display', 'none');
        return this.optional(element) || /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}/.test(value);
    }, "Letters only please");

    jQuery.validator.addMethod("pwdcheck2", function(value, element) {
        $('#span_cpwd').css('display', 'none');
        return this.optional(element) || /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(value);
    }, "Letters only please");

    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value.indexOf(" ") < 0;
    }, "No space allowed please.");

    function openPerInfoEditView(){

        jQuery('#perInfoDefaultDiv').slideUp().hide();
        jQuery('#PerInfo').show();
    }

    function openPerPassEditView(){

        jQuery('#perPassDefaultDiv').slideUp().hide();
        jQuery('#PerPass').show();
    }

    function openNotificationEditView(){

        jQuery('#notificationDefaultView').slideUp().hide();
        jQuery('#notificationEditView').show();
    }

    function closeNotificationEditView(){

        jQuery('#notificationEditView').slideUp().hide();
        jQuery('#notificationDefaultView').show();
    }


    function openPerPaymentEditView(){

        jQuery('#perPaymentDefaultDiv').slideUp().hide();
        jQuery('#PerPaymentDiv').show();
    }

    function closePerInfoEditView(){

        jQuery('#PerInfo').slideUp().hide();
        jQuery('#perInfoDefaultDiv').show();
    }

    function closePerPassEditView(){

        jQuery('#PerPass').slideUp().hide();
        jQuery('#perPassDefaultDiv').show();
    }

    function closePerPaymentEditView(){

        jQuery('#PerPaymentDiv').slideUp().hide();
        jQuery('#perPaymentDefaultDiv').show();
    }

    function showhelp(id) {
       $('#'+id).show();
    }

    function hidehelp(id) {
        $('#'+id).hide();
    }

    //$(document).on('change', '#country', function () {
    //    var country_id = $(this).val();
    //    $.ajax({
    //        url: '<?php //echo site_url().'/wp-content/themes/Divi/ajaxfile.php?param=getstates'?>//',
    //        type: 'POST',
    //        data: {country_id: country_id},
    //        success: function (response) {
    //            if(response) {
    //                $('#state').html(response);
    //            }
    //        }
    //    });
    //});
</script>
