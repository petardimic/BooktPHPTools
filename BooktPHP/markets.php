<?php
require_once('bapi.php');
$b = new BAPI();

/*$q = "SELECT 
		Country, 
		Region, 
		State, 
		Metro, 
		City, 
		Neighborhood 
	FROM Properties
	WHERE SolutionID = $b->solID
	GROUP BY Country, Region, State, Metro, City, Neighborhood";
$r = $b->dbQuery($q);*/

$qactive = "SELECT
		(CASE WHEN COUNT(DISTINCT Region) > 1 THEN 1 ELSE 0 END) AS Country,
		(CASE WHEN COUNT(DISTINCT State) > 1 THEN 1 ELSE 0 END) AS Region,
		(CASE WHEN COUNT(DISTINCT Metro) > 1 THEN 1 ELSE 0 END) AS State,
		(CASE WHEN COUNT(DISTINCT City) > 1 THEN 1 ELSE 0 END) AS Metro
	FROM Properties 
	WHERE SolutionID = $b->solID
	GROUP BY SolutionID";
$ractive = $b->dbQuery($qactive);
$active = mysql_fetch_assoc($ractive);

$markets = array();

$q = "SELECT DISTINCT Country, COUNT(BooktPropertyID) as NumProps FROM Properties WHERE Solutionid = $b->solID AND Country <> '' GROUP BY Country ORDER BY COUNT(BooktPropertyID) DESC, Country ASC";
$r_country = $b->dbQuery($q);
while($c = mysql_fetch_assoc($r_country)){
	$markets[$c['Country']] = array('Type' => 'country', 'Name' => $c['Country'], 'Count' => $c['NumProps'], 'SubMarkets' => array());
}

$q = "SELECT DISTINCT Region, Country, COUNT(BooktPropertyID) as NumProps FROM Properties WHERE Solutionid = $b->solID AND Region <> '' GROUP BY Country, Region ORDER BY COUNT(BooktPropertyID) DESC, Region ASC";
$r_region = $b->dbQuery($q);
while($reg = mysql_fetch_assoc($r_region)){
	$markets[$reg['Country']]['SubMarkets'][$reg['Region']] = array('Type' => 'region', 'Name' => $reg['Region'], 'Count' => $reg['NumProps'], 'SubMarkets' => array());
}
	
$q = "SELECT DISTINCT State, Region, Country, COUNT(BooktPropertyID) as NumProps FROM Properties WHERE Solutionid = $b->solID AND State <> '' GROUP BY Country, Region, State ORDER BY COUNT(BooktPropertyID) DESC, State ASC";
$r_state = $b->dbQuery($q);
while($st = mysql_fetch_assoc($r_state)){
	$markets[$st['Country']]['SubMarkets'][$st['Region']]['SubMarkets'][$st['State']] = array('Type' => 'state', 'Name' => $st['State'], 'Count' => $st['NumProps'], 'SubMarkets' => array());
}

$q = "SELECT DISTINCT Metro, State, Region, Country, COUNT(BooktPropertyID) as NumProps FROM Properties WHERE Solutionid = $b->solID AND Metro <> '' GROUP BY Country, Region, State, Metro ORDER BY COUNT(BooktPropertyID) DESC, Metro ASC";
$r_metro = $b->dbQuery($q);
while($met = mysql_fetch_assoc($r_metro)){
	$markets[$met['Country']]['SubMarkets'][$met['Region']]['SubMarkets'][$met['State']]['SubMarkets'][$met['Metro']] = array('Type' => 'metro', 'Name' => $met['Metro'], 'Count' => $met['NumProps'], 'SubMarkets' => array());
}

$q = "SELECT DISTINCT City, Metro, State, Region, Country, COUNT(BooktPropertyID) as NumProps FROM Properties WHERE Solutionid = $b->solID AND City <> '' GROUP BY Country, Region, State, Metro, City ORDER BY COUNT(BooktPropertyID) DESC, City ASC";
$r_city = $b->dbQuery($q);
while($city = mysql_fetch_assoc($r_city)){
	$markets[$city['Country']]['SubMarkets'][$city['Region']]['SubMarkets'][$city['State']]['SubMarkets'][$city['Metro']]['SubMarkets'][$city['City']] = array('Type' => 'city', 'Name' => $city['City'], 'Count' => $city['NumProps'], 'SubMarkets' => array());
}

