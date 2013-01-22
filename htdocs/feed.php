<?php

require_once('config.php');

function compare_items($a, $b) {
	return strnatcmp($b["publishedAt_raw"], $a["publishedAt_raw"]); // Order switched to have reverse sorting
}

if($handle = opendir('data/meta')){
	while (false !== ($file = readdir($handle))) {
        if(strstr($file, 'json')){
        	$metafiles[] = json_decode(file_get_contents('data/meta/'.$file), 1);
        }
    }
}

$items = array();

$search = array("PT", "H", "M", "S");
$replace = array ("", ":", ":", "");

foreach ($metafiles as $metafile) {
	$item = array();
	$item["duration"] = str_replace($search, $replace, $metafile["duration"]);
	$item["description"] = $metafile["description"];
	$item["summary"] = substr($metafile["description"], 0, 255);
	$item["id"] = $metafile["id"];
	$item["publishedAt"] = date(DATE_RFC822, strtotime($metafile["publishedAt"]));
	$item["title"] = htmlentities($metafile["title"]);
	$item["publishedAt_raw"] = strtotime($metafile["publishedAt"]);
	$item["filesize"] = filesize('data/videos/'.$item["id"].'.mp4');
	$items[] = $item;
}

usort($items, "compare_items");


header("Content-Type: application/rss+xml");
?><?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
	<channel>
		<title><?= $title; ?></title>
		<link><?= $url; ?></link>
		<language><?= $language; ?></language>
		<copyright>&#x2117; <?= date("Y")." ".$author;?></copyright>
		<itunes:subtitle><?= $subtitle; ?></itunes:subtitle>
		<itunes:author><?= $author; ?></itunes:author>
		<itunes:summary><?= $summary; ?></itunes:summary>
		<description><?= $description; ?></description>
		<itunes:owner>
			<itunes:name><?= $owner_name; ?></itunes:name>
			<itunes:email><?= $owner_mail; ?></itunes:email>
		</itunes:owner>
		<itunes:image href="http://rzlcast.horo.li/podcast.jpg" />
		<itunes:category text="Technology" />
		<itunes:explicit>clean</itunes:explicit>

			<?php foreach ($items as $item) { ?>
			<item>
				<title><?=$item["title"];?></title>
				<description><![CDATA[<?=$item["description"];?>]]></description>
				<enclosure url="http://rzlcast.horo.li/data/videos/<?=$item["id"];?>.mp4" length="<?=$item["filesize"];?>" type="video/mp4" />
				<guid>http://rzlcast.horo.li/data/videos/<?=$item["id"];?>.mp4</guid>
				<pubDate><?=$item["publishedAt"];?></pubDate>
				<itunes:author><?=$author;?></itunes:author>
				<itunes:subtitle><![CDATA[<?=$item["description"];?>]]></itunes:subtitle>
				<itunes:summary><![CDATA[<?=$item["summary"];?>]]></itunes:summary>
				<itunes:image href="http://rzlcast.horo.li/podcast.png" />
				<itunes:duration><?=$item["duration"];?></itunes:duration>
			</item>
			<?}?>
	</channel>
</rss>
