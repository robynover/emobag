<?php
/*sendmessage.php*/
session_start();
ini_set('display_errors',0);
//--------set cookie to track overuse
$fake1 = rand(600,13400);
$chr = rand(0,51);
$a_z = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
$fake2 = md5($a_z[$chr]."_$".rand(1000,6000));
$timeout = time() + (60*2);
setcookie('usertrack',$fake1,$timeout);
setcookie('xjd',$a_z[$chr].$chr.strlen($fake1),$timeout);
setcookie('_dbag_rr',$fake2,$timeout);
//------------//
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Emotional Bag Check</title>
	<meta name="author" content="Robyn Overstreet">
	<!-- Date: 2011-01-30 -->
	<link type="text/css" rel="stylesheet" media="all" href="css/emobag.css" /> 
	<script type="text/javascript" src="js/emo.js"></script>
</head>
<body class="thanksForMsg">
	<div id="wrapper">
		<div id="container">
		<div id ="full_head_block"><a href="/"><img src="images/bag_icon.gif" class="bag_icon"/></a></div>
			
<?php
//check token
$send_salt = file_get_contents('/your_full_path/private/send_salt.txt');
if ($_SESSION['sendtoken'] != md5($_SERVER['REMOTE_ADDR'].$send_salt)){
	echo "<p>Sorry. We've been having hacker trouble and can't send your song. Please try again later.</p>";
	exit;
}

require_once('/your_full_path/private/class.Song.php');
require_once('/your_full_path/private/class.BagChecker.php');
$success = 0;

$link_home = '<p>Want to share some of your own baggage? <a href="/">Start again</a>.</p>';
$link_home .= '<p>Curious to know which songs are sent most often? Check out the <a href="topsongs.php">Top Songs</a>.</p>';

$taker_msg = trim(strip_tags($_POST['taker_msg']));
$has_msg = 0;
if (strlen($taker_msg) > 1){
	$has_msg = 1;
}


if ($_POST['submit'] == 'Send' && $has_msg){
	$queue_id = (int)$_POST['sid']; //queue id = song id
	//$taker_msg = strip_tags($_POST['taker_msg']);
	$song_queue = new Song();
	$success = $song_queue->addMessage($queue_id,$taker_msg);
	
	if ($success){
		echo "<p>Thank you for taking the time to send a message. You've probably made someone's day a little better.</p>";
		echo $link_home;
		
	}
} else if ($_POST['submit']){
	//no message. just mark queue as ready to send.
	$queue_id = (int)$_POST['sid']; //queue id = song id
	$song_queue = new Song();
	$success = $song_queue->markReady($queue_id);    
	if ($success){
		echo "<p>Thank you! You've probably made someone's day a little better.</p>";
		echo $link_home;
	}	
} else {
	echo "<p>no form data</p>";
	echo $link_home;
}
//wrap up and set final taken_flag on bag
if ($success){
	$ck_id = $song_queue->getCheckerId($queue_id);
	$bag = new BagChecker($ck_id);
	$bag->markTaken();
} else {
	echo '112';
}


?>
		</div><!-- /container -->	
	</div> <!-- /wrapper -->

</body>
</html>