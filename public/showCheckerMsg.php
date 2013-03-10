<?php 
session_start();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
	<html lang="en">

	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Checked Baggage</title>
		<link type="text/css" rel="stylesheet" media="all" href="css/emobag.css" /> 
		<script type="text/javascript" src="js/jquery-1.5.js"></script>
		<script type="text/javascript" src="js/emo.js"></script>
		<script type="text/javascript" src="http://use.typekit.com/id.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
		
	</head>
		<body class="showMsg_pg">
		<div id="wrapper">
			<div id="container">
			<div id ="full_head_block"><a href="http://emotionalbagcheck.com"><img src="images/bag_icon.gif" class="bag_icon"/></a></div>
			<div id="left_col">
				<div class="col_inner">
<?php
$salt = file_get_contents('/your_full_path/private/salt.txt');
$_SESSION['token'] = md5($_SERVER['REMOTE_ADDR'].$salt);
require_once('/your_full_path/private/class.BagChecker.php');
require_once('/your_full_path/private/class.Song.php');

//get the next bag in line -- oldest in bag_checker with taken_flag = 0
$queue = new Song();

$checker_id = $queue->getNextCheckerId(true);
$checker = new BagChecker($checker_id);
$got_bag = 0;

if (!is_null($checker->msg)){
	$got_bag = 1;
	echo "<h2>Here's the emotional baggage someone recently checked.</h2>\n<p>";
	echo nl2br($checker->msg);	
	echo "</p>";
} else {
	echo "<h2>We're currently out of emotional baggage</h2>";
	echo "<p>Try again later, when there's more pain to go around. Of course, you can always <a href='/'>add some</a>.</p>";
	
}



//song search:
?>
				</div> <!-- /col_inner -->
			</div> <!-- /left_col -->

			<div id="right_col">
				<div class="col_inner">
				<?php if ($got_bag): ?>
					<h2>Send a song?</h2>
					<p>Have a song that you’d listen to if you were in the same sitch? 
					Hand it over in exchange for the baggage.</p>
				
					<!-- //song search form -->
					<form id="form1" name="form1" method="post" action="" onsubmit="songSearch();return false;">
					<input type="text" name="song_search" id="song">
					<input type="hidden" name="checker_id" id="checker_id" value="<?php echo $checker_id; ?>"/>
					<input type="submit" name="btn" id="btn" value="Get Song"/>
					</form>
					<div id="songbox"></div>
				<?php else: ?>
					<p>&nbsp;</p>
				<?php endif; ?>
	
			</div><!-- /col_inner -->
		</div>	<!-- /right_col -->
	</div><!-- /container -->
</div> <!-- /wrapper -->
</body>
</html>