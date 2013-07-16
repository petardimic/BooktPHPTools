<?php
/*

I'm a developer just working on code that should not be in build 20130716

*/


/*
I am now making a fix (maybe even a hotfix) for 20130716
*/
require_once('bapi-config.php');
class BAPI
{
	public $solID;
	public $solConfig;
	public $apiKey;
	public $propID;
	public $propList;
	public $altID;
	public $language = 'en-US';
    public $currency = 'USD';
    public $baseURL = 'https://connect.bookt.com';
	public $dbURL = DB_LOCATION;
	public $dbUser = DB_USER;
	public $dbDB = DB_NAME;
	public $dbPasswd = DB_PASSWORD;
	public $dbMode = DB_MODE;
	
	public function __construct($s=SOLUTIONID,$a=API_KEY){
		$this->solID = $s;
		$this->apiKey = $a;
	}
	
	public function dbConnect(){
		mysql_connect($this->dbURL,$this->dbUser,$this->dbPasswd);
		mysql_select_db($this->dbDB);
	}
	
	public function dbQuery($query){
		$this->dbConnect();
		$r = mysql_query($query) or die(mysql_error());
		return($r);
	}
	
	public function getSolutionConfig($apiKey){
		$url = $this->baseURL."/ws/?method=getconfig&apikey=".$apiKey;
		$json = file_get_contents($url);
		$data = json_decode($json, TRUE);
		if($data['status']==1){
			return($data['result']);
		}
		else{
			return(false);
		}
	}
	
	public function getPropertyList(){
		$url = $this->baseURL."/ws/?method=search&apikey=".$this->apiKey."&entity=property";
		//echo $url; exit();
		$json = file_get_contents($url);
		$data = json_decode($json, TRUE);
		if($data['status']==1){
			$this->propList = $data['result'];
			return($data['result']);
		}
		else{
			return(false);
		}
	}
	
	public function syncStart($mode){
		$q = "UPDATE Solutions SET Last".$mode."SyncStart = CURRENT_TIMESTAMP, Running = 1 WHERE SolutionID = $this->solID LIMIT 1";
		$this->dbQuery($q);
		return(true);
	}
	
	public function syncEnd($mode){
		$q = "UPDATE Solutions SET Last".$mode."SyncEnd = CURRENT_TIMESTAMP, Running = 0 WHERE SolutionID = $this->solID LIMIT 1";
		$this->dbQuery($q);
		return(true);
	}
	
	public function importPropertyAvail(){
		$this->syncStart('Avail');
		$pl = $this->getPropertyList();
		$i = 0;
		$c = count($pl);
		while($i<$c){
			$p = new Property($this->solID,$this->apiKey,$pl[$i]);
			$p->importPropertyAvail();
			$i++;
		}
		$this->syncEnd('Avail');
	}
	
	public function importPropertyRates(){
		$this->syncStart('Rate');
		$pl = $this->getPropertyList();
		$i = 0;
		$c = count($pl);
		while($i<$c){
			$p = new Property($this->solID,$this->apiKey,$pl[$i]);
			$p->importPropertyRates();
			$i++;
		}
		$this->syncEnd('Rate');
	}
	
	public function purgeProperties(){
		$pl = $this->propList;
		$plsrt = "(".implode(',', $pl).")";
		//echo $pl;exit();
		
		$q = "DELETE FROM Properties WHERE BooktPropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		$q = "DELETE FROM Rates WHERE PropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		$q = "DELETE FROM Photos WHERE BooktPropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		$q = "DELETE FROM Availability WHERE PropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
	}
	
	public function addMissingProperties(){
		$pl = $this->propList;
		foreach($pl as $id){
			$q = "SELECT COUNT(BooktPropertyID) as PCount FROM Properties WHERE SolutionID = $this->solID AND BooktPropertyID = $id";
			$r = $this->dbQuery($q);
			if(mysql_result($r,0,"PCount")==0){
				$p = new Property($this->solID,$this->apiKey,$id);
				$p->importPropertyData();
			}
		}
	}
	
