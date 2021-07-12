/* Image drage drop + validation code start*/
jQuery(function () {
  var imagesPreview = function (input, placeToInsertImagePreview) {
    var fileTypes = ["jpg", "jpeg", "png"];
    var pdfFileType = ["pdf"];

    if (placeToInsertImagePreview == "#pdf_files") {
      jQuery(input).siblings("#file_name").hide();
      jQuery("#choose_pdf_div").show();
    }
    if (input.files) {
      if (placeToInsertImagePreview == "#pdf_files") {
        var filesAmount = input.files.length;
        var extension = input.files[0].name.split(".").pop().toLowerCase();

        for (i = 0; i < filesAmount; i++) {
          var reader = new FileReader();

          reader.onload = function (event) {
            if (pdfFileType.indexOf(extension) <= -1) {
              jQuery(input).val("");
              jQuery(input).siblings("#file_type_err").show();
              jQuery(input).siblings("#file_err").hide();
              jQuery(input).siblings("#file_name").hide();
              jQuery("#DDText1").show();
              jQuery("#pdfdropZone").css({
                "background-image":
                  "url('https://webdev.snapd.com/wp-content/uploads/2019/09/upload-image.jpg')",
                border: "2px dashed #b9b7b7",
              });
              jQuery("#choose_pdf_div").show();
            } else if (input.files[0].size > 11000000) {
              jQuery(input).val("");
              jQuery(input).siblings("#file_err").show();
              jQuery(input).siblings("#file_type_err").hide();
              jQuery(input).siblings("#file_name").hide();
              jQuery("#DDText1").show();
              jQuery("#pdfdropZone").css({
                "background-image":
                  "url('https://webdev.snapd.com/wp-content/uploads/2019/09/upload-image.jpg')",
                border: "2px dashed #b9b7b7",
              });
              jQuery("#choose_pdf_div").show();
            } else {
              jQuery(input).siblings("#file_err").hide();
              jQuery(input).siblings("#file_type_err").hide();
              jQuery(input)
                .siblings("#file_name")
                .children("p")
                .html(input.files[0].name);
              jQuery(input).siblings("#file_name").show();
              jQuery("#DDText1").hide();
              jQuery("#pdfdropZone").css({
                "background-image":
                  "url('https://webdev.snapd.com/wp-content/uploads/2019/09/pdf_icon.png')",
                border: "2px dashed #ffffff",
              });
              jQuery("#choose_pdf_div").hide();
              console.log(input.files[0]);
              jQuery("#modal_attachment").text(input.files[0].name);

              jQuery(
                jQuery.parseHTML(
                  '<span class="remove-img" style="cursor: pointer;">-</span>'
                )
              ).appendTo("#pdfdropZone");
              jQuery("#pdfdropZone .remove-img").on("click", function () {
                jQuery("#pdfdropZone").css({
                  "background-image":
                    "url('https://webdev.snapd.com/wp-content/uploads/2019/09/upload-image.jpg')",
                  border: "2px dashed #b9b7b7",
                });
                jQuery("#choose_pdf_div").show();
                jQuery("#pdf_image").val("");
                jQuery("#DDText1").show();
                jQuery(this).remove();
                jQuery("#pdf_image").siblings("#file_name").hide();
              });
            }
          };

          reader.readAsDataURL(input.files[i]);
        }
      } else {
        var filesAmount = input.files.length;
        if (filesAmount == 0) {
          console.log("Resetting image...");
          jQuery("#event_image_base64").val("");
          jQuery("#files").empty();
          jQuery("#fileupload").val("");
          jQuery(".for_clone").remove();
        }

        for (i = 0; i < filesAmount; i++) {
          var extension = input.files[0].name.split(".").pop().toLowerCase();
          console.log(extension);
          var reader = new FileReader();

          reader.onload = function (event) {
            if (fileTypes.indexOf(extension) <= -1) {
              jQuery(input).val("");
              jQuery(input).siblings("#file_type_err").show();
              jQuery(input).siblings("#file_err").hide();
              jQuery(input).siblings("#file_succ").hide();
            } else if (input.files[0].size > 11000000) {
              jQuery(input).val("");
              jQuery(input).siblings("#file_err").show();
              jQuery(input).siblings("#file_type_err").hide();
              jQuery(input).siblings("#file_succ").hide();
            } else {
              if (placeToInsertImagePreview == "#files") {
                startCroperIfFileOk(
                  input,
                  placeToInsertImagePreview
                ); /* call image croper */
              }
              jQuery(input).siblings("#file_err").hide();
              jQuery(input).siblings("#file_type_err").hide();
              jQuery(input).siblings("#file_succ").show();

              if (placeToInsertImagePreview == "#logo_files") {
                jQuery(placeToInsertImagePreview).empty();
                jQuery(jQuery.parseHTML('<img class="uploaded-img">'))
                  .attr("src", event.target.result)
                  .appendTo(placeToInsertImagePreview);
                jQuery(
                  jQuery.parseHTML(
                    '<span class="remove-img" style="cursor: pointer;">-</span>'
                  )
                ).appendTo(placeToInsertImagePreview);
                jQuery("#logo_files .remove-img").on("click", function () {
                  jQuery("#logo_files").empty();
                  jQuery("#logo_image").val("");
                });
              }
            }
          };

          reader.readAsDataURL(input.files[i]);
        }
      }
    }
  };

  jQuery("#fileupload").on("change", function () {
    imagesPreview(this, "#files");
  });
  jQuery("#logo_image").on("change", function () {
    imagesPreview(this, "#logo_files");
  });
  jQuery("#pdf_image").on("change", function () {
    imagesPreview(this, "#pdf_files");
  });

  jQuery("#logo_files .remove-img").on("click", function () {
    jQuery("#logo_files").empty();
    jQuery("#logo_image").val("");
  });

  jQuery("#pdfdropZone .remove-img").on("click", function () {
    jQuery("#pdfdropZone").css({
      "background-image":
        "url('https://webdev.snapd.com/wp-content/uploads/2019/09/upload-image.jpg')",
      border: "2px dashed #b9b7b7",
    });
    jQuery("#choose_pdf_div").show();
    jQuery("#pdf_image").val("");
    jQuery("#DDText1").show();
    jQuery(this).remove();
    jQuery("#pdf_image").siblings("#file_name").hide();
  });

  jQuery("#files .remove-img").on("click", function () {
    jQuery("#event_image_base64").val("");
    jQuery("#files").empty();
    jQuery("#fileupload").val("");
    jQuery(".for_clone").remove();
  });
});
function HighlightArea(id, isTrue) {
  console.log(id);
  isTrue == true
    ? jQuery("#" + id).addClass("HighlightArea")
    : jQuery("#" + id).removeClass("HighlightArea");
}

