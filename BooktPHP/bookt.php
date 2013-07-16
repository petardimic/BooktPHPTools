<?php
require_once('bapi.php');

if(!empty($_REQUEST['sync'])&&($_REQUEST['sync']==1)){
	$b = new BAPI($_REQUEST['solID'],$_REQUEST['apiKey']);
	$b->importPropertyFull();
	header('Location: bookt.php');
	exit();
}

$b = new BAPI();
//$b->importAll(1,0);
$r = $b->getAccounts();
?>
<style>
th,td{padding:2px 8px;}
</style>
<table>
    <thead>
        <th>Solution ID</th>
        <th>Solution Name</th>
        <th>API Key</th>
        <th>Property Count</th>
        <th>Booking Count</th>
        <th>Rates Count</th>
        <th>Photos Count</th>
        <th>Last Sync Time</th>
        <th>Running</th>
        <th>Action</th>
    </thead>
<?php
while($sol = mysql_fetch_assoc($r)){
    ?>
    <tr>
        <td><?= $sol['SolutionID'] ?></td>
        <td><?= $sol['SolutionName'] ?></td>
        <td><?= $sol['APIKey'] ?></td>
        <td><?= $sol['PropertyCount'] ?></td>
        <td><?= $sol['BookingCount'] ?></td>
        <td><?= $sol['RatesCount'] ?></td>
        <td><?= $sol['PhotosCount'] ?></td>
        <td><?= max($sol['LastAvailSyncEnd'],$sol['LastRateSyncEnd'],$sol['LastFullSyncEnd']) ?></td>
        <td><?= $sol['Running'] ?></td>
        <td><a href="bookt.php?sync=1&solID=<?= $sol['SolutionID'] ?>&apiKey=<?= $sol['APIKey'] ?>">Sync</a></td>
    </tr>
    <?php
}
?>
</table>