	public function importPropertyFull(){
		$this->syncStart('Rate');
		$this->syncStart('Avail');
		$pl = $this->getPropertyList();
		$plsrt = "(".implode(',', $pl).")";
		//echo $pl;exit();
		
		$q = "DELETE FROM Properties WHERE BooktPropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		$q = "DELETE FROM Rates WHERE PropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		$q = "DELETE FROM Photos WHERE BooktPropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		$q = "DELETE FROM Availability WHERE PropertyID not in $plsrt and SolutionID = $this->solID";
		$this->dbQuery($q);
		
		$i = 0;
		$c = count($pl);
		while($i<$c){
			$p = new Property($this->solID,$this->apiKey,$pl[$i]);
			$p->importPropertyData();
			$i++;
		}
		$this->syncEnd('Rate');
		$this->syncEnd('Avail');
	}
	
	public function getAccounts(){
		$q = "SELECT 
					*, 
					(SELECT COUNT(PropertyID) FROM Properties WHERE SolutionID = s.SolutionID) as PropertyCount,
					(SELECT COUNT(ID) FROM Availability WHERE SolutionID = s.SolutionID) as BookingCount,
					(SELECT COUNT(SolutionID) FROM Rates WHERE SolutionID = s.SolutionID) as RatesCount,
					(SELECT COUNT(PhotoID) FROM Photos WHERE SolutionID = s.SolutionID) as PhotosCount
				FROM Solutions s ORDER BY SolutionID ASC";
		$r = $this->dbQuery($q);
		return($r);
	}
	
	public function getSolutionsForAvailabilityRefresh($c,$t){
		$q = "SELECT SolutionID, SolutionName, APIKey, LastAvailSyncEnd FROM Solutions WHERE LastAvailSyncEnd < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL $t HOUR) AND Running = 0 ORDER BY LastAvailSyncEnd ASC LIMIT $c";
		$r = $this->dbQuery($q);
		return($r);
	}
	
	public function getSolutionsForRatesRefresh($c,$t){
		$q = "SELECT SolutionID, SolutionName, APIKey, LastRateSyncEnd FROM Solutions WHERE LastRateSyncEnd < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL $t HOUR) AND Running = 0 ORDER BY LastRateSyncEnd ASC LIMIT $c";
		$r = $this->dbQuery($q);
		return($r);
	}
	
	public function getSolutionsForFullRefresh($c,$t){
		$q = "SELECT SolutionID, SolutionName, APIKey, LastRateSyncEnd, LastAvailSyncEnd FROM Solutions WHERE ((LastRateSyncEnd < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL $t HOUR)) OR (LastAvailSyncEnd < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL $t HOUR))) AND Running = 0 ORDER BY LEAST(LastRateSyncEnd,LastAvailSyncEnd) ASC LIMIT $c";
		$r = $this->dbQuery($q);
		return($r);
	}
	
	public function importAvailabilityAll($c = 1, $t = 4){
		$r = $this->getSolutionsForAvailabilityRefresh($c,$t);
		while($s = mysql_fetch_assoc($r)){
			$this->solID = $s['SolutionID'];
			$this->apiKey = $s['APIKey'];
			$this->importPropertyAvail();
		}
	}
	
	public function importRatesAll($c = 1, $t = 24){
		$r = $this->getSolutionsForRatesRefresh($c,$t);
		while($s = mysql_fetch_assoc($r)){
			$this->solID = $s['SolutionID'];
			$this->apiKey = $s['APIKey'];
			$this->importPropertyRates();
		}
	}
	
	public function importAll($c=1,$t=24){
		$r = $this->getSolutionsForFullRefresh($c,$t);
		while($s = mysql_fetch_assoc($r)){
			$this->solID = $s['SolutionID'];
			$this->apiKey = $s['APIKey'];
			$this->importPropertyFull();
		}
	}
	
