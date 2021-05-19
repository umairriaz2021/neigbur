<?php

require('../../../../wp-config.php');
require('../../../../wp-blog-header.php');

global $wpdb;

if(isset($_GET['param']) && $_GET['param'] == 'getstates') {

    $country_id =   $_POST['country_id'];
    $state  =   $wpdb->get_results("Select * from wp_states where country_id = $country_id"); ?>

    <option value="">Select...</option>
    <?php foreach($state as $row) { ?>

        <option value="<?php echo $row->id?>"><?php echo $row->name;?> </option>

    <?php }

}
