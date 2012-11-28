<?php
require_once('bapi.php');
$b = new BAPI();

$ri = $b->randImage();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>VR Demo - BAPI Powered!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<script src="http://code.jquery.com/jquery-1.8.2.min.js" type="text/javascript"></script>
    <script src="http://jtruncate.googlecode.com/svn/trunk/jquery.jtruncate.js" type="text/javascript"></script>
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
	  .hero-unit {background-color:#D5E1F0;background:url('<?= $ri['OriginalURL'] ?>') no-repeat;background-size:100%;background-position:center;height:400px;min-height:0;overflow:hidden;position:relative;}
	  .hero-unit img {height:auto;max-height:160px;}
	  #caption{position:absolute;right:40px;bottom:40px;}
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body>

<?php include('nav.php'); ?>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
      	<img src="img/logo.png">
        <!--<h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>-->
        <div id="push">&nbsp;</div>
        <div id="caption"><a class="btn btn-primary btn-large" href="propertydetail.php?propertyid=<?= $ri['BooktPropertyID'] ?>"><?= $ri['Headline'] ?></a></div>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
          <h2>Search Demo</h2>
          <p>This page is rendered using javascript and jQuery with no local storage required.  Inifnite scroll feature allows visitors to see all results in an efficient manner.</p>
          <p><a class="btn" href="propertysearch">View details &raquo;</a></p>
        </div>
        <div class="span4">
          <h2>Map Demo</h2>
          <p>Provides instant visualization of all properties on a map.  Intended to help provide a geographical search context with links to property detail pages.</p>
          <p><a class="btn" href="map">View details &raquo;</a></p>
       </div>
        <div class="span4">
          <h2>Market Area List</h2>
          <p>Display a hierarchical view of your propert locations with links to market-specific landing pages.</p>
          <p><a class="btn" href="markets">View details &raquo;</a></p>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; VR Demo 2012</p>
      </footer>

    </div> <!-- /container -->
  </body>
</html>