	public function getPropertyMapArray(){
		$q = "SELECT 
				Latitude as latitude, 
				Longitude as longitude, 
				Headline as title, 
				CONCAT('<div class=''gallery-block''><a href=''propertydetail.php?propertyid=',p.BooktPropertyID,'''><h2>',Headline,'</h2></a><div><a href=''propertydetail.php?propertyid=',p.BooktPropertyID,'''><img src=''',OriginalURL,'''></a></div><a href=''propertydetail.php?propertyid=',p.BooktPropertyID,'''><span>',Beds,' Bedrooms | ',Baths,' Bathrooms | Sleeps ',Sleeps,'</span></a></div>') as content 
			from Properties p
			left join Photos ph
			ON p.BooktPropertyID = ph.BooktPropertyID and ph.`Order` = 0
			WHERE p.SolutionID = $this->solID";
		$r = $this->dbQuery($q);
		return($r);
	}
	
	public function randImage(){
		$q = "SELECT * FROM Photos p INNER JOIN Properties pp ON pp.BooktPropertyID = p.BooktPropertyID WHERE p.SolutionID = $this->solID and p.`Order` = 0 ORDER BY RAND() LIMIT 1";
		$r = $this->dbQuery($q);
		return(mysql_fetch_assoc($r));
	}
	
	public function submarketList($t,$c,$s,$m,$ta=''){
		$ct = ucfirst($t);
		$cc = urldecode($c);
		$cm = urldecode($m);
		$s_sql = '';
		if(($s=='')&&($t=='state')){
			$cs=$cm;
			$s_sql = "AND State = '$cs'";
		}
		if($s!=''){
			$cs = urldecode($s);
			$s_sql = "AND State = '$cs'";
		}
		$type = array("country"=>"Region","region"=>"State","state"=>"Metro","metro"=>"City","city"=>"Neighborhood");
		if($t=='neighborhood'){ return(false); }
		if($ta==''){
			$ta=$ct;
		}
		$q = "SELECT $type[$t] as MarketName, COUNT(*) AS NumProps FROM Properties WHERE SolutionID = $this->solID AND Country = '$cc' $s_sql AND $ta = '$cm' AND $type[$t] <> '' GROUP BY $type[$t] ORDER BY COUNT(*) DESC";
		//echo $q;
		$r = $this->dbQuery($q);
		if(mysql_num_rows($r)==0){
			$this->submarketList(lcfirst($type[$t]),$c,$s,$m,$ta);
		}
		if(mysql_num_rows($r)>0){
			?>
			<ul>
			<?php
			while($mk = mysql_fetch_assoc($r)){
				?>
				<li><a class="btn btn-large" href="location.php?country=<?= $c ?><?php if($s!=''){echo "&state=".$s; } ?>&<?= strtolower($type[$t]) ?>=<?= urlencode($mk['MarketName']) ?>&loctype=<?= strtolower($type[$t]) ?>"><?= $mk['MarketName'] ?> (<?= $mk['NumProps'] ?>)</a></li>
				<?php
			}
			?>
			</ul>
        	<div class="clearfix"></div>
            <hr>
			<?php
		}
	}
}

class Property extends BAPI
{
	public $propInfo;
	public $propType;
	public $propStatus;
	public $propLivingSpace;
	public $propBaths;
	public $propBeds;
	public $propInst;
	public $propDesc;
	public $propDevelopment;
	public $propAvailOnline;
	public $propGarageSpaces;
	public $propAddress1;
	public $propAddress2;
	public $propCity;
	public $propState;
	public $propPostalCode;
	public $propRegion;
	public $propCountry;
	public $propMetro;
	public $propCounty;
	public $propNeighborhood;
	public $propLongitude;
	public $propLatitude;
	public $propLotSize;
	public $propMaxRate;
	public $propMaxRateCurrency;
	public $propMinRate;
	public $propMinRateCurrency;
	public $propHeadline;
	public $propSleeps;
	public $propStories;
	public $propSummary;
	public $propYearBuilt;
	public $propManagedBy;
	public $propIsBookable;
	public $propNumReviews;
	public $propAvgReview;
	public $propSEO;
	public $propLastMod;
	
