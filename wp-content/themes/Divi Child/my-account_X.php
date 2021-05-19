<?php 
//echo $_SESSION['Api_token'];die;
/*
echo "<pre>"; print_r($_SESSION); die();
when social login 
Array
(
    [appname] => facebook
    [mo_login] => 
    [social_app_name] => facebook
    [social_user_id] => 636676976822386
    [user_email] => vishal.mehta@reputation.ca
)
*/
/*
Template Name: My Account Page
*/

$token   =  $_SESSION['Api_token'];

if(isset($_POST['perInfoUpdate'])) {

    extract($_POST);
    $data    = array(
        'first'     =>  $first,
        'last'      =>  $last,
        'email'     =>  $email,
        'phone'     =>  $phone,
        'age_range' =>  $age_range
    );

    $payload = json_encode($data);
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
    $apirespons = json_decode($result);	
	
	echo '<pre>';print_r($apirespons); echo "</pre>";
}

$userdata = wp_get_current_user();
$usermatadata = get_user_meta ($userdata->ID);

$user_info = $userdata->ID ? new WP_User($userdata->ID) : wp_get_current_user();
$ch      = curl_init(API_URL.'users/'.$usermatadata['uid'][0]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);



function km_get_users_name( $user_id = null ) {
	
	if ( $user_info->first_name ) {
		if ( $user_info->last_name ) {
			return $user_info->first_name . ' ' . $user_info->last_name;
		}
		return $user_info->first_name;
	}
	return $user_info->display_name;
}

get_header(); 

?>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
<style>

    .error {
        color: red; adding-left: 10px;padding-bottom: 10px;
    }
