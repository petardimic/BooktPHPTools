<?php
require_once('bapi.php');
$b = new BAPI();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="<?= $p->propSEO['MetaDescrip'] ?>">
<meta name="keywords" content="<?= $p->propSEO['MetaKeywords'] ?>">
<title>Property Search</title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/galleria-1.2.8.min.js"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>
<script src="//booktplatform.s3.amazonaws.com/shared/js/4/bapi.1.js" type="text/javascript"></script>
<script src="//booktplatform.s3.amazonaws.com/shared/js/4/bapi.ui.1.js" type="text/javascript"></script>
<script src="//jtruncate.googlecode.com/svn/trunk/jquery.jtruncate.pack.js" type="text/javascript"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-responsive.min.css">
<link rel="stylesheet" href="css/galleria.classic.css" type="text/css" />
<link rel="icon" type="image/ico" href="img/favicon.ico">
<style>
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
margin: 40px auto -120px;
}

/* Set the fixed height of the footer here */
#push { height:100px; }
#footer { min-height: 60px; height:auto; }
#footer {
background-color: #e5e5f5;
position:fixed;
bottom:0;
width:100%;
text-align:center;
  z-index:100;
}

/* Lastly, apply responsive CSS fixes as necessary */
@media (max-width: 767px) {
#footer {
  margin-left: -20px;
  margin-right: -20px;
  padding-left: 20px;
  padding-right: 20px;
  z-index:100;
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
/* CSS Document */
#map-view-map {width:100% !important; height:500px; margin-top:12px; }
.portal-block { background-color: #F7F7F9; border: 1px solid #E1E1E8;margin:15px 8px; padding:8px; width:100%;clear:both; -webkit-border-radius:8px; -moz-border-radius:8px; border-radius:8px;-moz-box-shadow:0 0 5px #E1E1E8;-webkit-box-shadow:0 0 5px #E1E1E8;box-shadow:0 0 5px #E1E1E8;}
.portal-thumbnail .rownumber { padding:4px 6px; background:#39C; width:12px; color:#FFF; font-weight:bold; position:relative; top:28px; margin-top:-30px; min-width:24px; text-align:center; }
.portal-block:hover { background:#e4e4e4; }
.portal-thumb-img { max-height:150px; }
#bapi-left-content, #map-view { float:left; width:100%; }
.top-amenities { border-bottom:1px solid #999; }
.portal-thumbnail { float:left; padding:4px; }
.portal-info { float:left; min-width:420px; max-width:750px; width:auto; padding-left:4px; }
.portal-info h2 a,.gallery-info h2 a{text-decoration:none;}
.portal-info h2 a:hover,.gallery-info h2 a:hover{color:#0066CC;text-decoration:underline;}
.portal-info h2, .portal-info p, .gallery-info h2 { margin:0 !important; }
/*.gallery-info h2 { font-size:18px; }
.gallery-info h2.property-rate-value { font-size:16px; }*/
.one-column #content { width:100%; }
.right-sidebar { float:right; width:21%; }
.right-sidebar h1 { font-size:24px; }
.views-block { margin-left:-24px; }
.views-block ul { list-style-type:none; }
.views-block ul li {display:inline; padding:8px 12px; border:1px solid #444; }
.property-search-button, .property-search-reset { padding:6px 8px; border:1px solid #444; margin-right:4px; cursor:pointer; }
.views-block a { cursor:pointer; }

.portal-block .btn-details-portal,a.btn-quote:link, a.btn-quote:visited{-moz-border-radius:5px;border-radius:5px;background:#1D6CCE;color:#fff;font-weight:bold;padding:0 10px;text-align:center}
.portal-block .btn-details-portal a,.portal-block .btn-details-portal a:link,.portal-block .btn-details-portal a:visited{color:#fff;text-decoration:none;}

.reviseSearchBlock h1{font-size:17px;border-bottom:1px solid #ccc;color:#000;font-weight:bold;height:auto;padding:0 0 8px;width:100%;}
.reviseSearchBlock{position:fixed;right:0;top:10%;-webkit-border-top-left-radius: 10px;-webkit-border-bottom-left-radius: 10px;-moz-border-radius-topleft: 10px;-moz-border-radius-bottomleft: 10px;border-top-left-radius: 10px;border-bottom-left-radius: 10px;background:#eee;padding:0 12px 8px;-moz-box-shadow:0 0 5px #ccc;-webkit-box-shadow:0 0 5px #ccc;box-shadow:0 0 5px #ccc;}
.property-search-revise-block{margin:0 auto;padding:10px;}

/* select and input */
.entry-content select,.entry-content textarea,.entry-content input[type="text"]{
	background-color: #fff;
	-moz-border-radius:3px;border-radius:3px;
    color: #555;
    display: inline-block;
	font-weight: normal;
    font-size: 12px;
    height: 20px;
    line-height: 20px;
    margin-bottom: 24px;
    padding: 4px 6px;
	vertical-align: middle;
	width:92%;
}
.entry-content select {
	background-color: #fff;
    border: 1px solid #ccc;
    height: 30px;
    line-height: 30px;
	width:100%;
}

/*buttons*/
.entry-content .btn {
  display: inline-block;
  *display: inline;
  padding: 4px 14px;
  margin-bottom: 0;
  *margin-left: .3em;
  font-size: 14px;
  line-height: 20px;
  *line-height: 20px;
  color: #333333;
  text-align: center;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  vertical-align: middle;
  cursor: pointer;
  background-color: #f5f5f5;
  *background-color: #e6e6e6;
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
  background-repeat: repeat-x;
  border: 1px solid #bbbbbb;
  *border: 0;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
  border-bottom-color: #a2a2a2;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
  filter: progid:dximagetransform.microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
  *zoom: 1;
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
     -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
          box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}

.entry-content .btn:hover,
.entry-content .btn:active,
.entry-content .btn.active,
.entry-content .btn.disabled,
.entry-content .btn[disabled] {
  color: #333333;
  background-color: #e6e6e6;
  *background-color: #d9d9d9;
}

.entry-content .btn:active,
.entry-content .btn.active {
  background-color: #cccccc \9;
}

.entry-content .btn:first-child {
  *margin-left: 0;
}

.entry-content .btn:hover {
  color: #333333;
  text-decoration: none;
  background-color: #e6e6e6;
  *background-color: #d9d9d9;
  /* Buttons in IE7 don't get borders, so darken on hover */

  background-position: 0 -15px;
  -webkit-transition: background-position 0.1s linear;
     -moz-transition: background-position 0.1s linear;
       -o-transition: background-position 0.1s linear;
          transition: background-position 0.1s linear;
}

.entry-content .btn:focus {
  outline: thin dotted #333;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}

.entry-content .btn.active,
.entry-content .btn:active {
  background-color: #e6e6e6;
  background-color: #d9d9d9 \9;
  background-image: none;
  outline: 0;
  -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
     -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
          box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
}
.entry-content .btn {
  border-color: #c5c5c5;
  border-color: rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.25);
}
.entry-content .btn-group {
  position: relative;
  *margin-left: .3em;
  font-size: 0;
  white-space: nowrap;
  vertical-align: middle;
}

.entry-content .btn-group:first-child {
  *margin-left: 0;
}

.entry-content .btn-group + .btn-group {
  margin-left: 5px;
}
.entry-content .btn-group > .btn {
  position: relative;
  margin-right:0;
  -webkit-border-radius: 0;
     -moz-border-radius: 0;
          border-radius: 0;
}

.entry-content .btn-group > .btn + .btn {
  margin-left: -1px;
}

.entry-content .btn-group > .btn,
.entry-content .btn-group > .dropdown-menu {
  font-size: 14px;
}
.entry-content .btn-group > .btn:first-child {
  margin-left: 0;
  -webkit-border-bottom-left-radius: 4px;
          border-bottom-left-radius: 4px;
  -webkit-border-top-left-radius: 4px;
          border-top-left-radius: 4px;
  -moz-border-radius-bottomleft: 4px;
  -moz-border-radius-topleft: 4px;
}

.entry-content .btn-group > .btn:last-child,
.entry-content .btn-group > .dropdown-toggle {
  -webkit-border-top-right-radius: 4px;
          border-top-right-radius: 4px;
  -webkit-border-bottom-right-radius: 4px;
          border-bottom-right-radius: 4px;
  -moz-border-radius-topright: 4px;
  -moz-border-radius-bottomright: 4px;
}
.entry-content .btn-group > .btn:hover,
.entry-content .btn-group > .btn:focus,
.entry-content .btn-group > .btn:active,
.entry-content .btn-group > .btn.active {
  z-index: 2;
}
.search-loading{clear:both;text-align:center;}
.property-search-field{display:inline; padding:0 2px; margin:0 2px;}
.navbar-inner .container, #footer .container{max-width:100%;}

#map-view-map img { max-width: none; }


.gallery-block{min-width:340px;width:48%;height:320px;overflow:hidden;float:left;margin:8px;}
.gallery-block a{text-decoration:none;}
.gallery-block img{width:100%;height:100%;position:relative;top:0;z-index:0;}
.gallery-block h2{line-height:18px;font-size:16px;position:relative;top:0;z-index:1;width:96%;padding:6px 4%;margin:0;margin-bottom:-70px;margin-left:-2%;height:auto;min-height:24px;background:rgba(250,250,250,.8);}
.gallery-block span{text-align:center;font-size:18px;position:relative;top:-36px;width:100%;padding:8px 0;background:rgba(250,250,250,.8);float:left;}
</style>
<script type="text/javascript">
var ids = [], curpage=1, theEnd=0, loading=0;
//var searchmode = 1;
var map, content, title, streetview, infowindow;
var config;
BAPI.init('<?= $b->apiKey ?>', { loadconfig: true, jsonp: true });
$(document).ready(function () {
	BAPI.config.get({ searchtextdata: 1, forceload: true }, function (data) {
		config = data;
		BAPI.createSearchWidget('qsearch', null, 
			function () {
				// record the search paarams to our session
				var sparams = BAPI.session().searchparams;
				sparams.checkin = (typeof ($("#qsearch-checkin-val").val()) === "undefined" ? null : $("#qsearch-checkin-val").val());
				sparams.checkout = (typeof ($("#qsearch-checkout-val").val()) === "undefined" ? null : $("#qsearch-checkout-val").val());
				sparams.los = (typeof ($("#qsearch-los").val()) === "undefined" ? null : $("#qsearch-los").val());
				sparams.beds.min = $("#qsearch-beds").val();
				sparams.category = (typeof ($("#qsearch-category").val()) === "undefined" ? null : $("#qsearch-category").val());
				BAPI.savesession();

				curpage = 1; // go back to beginning of list
				doSearch(BAPI.session().searchmode); // perform the search
			},
			function () {
				if (confirm('Do you want to clear your search?')) {
					BAPI.clearsession();
					$("#qsearch-checkin-val").val('');
					$("#qsearch-checkout-val").val('');
					doSearch(0);
				}                
			},
			function () {
				alert('advanced clicked');
			}
		);
		
		if(BAPI.session().searchmode==0){
			BAPI.session().searchmode = 2;
		}
		BAPI.log(BAPI.session());
		doSearch(BAPI.session().searchmode);                
	});


	$(window).scroll(function() {
	   if(($(window).scrollTop() + $(window).height() == $(document).height())&&(theEnd==0)&&loading==0) {
			$('#more-results').html('<div class="search-loading"><p>Finding more rentals...</p><img src="img/ajax-loader-search.gif"></div>');
			$('html, a').css('cursor','wait');
			loading=1;
			if ((curpage * 5) >= ids.length) {
				$('#more-results').html('<div class="search-loading"><h3>No more search results available</h3></div>');
				$('html, a').css('cursor','');
				return;
			}     
			curpage++;
			var wrapper = new Object();
			wrapper.result = ids;
			searchCallback(wrapper);    
	   }
	});

});

function onSearch(c) {
	var sparams = BAPI.session().searchparams;
	sparams.checkin = (typeof ($("#qsearch-checkin-val").val()) === "undefined" ? null : $("#qsearch-checkin-val").val());
	sparams.checkout = (typeof ($("#qsearch-checkout-val").val()) === "undefined" ? null : $("#qsearch-checkout-val").val());
	sparams.los = (typeof ($("#qsearch-los").val()) === "undefined" ? null : $("#qsearch-los").val());
	sparams.beds.min = $("#qsearch-beds").val();
	sparams.category = (typeof ($("#qsearch-category").val()) === "undefined" ? null : $("#qsearch-category").val());
	BAPI.savesession();
	//BAPI.log(sparams);
	curpage = 1;
	doSearch(BAPI.session().searchmode);
}

function doSearch(newSearchMode) {
	//$('#more-results').html('<div class="search-loading"><p>Finding more rentals...</p><img src="img/ajax-loader-search.gif"></div>');
	$('#init-loading').html('<div class="search-loading"><p>Finding more rentals...</p><img src="img/ajax-loader-search.gif"></div>');
	$('html, a').css('cursor','wait');
	BAPI.session().searchmode = newSearchMode; // switch our session to the new mode
	BAPI.savesession();
	$('#search-results').empty(); // make sure prev search results are cleared
	BAPI.search(BAPI.entities.property, BAPI.session().searchparams, searchCallback);
}

function searchCallback(data) {
	ids = data.result;
	$('#numresults').text(ids.length);
	BAPI.get(ids, BAPI.entities.property,
		{
		"seo": true,
		"favorites": true, 
		"page": curpage,
		"checkin": BAPI.session().searchparams.checkin,
		"checkout": BAPI.session().searchparams.checkout,
		"los": BAPI.session().searchparams.los
		},       
		function (pdata) {
			if (pdata.result.length == 0) { return; }

			var searchmode = BAPI.session().searchmode;
			if (searchmode == 1) {
				$('#reg-view').show();
				$('#map-view').hide();
				var tmp = displayListView(pdata);
				$("#search-results").append(tmp);
				$('html, a').css('cursor','');
				$('#init-loading').html('');
				loading=0;
			}
			else if (searchmode == 2) {
				$('#reg-view').show();
				$('#map-view').hide();
				var tmp = displayGalleryView(pdata);
				$("#search-results").append(tmp);
				$('html, a').css('cursor','');
				$('#init-loading').html('');
				loading=0;
			}
			else if (searchmode == 3) {
				$('#reg-view').hide();
				$('#map-view').show();
				var options = {width:"400px", height:"200px"};
				BAPI.searchResults.mapview(pdata, 'map-view-map', options, mapInfoWindow);
				$('html, a').css('cursor','');
				$('#init-loading').html('');
				loading=0;
			}

			$('.favorite').click(function (event) {
				var pkid = $(this).attr('data-id');
				var isfavorited = $(this).attr('checked')
				if (isfavorited == 'checked') {
					BAPI.session().favorites.add(pkid, function (res) {
						BAPI.log(pkid + ' added to favorites.');
					});
				}
				else {
					BAPI.session().favorites.del(pkid, function (res) {
						BAPI.log(pkid + ' removed from favorites.');
					});
				}
			});
		});
}

function displayListView(pdata) {    
	$('#more-results').html('');
	$.each(pdata.result, function (i, item) {
		BAPI.log(item);
		$("#search-results").append($("<div>", { class: "portal-block", "data-lat": item.latitude, "data-lng": item.longitude, "id": "p" + item.ID })
			.append($("<div>", { class: "portal-inner-left" })
				.append($("<div>", { class: "portal-thumbnail" })
					.append($("<div>", { class: "rownumber", text: ((curpage-1)*5 + i + 1).toString() }))
					.append($("<a>", { href: 'propertydetail.php?propertyid='+item.ID })
					.append($("<img>", { class: "portal-thumb-img", alt: item.Images[0].Caption, src: item.Images[0].ThumbnailURL })))
			.append($("<div>", { class: "btn-details-portal" })
				.append($("<a>", { href: 'propertydetail.php?propertyid='+item.ID, text: "Details & Availability" }))))
			.append($("<div>", { class: "portal-info" })
				.append($("<h2>").append($("<a>", { href: 'propertydetail.php?propertyid='+item.ID }).append($("<span>", { text: item.Headline }))))
				.append($("<small>", { text: "XXX No reviews yet for this Property XXX" }))
				.append($("<div>", { class: "clear" }))
				.append($("<p>", { class: "first" })
					.append($("<span>", { html: "<b>City:</b> " + item.City })))
				.append($("<p>", { html: "<b>Bedrooms:</b> " + item.Bedrooms + "</b> | <b>Bathrooms:</b> " + item.Bathrooms + "</b> | <b>Sleeps:</b> " + item.Sleeps }))
				.append($("<span>").append($("<p>", { html: item.Summary })).jTruncate({ length: 75, moreText: "" }))
			.append($("<div>", { class: "portal-rates", style: "top:0", text: item.ContextData.Quote.PublicNotes }))
			.append($("<div>", { style: "clear:both" })))).append($("<div>", { style: "clear:both" })));
	});
}

function displayGalleryView(pdata) {
	$('#more-results').html('');
	$.each(pdata.result, function (i, item) {
		//BAPI.log(item);
		$("#search-results").append($("<div>", { class: "gallery-block", "data-lat": item.latitude, "data-lng": item.longitude, "id": "p" + item.ID })
			.append($("<a>", { href: 'propertydetail.php?propertyid='+item.ID })
				.append($("<h2>", { text: item.Headline })))
			.append($("<div>", { class: "" })
				.append($("<a>", { href: 'propertydetail.php?propertyid='+item.ID })
					.append($("<img>", { alt: item.Images[0].Caption, src: item.Images[0].OriginalURL }))))
			.append($("<a>", { href: 'propertydetail.php?propertyid='+item.ID })
				.append($("<span>", { class: "", text: item.Bedrooms+" Bedrooms | "+item.Bathrooms+" Bathrooms | Sleeps "+item.Sleeps }))
			.append($("<div>", { style: "clear:both" }))).append($("<div>", { style: "clear:both" })));
	});
}

function mapInfoWindow(title, marker, p, type) {
	//BAPI.log(p);
	var outerdiv = $("<div>");
	var imgdiv = $("<div>", { class: "left", style: "width:175px; padding-left:10px; float:left;" });
	imgdiv.append($("<img>", { src: p.Images[0].ThumbnailURL, caption: p.Images[0].Caption, width: "175" }));
	imgdiv.append($("<a>", { href: 'propertydetail.php?propertyid='+p.ID, text: "Details & Availability" }));
	outerdiv.append(imgdiv);

	var ddiv = $("<div>", { class: "right", style: "width:220px; padding:0 10px; float:right;" });
	outerdiv.append(ddiv);
	ddiv.append($("<div>").append($("<b>", { text: p.Headline })));
	//ddiv.append($("<div>", { text: p.Type }));

	var st = p.Summary
	var stl = st.length
	if(stl>85){
		st = st.substring(0,85)+'...';
	}
	var summary = $("<div>").st
	ddiv.append($("<div>", { text: summary, style:"display:block;border-bottom:1px solid #ccc;margin-top:8px;" }));
	ddiv.append($("<div>", { text: p.Amenities.slice(0,5).join(", "), style:"display:block;border-bottom:1px solid #ccc;margin-top:8px;" }));
	ddiv.append($("<div>", { text: p.ContextData.Quote.PublicNotes, style:"display:block;margin-top:8px;" }));
	ddiv.append($("<div>", { class: "clear" }));
	//BAPI.log(outerdiv.html());
	title.innerHTML = outerdiv.html(); //marker.getTitle();
	infowindow.open(map, marker);
}

function getQuote(pid) {
	alert("Get Quote");
}
</script>
</head>
<body>
<?php include('nav.php'); ?>
    <!-- Part 1: Wrap all page content here -->
    <div id="wrap">

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>Property Search</h1>
        </div>
        <div class="lead btn-group">
            <a class="btn" onclick="curpage=1; doSearch(1)" title="List View"><i class="icon-list"></i></a>
            <a class="btn" onclick="curpage=1; doSearch(2)" title="Gallery View"><i class="icon-th-large"></i></a>
            <a class="btn" href="map" title="Map View"><i class="icon-map-marker"></i></a>
        </div>
        
        <div id="map-view" style="display:none">
            <div id="content-nosidebar">
                <div class="map-view-page">   
                <div class="clear2"></div>
                <div id="map-view-map" class="MapStyle" style="width:650px"></div>
                <div class="clear clear-map-view"></div>
                </div>
            </div>
        </div>
        
        <div class="left-content">    
            <div class="clear"></div>
            
            <div class="sidebar-ip" id="reg-view" style="display:none">
                <div id="bapi-left-content">
                <div class="list-view-page">
                    <div class="portal-results">    
                    <h1><span id="numresults"></span> Results</h1>
                    <div class="clear2"></div>
                    <div id="search-results"></div>        
                    <div id="more-results"><!--<a id="nextpage">More Results</a>--></div>
                    </div>
                    <div class="clear"></div>
                </div>
                </div> 
            </div>	
        </div>
        <div id="init-loading"><div class="search-loading"><p>Finding more rentals...</p><img src="img/ajax-loader-search.gif"></div></div>
		<div class="clearfix">&nbsp;<br></div>
      </div>

      <div id="push"></div>
    </div>

    <div id="footer">
      <div class="container">
      	<div id="qsearch" class="property-search-revise-block">
      </div>
    </div>

  </body>
</html>