	public function __construct($s,$a,$pid,$forced=true){
		$this->solID = $s;
		$this->apiKey = $a;
		$this->propID = $pid;
		$this->propInfo = $this->getPropertyInfo($forced);
		$this->altID = $this->propInfo['AltID'];
		$this->propType = $this->propInfo['Type'];
		$this->propStatus = $this->propInfo['Status'];
		$this->propLivingSpace = $this->propInfo['AdjLivingSpace'];
		$this->propBaths = $this->propInfo['Bathrooms'];
		$this->propBeds = $this->propInfo['Bedrooms'];
		$this->propInst = $this->propInfo['CheckInInstructions'];
		$this->propDesc = $this->propInfo['Description'];
		$this->propDevelopment = $this->propInfo['Development'];
		$this->propAvailOnline = $this->propInfo['AvailableOnline'];
		$this->propGarageSpaces = $this->propInfo['GarageSpaces'];
		$this->propAddress1 = $this->propInfo['Address1'];
		$this->propAddress2 = $this->propInfo['Address2'];
		$this->propCity = $this->propInfo['City'];
		$this->propState = $this->propInfo['State'];
		$this->propPostalCode = $this->propInfo['PostalCode'];
		$this->propRegion = $this->propInfo['Region'];
		$this->propCountry = $this->propInfo['Country'];
		$this->propMetro = $this->propInfo['Metro'];
		$this->propCounty = $this->propInfo['County'];
		$this->propNeighborhood = $this->propInfo['Neighborhood'];
		$this->propLongitude = $this->propInfo['Longitude'];
		$this->propLatitude = $this->propInfo['Latitude'];
		$this->propLotSize = $this->propInfo['LotSize'];
		$this->propMaxRate = $this->propInfo['MaxRate'];
		$this->propMaxRateCurrency = $this->propInfo['MaxRateCurrency'];
		$this->propMinRate = $this->propInfo['MinRate'];
		$this->propMinRateCurrency = $this->propInfo['MinRateCurrency'];
		$this->propHeadline = $this->propInfo['Headline'];
		$this->propSleeps = $this->propInfo['Sleeps'];
		$this->propStories = $this->propInfo['Stories'];
		$this->propSummary = $this->propInfo['Summary'];
		$this->propYearBuilt = $this->propInfo['YearBuilt'];
		$this->propManagedBy = $this->propInfo['ManagedBy'];
		$this->propIsBookable = $this->propInfo['IsBookable'];
		$this->propNumReviews = $this->propInfo['NumReviews'];
		$this->propAvgReview = $this->propInfo['AvgReview'];
		$this->propSEO = $this->propInfo['ContextData']['SEO'];
		$this->propPhotos = $this->propInfo['Images'];
	}
	
