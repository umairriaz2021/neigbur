   var site_url = document.location.origin;
   jQuery.validator.addMethod("notset", function(value, element) {

       return value != 'NOT SET';
   }, "Please select date");

   jQuery.validator.addMethod("postalcode", function(value, element) {

       if($('#country').val() == '2') {

           return this.optional(element) || /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/.test(value);
       } else {
           return true;
       }
   }, "Postalcode is invalid");

   function modifyTkt(event_id) {

       window.location.href = site_url+"?page_id=364&edit="+event_id;
   }

   $(document).on('click', '#tix-tkt', function () {

       if($(this).is(':checked')) {

           $('#div_add_ticket').show();
       } else {

           $('#div_add_ticket').hide();
       }
   });

    $(function () {
      var imagesPreview = function(input, placeToInsertImagePreview) {
        var fileTypes = ['jpg', 'jpeg', 'png'];
        var pdfFileType= ['pdf'];
        //$(placeToInsertImagePreview).empty();

          if (input.files) {
               if(placeToInsertImagePreview == "#pdf_files"){
                 var filesAmount = input.files.length;
                 var extension = input.files[0].name.split('.').pop().toLowerCase();
                 for (i = 0; i < filesAmount; i++) {
                  //$($.parseHTML('<p>')).text(input.files[i].name).append(' <i class="fa fa-check" aria-hidden="true"></i>').appendTo(placeToInsertImagePreview);
                  var reader = new FileReader();
                  reader.onload = function(event) {
                    if(pdfFileType.indexOf(extension) <= -1) {//alert(1)
                       $(input).val('');
                       $(input).siblings("#file_type_err").show();
                       $(input).siblings("#file_err").hide();
                       $(input).siblings("#file_name").hide();
                       $("#pdfdropZone").css("background-image", "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')");
                       $("#dd_text_pdf").show();
                       $("#btnpdf").show();

                    }else if(input.files[0].size > 10485760 ) {
                       $(input).val('');
                       $(input).siblings("#file_type_err").hide();
                       $(input).siblings("#file_err").show();
                       $(input).siblings("#file_name").hide();
                       $("#pdfdropZone").css("background-image", "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')");
                       $("#dd_text_pdf").show();
                       $("#btnpdf").show();

                    } else {
                       $(input).siblings("#file_err").hide();
                       $(input).siblings("#file_type_err").hide();
                       $(input).siblings("#file_name").children("p").html(input.files[0].name);
                       $(input).siblings("#file_name").show();
                       console.log(input.files[0]);
                       $('#check_remove_pdf').val("removepdf");
                       $("#btnpdf").hide();
                       $("#dd_text_pdf").hide();
                       $("#pdfdropZone").css("background-image", "url('"+site_url+"/wp-content/uploads/2019/09/pdf_icon.png')");
                       jQuery(jQuery.parseHTML('<span class="remove-img" id="remove-pdf" style="cursor: pointer;">-</span>')).appendTo('#pdfdropZone');
								jQuery('#remove-pdf').on('click', function() {
									jQuery("#pdfdropZone").css({"background-image": "url('https://webdev.snapd.com/wp-content/uploads/2019/09/upload-image.jpg')", "border": "2px dashed #b9b7b7"});
									$("#dd_text_pdf").show();
                                    $("#btnpdf").show();
									$(this).remove();
									$(input).siblings("#file_name").hide();

								});
                     //jQuery("#modal_image").attr("src",event.target.result);
                     //$($.parseHTML('<img style="width: 100%!important; height: 100%!important;padding-right: 2px;padding-left: 5px; padding-top: 2px; padding-bottom: 7px;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    }
                  }
                  reader.readAsDataURL(input.files[i]);
              }
               }else{
              var filesAmount = input.files.length;
              var extension = input.files[0].name.split('.').pop().toLowerCase();
              for (i = 0; i < filesAmount; i++) {
                  //$($.parseHTML('<p>')).text(input.files[i].name).append(' <i class="fa fa-check" aria-hidden="true"></i>').appendTo(placeToInsertImagePreview);
                  var reader = new FileReader();
                  reader.onload = function(event) {
                     if(fileTypes.indexOf(extension) <= -1) {
                       $(input).val('');
                       $("#logodropZone").css({"padding-top":"50px"});
                       $(input).siblings("#file_type_err").show();
                       $(input).siblings("#file_err").hide();
                       $(input).siblings("#file_name").hide();
                       $(input).siblings('#btnicon').show();
                       $('#logodropZone h1').show();
                       if(placeToInsertImagePreview == '#files'){
                       $('#check_remove_img').val("");
					   $('#files').attr('onClick', '$("#fileupload").click()');
					   $("#dropZone").find("h1").show();
                       $("#dropZone").find("button").show();
                       $("#dropZone").css({"background-image": "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')"});
						}
                    }else if(input.files[0].size > 1000000) {
                       $(input).val('');
                       $("#logodropZone").css({"padding-top":"50px"});
                       $(input).siblings("#file_type_err").hide();
                       $(input).siblings("#file_err").show();
                       $(input).siblings("#file_name").hide();
                       $(input).siblings('#btnicon').show();
                       $('#logodropZone h1').show();

                       if(placeToInsertImagePreview == '#files'){
                       $('#check_remove_img').val("");
					   $('#fileshide').attr('onClick', '$("#fileupload").click()');
					   $("#dropZone").find("h1").show();
                       $("#dropZone").find("button").show();
                       $("#dropZone").css({"background-image": "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')"});
						}

                   }else {
                    if(placeToInsertImagePreview == '#files'){

                       $('#check_remove_img').val("removeimg");
						startCroperIfFileOk(input,placeToInsertImagePreview);  /* call image croper */
						$('#fileshide').attr('onClick', '$("#fileupload").click()');
				// 		$("#dropZone").find("h1").hide();
    //                   $("#dropZone").find("button").hide();
    //                   $("#dropZone").css({"background-image": "none"});



                       }
                       $(input).siblings("#file_err").hide();
                       $(input).siblings("#file_type_err").hide();
                       $(input).siblings("#file_name").show();

                    if(placeToInsertImagePreview == '#logo_files'){
                       $('#check_remove_logo').val("removelogo");
                       $(input).siblings('#btnicon').hide();
                       $('#logodropZone h1').hide();
                       $("#logodropZone").css({"background-image": "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')","padding-top":"0px"});
                       jQuery("#modal_image").attr("src",event.target.result);

                       $($.parseHTML('<img class="uploaded-img" onClick="clickit()">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                       jQuery(jQuery.parseHTML('<span class="remove-img" id="removelogo" style="cursor: pointer;">-</span>')).appendTo(placeToInsertImagePreview);
								jQuery('#removelogo').on('click', function() {
									jQuery('#logo_files').empty();
									jQuery('#logo_image').val('');
									$("#logodropZone").css({"padding-top":"50px"});
                                    $(input).siblings("#file_type_err").hide();
                                   // $(input).siblings("#file_err").show();
                                    $(input).siblings("#file_name").hide();
                                    $(input).siblings('#btnicon').show();
                                    $('#logodropZone h1').show();

								});
                    }
                 }
                //	$(input).siblings("#file_err").hide();
                //    jQuery("#modal_image").attr("src",event.target.result);
                //   $($.parseHTML('<img style="width: 100%!important; height: 100%!important;padding-right: 2px;padding-left: 5px; padding-top: 2px; padding-bottom: 7px;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);

                  };

                  reader.readAsDataURL(input.files[i]);
              }
            }
          }

      };

      $('#fileupload').on('change', function() {
          imagesPreview(this, '#files');
      });
      $('#logo_image').on('change', function() {
          imagesPreview(this, '#logo_files');
      });
      $('#pdf_image').on('change', function() {
          imagesPreview(this, '#pdf_files');
      });
     // window.addEventListener("dragover",function(e){
     //   e = e || event;
     //   e.preventDefault();
     // },false);
     // window.addEventListener("drop",function(e){
     //   e = e || event;
     //   e.preventDefault();
     // },false);
   });

   	/* cropper code start */
var cropper;

window.addEventListener('DOMContentLoaded', function() {
	var image = document.getElementById('imageforcrop');
    var $modal = $('#modal');


    $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
			viewMode: 1,
			aspectRatio: 16 / 9,
			autoCropArea: 1,
			restore: true,
			guides: true,
			center: true,
			highlight: true,
			cropBoxMovable: true,
			cropBoxResizable: true,
			toggleDragModeOnDblclick: false,
			scalable: false,
			zoomable: false,
			zoomOnTouch: false,
			zoomOnWheel: false,
		  });

    }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
    });

    jQuery('.cropOptions').on('click', function() {
        var method = jQuery(this).data('method');
        var option = jQuery(this).data('option');

        switch (method) {
            case 'move':
                var secondoption = jQuery(this).data('second-option');
                cropper.move(option, secondoption);
                break;
            case 'zoom':
                cropper.zoom(option);
                break;
            case 'rotate':
                cropper.rotate(option);
                break;
        }
    });

});

