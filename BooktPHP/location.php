<?php
require_once('bapi.php');
$b = new BAPI();

if(isset($_GET['loctype'])){
	$b->dbConnect();
	$q = "SELECT *, ph.OriginalURL FROM Properties p LEFT JOIN Photos ph ON p.BooktPropertyID = ph.BooktPropertyID and ph.`Order` = 0 WHERE p.SolutionID = $b->solID";
	$lt = mysql_real_escape_string($_GET['loctype']);
	if(isset($_GET['country'])){
		$country = mysql_real_escape_string($_GET['country']);
		$q .= " AND Country = '$country'";
	}
	if(isset($_GET['region'])){
		$region = mysql_real_escape_string($_GET['region']);
		$q .= " AND Region = '$region'";
	}
	if(isset($_GET['state'])){
		$state = mysql_real_escape_string($_GET['state']);
		$q .= " AND State = '$state'";
	}
	if(isset($_GET['metro'])){
		$metro = mysql_real_escape_string($_GET['metro']);
		$q .= " AND Metro = '$metro'";
	}
	if(isset($_GET['city'])){
		$city = mysql_real_escape_string($_GET['city']);
		$q .= " AND City = '$city'";
	}
	if(isset($_GET['neighborhood'])){
		$neighborhood = mysql_real_escape_string($_GET['neighborhood']);
		$q .= " AND Neighborhood = '$neighborhood'";
	}
	if(isset($_GET['p'])){
		$ps = 10*($_GET['p']-1);
		$q .= "LIMIT $ps, 10";
	}
	$r = $b->dbQuery($q);
}

if(!empty($_GET['p'])&&!empty($_GET['scroll'])&&$_GET['scroll']){
	while($pr = mysql_fetch_assoc($r)){
		?>
		<div class="location-result" style="background:url('<?= $pr['OriginalURL'] ?>') no-repeat;background-size:100%;background-position:center;" onClick="location.href='propertydetail.php?propertyid=<?= $pr['BooktPropertyID'] ?>'">
			<a href="propertydetail.php?propertyid=<?= $pr['BooktPropertyID'] ?>"><h2 class=""><?= $pr['Headline'] ?></h2></a>
			<a href="propertydetail.php?propertyid=<?= $pr['BooktPropertyID'] ?>"><span class=""><?= $pr['Beds'] ?> Bedrooms | <?= $pr['Baths'] ?> Bathrooms | Sleeps <?= $pr['Sleeps'] ?></span></a>
		</div>
		<?php
	}
	exit();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="">
<meta name="keywords" content="">
<title>Rentals in <?= $_GET[$_GET['loctype']] ?></title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/galleria-1.2.8.min.js"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>
<script src="js/bapi.1.js" type="text/javascript"></script>
<script src="//booktplatform.s3.amazonaws.com/shared/js/4/bapi.ui.1.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css">
<link rel="stylesheet" href="css/galleria.classic.css" type="text/css" />
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
.location-result{width:100%;overflow:hidden;height:420px;margin:8px 0 12px;cursor:pointer; position:relative;}
.location-result a{text-decoration:none;}
.location-result h2{font-size:22px;position:absolute;top:0;width:99%;padding:8px;margin:0;height:auto;min-height:48px;background:rgba(250,250,250,.8);float:left;text-align:left;overflow:hidden;}
.location-result span{font-size:18px;position:absolute;bottom:0;width:auto;padding:8px 8px 6px;margin:0;height:24px;background:rgba(250,250,250,.8);float:left;}<br>

.submarket-list{float:left;height:100px;overflow:hidden;}
.submarket-list a{text-decoration:none;}
.submarket-list ul li{display:inline;float:left;margin:4px;} 

.property-list{margin-top:8px;}

#more-results{width:100%;text-align:center;padding:16px 0 56px;}
</style>
<script type="text/javascript">
	var pagenum = 1;
	var loading = 1;
	var theEnd = 0;
	function loadPage(pagenum){
		var url = document.URL+'&scroll=true&p='+pagenum;
		$.ajax(url).done(function(data){
			$('.property-list').append(data);
			loading = 0;
			$('#more-results').html('');
			$('html, a').css('cursor','');
		});
		console.log(pagenum);
	}
	$(document).ready(function () {
		loadPage(pagenum);
	});
	$(window).scroll(function() {
	   if(($(window).scrollTop() + $(window).height() == $(document).height())&&(theEnd==0)&&loading==0) {
			$('#more-results').html('<div class="search-loading"><p>Finding more rentals...</p><img src="img/ajax-loader-search.gif"></div>');
			$('html, a').css('cursor','wait');
			loading=1;
			if ((pagenum * 10) >= <?= mysql_num_rows($r) ?>) {
				$('#more-results').html('<div class="search-loading"><h3>No more search results available :(</h3></div>');
				$('html, a').css('cursor','');
				return(false);
			}     
			pagenum++;
			loadPage(pagenum); 
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
        	<h1>Rentals in <?= $_GET[$_GET['loctype']] ?><?php if($_GET['loctype']=='region'||$_GET['loctype']=='state'){ echo ", ".$_GET['country']; } if($_GET['loctype']=='metro'||$_GET['loctype']=='city'){ echo ", ".$_GET['state']; } ?></h1>
            <h3><?= mysql_num_rows($r) ?> Results</h3>
        </div>
        <div class="submarket-list">
        <?php $b->submarketList($_GET['loctype'],$_GET['country'],$_GET['state'],$_GET[$_GET['loctype']]); ?>
        </div>
        <div class="property-list">
        </div>
        <div id="more-results"></div>
      <div id="push"></div>
    </div>

    <div id="footer">
      <div class="container">
        <p class="muted credit">Example courtesy <a href="http://bookt.com/" target="_blank">Bookt LLC</a>.</p>
      </div>
    </div>

  </body>
</html>