	public function getPropertyInfo($forced = false){
		$q = "SELECT * FROM Properties WHERE BooktPropertyID = $this->propID AND SolutionID = $this->solID AND LastModified >= DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 1 HOUR)";
		$r = $this->dbQuery($q);
		if(mysql_num_rows($r)>0&&!$forced){
			$pr = mysql_fetch_assoc($r);
			//TBD = Load property data from DB when data is fresh. Some columns must be added to Properties table.  Rates/Avail should remain "FRESH" from API client-side.
			$data = array();
			$data['AltID'] = $pr['AltID'];
			$data['Type'] = $pr['Category'];
			$data['Status'] = 'Active';
			$data['AdjLivingSpace'];
			$data['Bathrooms'] = $pr['Baths'];
			$data['Bedrooms'] = $pr['Beds'];
			$data['CheckInInstructions'];
			$data['Description'] = $pr['Description'];
			$data['Development'];
			$data['AvailableOnline'];
			$data['GarageSpaces'];
			$data['Address1'] = $pr['Address1'];
			$data['Address2'] = $pr['Address2'];
			$data['City'] = $pr['City'];
			$data['State'] = $pr['State'];
			$data['PostalCode'] = $pr['PostalCode'];
			$data['Region'] = $pr['Region'];
			$data['Country'] = $pr['Country'];
			$data['Metro'] = $pr['Metro'];
			$data['County'];
			$data['Neighborhood'] = $pr['Neighborhood'];
			$data['Longitude'] = $pr['Longitude'];
			$data['Latitude'] = $pr['Latitude'];
			$data['LotSize'];
			$data['MaxRate'];
			$data['MaxRateCurrency'];
			$data['MinRate'];
			$data['MinRateCurrency'];
			$data['Headline'] = $pr['Headline'];
			$data['Sleeps'] = $pr['Sleeps'];
			$data['Stories'];
			$data['Summary'] = $pr['Summary'];
			$data['YearBuilt'];
			$data['ManagedBy'];
			$data['IsBookable'];
			$data['NumReviews'] = $pr['NumReviews'];
			$data['AvgReview'] = $pr['AvgReview'];
			$data['ContextData']['SEO'] = array(
				"MetaDescrip"=>$pr['MetaDescrip'],
				"MetaKeywords"=>$pr['MetaKeywords'],
				"PageTitle"=>$pr['PageTitle']
			);
			$data['Images'] = array();
			$data['Amenities'] = array(); //TBD
			
			$iq = "SELECT * FROM Photos WHERE BooktPropertyID = $this->propID AND SolutionID = $this->solID ORDER BY `Order` ASC";
			$ir = $this->dbQuery($iq);
			while($ph = mysql_fetch_assoc($ir)){
				$data['Images'][] = array(
					"OriginalURL" => $ph['OriginalURL'],
					"Caption" => $ph['Caption']
				);
			}
			
			return($data);
		}		
		else{
			$url = $this->baseURL.'/ws/?method=get&apiKey='.$this->apiKey.'&entity=property&seo=1&rates=1&poi=1&reviews=1&descrip=1&avail=1&ids='.$this->propID;
			$json = file_get_contents($url);
			$data = json_decode($json, TRUE);
			if(!empty($data['error'])){ 
				return(false); 
			}
			return($data['result'][0]);
		}
	}
	
	public function getPropertyRates(){
		$data = $this->propInfo;
		return($data['ContextData']['Rates']);
	}
	
	public function getPropertyAvail(){
		$data = $this->propInfo;
		if(empty($data['ContextData']['Availability'])){
			return(false);
		}
		return($data['ContextData']['Availability']);
	}
	
	public function getPropertyPhotos(){
		$data = $this->propInfo;
		if(empty($data['Images'])){
			return(false);
		}
		return($data['Images']);
	}
	
	public function importPropertyDetails(){
		$this->resetPropertyDetails();
		$this->savePropertyDetails();
		return(true);
	}
	
	public function importPropertyRates(){
		$this->resetPropertyRates();
		$a = $this->getPropertyRates();
		$a = $a['Values'];
		foreach($a as $d){
			$sd = date('Y-m-d', strtotime($d[0]));
			$ed = date('Y-m-d', strtotime($d[1]));
			$daily = 'null';
			$weekly = 'null';
			$monthly = 'null';
			if(!empty($d[2])&&$d[2]!='N/A'&&$d[2]!='null'&&$d[2]!=-1){
				$daily = str_replace('€','',$d[2]);
				$daily = str_replace('$','',$daily);
				$daily = number_format((float)$daily, 2, '.', '');
				//echo $daily."<br>\n\r";
			}
			if(!empty($d[3])&&$d[3]!='N/A'&&$d[3]!='null'&&$d[3]!=-1){
				$weekly = str_replace('€','',$d[3]);
				$weekly = str_replace('$','',$weekly);
				$weekly = number_format((float)$weekly, 2, '.', '');
				//echo $weekly."<br>\n\r";
			}
			if(!empty($d[4])&&$d[4]!='N/A'&&$d[4]!='null'&&$d[4]!=-1){
				$monthly = str_replace('€','',$d[4]);
				$monthly = str_replace('$','',$monthly);
				$monthly = number_format((float)$monthly, 2, '.', '');
				//echo $monthly."<br>\n\r";
			}
			//exit();
			$this->savePropertyRates($sd,$ed,$daily,$weekly,$monthly);
		}
	}
	
	public function importPropertyAvail(){
		$this->resetPropertyAvail();
		$a = $this->getPropertyAvail();
		if($a){
			foreach($a as $d){
				$cin = date('Y-m-d', preg_replace('/[^\d]/','', $d['CheckIn'])/1000);
				$cout = date('Y-m-d', preg_replace('/[^\d]/','', $d['CheckOut'])/1000);
				$this->savePropertyAvail($cin,$cout);
			}
		}
	}
	
