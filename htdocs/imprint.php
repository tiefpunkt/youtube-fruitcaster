<?php

function compare_items($a, $b) {
    return strnatcmp($b["publishedAt_raw"], $a["publishedAt_raw"]); // Order switched to have reverse sorting
}

function xmlentities($str) {
    $str = str_replace("&", "&amp;", $str);
    $search = array("<", ">", "'", "\"");
    $replace = array("&lt;", "&gt;", "&apos;", "&quot;");
    $str = str_replace($search, $replace, $str);
    return $str;
}

if($handle = opendir('data/meta')){
    while (false !== ($file = readdir($handle))) {
        if(strstr($file, 'json')){
            $metafiles[] = json_decode(file_get_contents('data/meta/'.$file), 1);
        }
    }
}

$items = array();

foreach ($metafiles as $metafile) {
    if (!file_exists('data/videos/'.$metafile["id"].'.mp4')) {
        continue;
    }
    $item = array();
    $item["duration"] = str_replace(array("PT", "H", "M", "S"), array ("", ":", ":", ""), $metafile["duration"]);
    $item["description"] = $metafile["description"];
    $item["summary"] = substr($metafile["description"], 0, 255);
    $item["id"] = $metafile["id"];
    $item["publishedAt"] = date(DATE_RFC822, strtotime($metafile["publishedAt"]));
    $item["title"] = xmlentities($metafile["title"]);
    $item["publishedAt_raw"] = strtotime($metafile["publishedAt"]);
    $item["filesize"] = filesize('data/videos/'.$item["id"].'.mp4');
    $items[] = $item;
}

usort($items, "compare_items");

?>

<!DOCTYPE html>
<html>
<head>
    <title>RaumZeitLabor Podcast</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <meta charset="utf-8">
</head>
<body>
    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li><a href="index.php">Vortragsliste</a></li>
          <li  class="active"><a href="imprint.php">Kontakt/Impressum</a></li>
        </ul>
        <h3 class="muted">RaumZeitLabor Podcast</h3>
      </div>

      <hr>

        <p>Angaben gemäß § 5 TMG:</p>
        <p><strong>RaumZeitLabor e.V.</strong><br>
        Boveristraße 22-24<br>
        68309 Mannheim<br>
        E-Mail: <a href="mailto:info@raumzeitlabor.de">info@raumzeitlabor.de</a></p>

      <hr>

      <div class="footer">
        <p style="text-align: center;"><a rel="license" href="http://creativecommons.org/licenses/by/3.0/de/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/de/88x31.png" /></a></p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/js/jquery.js"></script>
    <script src="/js/bootstrap-transition.js"></script>
    <script src="/js/bootstrap-alert.js"></script>
    <script src="/js/bootstrap-modal.js"></script>
    <script src="/js/bootstrap-dropdown.js"></script>
    <script src="/js/bootstrap-scrollspy.js"></script>
    <script src="/js/bootstrap-tab.js"></script>
    <script src="/js/bootstrap-tooltip.js"></script>
    <script src="/js/bootstrap-popover.js"></script>
    <script src="/js/bootstrap-button.js"></script>
    <script src="/js/bootstrap-collapse.js"></script>
    <script src="/js/bootstrap-carousel.js"></script>
    <script src="/js/bootstrap-typeahead.js"></script>

</body>
</html>