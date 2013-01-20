<?php

require_once('config.php');


if($handle = opendir('data/meta')){
	while (false !== ($file = readdir($handle))) {
        if(strstr($file, 'json')){
        	$metafiles[] = json_decode(file_get_contents('data/meta/'.$file), 1);
        }
    }
}

foreach ($metafiles as $metafile) {
	$item["duration"][] = str_replace(["PT", "H", "M", "S"], ["", ":", ":", ""], $metafile["duration"]);
	$item["description"][] = substr($metafile["description"], 0, 255);
	$item["id"][] = $metafile["id"];
	$item["publishedAt"][] = date(DATE_RFC822, strtotime($metafile["publishedAt"]));
	$item["title"][] = str_replace(["&"], ["&amp;"], $metafile["title"]);
}

array_multisort($item["publishedAt"], $item["duration"], $item["description"], $item["id"], $item["title"]);
$item["duration"] = array_reverse($item["duration"]);
$item["description"] = array_reverse($item["description"]);
$item["id"] = array_reverse($item["id"]);
$item["publishedAt"] = array_reverse($item["publishedAt"]);
$item["title"] = array_reverse($item["title"]);


header("Content-Type: application/rss+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

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
		<itunes:image href="http://rzlcast.horo.li/podcast.png" />
		<itunes:category text="Technology" />
		<itunes:explicit>clean</itunes:explicit>

			<?php for ($i=0; $i < count($item["id"]); $i++) { ?>
<item>
				<title><?=$item["title"][$i];?></title>
				<itunes:author><?=$author;?></itunes:author>
				<itunes:subtitle><![CDATA[<?=$item["description"][$i];?>]]></itunes:subtitle>
				<itunes:summary><![CDATA[<?=$item["description"][$i];?>]]></itunes:summary>
				<itunes:image href="http://rzlcast.horo.li/podcast.png" />
				<enclosure url="http://rzlcast.horo.li/data/videos/<?=$item["id"][$i];?>.mp4" length="<?=filesize('data/videos/'.$item["id"][$i].'.mp4');?>" type="video/mp4" />
				<guid>http://rzlcast.horo.li/data/videos/<?=$item["id"][$i];?>.mp4</guid>
				<pubDate><?=$item["publishedAt"][$i];?></pubDate>
				<itunes:duration><?=$item["duration"][$i];?></itunes:duration>
			</item>
			<?}?>
	</channel>
</rss>