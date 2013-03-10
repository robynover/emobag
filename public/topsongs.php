<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Emotional Bag Check</title>
	<!-- Date: 2011-01-30 -->
	<link type="text/css" rel="stylesheet" media="all" href="css/emobag.css" /> 
	<script type="text/javascript" src="js/emo.js"></script>
</head>
<body class="stats">
	<div id="wrapper">
		<div id="container">
		<div id ="full_head_block"><a href="http://emotionalbagcheck.com"><img src="images/rack_icon.gif" class="rack_icon"/></a></div>
<h2>Top 25 Songs</h2>			
<?php
require_once('/your_full_path/private/class.Cache.php');
//check to see if the results are cached
$cache = new Cache();
$contents = $cache->getCache();
if ($contents){
	echo $contents;
} else {
	//pull records from the DB
	$output = '';
	require_once('/your_full_path/private/class.DB.php');
	$query = "SELECT tiny_song_url,song_title,song_artist, COUNT(tiny_song_url) as ct FROM bag_queue GROUP BY tiny_song_url ORDER BY ct DESC LIMIT 25";
	$db = DB::getInstance();
	$output .= '<ol>';
	foreach ($db->query($query) as $row) {
		$output .= '<li>';
		$output .= '<a href="'.$row['tiny_song_url'] .'">'.$row['song_title']. "</a> by ".$row['song_artist'] ;
		$output .= ' ('.number_format($row['ct'],0,'.',',').')';
		$output .= "</li>\n";
	}
 	$output .='</ol>';
	echo $output;
	//save the new info to cache
	$cache->writeCache($output);
}



?>
<p class="backhome">
<strong>Top 25 Songs</strong> | <a href="http://emotionalbagcheck.com/topartists.php">Top 25 Artists</a> | <a href="randomsongs.php">25 Random Songs</a><br/>
<a href="http://emotionalbagcheck.com">emotionalbagcheck.com</a></p><p>&nbsp;</p>
		</div><!-- /container -->	
	</div> <!-- /wrapper -->

</body>
</html>