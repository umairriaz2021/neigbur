 <?php
 require('../../../wp-config.php');
 global $wpdb;
 $state = '';
 if(isset($_POST["countryid"])){
    $states = $wpdb->get_results("Select * from wp_states");
    $state .= '<option value="">Province*</option>';
      if($_POST["countryid"] == '2'){
           foreach($states as $row) {
               if($row->id >= "2" && $row->id <= "14"){
                   $state .= '<option value="'.$row->id.'">'.$row->name.'</option>';
               }
           }

      }
      else if($_POST["countryid"] == '3'){
           foreach($states as $row) {
               if($row->id >= "15" && $row->id <= "65"){
                   $state .= '<option value="'.$row->id.'">'.$row->name.'</option>';
               }
           }
      }
      else if($_POST["countryid"] != '3' && $_POST["countryid"] != '2'){
          foreach($states as $row) {
            if($row->id >= "2" && $row->id <= "65"){
              $state .= '<option value="'.$row->id.'">'.$row->name.'</option>';
            }
          }
      }
      echo $state;
 }
 ?>
