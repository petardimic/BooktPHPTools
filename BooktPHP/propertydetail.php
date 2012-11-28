<?php
require_once('bapi.php');
$b = new BAPI();

if(isset($_GET['propertyid'])){
	$pid = (int)$_GET['propertyid'];
	
	$q = "SELECT 
	  p.LastModified
	FROM Properties p 
	WHERE p.BooktPropertyID = $pid
	AND p.SolutionID = $b->solID
	AND p.LastModified >= DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 1 HOUR)
	LIMIT 1";
	$r = $b->dbQuery($q);
	
	if(mysql_num_rows($r)==1){
		$p = new Property($b->solID,$b->apiKey,$pid,false);
	}
	else{
		$p = new Property($b->solID,$b->apiKey,$pid);
		$p->importPropertyData();
	}
}

function starDisplayString($r){
	$i=0;
	$str = "<span class='rating-block'>";
	$s = round($r,2);
	$r = round($r*5,0);
	while($i<25){
		$i++;
		$str .= "<input type='radio' title='".$s."' class='rating {split:5}' ";
		if($i == $r){
			$str .="checked='checked'";	
		}
		$str .= "/>";
	}
	$str .= "</span>";
	return $str;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="<?= $p->propSEO['MetaDescrip'] ?>">
<meta name="keywords" content="<?= $p->propSEO['MetaKeywords'] ?>">
<title><?= $p->propSEO['PageTitle'] ?></title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/galleria-1.2.8.min.js"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>
<script src="js/bapi.1.js" type="text/javascript"></script>
<script src="js/jquery.rating.pack.js" type="text/javascript"></script>
<script src="js/jquery.MetaData.js" type="text/javascript"></script>
<script src="//booktplatform.s3.amazonaws.com/shared/js/4/bapi.ui.1.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css">
<link rel="stylesheet" href="css/galleria.classic.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.rating.css" type="text/css" />
<link rel="icon" type="image/ico" href="img/favicon.ico">
<style>
.slideshow{height:600px;}
#galleria{height:100%; max-height:600px;}
/* Sticky footer styles
-------------------------------------------------- */

html,
body {
height: 100%;
/* The html and body elements cannot have any padding or margin. */
}

/* Wrapper for page content to push down footer */
#wrap {
min-height: 100%;
height: auto !important;
height: 100%;
/* Negative indent footer by it's height */
margin: 40px auto -60px;
}

/* Set the fixed height of the footer here */
#push,
#footer {
height: 60px;
}
#footer {
background-color: #f5f5f5;
position:fixed;
bottom:0;
width:100%;
z-index:100;
}

/* Lastly, apply responsive CSS fixes as necessary */
@media (max-width: 767px) {
#footer {
  margin-left: -20px;
  margin-right: -20px;
  padding-left: 20px;
  padding-right: 20px;
}
}
/* Custom page CSS
-------------------------------------------------- */
/* Not required for template or sticky footer method. */

.container {
width: auto;
max-width: 980px;
}
.container .credit {
margin: 20px 0;
}
.rate-table{width:80%; min-width:400px; border:2px #ddd solid;}
.rate-table thead{background:#ccc;}
.rate-table tr.rate-table-tralt{background:#eee;}
.rate-table tr:hover{background:#ddf;}
.rate-table td{text-align:center;}

h4 span.rating-block{width:auto;display:inline-block;}
.property-amenities{margin-bottom:32px;}
.property-amenities ul{margin-left:4px;}
.property-amenities ul li{display:inline;float:left;margin:4px;}
</style>
<script type="text/javascript">
	var propid = <?= $p->propID ?>;
	var prop;
	var config;
	BAPI.init('<?= $b->apiKey ?>', { jsonp: true });		

	$(document).ready(function() {
		// Load the classic theme
		Galleria.loadTheme('js/galleria.classic.min.js');
	
		// Initialize Galleria
		Galleria.run('#galleria', {
			autoplay: 3500,
			transitionSpeed: 600	
		});
		
		$('input.rating').rating({
			'readOnly':true
		});
		
		BAPI.config.get({ forceload: true }, function(data){
			config = data;
			//BAPI.log(config);
			BAPI.get(propid, BAPI.entities.property, {
				rates: 1, avail: 1, poi: 1
			}, getListCallback);
		}); 
		//BAPI.loadsession();
		
		function getListCallback(data) {
			prop = data.result[0];        
			//BAPI.log(prop);
//		
			//BAPI.log(config); // config object has been loaded with solution configuration data
					
			$.each(prop.Amenities, function(i,val){
				$('#amenity-list').append('<li class="btn btn-large">'+val+'</li>');
			});
			
			// create the rate block
			if(prop.ContextData.Rates.Values.length > 0){
				$('.rate-grid').prepend('<h3>Rates</h3>');
				BAPI.createRateWidget(prop, '.rate-grid', null);
			}
//		
//				// create the availability calendar
			$('.availability').prepend('<h3>Availability</h3>');
			BAPI.createAvailabilityWidget(prop, '.availability', { availcalendarmonths: 3, minbookingdays: config.minbookingdays, maxbookingdays: config.maxbookingdays });
			
			$('.property-map').prepend('<h3>Map</h3>');
			BAPI.createStaticPropertyMap('.property-map', prop, {zoom: 13, width: 600, height: 300});
		}
	});
</script>
</head>
<body>
<?php include('nav.php'); ?>
    <!-- Part 1: Wrap all page content here -->
    <div id="wrap">

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1><?= $p->propHeadline ?></h1>
          <div class="bct"><?= $p->getBreadCrumbTrail() ?></div>
        </div>
        <p class="lead"><?= $p->propBeds ?> Bedroom <?= $p->propType ?> in <?= $p->propCity ?></p>
        <div class="slideshow">
            <div id="galleria">
            <?php 
            $imgArr = $p->propInfo['Images'];
            foreach($imgArr as $img){
                ?>
                <a href="<?= $img['OriginalURL'] ?>">
                    <img src="<?= $img['OriginalURL'] ?>" data-title="<?= $img['Caption'] ?>">
                </a>
                <?php
            }// value='".round(($p->propAvgReview*5),0)."'
            ?>
            </div>
        </div>
        <h3><?= $p->propBeds ?> Bedrooms - <?= $p->propBaths ?> Bathrooms - Accommodates <?php echo (int)$p->propSleeps; ?> Guests</h3>
        <h4><span><?php if($p->propNumReviews>0){ echo "Avg Review: ".starDisplayString($p->propAvgReview)." (".$p->propNumReviews." total reviews)"; } else{echo "This property has not yet been reviewed";} ?></span></h4>
        <p><?= $p->propDesc ?></p>
        <div class="property-map"></div>
        <div class="property-amenities">
        	<h3>Amenities</h3>
            <ul id="amenity-list"></ul>
            <div class="clearfix"></div>
        </div>
		<div class="availability"></div>
		<div class="rate-grid"></div>
		<div class="clearfix">&nbsp;<br></div>
      </div>

      <div id="push"></div>
    </div>

    <div id="footer">
      <div class="container">
        <p class="muted credit">Example courtesy <a href="http://bookt.com/" target="_blank">Bookt LLC</a>.</p>
      </div>
    </div>

  </body>
</html>