function CropClick(previewDiv){

	var $modal = $('#modal');
	$modal.modal('hide');

    var canvas;

	if(cropper){
		canvas = cropper.getCroppedCanvas({
			width: 390,
			height: 260,
		});

		jQuery(previewDiv).find('img').attr('src', canvas.toDataURL());

		/* if(previewDiv == '#logo_files'){
			jQuery('#logo_image_base64').val(canvas.toDataURL());
			jQuery("#modal_logo").attr("src",canvas.toDataURL());
		} */
		if(previewDiv == '#files'){
			jQuery('#fileshide').empty();
			jQuery(jQuery.parseHTML('<img id="eve_image" class="uploaded-img">')).attr("src",canvas.toDataURL()).appendTo('#fileshide');
			jQuery(jQuery.parseHTML('<span class="remove-img" id="removeimg" style="cursor:pointer;">-</span>')).appendTo('#files');
			jQuery(jQuery.parseHTML('<input type="hidden" id="event_image_base64" name="event_image_base64">')).appendTo('#files');

			var base64 = canvas.toDataURL().split(';');
            //console.log(base64);
            var base64Code = base64[1].split(',');
            //console.log(base64Code[1]);
            jQuery('#event_image_base64').val(base64Code[1]);

                       $('#removeimg').on('click', function() {
		               $('#fileshide').empty();
		               $('#fileupload').val('');
		               $("#dropZone").find("h1").show();
                       $("#dropZone").find("button").show();
                       $("#removeimg").remove();
                       $("#dropZone").css("background-image", "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')");
			           });
			jQuery('#fileupload').val(canvas.toDataURL());
			jQuery("#eve_image").attr("src",canvas.toDataURL());



		}
	}

}
function startCroperIfFileOk(inputfile,previewDiv){
	jQuery('#crop').attr('onClick','CropClick("'+previewDiv+'")');

    var image = document.getElementById('imageforcrop');
    var $modal = $('#modal');
    var filescrp = inputfile.files;
    var done = function(url) {
        image.src = url;
        $modal.modal('show');
    };
    var reader;
    var file;
    var url;

    if (filescrp && filescrp.length > 0) {
        file = filescrp[0];

        if (URL) {
            done(URL.createObjectURL(file));
        } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function(e) {
                done(reader.result);
            };
            reader.readAsDataURL(file);
        }
    }
}