$q = "SELECT DISTINCT Neighborhood, City, Metro, State, Region, Country, COUNT(BooktPropertyID) as NumProps FROM Properties WHERE Solutionid = $b->solID AND Neighborhood <> '' GROUP BY Country, Region, State, Metro, City, Neighborhood ORDER BY COUNT(BooktPropertyID) DESC, Neighborhood ASC";
$r_neighborhood = $b->dbQuery($q);
while($nh = mysql_fetch_assoc($r_neighborhood)){
	$markets[$nh['Country']]['SubMarkets'][$nh['Region']]['SubMarkets'][$nh['State']]['SubMarkets'][$nh['Metro']]['SubMarkets'][$nh['City']]['SubMarkets'][$nh['Neighborhood']] = array('Type' => 'neighborhood', 'Name' => $nh['Neighborhood'], 'Count' => $nh['NumProps'], 'SubMarkets' => array());
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="">
<meta name="keywords" content="">
<title>Market Area List</title>
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
</style>
<script type="text/javascript">
</script>
</head>
<body>
<?php include('nav.php'); ?>
    <!-- Part 1: Wrap all page content here -->
    <div id="wrap">

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
        	<h1>Areas We Serve</h1>
        </div>
        <div class="markets-list">
        <ul>
        <?php
		foreach($markets as $m){
			?>
			<?php if($active['Country']){?><li>
				<?php if(empty($m['Name'])){?>-<?php } else{?><a href="location.php?<?= $m['Type'] ?>=<?= urlencode($m['Name']) ?>&loctype=<?= $m['Type'] ?>"><?= $m['Name'] ?> (<?= $m['Count'] ?> Rentals)</a><?php } ?>
            </li><?php } ?>
            <ul>
			<?php
			foreach($m['SubMarkets'] as $sm){
				?>
                <?php if($active['Region']){?><li>
					<?php if(empty($sm['Name'])){?>-<?php } else{?><a href="location.php?<?= $m['Type'] ?>=<?= urlencode($m['Name']) ?>&<?= $sm['Type'] ?>=<?= urlencode($sm['Name']) ?>&loctype=<?= $sm['Type'] ?>"><?= $sm['Name'] ?> (<?= $sm['Count'] ?> Rentals)</a><?php } ?>
            	</li><?php } ?>
                <ul>
                <?php
                foreach($sm['SubMarkets'] as $sm2){
                    ?>
                    <?php if($active['State']){?><li>
						<?php if(empty($sm2['Name'])){?>-<?php } else{?><a href="location.php?<?= $m['Type'] ?>=<?= urlencode($m['Name']) ?>&<?= $sm['Type'] ?>=<?= urlencode($sm['Name']) ?>&<?= $sm2['Type'] ?>=<?= urlencode($sm2['Name']) ?>&loctype=<?= $sm2['Type'] ?>"><?= $sm2['Name'] ?> (<?= $sm2['Count'] ?> Rentals)</a><?php } ?>
            		</li><?php } ?>
                    <ul>
                    <?php
                    foreach($sm2['SubMarkets'] as $sm3){
                        ?>
                        <?php if($active['Metro']){?><li>
							<?php if(empty($sm3['Name'])){?>-<?php } else{?><a href="location.php?<?= $m['Type'] ?>=<?= urlencode($m['Name']) ?>&<?= $sm['Type'] ?>=<?= urlencode($sm['Name']) ?>&<?= $sm2['Type'] ?>=<?= urlencode($sm2['Name']) ?>&<?= $sm3['Type'] ?>=<?= urlencode($sm3['Name']) ?>&loctype=<?= $sm3['Type'] ?>"><?= $sm3['Name'] ?> (<?= $sm3['Count'] ?> Rentals)</a><?php } ?>
            			</li><?php } ?>
                        <ul>
                        <?php
                        foreach($sm3['SubMarkets'] as $sm4){
                            ?>
                            <li>
								<?php if(empty($sm4['Name'])){?>-<?php } else{?><a href="location.php?<?= $m['Type'] ?>=<?= urlencode($m['Name']) ?>&<?= $sm['Type'] ?>=<?= urlencode($sm['Name']) ?>&<?= $sm2['Type'] ?>=<?= urlencode($sm2['Name']) ?>&<?= $sm3['Type'] ?>=<?= urlencode($sm3['Name']) ?>&<?= $sm4['Type'] ?>=<?= urlencode($sm4['Name']) ?>&loctype=<?= $sm4['Type'] ?>"><?= $sm4['Name'] ?> (<?= $sm4['Count'] ?> Rentals)</a><?php } ?>
            				</li>
                            <ul>
                            <?php
                            foreach($sm4['SubMarkets'] as $sm5){
                                ?>
                                <li>
									<?php if(empty($sm5['Name'])){?>-<?php } else{?><a href="location.php?<?= $m['Type'] ?>=<?= urlencode($m['Name']) ?>&<?= $sm['Type'] ?>=<?= urlencode($sm['Name']) ?>&<?= $sm2['Type'] ?>=<?= urlencode($sm2['Name']) ?>&<?= $sm3['Type'] ?>=<?= urlencode($sm3['Name']) ?>&<?= $sm4['Type'] ?>=<?= urlencode($sm4['Name']) ?>&<?= $sm5['Type'] ?>=<?= urlencode($sm5['Name']) ?>&loctype=<?= $sm5['Type'] ?>"><?= $sm5['Name'] ?> (<?= $sm5['Count'] ?> Rentals)</a><?php } ?>
            					</li>
                                <?php
                            }
                            ?>
                            </ul>
                            <?php
                        }
                        ?>
                        </ul>
                        <?php
                    }
                    ?>
                    </ul>
                    <?php
                }
                ?>
                </ul>
                <?php
			}
			?>
            </ul>
            <?php
        }
		?>
        </ul>
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