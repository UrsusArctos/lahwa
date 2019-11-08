<?php // (LahtaWatch) Comment timeline plotter
declare(encoding='UTF-8');
//
require_once('conf_mysql.php');
require_once('lib/aby_mysql.php');
//
define('MINUTES_PER_HOUR',60);
define('MINUTES_PER_DAY',MINUTES_PER_HOUR*24);
define('DAYS_PER_YEAR',365); // Yes I know about leap years
// Parameters init
$subj_ID = intval($argv[1]);	// Subject ID
// DB init
$mysql = new MySQL();
// Determine activity span
$qres = $mysql->SelectQuery('SELECT DATE(MIN(commevent)) AS dmin,DATE(MAX(commevent)) AS dmax,DATEDIFF(DATE(MAX(commevent)),DATE(MIN(commevent))) AS days FROM '.MYSQL_TABLE_EVENTS.' WHERE sid='.$subj_ID);
assert(!is_null($qres));
$days = intval($qres[0]['days']);
echo "Activity span from ".$qres[0]['dmin']." to ".$qres[0]['dmax']." totaling $days days\n";
assert($days>0);
// Prepare canvas and colors
$graph = imagecreatetruecolor(MINUTES_PER_DAY+1,$days);
$greenColor = imagecolorallocate($graph,0x80,0xFF,0x80);
$grayColor = imagecolorallocatealpha($graph,0x80,0x80,0x80,0x60);
// Draw hourly/yearly grid
for ($h=0; $h<=24; $h++) { imageline($graph,$h*MINUTES_PER_HOUR,0,$h*MINUTES_PER_HOUR,$days,$grayColor); };
for ($y=0; $y<=intdiv($days,DAYS_PER_YEAR); $y++) { imageline($graph,0,$y*(DAYS_PER_YEAR),MINUTES_PER_DAY+1,$y*DAYS_PER_YEAR,$grayColor); };
// Day iterator
$mysql->SelectQuery('SET @BaseDate = (SELECT DATE(MIN(commevent)) FROM '.MYSQL_TABLE_EVENTS.' WHERE (sid='.$subj_ID.'))');
for ($d = 0; $d<=$days; $d++) {
	// Auxiliary queries
	$mysql->SelectQuery('SET @LoDate = DATE_ADD(@BaseDate,INTERVAL '.$d.' DAY)');
	$mysql->SelectQuery('SET @HiDate = DATE_ADD(@BaseDate,INTERVAL '.($d+1).' DAY)');
	$qres = $mysql->SelectQuery('SELECT *,((UNIX_TIMESTAMP(commevent)-UNIX_TIMESTAMP(@LoDate)) DIV '.MINUTES_PER_HOUR.') AS event_minute FROM '.MYSQL_TABLE_EVENTS.' WHERE (commevent BETWEEN @LoDate AND @HiDate) AND (sid='.$subj_ID.')');
	if (!is_null($qres)) {
		// Dataset for a day
		echo "\nHabraDay $d of $days : ";
		if (count($qres)>0) foreach ($qres as $comment) {
			// Set pixel
			$minute = intval($comment['event_minute']);
			imagesetpixel($graph,$minute,$days-$d,$greenColor);
			imageellipse($graph,$minute,$days-$d,3,3,$greenColor);
			echo "#";
		}
		echo "\n";
	} else echo ".";
}
// Save image
imagepng($graph,'lwgraph-'.$subj_ID.'.png',-1);
// Cleanup
imagedestroy($graph);
unset($mysql);
?>