/* cropper code ends */

   jQuery(document).ready(function(e){

       e.preventDefault();


       let link = jQuery(".cancel-btn").parent("a").prop("href");
       let eventstate = "<?php echo $_REQUEST['eventstate'];?>";
       let eventid = "<?php echo isset($events) ? base64_encode($events->id) : '';?>";
       let newlink = link+"?state="+eventstate+"&cancelsave="+eventid;
       jQuery(".cancel-btn").parent("a").prop("href",newlink);
   });
   	jQuery("#event_form").submit(function(event){
		if($("#event_form").valid()){
			jQuery('#edit_modal_loader_text').text('Updating event details and images...');
			jQuery('#loadingeditModal').show();
		}
	});


	jQuery('.remove-img , .remove-file').on('click', function() {
	    $('#confirmdelete').css({"display":"block"});
	   var fileid = $(this).attr('id');
	    //$('#confirmtext').text($(this).attr('id'));

	    if(fileid == "remove-img") {
	        $('#confirmtext').text("Are you sure you want to remove event image?");
	         //$('#confirmdelete').css({"display":"none"});
	        $('#yesdelete').attr("onclick","imgDelete()");
	    }
	    if(fileid == "remove-logo") {
	        $('#confirmtext').text("Are you sure you want to remove logo image?");
	        // $('#confirmdelete').css({"display":"none"});
	        $('#yesdelete').attr("onclick","logoDelete()");
	    }
	    if(fileid == "remove-pdf") {
	        $('#confirmtext').text("Are you sure you want to remove PDF file?");
	        // $('#confirmdelete').css({"display":"none"});
	        $('#yesdelete').attr("onclick","pdfDelete()");
	    }
	});



