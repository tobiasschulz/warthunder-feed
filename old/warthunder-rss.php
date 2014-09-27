<?php
header ("Content-type: application/rss+xml");

$URL = "http://forum.warthunder.com/index.php?/forum/26-official-project-news-read-only/";

$html = file_get_contents($URL);
$lines = explode("\n", $html);

$threads = array();

foreach ($lines as $line) {
	$line = str_replace("'", "\"", $line);
	if (preg_match('/<a itemprop="url" id="(?:[^"]+)" href="([^"]+)" title="([^"]+)"/', $line, $groups)) {
		$url = $groups[1];
		$title = $groups[2];
		if (preg_match('/\/topic\/([0-9]+)/', $url, $groups2)) {
			$threadid = $groups2[1];
			$pubDate = date(DATE_RSS, $threadid * 500 + 1325376000);
			$threads[] = array("url" => $url, "title" => $title, "threadid" => $threadid, "pubDate" => $pubDate);
		}
	}
}

echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">

<channel>
  <title>War Thunder News</title>
  <link>'.$URL.'</link>
  <description></description>
';

foreach ($threads as $thread) {
	print "
  <item>
    <title>$thread[title]</title>
    <link>$thread[url]</link>
    <guid>$thread[url]</guid>
    <description>$thread[title]</description>
    <pubDate>$thread[pubDate]</pubDate>
  </item>
";
}

echo '
</channel>
</rss>
';

?>
