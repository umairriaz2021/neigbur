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
  // Create the autocomplete object, restricting the search predictions to
  // geographical location types.
  autocomplete = new google.maps.places.Autocomplete(
      document.getElementById('autocomplete'), {types: ['geocode']});

  // Avoid paying for data that you don't need by restricting the set of
  // place fields that are returned to just the address components.
  autocomplete.setFields(['address_component', 'geometry']);
  autocomplete.setComponentRestrictions({
    country: ["ca"],
  });
  autocomplete.addListener('place_changed', fillInAddress);

}

function fillInAddress()
{
  var place = autocomplete.getPlace();
  console.log("autocomplete place_changed");
  console.log(place.geometry.location.lat());
  console.log(place.geometry.location.lng());
  jQuery('#locLocation').val($('#autocomplete').val());
  jQuery('#locLat').val(place.geometry.location.lat());
  jQuery('#locLong').val(place.geometry.location.lng());
}

jQuery(document).ready(function(){
	/* jQuery('#autocomplete').on('change ', function() {
		fillsaerchLatLong();
	}); */
  $("#autocomplete").on('keydown paste input', function(){
    console.log("** latlong reset");
    jQuery('#locLocation').val("");
    jQuery('#locLat').val("");
		jQuery('#locLong').val("");
    });
});
/*
function fillsaerchLatLong(){
	var autocomplete = jQuery('#autocomplete').val();
	var fulladdress = '';
	if(autocomplete !=''){fulladdress += autocomplete+'+'; }
	alert(fulladdress);
	if(autocomplete !=''){fulladdress = autocomplete; }
	alert(fulladdress);
	jQuery.ajax({
			url: 'https://maps.googleapis.com/maps/api/geocode/json?address='+fulladdress+'&key=AIzaSyAlMWhWMHlxQzuolWb2RrfUeb0JyhhPO9c',
			success: function (res){
				if(res.status == "OK"){
					console.log(res);
					console.log(res.results[0].geometry.location.lat);
					console.log(res.results[0].geometry.location.lng);
					jQuery('#locLat').val(res.results[0].geometry.location.lat);
					jQuery('#locLong').val(res.results[0].geometry.location.lng);
				}
			}
	});
}
 */

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate(){ //alert()
    var autocomplete_val=jQuery('#autocomplete').val();
    var locLat =jQuery('#locLat').val();
   /* if(autocomplete_val !='' ){
        jQuery('.btn_search').prop('disabled', 'disabled');
        jQuery('.btn_search').prop('title', 'Please Select the address from google suggestion');
        jQuery('.btn_search').css('cursor', 'progress');
    }*/
// 	if(jQuery('#autocomplete').val() == 'canada'){
// 	  if (navigator.geolocation) {
// 		navigator.geolocation.getCurrentPosition(function(position) {
// 		  var geolocation = {
// 			lat: position.coords.latitude,
// 			lng: position.coords.longitude
// 		  };
          
// 		  console.log('autosearch fill satrt');
// 		  geocodeLatLng(position.coords.latitude,position.coords.longitude);

// 		});
// 	  }
//   }
}

function geocodeLatLng(lat ,lng) {
	var geocoder = new google.maps.Geocoder;
	var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
	geocoder.geocode({'location': latlng}, function(results, status) {
	  if (status === 'OK') {
		  console.log('ok');
		if (results[0]) {

		  var formatted_address = '';
		  var place = results[0];
		  console.log(place);
		  console.log(place.address_components);
		  for (var i = 0; i < place.address_components.length; i++) {
			  var addressType = place.address_components[i].types[0];

			  var value = place.address_components[i][componentForm[addressType]];

			  if(addressType=='locality'){ formatted_address += value+', ';}
			  if(addressType=='administrative_area_level_1'){ formatted_address += value+', '; }
			  if(addressType=='country'){ formatted_address += value;	}
		  }
		  if(formatted_address != ''){
			  if(jQuery('#autocomplete').val() == ''){
				  jQuery('#autocomplete').val(formatted_address);
				  jQuery('#resultlocation').text(formatted_address);
				  //jQuery('#SearchForm').submit();
          jQuery('#locLocation').val(formatted_address);
				  jQuery('#locLat').val(lat);
				  jQuery('#locLong').val(lng);
          console.log(lat);
          console.log(lng);

				//   jQuery('.btn_search').removeAttr('disabled');
    //               jQuery('.btn_search').removeAttr('title');
    //               jQuery('.btn_search').css('cursor', 'pointer');

			  }
		  }
		} else {
		  window.alert('No results found');
		}
	  } else {
		window.alert('Geocoder failed due to: ' + status);
	  }
	});
  }


/*search form validator statrt*/
jQuery( function() {
	if(jQuery('#SearchForm')){
		jQuery('#SearchForm').validate({
			focusInvalid: false,
			invalidHandler: function(form, validator) {
				var errors = validator.numberOfInvalids();
				if (errors) {
					if(jQuery(validator.errorList[0].element).is(":visible"))
					{
						jQuery('html, body').animate({
							scrollTop: jQuery(validator.errorList[0].element).offset().top-250
						}, 1000);
					}
					else
					{
						jQuery('html, body').animate({
							scrollTop: jQuery("#" + jQuery(validator.errorList[0].element).attr("focusID")).offset().top
						}, 1000);
					}
				}
			},
			rules: {
				 location: "required"
			},
			messages: {
				location: "Enter Location",
			},
			errorPlacement: function (error, element) {
				if (element.parent('.input-group').length) {
					error.insertAfter(element.parent());      // radio/checkbox?
				} else if (element.hasClass('select2-hidden-accessible')) {
					error.insertAfter(element.next('span'));  // select2
					element.next('span').addClass('error').removeClass('valid');
				} else if(element.hasClass('exclude_input')){
					error.insertAfter(element.next('span'));
				} else {
					error.insertAfter(element);               // default
				}
			}
		});
	}
});
/*search form validator ends*/

/*--------------------------------*/
  var filter_div = document.getElementById('filter_div');
  function showfilter(){
      if(filter_div.style.display == 'none'){
    filter_div.style.display = 'block';
      }
    else{
        filter_div.style.display = 'none';
    }
  }
  function closefilter(){
        filter_div.style.display = 'none';
  }
/*--------------------------------*/
