<?php
require_once('bapi.php');
$b = new BAPI();

$qi = "SELECT COUNT(*) as Total FROM Properties";
$ri = $b->dbQuery($qi);
$i = mysql_result($ri,0,'Total');
$l = ceil(($i/(4*12))*1.2);


$q2 = "SELECT 
  s.SolutionID, 
  s.APIKey
FROM Solutions s
WHERE s.LastFullSyncEnd < DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 0 HOUR)
ORDER BY s.LastFullSyncEnd ASC";
//echo $q2; exit();
$r2 = $b->dbQuery($q2);

while($row = mysql_fetch_assoc($r2)){
	$b->solID = $row['SolutionID'];
	$b->apiKey = $row['APIKey'];
	$b->syncStart('Full');
	$b->getPropertyList();
	$b->purgeProperties();
	$b->addMissingProperties();
	$b->syncEnd('Full');	
}

$q = "SELECT 
  s.SolutionID, 
  p.BooktPropertyID, 
  p.LastModified,
  s.APIKey
FROM Solutions s
LEFT JOIN Properties p ON p.SolutionID = s.SolutionID
WHERE p.LastModified < DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 4 HOUR)
GROUP BY s.SolutionID, p.BooktPropertyID
ORDER BY p.LastModified ASC
LIMIT $l";
$r = $b->dbQuery($q);

while($row = mysql_fetch_assoc($r)){
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
	
	$b->solID = $row['SolutionID'];
	$b->apiKey = $row['APIKey'];
	
	$p = new Property($row['SolutionID'],$row['APIKey'],$row['BooktPropertyID']);
	$p->importPropertyData();
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	echo $row['SolutionID'].", ".$row['BooktPropertyID'].", ".$total_time." seconds<br>";
}
?>