</style>
    <div id="main-content">
        <div class="outer-wrapper ">
            <div class="container container-home">
                <div class="account-outer">
                    <!--   Personal info end --->
                  <!--  <div class="login-form account-info" id="perInfoDefaultDiv">
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Personal Information</h3> <span style="cursor:pointer" class="edit" onclick="openPerInfoEditView()"><img src="<?php /*echo site_url(); */?>/wp-content/uploads/2019/07/edit.png">Edit</span>
                            </div>
                            <div class="tab-row1">
                                <strong><?/*=$user_info->first_name*/?> <?/*=$user_info->last_name*/?> </strong>
                                <p>Newmarket, Ontario, Canada</p>
                            </div>
                            <div class="tab-row1">
                                <strong>Mobile Number </strong>
                                <p>
                                    <?/*=$usermatadata['phone'][0]*/?>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Email </strong>
                                <p>
                                    <?/*=$userdata->data->user_email*/?>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Age </strong>
                                <p>
                                    <?/*=$usermatadata['age_range'][0]*/?>
                                </p>
                            </div>
                        </div>
                    </div>-->
                    <!--   Personal info end --->
                    
                     <div class="login-form account-info" id="PerInfo">
					 <form action="" method="post" id="PerInfoForm">
                        <!--   Personal info edit start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Personal Information</h3> </div>
                            <div class="tab-row1">
                                <input type="text" name="first" value="<?=$user_info->first_name?>" required>
                                <input type="text" name="last" value="<?=$user_info->last_name?>" required>
                                <input type="hidden" value="<?php echo $usermatadata['uid'][0];?>" name="hidden_user_id">
                                <input type="text" name="address" placeholder="Street Address">
                                <select name="country_id">
                                    <option value="">Select Country</option>
                                    <option value="CA">Canada</option>
                                    <option value="US">United State</option>
                                </select>
                                <input name="province_id" type="text" placeholder="Province/State">
                                <input name="city" type="text" placeholder="City">
                            </div>
                            <div class="tab-row1">
                                <strong>Mobile Number </strong>
                                <p>
                                    <input type="text" name="phone" id="phone" value="<?=$usermatadata['phone'][0]?>" required placeholder="(XXX) XXX-XXXX">
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Email </strong>
                                <p>
                                    <input type="text" name="email" value="<?=$userdata->data->user_email?>" required>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <strong>Age </strong>
                                <p>
                                    <select name="age_range" required>
                                        <option value="">Age Range</option>
                                        <option value="13-17" <?php echo ($usermatadata['age_range'][0] == '13-17' ? 'selected' : '')?>>13-17</option>
                                        <option value="18-29" <?php echo ($usermatadata['age_range'][0] == '18-19' ? 'selected' : '')?>>18-29</option>
                                        <option value="30-39" <?php echo ($usermatadata['age_range'][0] == '30-39' ? 'selected' : '')?>>30-39</option>
                                        <option value="40-49" <?php echo ($usermatadata['age_range'][0] == '40-49' ? 'selected' : '')?>>40-49</option>
                                        <option value="50-59" <?php echo ($usermatadata['age_range'][0] == '50-59' ? 'selected' : '')?>>50-59</option>
                                        <option value="60-69" <?php echo ($usermatadata['age_range'][0] == '60-69' ? 'selected' : '')?>>60-69</option>
                                        <option value="70-79" <?php echo ($usermatadata['age_range'][0] == '70-79' ? 'selected' : '')?>>70-79</option>
                                        <option value="80-89" <?php echo ($usermatadata['age_range'][0] == '80-89' ? 'selected' : '')?>>80-89</option>
                                        <option value="90-99" <?php echo ($usermatadata['age_range'][0] == '90-99' ? 'selected' : '')?>>90-99</option>
                                        <option value="over 100" <?php echo ($usermatadata['age_range'][0] == 'Over 100' ? 'selected' : '')?>>Over 100 (Congratulations)</option>
                                    </select>
                                </p>
                            </div>
                            <div class="tab-row1">
                                <p>
                                    <input type="submit" name="perInfoUpdate" value="UPDATE" class="save-value">
                                    <input onClick="closePerInfoEditView()" type="button" value="CANCEL" class="cancel-value">
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
                                <strong>Password has been set </strong>
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
                                    <h3> Password</h3></div>
                                <div class="tab-row1">
                                    <strong>Password has been set </strong>
                                    <p>Choose a Strong, unique password atleast 8 characters long.</p>
                                    <p>
                                        <input type="password" name="old_pwd" placeholder="Old Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required onfocus="showhelp('span_old_pwd');" onfocusout="hidehelp('span_old_pwd')">
                                    </p>
                                    <span id="span_old_pwd" style="display: none;"><b>Must contain at least one number and one uppercase and lowercase letter and at least 8 or more characters.</b></span>
                                    <p>
                                        <input type="password" name="new_pwd" placeholder="New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required onfocus="showhelp('span_new_pwd');" onfocusout="hidehelp('span_new_pwd')">
                                    </p>
                                    <span id="span_new_pwd" style="display: none;"><b>Must contain at least one number and one uppercase and lowercase letter and at least 8 or more characters.</b></span>
                                    <p>
                                        <input type="password" name="cpwd" placeholder="Confirm Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required onfocus="showhelp('span_cpwd');" onfocusout="hidehelp('span_cpwd')">
                                    </p>
                                    <span id="span_cpwd" style="display: none;"><b>Must contain at least one number and one uppercase and lowercase letter and at least 8 or more characters.</b></span>
                                </div>
                                <div class="tab-row1">
                                    <p>
                                        <!--<input onClick="jQuery('#PerPass').hide();" type="button" value="UPDATE" class="save-value">-->
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
                                <h3> Payments Methods</h3><span style="cursor:pointer" class="edit" onClick="openPerPaymentEditView();"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
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
                                <h3> Payments Methods</h3></div>
                            <div class="tab-row1">
                                <p>
                                    <input type="text" value="" placeholder="Paypal j***a@snapd.com (Primary)">
                                </p>
                                <p>Add New + </p>
                            </div>
                            <div class="tab-row1">
                                <p>
                                    <input onClick="jQuery('#PerPaymentDiv').hide();" type="button" value="UPDATE" class="save-value">
                                    <input onClick="closePerPaymentEditView()" type="button" value="CANCEL" class="cancel-value">
                                </p>
                            </div>
                        </div>
                    </div>
                    <!--   Payments edit end --->
                    <div class="login-form account-info">
                        <!--   Notifications  start --->
                        <div class="tab-1">
                            <div class="tab-header">
                                <h3> Notifications</h3><span style="display:none;" class="edit"><img src="<?php echo site_url(); ?>/wp-content/uploads/2019/07/edit.png">Edit</span></div>
                            <div class="tab-row1">
                                <p>
                                     <span class="chkbox"><input type="checkbox" checked="checked" name="remember"> <span class="checkmark"></span>Allow FAV event notifications 3 days prior</span></p>
                                <p>
                                     <span class="chkbox"><input type="checkbox" checked="checked" name="remember"><span class="checkmark"></span> Allow news event notifications based on my experience profile</span></p>
                                <p>
                                     <span class="chkbox"><input type="checkbox" checked="checked" name="remember"><span class="checkmark"></span> Allow notifications when I am near an event based on my experience profile</span></p>
                            </div>
                        </div>
                    </div>
                    
                
            </div>
        </div>
        <!-- #content-area -->

    </div>
    <!-- #End Main -->

    <?php get_footer(); ?>

<script>

    $(function () {

        $('#phone').mask('(000) 000-0000');
        //$("#PwdInfoForm").validate();
    });

    function openPerInfoEditView(){

        jQuery('#perInfoDefaultDiv').slideUp().hide();
        jQuery('#PerInfo').show();
    }

    function openPerPassEditView(){

        jQuery('#perPassDefaultDiv').slideUp().hide();
        jQuery('#PerPass').show();
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
</script>
