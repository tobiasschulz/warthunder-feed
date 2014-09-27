<?php
header ("Content-type: application/atom+xml; charset=UTF-8");

function file_get_contents_utf8($fn) {
	$content = file_get_contents($fn);
	return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

$URL = "http://forum.warthunder.com/index.php?/forum/26-official-project-news-read-only/";

$html = file_get_contents_utf8($URL);
$lines = explode("\n", $html);

$threads = array();
$biggestThreadId = 0;

function formatdate($threadid) {
	return date(DATE_ATOM, $threadid * 475 + 1325376000);
}

foreach ($lines as $line) {
	$line = str_replace("'", "\"", $line);
	if (preg_match('/<a itemprop="url" id="(?:[^"]+)" href="([^"]+)" title="([^"]+?)\s*- started\s*([^"]+?)\s*"/', $line, $groups)) {
		$url = $groups[1];
		$title = $groups[2];
		$formatted_date = $groups[3];
		if (preg_match('/\/topic\/([0-9]+)/', $url, $groups2)) {
			$threadid = $groups2[1];
			$biggestThreadId = $threadid > $biggestThreadId ? $threadid : $biggestThreadId;
			$pubDate = formatdate($threadid);
			$threads[] = array("url" => $url, "title" => $title, "threadid" => $threadid, "pubDate" => $pubDate, "formatted_date" => $formatted_date);
		}
	}
}

function compare_by_threadid($a, $b) {
	return $b['threadid'] - $a['threadid'];
}

uasort($threads, 'compare_by_threadid');

$updated = formatdate($biggestThreadId);

echo '<?xml version="1.0" encoding="UTF-8" ?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>War Thunder News</title>
  <author><name>War Thunder</name></author>
  <link type="text/html" href="'.$URL.'" />
  <id>'.$URL.'</id>
  <updated>'.$updated.'</updated>
  <icon>http://forum.warthunder.com/favicon.ico</icon>
';

foreach ($threads as $thread) {
	$url = htmlentities($thread[url]);
	print "
  <entry>
    <updated>$thread[pubDate]</updated>
    <published>$thread[pubDate]</published>
    <title>$thread[title] ($thread[formatted_date])</title>
    <link href='$url' />
    <id>$url</id>
    <summary>$thread[title]</summary>
  </entry>
";
}

echo '
</feed>
';

?>