/* Image drage drop + validation code ends*/

/* cropper code start */
var cropper;

window.addEventListener("DOMContentLoaded", function () {
  var $modal = $("#modal");
  $modal
    .on("shown.bs.modal", function () {
      var image = document.getElementById("imageforcrop");

      var NaturalImageContainer =
        document.getElementsByClassName("img-container");
      var height = NaturalImageContainer.naturalHeight;
      var width = NaturalImageContainer.naturalWidth;
      cropper = new Cropper(image, {
        aspectRatio: 4 / 3,
        autoCropArea: 1,
        viewMode: 0,
        center: true,
        dragMode: "move",
        movable: false,
        scalable: true,
        guides: true,
        zoomOnWheel: false,
        cropBoxMovable: true,
        wheelZoomRatio: 0.1,
        minContainerWidth: width,
        minContainerHeight: height,
        ready: function () {
          //Should set crop box data first here
          cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
        },
      });
    })
    .on("hidden.bs.modal", function () {
      cropper.destroy();
      cropper = null;
    });

  jQuery(".cropOptions").on("click", function () {
    var method = jQuery(this).data("method");
    var option = jQuery(this).data("option");

    switch (method) {
      case "move":
        var secondoption = jQuery(this).data("second-option");
        cropper.move(option, secondoption);
        break;
      case "zoom":
        cropper.zoom(option);
        break;
      case "rotate":
        cropper.rotate(option);
        break;
    }
  });
});

function CropClick(previewDiv) {
  var $modal = $("#modal");
  $modal.modal("hide");

  var canvas;

  if (cropper) {
    canvas = cropper.getCroppedCanvas({});

    jQuery(previewDiv).find("img").attr("src", canvas.toDataURL());

    /* if(previewDiv == '#logo_files'){
			jQuery('#logo_image_base64').val(canvas.toDataURL());
			jQuery("#modal_logo").attr("src",canvas.toDataURL());
		} */
    if (previewDiv == "#files") {
      jQuery(previewDiv).empty();
      jQuery(jQuery.parseHTML('<img class="uploaded-img">'))
        .attr("src", canvas.toDataURL())
        .appendTo("#files");
      jQuery(
        jQuery.parseHTML(
          '<span class="remove-img" style="cursor: pointer;">-</span>'
        )
      ).appendTo("#files");
      jQuery("#event_image_base64").val(canvas.toDataURL());
      jQuery("#modal_image").attr("src", canvas.toDataURL());

      jQuery("#files .remove-img").on("click", function () {
        jQuery("#event_image_base64").val("");
        jQuery("#files").empty();
        jQuery("#fileupload").val("");
      });
    }
  }
}
function startCroperIfFileOk(inputfile, previewDiv) {
  jQuery("#crop").attr("onClick", 'CropClick("' + previewDiv + '")');

  var image = document.getElementById("imageforcrop");
  var $modal = $("#modal");
  var filescrp = inputfile.files;
  var done = function (url) {
    image.src = url;
    $modal.modal("show");
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
      reader.onload = function (e) {
        done(reader.result);
      };
      reader.readAsDataURL(file);
    }
  }
}