	public function importPropertyPhotos(){
		$this->resetPropertyPhotos();
		$a = $this->getPropertyPhotos();
		if($a){
			$i=0;
			foreach($a as $d){
				//print_r($d);exit();
				$orig = mysql_real_escape_string($d['OriginalURL']);
				$med = mysql_real_escape_string($d['MediumURL']);
				$thumb = mysql_real_escape_string($d['ThumbnailURL']);
				$cap = mysql_real_escape_string($d['Caption']);
				$this->savePropertyPhotos($orig,$med,$thumb,$cap,$i);
				$i++;
			}
		}
	}
	
	public function importPropertyData(){
		$this->importPropertyDetails();
		$this->importPropertyRates();
		$this->importPropertyAvail();
		$this->importPropertyPhotos();
	}
	
	public function savePropertyDetails(){
		$summary = mysql_real_escape_string($this->propSummary);
		$desc = mysql_real_escape_string($this->propDesc);
		$cinst = mysql_real_escape_string($this->propInst);
		$headline = mysql_real_escape_string($this->propHeadline);
		$address1 = mysql_real_escape_string($this->propAddress1);
		$address2 = mysql_real_escape_string($this->propAddress2);
		$city = mysql_real_escape_string($this->propCity);
		$state = mysql_real_escape_string($this->propState);
		$postal = mysql_real_escape_string($this->propPostalCode);
		$metro = mysql_real_escape_string($this->propMetro);
		$country = mysql_real_escape_string($this->propCountry);
		$region = mysql_real_escape_string($this->propRegion);
		$neighborhood = mysql_real_escape_string($this->propNeighborhood);
		$metadesc = mysql_real_escape_string($this->propSEO['MetaDescrip']);
		$metakey = mysql_real_escape_string($this->propSEO['MetaKeywords']);
		$ptitle = mysql_real_escape_string($this->propSEO['PageTitle']);
		$this->propBeds = (int)$this->propBeds;
		$this->propBaths = (int)$this->propBaths;
		$this->propSleeps = (int)$this->propSleeps;
		$this->propLatitude = (float)$this->propLatitude;
		$this->propLongitude = (float)$this->propLongitude;
		$this->propAvgReview = (float)$this->propAvgReview;
		$this->propNumReviews = (float)$this->propNumReviews;
		$q = "INSERT INTO Properties (
							SolutionID, 
							BooktPropertyID, 
							AltID, 
							Headline, 
							Category,
							Summary,
							Description,
							Beds,
							Baths,
							Sleeps,
							Address1,
							Address2,
							City,
							State,
							PostalCode,
							Metro,
							Country,
							Region,
							Neighborhood,
							Latitude,
							Longitude,
							AvgReview,
							NumReviews,
							MetaDescrip,
							MetaKeywords,
							PageTitle) 
						VALUES (
							$this->solID, 
							$this->propID, 
							'$this->altID', 
							'$headline', 
							'$this->propType',
							'$summary',
							'$desc',
							$this->propBeds,
							$this->propBaths,
							$this->propSleeps,
							'$address1',
							'$address2',
							'$city',
							'$state',
							'$postal',
							'$metro',
							'$country',
							'$region',
							'$neighborhood',
							$this->propLatitude,
							$this->propLongitude,
							$this->propAvgReview,
							$this->propNumReviews,
							'$metadesc',
							'$metakey',
							'$ptitle')";
		//echo $q."<br>"; //exit();
		$this->dbQuery($q);
		return(true);
	}
	
	public function resetPropertyDetails(){
		$q = "DELETE FROM Properties WHERE SolutionID = $this->solID AND BooktPropertyID = $this->propID";
		$this->dbQuery($q);
		return(true);
	}
	
	public function savePropertyAvail($cin,$cout){
		$q = "INSERT INTO Availability (SolutionID, PropertyID, AltID, CheckIn, CheckOut) VALUES ($this->solID, $this->propID, '$this->altID', '$cin', '$cout')";
		$this->dbQuery($q);
		return(true);
	}
	
