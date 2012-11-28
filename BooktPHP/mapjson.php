<?php
require_once('bapi.php');
$b = new BAPI();
$b->solID = $_GET['bsolid'];
$r = $b->getPropertyMapArray();
$arr = array();
while($m = mysql_fetch_assoc($r)){
	$arr['markers'][]=$m;
}
print json_encode($arr);
?>