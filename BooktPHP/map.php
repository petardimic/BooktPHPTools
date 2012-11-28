<?php
require_once('bapi.php');
$b = new BAPI();

$r = $b->getPropertyMapArray();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Rental Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<script src="http://code.jquery.com/jquery-1.8.2.min.js" type="text/javascript"></script>
    <script src="http://jtruncate.googlecode.com/svn/trunk/jquery.jtruncate.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    <script src="js/jquery.ui.map.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css">
	<link rel="icon" type="image/ico" href="img/favicon.ico">

    <!-- Le styles -->
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
	  .hero-unit {background-color:#D5E1F0;}
	  .hero-unit img {height:160px;}
	  #map_div img { max-width: none; }
	.gallery-block{width:400px;overflow:hidden;float:left;margin:8px;}
	.gallery-block a{text-decoration:none;}
	.gallery-block img{width:100%;height:300px;position:relative;top:0;z-index:0;}
	.gallery-block h2{line-height:18px;font-size:16px;position:relative;top:0;z-index:1;width:96%;padding:6px 4%;margin:0;margin-bottom:-70px;margin-left:-2%;height:auto;min-height:24px;background:rgba(250,250,250,.8);}
	.gallery-block span{text-align:center;font-size:18px;position:relative;top:-46px;margin-bottom:-46px;width:100%;padding:8px 0;background:rgba(250,250,250,.8);float:left;}
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	<script type="text/javascript">
	$(document).ready(function () {
		var map = $('#map_div').gmap({ 'center': new google.maps.LatLng(39.828175, -98.5795), 'zoom': 3}).bind('init', function() { 
			// This URL won't work on your localhost, so you need to change it
			// see http://en.wikipedia.org/wiki/Same_origin_policy
			$.getJSON( 'mapjson.php?bsolid=<?= $b->solID ?>', function(data) { 
				$.each( data.markers, function(i, marker) {
					$('#map_div').gmap('addMarker', { 
						'position': new google.maps.LatLng(marker.latitude, marker.longitude), 
						'title': marker.title, 
						'bounds': true
					}).click(function() {
						$('#map_div').gmap('openInfoWindow', { 'content': marker.content }, this);
					});
				});
			});
		});
	});
    </script>
  </head>

  <body>

<?php include('nav.php'); ?>

    <div class="container">
        <div class="page-header">
          <h1>Property Search</h1>
        </div>
        <div class="lead btn-group">
            <a class="btn" href="propertysearch" title="Search View"><i class="icon-search"></i></a>
            <a class="btn" href="map" title="Map View"><i class="icon-map-marker"></i></a>
        </div>

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
      	<div id="map_div" style="width: 100%; height: 560px;"></div>
      </div>

      <hr>

      <footer>
        <p>&copy; VR Demo 2012</p>
      </footer>

    </div> <!-- /container -->
  </body>
</html>
