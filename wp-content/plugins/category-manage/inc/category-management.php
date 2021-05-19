<?php
define( 'WP_DEBUG', true );
    $user = array(
        'email' => 'uvtesting123@gmail.com',
        'password' => 'Sony12345'
    );
    $payload = json_encode($user);

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

    if($apirespons->success) {
        $token = $apirespons->token;
    } else {
        $token = '';
    }

    if(isset($_POST['btnSubmit'])) {

        if($_FILES['cat_image']['name'] != '') {

            $type = pathinfo($_FILES['cat_image']['name'], PATHINFO_EXTENSION);
            $content_type = 'image/'.$type;          

            $data = file_get_contents($_FILES['cat_image']['tmp_name']);       
            $base64 = base64_encode($data);        
			$imgname = date('Ymd').time().rand(0, 9999).".".$type;

            $img_data = array(
                'name' => $imgname,
                'contentType' => $content_type,
                'data' => $base64
            );        
           
		    if(move_uploaded_file($_FILES['cat_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/category-manage/inc/uploads/".$imgname)){
				
				$payload = json_encode($img_data);
				$ch      = curl_init(API_URL.'files');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length:' . strlen($payload),
					'Authorization: ' . $token
				));

				$result2 = curl_exec($ch);
				curl_close($ch);
				$imgresponse = json_decode($result2);
				$file = $imgresponse->file;   
			}	
			
           
        }

        $data    = array(
              'name' => $_POST['cat_name'],
              'translation_code'=> $_POST['cat_desc'],
              'status' => 'active'
        );
		$insertdata=array(
							'title' => $_POST['cat_name'],
								'description' => $_POST['cat_desc'],
								'status' => 'active'
							);

          if(isset($file->id)){
            $data['image_id'] = $file->id;
			$insertdata['image_id'] = $imgname;
          }

          $payload = json_encode($data);
          $ch      = curl_init(API_URL.'categories');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLINFO_HEADER_OUT, true);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Content-Length:' . strlen($payload),
              'Authorization: ' . $token
          ));

          $result1 = curl_exec($ch);
          curl_close($ch);
          $catresponse = json_decode($result1);
		  
		 
          if($catresponse->success) {
			  $insertdata['api_cat_id'] = $catresponse->category->id;
			$wpdb->insert('api_category', $insertdata); 
			  $msg = 'Created successfully!';

          } else {

              $error = 'Error in creation.';
          }
   }

    if(isset($_GET['edit_id']) && $_GET['edit_id']!='') {

        $id = $_GET['edit_id'];

        if(isset($_POST['btnUpdate'])){

            if($_FILES['cat_image']['name'] != '') {

                $type = pathinfo($_FILES['cat_image']['name'], PATHINFO_EXTENSION);
                $content_type = 'image/'.$type;          
    
                $data = file_get_contents($_FILES['cat_image']['tmp_name']);       
                $base64 = base64_encode($data);           
    
                  
				$imgname = date('Ymd').time().rand(0, 9999).".".$type;
				$img_data = array(
					'name' => $imgname,
					'contentType' => $content_type,
					'data' => $base64
				);        
			   
				if(move_uploaded_file($_FILES['cat_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/category-manage/inc/uploads/".$imgname)){
					
					$payload = json_encode($img_data);
					$ch      = curl_init(API_URL.'files');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLINFO_HEADER_OUT, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length:' . strlen($payload),
						'Authorization: ' . $token
					));
		
					$result7 = curl_exec($ch);
					curl_close($ch);
					$imgresponse = json_decode($result7);
					$file = $imgresponse->file;             
				}
               
            }    

            $udata  = array(
                'name'   =>  $_POST['cat_name'],
                'translation_code'=> $_POST['cat_desc'],
                'status'   =>  'active'
            );
			
			$wpcatupdated = array(
								'title' => $_POST['cat_name'],
								'description' => $_POST['cat_desc'],
								'status' => 'active'
							);

            
            if(isset($file->id)) {

                $udata['image_id'] = $file->id;
				$wpcatupdated['image_id']=$imgname;
            }

            $payload = json_encode($udata);
            $ch      = curl_init(API_URL.'categories/'.$id);
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

            $result4 = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result4);           
            $msg ='Updated successfully !';
			$wpdb->update('api_category',$wpcatupdated,array('api_cat_id' => $id) ); 
        }

        /* $ch   = curl_init(API_URL.'categories/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . $token
        ));
        $result3 = curl_exec($ch);
        curl_close($ch);
        $category=json_decode($result3);
        $category_info = $category->category;  */
		
        $category_info = $wpdb->get_row( "SELECT * FROM api_category where api_cat_id=".$id);
			/* echo "<pre>"; print_r($category_info); die; */
    }

    if(isset($_GET['del_id']) && $_GET['del_id'] !='') {

        $id = $_GET['del_id'];

        $ch = curl_init(API_URL.'categories/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . $token
        ));

        $result5 = curl_exec($ch);
        curl_close($ch);
        $categoryDelete=json_decode($result5);

		$wpdb->delete('api_category',array('api_cat_id' => $id) ); 
        $msg = 'Deleted successfully !';
    }

    $ch   = curl_init(API_URL.'categories?sort=ASC&sortType=name');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: ' . $token
    ));
    $result2 = curl_exec($ch);
    curl_close($ch);
    $categoryList=json_decode($result2);

    /* echo '<pre>';print_r($categoryList);die; */

