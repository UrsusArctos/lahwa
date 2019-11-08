<?php // (LahtaWatch) HabraScanner for timestamps
declare(encoding='UTF-8');
//
require_once('conf_mysql.php');
require_once('lib/aby_mysql.php');
require_once('lib/lw_funcs.php');
// Parameters init
$subj = strtolower($argv[1]);	// habrauser name
$pidx = 1;						// first page is always 1
// DB init
$mysql = new MySQL();
// Establish subject ID (either create new or find existing)
$qres = $mysql->SelectQuery('SELECT sid FROM '.MYSQL_TABLE_SUBJECTS.' WHERE username="'.$mysql->RealEscapeString($subj).'"');
if (is_null($qres)) {
	// New subject
	$subj_ID = $mysql->InsertUpdateQuery('INSERT INTO '.MYSQL_TABLE_SUBJECTS.' (username) VALUES ("'.$mysql->RealEscapeString($subj).'")');
	assert($subj_ID!==false);
} else { $subj_ID = intval($qres[0]['sid']); };
echo "Investigating subject $subj_ID ($subj)\n";
// Comment page retrieval loop
do {
	// Load page
	echo "Retrieving comments page $pidx of ";
	$page_raw_html = file_get_contents(GetCommentPageUrl($subj,$pidx));
	$maxidx = GetMaxCommentPageIndex($subj,$page_raw_html);
	echo $maxidx." : ";
	// Get all timestamps from raw page
	$tstamps = GetAllTimeStamps($page_raw_html);
	// Update database	
	$query = 'INSERT IGNORE INTO '.MYSQL_TABLE_EVENTS.' (sid,commevent) VALUES ';
	$comma = '';
	foreach ($tstamps as $event) {
		$query .= $comma.'('.$subj_ID.',FROM_UNIXTIME('.$event['unixtime'].'))';
		$comma = ',';
		echo ".";
	}
	$qres = $mysql->InsertUpdateQuery($query);
	if ($qres!==false) { echo "+\n"; } else { echo "?\n"; }	
	// Prepare for next page
	$pidx++;
} while ($pidx<=$maxidx);
// Cleanup
unset($mysql);
?>