function pdfDelete(){
                    $('#edit_modal_loader_text').text("Removing PDF File...");
                    $('#confirmdelete').css({"display":"none"});
                    $('#loadingeditModal').css({"display":"block"});
                    $('#check_remove_pdf').val("removepdf");
                    $('[name="attach_image"]').val("");
                    empty_pdf_zone();
                    setTimeout(function(){ $('#loadingeditModal').css({"display":"none"}) }, 2000);
                }
function logoDelete() {
                    $('#edit_modal_loader_text').text("Removing Logo Image...");
                    $('#confirmdelete').css({"display":"none"});
		            $('#loadingeditModal').css({"display":"block"});
		            $('#check_remove_logo').val("removelogo");
		            $('[name="logo_image"]').val("");
		            $('#logohide').css({"display":"none"});
		            $('#logodropZone').css({"padding":"50px 0px 0px 0px"});
		            $('#logozoneh1').css({"display":"block"});
		            $('#btnicon').attr("style", "display:inline-block;");
                    setTimeout(function(){ $('#loadingeditModal').css({"display":"none"}) }, 2000);
	            }
function imgDelete() {
                $('#edit_modal_loader_text').text("Removing Event Image...");
                $('#confirmdelete').css({"display":"none"});
		        $('#loadingeditModal').css({"display":"block"});
		        $('#check_remove_img').val("removeimg");
		        $("#fileupload").prop('required','true');
		        $('[name="filetouploadname"]').val("");
		        $('#fileshide').html("");
		        $('#fileshide').attr('onClick', '$("#fileupload").click()');
                setTimeout(function(){ $('#loadingeditModal').css({"display":"none"}) }, 2000);
                jQuery("#dropZone").find("h1").show();
                jQuery("#dropZone").find("button").show();
                jQuery("#dropZone").css({"background-image": "url(http://webdev.snapd.com/wp-content/uploads/2019/09/upload-image.jpg)"});
            }





/*  address auto fill and map script start  */

// This sample uses the Autocomplete widget to help the user select a
// place, then it retrieves the address components associated with that
// place, and then it populates the form fields with those details.
// This sample requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script
// src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var placeSearch, autocomplete;

var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'long_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
  autocomplete = new google.maps.places.Autocomplete(
  document.getElementById('address'), {types: ['geocode']});
  autocomplete.setFields(['address_component']);
  autocomplete.setComponentRestrictions({
    country: ["ca"],
  });
  autocomplete.addListener('place_changed', fillInAddress);

}

