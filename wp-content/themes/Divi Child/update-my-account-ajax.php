<?php
require('../../../wp-config.php');
require('../../../wp-blog-header.php');

$userdata = wp_get_current_user();  /* it will give you current login user data like id that is use in next function */

/* here user wp_user_update or update user meta to update the userdata  */