	public function resetPropertyAvail(){
		$q = "DELETE FROM Availability WHERE SolutionID = $this->solID AND PropertyID = $this->propID";
		$this->dbQuery($q);
		return(true);
	}
	
	public function savePropertyRates($sd,$ed,$d,$w,$m){
		$q = "INSERT INTO Rates (SolutionID, PropertyID, StartDate, EndDate, Daily, Weekly, Monthly) VALUES ($this->solID, $this->propID, '$sd', '$ed', $d, $w, $m)";
		$this->dbQuery($q);
		return(true);
	}
	
	public function resetPropertyRates(){
		$q = "DELETE FROM Rates WHERE SolutionID = $this->solID AND PropertyID = $this->propID";
		$this->dbQuery($q);
		return(true);
	}
	
	public function savePropertyPhotos($o,$m,$t,$c,$ord){
		$q = "INSERT INTO Photos (SolutionID, BooktPropertyID, OriginalURL, MediumURL, ThumbnailURL, Caption, `Order`) VALUES ($this->solID, $this->propID, '$o', '$m', '$t', '$c', $ord)";
		$this->dbQuery($q);
		return(true);
	}
	
	public function resetPropertyPhotos(){
		$q = "DELETE FROM Photos WHERE BooktPropertyID = $this->propID AND SolutionID = $this->solID";
		$this->dbQuery($q);
		return(true);
	}
	
	public function getBreadCrumbTrail(){
		$q = "SELECT
				(CASE WHEN COUNT(DISTINCT Region) > 1 THEN 1 ELSE 0 END) AS Country,
				(CASE WHEN COUNT(DISTINCT State) > 1 THEN 1 ELSE 0 END) AS Region,
				(CASE WHEN COUNT(DISTINCT Metro) > 1 THEN 1 ELSE 0 END) AS State,
				(CASE WHEN COUNT(DISTINCT City) > 1 THEN 1 ELSE 0 END) AS Metro
			FROM Properties 
			WHERE SolutionID = $this->solID
			GROUP BY SolutionID";
		$r = $this->dbQuery($q);
		$r = mysql_fetch_assoc($r);
		
		$bct="";
		$bctl="location.php?";
		$i = 0;
		if(!empty($this->propCountry)&&$r['Country']==1){
			if($i>0){
				$bct .= " >> ";
			}
			$bctl .= "&country=$this->propCountry";
			$bct .= "<a href='".$bctl."&loctype=country'>".$this->propCountry."</a>";
			$i++;
		}
		if(!empty($this->propRegion)&&$r['Region']==1){
			if($i>0){
				$bct .= " >> ";
			}
			$bctl .= "&region=$this->propRegion";
			$bct .= "<a href='".$bctl."&loctype=region'>".$this->propRegion."</a>";
			$i++;
		}
		if(!empty($this->propState)&&$r['State']==1){
			if($i>0){
				$bct .= " >> ";
			}
			$bctl .= "&state=$this->propState";
			$bct .= "<a href='".$bctl."&loctype=state'>".$this->propState."</a>";
			$i++;
		}
		if(!empty($this->propMetro)&&$r['Metro']==1){
			if($i>0){
				$bct .= " >> ";
			}
			$bctl .= "&metro=$this->propMetro";
			$bct .= "<a href='".$bctl."&loctype=metro'>".$this->propMetro."</a>";
			$i++;
		}
		if(!empty($this->propCity)){
			if($i>0){
				$bct .= " >> ";
			}
			$bctl .= "&city=$this->propCity";
			$bct .= "<a href='".$bctl."&loctype=city'>".$this->propCity."</a>";
			$i++;
		}
		if(!empty($this->propNeighborhood)){
			if($i>0){
				$bct .= " >> ";
			}
			$bctl .= "&neighborhood=$this->propNeighborhood";
			$bct .= "<a href='".$bctl."&loctype=neighborhood'>".$this->propNeighborhood."</a>";
			$i++;
		}
		return($bct);
	}
}
?>