/* cropper code ends */

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
  street_number: "short_name",
  route: "long_name",
  locality: "long_name",
  administrative_area_level_1: "long_name",
  country: "long_name",
  postal_code: "short_name",
};

function initAutocomplete() {
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("streetaddress2"),
    { types: ["geocode"] }
  );
  autocomplete.setFields(["address_component"]);
  autocomplete.setComponentRestrictions({
    country: ["ca"],
  });
  autocomplete.addListener("place_changed", fillInAddress);
}

function fillInAddress() {
  jQuery("#state, #city, #country, #postalcode").val("");

  var place = autocomplete.getPlace();
  //jQuery('#state, #city, #country, #postalcode').val('');

  // Get each component of the address from the place details,
  // and then fill-in the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var value = place.address_components[i][componentForm[addressType]];

      if (addressType == "street_number") {
        var street_number = value;
      }
      if (addressType == "route") {
        var route =
          street_number != undefined ? street_number + " " + value : value;
        jQuery("#streetaddress2").val(route);
        /* jQuery('#modal_address').text(street_number+' '+value); */
      }
      if (addressType == "locality") {
        jQuery("#city").val(value);
        /* jQuery('#modal_city').text(value); */
      }
      if (addressType == "postal_code_prefix") {
        console.log(value);
        jQuery("#postalcode").val(value);
        /* jQuery('#modal_zip').text(value); */
      }
      if (addressType == "postal_code") {
        console.log(value);
        jQuery("#postalcode").val(value);
        /* jQuery('#modal_zip').text(value); */
      }
      if (addressType == "administrative_area_level_1") {
        console.log(value);

        $("#state option[selected]").removeAttr("selected");
        $("#state option")
          .filter(function () {
            return $.trim($(this).text()) == value;
          })
          .prop("selected", true)
          .attr("selected", true);
        /* jQuery('#modal_province').text(value); */
      }
      if (addressType == "country") {
        console.log(value);
        $("#country option[selected]").removeAttr("selected");
        $("#country option")
          .filter(function () {
            return $.trim($(this).text()) == value;
          })
          .prop("selected", true)
          .attr("selected", true);
        /* jQuery('#modal_country').text(value); */

        /*
      var country_id = $('#country').val();
      if(country_id == ''){
         country_id = 0;
      }
      if($('#state').val() == ''){
        $.ajax({
            url:"https://webdev.snapd.com/wp-content/themes/Divi Child/get_state.php",
            method:"POST",
            data:"countryid="+country_id,
            success:function(html){
                $('#state').html(html);
                }
        });
      }
      */
      }
    }
  }

  // alert()
  jQuery("#streetaddress2").focus().focusout();
  jQuery("#city").focus().focusout();
  jQuery("#postalcode").focus().focusout();
  jQuery("#state").focus().focusout();
  jQuery("#country").focus().focusout();

  console.log(place);
  mapChange();
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate_event() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude,
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy,
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}

/*  address auto fill end  */

/*  address map script start  */
jQuery(document).ready(function () {
  mapChange();
  jQuery("#venue").on("change ", function () {
    mapChange();
  });
  jQuery("#streetaddress2").on("change", function () {
    mapChange();
  });
  jQuery("#city").on("change", function () {
    mapChange();
  });
  jQuery("#postalcode").on("change", function () {
    mapChange();
  });
});

function mapChange() {
  var venue = jQuery("#venue").val();
  var address = jQuery("#streetaddress2").val();
  var city = jQuery("#city").val();
  var postalcode = jQuery("#postalcode").val();
  var fulladdress = "";
  if (venue != "") {
    fulladdress += venue + "+";
  }
  if (address != "") {
    fulladdress += address + "+";
  }
  if (city != "") {
    fulladdress += city + "+";
  }
  if (postalcode != "") {
    fulladdress += postalcode + "+";
  }
  jQuery.ajax({
    url:
      "https://maps.googleapis.com/maps/api/geocode/json?address=" +
      fulladdress +
      "&key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw",
    success: function (res) {
      if (res.status == "OK") {
        /* console.log(res);
						console.log(res.results[0].geometry.location.lat);
						console.log(res.results[0].geometry.location.lng); */
        var srcc =
          "https://webdev.snapd.com/map.php?lat=" +
          res.results[0].geometry.location.lat +
          "&lng=" +
          res.results[0].geometry.location.lng;
        /* console.log(srcc); */
        jQuery("#mapLat").val(res.results[0].geometry.location.lat);
        jQuery("#mapLong").val(res.results[0].geometry.location.lng);
        jQuery(".mapIframe").attr("src", srcc);
      }
    },
  });
}
/*  address map script end  */

jQuery(document).on("change", "#country", function () {
  var country_id = $("#country").val();
  if (country_id == "") {
    country_id = 0;
  }
  if ($("#state").val() == "") {
    $.ajax({
      url: "https://webdev.snapd.com/wp-content/themes/Divi Child/get_state.php",
      method: "POST",
      data: "countryid=" + country_id,
      success: function (html) {
        $("#state").html(html);
      },
    });
  }
});