?>
<div class="container-fluid">
    
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
		
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <?php
    if(isset($msg) && $msg != '') { ?>
        <div class="alert alert-success">
            <p><?php echo $msg;?></p>
        </div>
    <?php }
    ?>

    <?php
    if(isset($error) && $error != '') { ?>
        <div class="alert alert-danger">
            <p><?php echo $error;?></p>
        </div>
    <?php }
    ?>
    <div class="row mt-3">
        <div class="col-md-3">
			<?php if(isset($category_info)) { ?>
            <h3>Edit Category  </h2>
			<?php }else{ ?>
            <h3>New Category   </h2>
            <?php } ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="cat_name"><b>Category Name:</b></label>
                        <input type="text" class="form-control" name="cat_name" placeholder="Category Name" required value="<?php echo isset($category_info) ? $category_info->title : ''?>">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="cat_desc"><b>Description:</b></label>
                        <textarea name="cat_desc" id="cat_desc" class="form-control" cols="2" rows="1"><?php echo isset($category_info) ?  $category_info->description : '';?></textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="cat_image"><b>Image:</b></label>
                        <input type="file" name="cat_image" id="cat_image" class="form-control" accept="image/*">
                    </div>
					<div class="form-group col-md-12" style="display:none;" id="div_image">
                        <img src="" alt="" style="width: 80px;" id="cat_img_src">
                    </div>
                    <div class="form-group col-md-12">
                        <?php if(isset($category_info) && isset($_GET['edit_id'])) { ?>
							<div class="form-group col-md-12" id="div_image">
								<!--img src="<?php //echo isset($row->image->bucket) ? 'https://storage.cloud.google.com/'.$row->image->bucket.'/'.$row->image->filename : '';?>" alt="" style="width: 80px;" id="cat_img_src"-->
								<img src="<?php echo site_url(); ?>/wp-content/plugins/category-manage/inc/uploads/<?php echo $category_info->image_id ?>" alt="" style="width: 80px;" id="cat_img_src">
							</div>
                            <button type="submit" name="btnUpdate" class="btn btn-primary">Update</button>
                        <?php } else { ?>
							<div class="form-group col-md-12" style="display:none;" id="div_image">
								<img src="" alt="" style="width: 80px;" id="cat_img_src">
							</div>
                            <button type="submit" name="btnSubmit" class="btn btn-primary">Submit</button>
                        <?php }?>
                    </div>
                    
                </div>
            </form>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-8">
            <h3>Categories List</h2>
            <table class="table table-striped" id="category_table" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Sort #</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Thumbnail</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i=1;
                // foreach($categoryList->categories as $row) {  
				$categoryList = $wpdb->get_results("SELECT * FROM api_category order by title ASC");
				foreach ( $categoryList as $row ) {
				?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo $row->title;?></td>                        
                        <td><?php echo wordwrap($row->description, 50, '<br/>');?></td>
                        <td>
						<!--img src="<?php //echo isset($row->image->bucket) ? 'https://storage.cloud.google.com/'.$row->image->bucket.'/'.$row->image->filename : '/wp-content/uploads/2019/06/f1.jpg';?>" alt="" style="width: 60px;"-->
						<img src="<?php echo site_url(); ?>/wp-content/plugins/category-manage/inc/uploads/<?php echo $row->image_id ?>" alt="" style="width: 60px;">
						</td>
                        <td>
                            <a href="admin.php?page=category_management&edit_id=<?php echo $row->api_cat_id;?>" class="btn btn-primary btn-sm">Edit</a> |
                            <a onclick="return confirm('Are you sure?')" href="admin.php?page=category_management&del_id=<?php echo $row->api_cat_id;?>" class="btn btn-danger btn-sm">Del</a>
                        </td>
                    </tr>

                <?php $i++;}
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Sort #</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Thumbnail</th>
                    <th>Action</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>

    $(document).ready(function() {

        $('#category_table').DataTable({

            "ordering": true,
            "info":     false,
        });
    } );

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {               
                $('#div_image').show();
                $('#cat_img_src').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#cat_image").change(function() {       
        readURL(this);
    });
</script>
