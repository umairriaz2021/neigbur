<!DOCTYPE html>
<html>
  <head>
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 250px;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
       }
    </style>
  </head>
  <body>
  
    <div id="map"></div>
    <script>

function initMap() {
  // The location of Uluru
  var uluru = {lat: <?php echo $_GET['lat'] ?>, lng: <?php echo $_GET['lng'] ?>};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 16, center: uluru});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru, map: map});
}
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyPW15L6uIJxk-8lSFDrPo8kB8G2-k4Tw&callback=initMap">
    </script>
  </body>
</html>