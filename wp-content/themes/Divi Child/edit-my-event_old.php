<?php 
/*
Template Name: Edit Event
*/

 if(!isset($_SESSION['Api_token'])){
	wp_redirect( site_url().'?page_id=187' );
	exit;
}
global $wpdb;
$token   =  $_SESSION['Api_token'];
$countries = $wpdb->get_results("Select * from wp_countries");

$ch      = curl_init(API_URL.'categories');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $token
));
$result = curl_exec($ch);
curl_close($ch);
$apirespons=json_decode($result);

if($apirespons->success) {
    $categories = $apirespons->categories;
}

get_header();

?>

<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/1.1.1/jquery.datetimepicker.min.css">
<link rel="stylesheet" href="<?php echo site_url()?>/wp-content/themes/Divi Child/selec2css/select2.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/1.1.1/jquery.datetimepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
<script src="<?php echo site_url()?>/wp-content/themes/Divi Child/js/select2.js"></script>



<style>
   .error {
   color: red;
   }
</style>
<div id="main-content">
   <div class="outer-wrapper ">
      <div class="container container-home">
         <h3 class="h3-title">Edit Your Big Event</h3>
         <ul class="progressbar">
            <li class="active">Page Design</li>
            <li>Option and Submit</li>
         </ul>
         <p style="float:left;width:100%;text-align:center;margin-bottom:10px;font-size: 15px;">Complete each required section; Select NEXT to proceed.</p>
         <hr/>
         <div class="event-type">
           <span class="radio-chk"> <input class="check-radio" type="radio" name="event" value="Single-day-Event" checked="checked" id="check_single"><span class="checkmark1"> </span> Single day Event</span>
          <span class="radio-chk">  <input class="check-radio" type="radio" name="event" value="Multi-day-event" id="check_multi"><span class="checkmark1"> </span> Multi-day event</span>
          
            <span>This event will start and end on same date</span>		    	
            <span class="event-preview"> <a href="<?php echo site_url(); ?>/preview-event/"> PREVIEW <i class="fa fa-eye"></i></a></span> <br/>
             <form  id="event_form" method="post" action="<?php echo site_url(); ?>?page_id=364" enctype="multipart/form-data">
                <div class="event-dates">
                   <label class="start-date" for="">Start <input type="text" required class="start_datepicker single_start_date" name="event_start_date[]" placeholder="Select Start Date/Time" style="cursor:pointer" value="<?php echo date('M d, Y H:i a')?>"></label>
                   <label class="start-date" for="">End <input type="text" required class="end_datepicker single_end_date" name="event_end_date[]" placeholder="Select End Date/Time" style="cursor:pointer" value="<?php echo date('M d, Y H:i a')?>"></label>
                </div>

            <a href="#" style="display: none;" id="add_more"><span> + </span>Add a new date</a>
         </div>
         <hr/>
          <p><b>All fields are required marked with (*).</b></p>
         <div class="event-detail">

               <div class="upload-image">
				<img src="<?php echo site_url(); ?>/wp-content/uploads/2019/08/r1.jpg" alt="uplaod images"><span class="remove-img">-</span>
                   </div>
               <div class="event-details">
                  <input type="hidden" name="start" class="start_date">
                  <input type="hidden" name="end" class="end_date">
                  
                  <input style="width:99%;" type="text" name="title" placeholder="Event Title *" value="My Sample Title" required oninvalid="scroll_to_validator(this)">
                  <input type="hidden" value="0" id="count">
                  <div class="evnt-dates">
                     <p><span id="span_start_date"><?php echo date('M d, Y H:i a')?></span>&nbsp;&nbsp; <span id="span_end_date"><?php echo date('M d, Y H:i a')?></span></p>
                  </div>
                  <input style="width:99%;" type="text" name="address1" placeholder="Venue *" value="Big blue hall" required oninvalid="scroll_to_validator(this)">
                  <input style="width:99%;"  type="text" name="address2" value="120 somewhere St." placeholder="Address">
                  
                  <select class="Country" name="country" id="country" required autocomplete="off">
                     <option value="Canada">Select Country *</option>
                     <?php foreach($countries as $row){ ?>
                     <option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
                     <?php }?>
                  </select>
                  
                  <select class="State" id="state" name="state" required autocomplete="off">
                     <option value="Ontario">Province/State *</option>
                  </select>
                  
                  <input type="text" name="city" placeholder="City *" value="Toronto" required>
                  <input type="text" name="postalcode" placeholder="Postal/Zip Code *" value="1A1 A1A" required oninvalid="scroll_to_validator(this)" pattern="[A-Za-z][0-9][A-Za-z] [0-9][A-Za-z][0-9]" title="">
                  <textarea rows="4" placeholder="Description" name="description">Curabitur ultrices ex quis dictum fringilla. Nam sagittis lectus eu enim fringilla, ac ornare libero sollicitudin.</textarea>
                  <select name="category_id[]" id="category1_id" required multiple="multiple" data-placeholder="Select Category *">
                     <option value="Cat3">Category *</option>
                     <?php foreach($categories as $row) { ?>
                         <option value="<?php echo $row->id?>"><?php echo $row->name;?></option>
                     <?php }?>
                  </select>
                   <span class="multiple"><b>You can select multiple categories</b></span>
                  <p class="get_ticket">
                    <!--   <a class="get-tkt" href="<?php echo site_url(); ?>/get-tickets/">Get Tickets</a> -->
					 <input type="button" name="create-tkt" value="Get Tickets" class="get-tkt"> 
                     <span class="chkbox"> <input class="tix-tkt" type="checkbox" checked="checked" name="remember">
                     <span class="checkmark"></span> I would like to create tickets for this event using snapd TIX or a thrid party provider</span>
                  </p>
                  <p class="tkt-active"><i class="fa fa-check-circle" aria-hidden="true"></i> Ticket Active</p>
                  <input type="button" name="create-tkt" value="Modify Tickets" class="modify-tkt">
                  <h3>Contact Details</h3>
                  <input style="width:99%;" type="text" placeholder="Organization" value="Music tour canada" name="org">
                  <input style="width:99%;" type="text" placeholder="Website URL" value="www.snapd.com" name="website_url">
                  <p class="exclude">
                     <input type="text" value="Jane Deo" placeholder="Full Name *" name="contact_name" value="Jane Deo" required>
                     <span class="chkbox">Exclude Name from public listing
                     <input class="tix-tkt" type="checkbox" checked="checked">
                     <span class="checkmark"></span> </span>
                  </p>
                  <p class="exclude">
                     <input type="text" placeholder="(XXX) XXX-XXXX *" name="contact_phone" value="(415)-555-5555" id="contact_phone" required>
                     <span class="chkbox">Exclude Phone from public listing
                     <input class="tix-tkt" type="checkbox" checked="checked">
                     <span class="checkmark"></span></span>
                  </p>
                  <p class="exclude">
                     <input type="text" value="EXT" placeholder="Extension" name="extension">
                     <span class="chkbox">Exclude Extension from public listing
                     <input class="tix-tkt" type="checkbox" checked="checked">
                     <span class="checkmark"></span> </span>
                  </p>
                  <p class="exclude">
                     <input type="email" value="janedeo@musictour.ca" placeholder="Email *" name="email" required >
                     <span class="chkbox">Exclude Email from public listing
                     <input class="tix-tkt" type="checkbox" checked="checked">
                     <span class="checkmark"></span></span>
                  </p>
                  <div class="attachemnts">
                     <div class="logo-details">
                        <div class="up-logo">
                           <h3>Logo</h3>
						   <div class="edit-logo">
							   <img id="logo_image_prev" src="<?php echo site_url(); ?>/wp-content/uploads/2019/09/logo.png" alt="uplaod images"><span class="remove-img">-</span>
							  <!-- <input type="file" id="logo_image" name="logo_image" style="position: relative;z-index: 999999999999;width: 212px;opacity: 0;margin-top:-70px;"> -->
						   </div>
                        </div>
                  <!--      <div class="up-attach">
                           <h3>Attachment</h3>
                           <span>Include a single PDF for all instructions, waivers, map etc...</span>
						   <div>
							   <img src="<?php //echo site_url(); ?>/wp-content/themes/Divi Child/img/pdfupload.jpg" alt="uplaod images">
							   <input type="file" name="attach_image" style="position: relative;z-index: 999999999999;width: 212px;opacity: 0;margin-top:-70px;">
							</div>
                        </div> 
                        <p class="adober">Adobe Reader Required.</p>
                         <a href="#"> Download here</a> -->
                     </div>  
                     <div class="map-details">
                        <h3>Map Details</h3>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d184551.80858184173!2d-79.51814199446795!3d43.718403811497105!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4cb90d7c63ba5%3A0x323555502ab4c477!2sToronto%2C%20ON%2C%20Canada!5e0!3m2!1sen!2sin!4v1568367410679!5m2!1sen!2sin" width="100%" height="290" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                     </div>
                  </div>
               </div>
               <button class="next-btn" type="submit" name="btnSubmit">NEXT</button>
            </form>
            <!--<div class="next-btn"><a href="<?php /*echo site_url(); */?>/create-tickets/">NEXT</a></div>-->
            <div class="help-btn"><i class="fa fa-question-circle"></i> Need Help? <a href="#">Visit our support site for answers</a></div>
         </div>
      </div>
      <!-- #container -->	
   </div>
   <!-- #outer-wrapper -->
