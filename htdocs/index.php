<?php
include("simplepie_1.3.1.compiled.php");

function convert_duration($sec) {
	$h = floor($sec/3600);
	$m = floor($sec/60) % 60;
	$s = $sec % 60;
	$out = "";
	if ($h>0)
		$out .= $h . "h&nbsp;";
	if ($m>0)
		$out .= $m . "m&nbsp;";
	$out .= $s . "s";
	return $out;
}

$feed = new SimplePie();
$feed->set_feed_url('http://feeds.feedburner.com/RaumzeitlaborPodcast');
$feed->init();
$feed->handle_content_type();

$items = array();

foreach ($feed->get_items() as $feed_item){
	if ($enclosure = $feed_item->get_enclosure()) {
		$item = array();
		$item["duration"] = convert_duration($enclosure->get_duration(false));
		$item["description"] = $feed_item->get_description();
		$item["id"] = basename($enclosure->get_link(), ".mp4");
		$item["publishedAt"] = $feed_item->get_date("d.m.Y");
		$item["title"] = $feed_item->get_title();
		$item["filesize"] = $enclosure->get_size();
		$item["file"] = $enclosure->get_link();
		$items[] = $item;
	}
}

?><!DOCTYPE html>
<html>
<head>
	<title>RaumZeitLabor Podcast</title>
	
	<meta charset="utf-8">
	
	<link rel="alternate" type="application/rss+xml" title="Podcast Feed" href="http://feeds.feedburner.com/RaumzeitlaborPodcast">
	
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	
	<script type="text/javascript" src = "http://api.flattr.com/js/0.6/load.js?mode=auto"></script>
	<script src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
</head>
<body>
	<div class="container-narrow">

	  <div class="masthead">
		<ul class="nav nav-pills pull-right">
		  <li class="active"><a href="index.php">Vortragsliste</a></li>
		  <li><a href="imprint.php">Kontakt/Impressum</a></li>
		</ul>
		<h3 class="muted">RaumZeitLabor Podcast</h3>
	  </div>

	  <hr>

	  <div class="jumbotron">
			<h1>RaumZeitLabor Podcast</h1>
			<p class="lead">Freies Wissen &mdash; nun auch als Podcast</p>
			<a href="https://itunes.apple.com/de/podcast/raumzeitlabor-podcast/id595143602?l=de"><img src="subscribe.png"></a>
			<div style="margin-top: 10px;">
				<a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="https://raumzeitlabor.de/wiki/Podcast"></a>
				<noscript><a href="http://flattr.com/thing/1106488/RaumZeitLabor-Podcast" target="_blank">
				<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></noscript>
			</div>
	  </div>

	  <hr>

	<table class="table table-condensed table-striped">
		<tr>
			<th>Titel</th><th>Erschienen</th><th>Länge</th><th>Download</th><th>Watch it</th>
		</tr>
		<?php foreach ($items as $item) { ?>
		<tr id="<?=$item["id"];?>">
			<td><?=$item["title"];?></td><td><?=$item["publishedAt"];?></td><td style="text-align: right;"><?=$item["duration"];?></td><td style="text-align: center;"><a href="<?=$item["file"];?>">.mp4</a></td><td><a href="https://www.youtube.com/watch?v=<?=$item["id"];?>">YouTube</a></td>
		</tr>
		<?php } ?>

	</table>

	  <hr>

	  <div class="footer">
		<p style="text-align: center;"><a rel="license" href="http://creativecommons.org/licenses/by/3.0/de/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/de/88x31.png" /></a></p>
	  </div>

	</div> <!-- /container -->

</body>
</html>