function fillInAddress() {
    jQuery('#state, #city, #country, #postalcode').val('');

  var place = autocomplete.getPlace();
//jQuery('#state, #city, #country, #postalcode').val('');

  // Get each component of the address from the place details,
  // and then fill-in the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {

      var value = place.address_components[i][componentForm[addressType]];

	  if(addressType=='street_number'){
		var street_number =value;
	  }
	  if(addressType=='route'){
		  var route = (street_number != undefined)?street_number+' '+value:value;
		jQuery('#address').val(route);
		/* jQuery('#modal_address').text(street_number+' '+value); */
	  }
	  if(addressType=='locality'){
		jQuery('#city').val(value);
		/* jQuery('#modal_city').text(value); */
	  }
	  if(addressType=='postal_code_prefix'){
		  console.log(value);
		jQuery('#postalcode').val(value);
		/* jQuery('#modal_zip').text(value); */
	  }
	  if(addressType=='postal_code'){
		  console.log(value);
		jQuery('#postalcode').val(value);
		/* jQuery('#modal_zip').text(value); */
	  }
	  if(addressType=='administrative_area_level_1'){
		  console.log(value);

		$("#state option[selected]").removeAttr("selected");
		$('#state option').filter(function() { return $.trim( $(this).text() ) == value; }).attr('selected',true);
		/* jQuery('#modal_province').text(value); */
	  }
	  if(addressType=='country'){

		$("#country option[selected]").removeAttr("selected");
		$('#country option').filter(function() { return $.trim( $(this).text() ) == value; }).attr('selected',true);
		/* jQuery('#modal_country').text(value); */
		var country_id = $('#country').val();
        if(country_id == ''){
         country_id = 0;
        }
        if(('#state').val() == ''){
        $.ajax({
            url:"https://webdev.snapd.com/wp-content/themes/Divi Child/get_state.php",
            method:"POST",
            data:"countryid="+country_id,
            success:function(html){
                $('#state').html(html);
                }
        });
        }
	  }
    }
  }

	// alert()
	jQuery('#address').focus().focusout();
	jQuery('#city').focus().focusout();
	jQuery('#postalcode').focus().focusout();
	jQuery('#state').focus().focusout();
	jQuery('#country').focus().focusout();


	console.log(place);
	mapChange();
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate_event(){
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({center: geolocation, radius: position.coords.accuracy});
      autocomplete.setBounds(circle.getBounds());
    });
  }
}

/*  address auto fill end  */


/*  address map script start  */
jQuery(document).ready(function(){
		jQuery('#venue').on('change ', function() {
			mapChange();
		});
		jQuery('#address').on('change', function() {
			mapChange();
		});
		jQuery('#city').on('change', function() {
			mapChange();
		});
		jQuery('#postalcode').on('change', function() {
			mapChange();
		});
    });

	function mapChange(){
		var venue = jQuery('#venue').val();
		var address = jQuery('#address').val();
		var city = jQuery('#city').val();
		var postalcode = jQuery('#postalcode').val();
		var fulladdress = '';
		if(venue !=''){fulladdress += venue+'+'; }
		if(address !=''){fulladdress += address+'+'; }
		if(city !=''){fulladdress += city+'+'; }
		if(postalcode !=''){fulladdress += postalcode+'+'; }
    if (fulladdress != '')
    {
		  jQuery.ajax({
				url: 'https://maps.googleapis.com/maps/api/geocode/json?address='+fulladdress+'&key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw',
				success: function (res){
					if(res.status == "OK"){
						/* console.log(res);
						console.log(res.results[0].geometry.location.lat);
						console.log(res.results[0].geometry.location.lng); */
						var srcc = 'https://webdev.snapd.com/map.php?lat='+res.results[0].geometry.location.lat+'&lng='+res.results[0].geometry.location.lng;
						/* console.log(srcc); */
						jQuery('#mapLat').val(res.results[0].geometry.location.lat);
						jQuery('#mapLong').val(res.results[0].geometry.location.lng);
						jQuery('.mapIframe').attr('src',srcc);
					}
				}
		  });
    }
	}
/*  address map script end  */

function clickit(){
    $('#logo_image').click();
}
function empty_pdf_zone(){
    $("#file_type_err").hide();
   // $("#file_err").show();
    $("#file_name").hide();
    $("#pdfdropZone").css("background-image", "url('"+site_url+"/wp-content/uploads/2019/09/upload-image.jpg')");
    $("#dd_text_pdf").show();
    $("#btnpdf").show();
    $("#remove-pdf").hide();
}

jQuery(document).on('change', '#country', function() {
    var country_id = $('#country').val();
    if(country_id == ''){
         country_id = 0;
    }
    //if($('#state').val() == ''){
        $.ajax({
            url:"https://webdev.snapd.com/wp-content/themes/Divi Child/get_state.php",
            method:"POST",
            data:"countryid="+country_id,
            success:function(html){
                $('#state').html(html);
                }
        });
    //}

});