</div>
<!-- #main content -->

<?php get_footer(); ?>

<script>

jQuery(document).ready(function(){
	
	jQuery("#logo_image").change(function(){
		if(this.files[0]!=undefined){
			var file  = this.files[0];
			getUploadImageUrl(this);
		}
	});
});

function getUploadImageUrl(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
		jQuery("#logo_image_prev").attr("src",e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}
    $(document).on('change', '#country', function () {
        var country_id = $(this).val();
        $.ajax({
            url: '<?php echo site_url().'/wp-content/themes/Divi/ajaxfile.php?param=getstates'?>',
            type: 'POST',
            data: {country_id: country_id},
            success: function (response) {
                if(response) {
                    $('#state').html(response);
                }
            }
        });
    });

    function scroll_to_validator(input)
    {
        input.focus();
    }

    $( function() {
        $('#category1_id').select2()
        $('#contact_phone').mask('(000) 000-0000');

        /*
                $("#event_form").validate({
                    rules: {
                        title: {
                            required: true
                        },
                        address1: {

                            required: true
                        },
                        country: {

                            required: true
                        },

                        state: {

                            required: true
                        },

                        city: {

                            required: true
                        },

                        postalcode: {

                            required: true
                        },
                        category1_id: {

                            required: true
                        },

                        contact_name: {

                            required: true
                        },

                        contact_phone: {

                            required: true
                        },

                        email: {

                            required: true
                        }
                    },

                    messages :{

                        title: "Please enter title",
                        address1: "Please enter venue",
                        country: "Please select Country",
                        state: "Please select state",
                        city: "Please enter city",
                        postalcode: "Please enter postalcode",
                        category1_id: "Please select category",
                        contact_name: "Please enter contact name",
                        contact_phone: "Please enter contact number",
                        email: "Please enter contact email"
                    }

                });
        */

        $(".single_start_date").datetimepicker({

            minDate: 0,
            step: 15,
            format:'M d, Y H:i a',
            closeOnDateSelect:false,
            closeOnTimeSelect:true,

            onChangeDateTime:function (e) {
                var start_date = $('.single_start_date').val();
               $('#span_start_date').text(start_date);
               $('.start_date').val(start_date);
            }
        });

        $(".single_end_date").datetimepicker({

            minDate: 0,
            step: 15,
            format:'M d, Y H:i a',
            closeOnDateSelect:false,
            closeOnTimeSelect:true,

            onChangeDateTime:function (e) {

                var end_date = $('.single_end_date').val();
                $('#span_end_date').text(end_date);
                $('.end_date').val(end_date);
            }
        });
    } );

    $(document).on('click', '#check_multi', function () {

        $('#add_more').show();
    });

    $(document).on('click', '#check_single', function () {

        $('#add_more').hide();
        $('.add_more_div').hide();
    });

    $(document).on('click', '#add_more', function (e) {

        e.preventDefault();

        var count = $('#count').val();
        var inc_count = parseInt(1)+parseInt(count);
        $('#count').val(inc_count);

        var html = '<div class="add_more_div">\n' +
            '                    <label class="start-date" for="">Start<input type="text" class="start_datepicker multi_start_date" name="event_start_date[]" id="'+inc_count+'" value="<?php echo date('M d, Y H:i a')?>" placeholder="Select Start Date/Time" style="cursor:pointer"></label>\n' +
            '                    <label class="start-date" for="">End<input type="text" class="end_datepicker multi_end_date" name="event_end_date[]" id="'+inc_count+'" value="<?php echo date('M d, Y H:i a')?>" placeholder="Select End Date/Time" style="cursor:pointer"></label>\n' +
            '                <span class="remove-date" style="cursor:pointer;" data-id="'+inc_count+'"> - </span></div>';

        $(this).before(html);

        var span_date = '<p><span id="span_start_date_'+inc_count+'"><?php echo date("M d, Y H:i a");?></span>&nbsp;&nbsp; <span id="span_end_date_'+inc_count+'"><?php echo date("M d, Y H:i a");?></span></p>';
        $('.evnt-dates').append(span_date);

        $(".multi_start_date").datetimepicker({
            minDate: 0,
            step: 15,
            format:'M d, Y H:i a',
            closeOnDateSelect:false,
            closeOnTimeSelect:true,

            onChangeDateTime:function (e) {
             var start_date = $('.multi_start_date').val();
                $("#span_start_date_"+inc_count).text(start_date);
                $('.start_date').val(start_date);
            }
        });

        $(".multi_end_date").datetimepicker({
            minDate: 0,
            step: 15,
            format:'M d, Y H:i a',
            closeOnDateSelect:false,
            closeOnTimeSelect:true,

            onChangeDateTime:function (e) {
                var end_date = $('.multi_end_date').val();
                $("#span_end_date_"+inc_count).text(end_date);
                $('.end_date').val(end_date);
            }
        });
    });
        
         $(document).on('click', '.remove-date', function () {

        var val = $(this).data('id');
        $('#p_'+val).remove();
        $('#multi_div_'+val).remove();
    });
    
  

</script>
