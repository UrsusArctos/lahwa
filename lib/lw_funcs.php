<?php // (LahtaWatch) Auxiliary Functions
declare(encoding='UTF-8');
//
function GetCommentPageUrl($subject, $pageindex = 0) { return sprintf("https://habr.com/ru/users/%s/comments/page%u/",$subject,intval($pageindex)); };

function GetMaxCommentPageIndex($subject, $raw_html) {
	$matches = array();
	$match_count = preg_match_all("/\/ru\/users\/$subject\/comments\/page(\d+)\//",$raw_html,$matches,PREG_PATTERN_ORDER);
	assert($match_count!==false);
	assert($match_count>0);
	return max($matches[1]);
}

function GetAllTimeStamps($raw_html) {
	$matches = array();
	$match_count = preg_match_all('/<time.*\>(.*)\sв\s(\d{2}\:\d{2})<\/time>/',$raw_html,$matches,PREG_PATTERN_ORDER);
	assert($match_count!==false);
	assert($match_count>0);
	// Handle special day cases (today and yesterday)
	setlocale(LC_TIME,"ru_RU.utf8");
	foreach ($matches[1] as $idx => &$dayname) {
		switch ($dayname) {
			// YES I KNOW this is a very dirty hack!
			// So please do not run this tool whenever HABR servers are past midnight and you are not yet.
			// BTW, in order for this to work correctly, your locale -a command should display ru_RU.utf8 locale in the list.
			// Otherwise, messages for past two days will be unaccounted for.
			case "сегодня" : $dayname =trim(strftime("%e %B %Y")); break;
			case "вчера" : $dayname = trim(strftime("%e %B %Y",mktime(0,0,0,date("m"),date("d")-1,date("Y")))); break;
		}
		// Prepare resulting array
		$result[$idx]['text'] = $matches[1][$idx]." ".$matches[2][$idx];
		$parsed = strptime($result[$idx]['text'],"%e %B %Y %H:%M");
		assert($parsed!==false);
		$parsed['tm_year']+=1900;
		$parsed['tm_mon']++;
		$result[$idx]['unixtime'] = mktime($parsed['tm_hour'],$parsed['tm_min'],$parsed['tm_sec'],$parsed['tm_mon'],$parsed['tm_mday'],$parsed['tm_year']);
		$result[$idx]['parsed'] = $parsed;
	}
	return $result;
}

function GetAllRatings($raw_html) {
	$matches = array();
	// Bear in mind, what appears to be the minus sign is actually U+2013 EN DASH
	$match_count = preg_match_all('/<span class="voting-wjt__counter.*">([\x{2013}+]?\d*)<\/span>/u',$raw_html,$matches,PREG_PATTERN_ORDER);
	assert($match_count!==false);
	assert($match_count>0);
	// Make it all numbers. Intval needs good old U+002D HYPHEN MINUS for minus, so convert it.
	array_walk($matches[1],function (&$item, $key) { $item = intval(str_replace("\u{2013}","\u{002D}",$item)); });
	return $matches[1];
}

?>