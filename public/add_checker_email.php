<?php
//cookie to prevent overuse
$fake1 = rand(600,13400);
$timeout = time() + (60*4);
setcookie('ams',$fake1,$timeout);

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
<body class="thanksToChecker">
	<div id="wrapper">
		<div id="container">
		<div id ="full_head_block"><a href="/"><img src="images/rack_icon.gif" class="rack_icon"/></a></div>
			
<?php
require_once('/your_full_path/private/class.BagChecker.php');
$link_home = '<p>If you\'re in danger or need help right away, please see ';
$link_home .= '<a href="http://us.reachout.com/get-help/getting-help-in-a-crisis">ReachOut.com</a> ';
$link_home .= '(Outside the US, find your country at <a href="http://www.befrienders.org/">Befrienders.org</a>) or take a look at the <a href="resources.php">list we put together</a>.</p>';
$link_home .= '<p>Want to carry someone else\'s baggage for a change? <a href="/">Start again</a>.</p>';


if (($email = filter_var($_POST['checker_email'], FILTER_VALIDATE_EMAIL)) && is_numeric($_POST['ckid'])){
	$ckid = (int)$_POST['ckid'];
	$checker = new BagChecker($ckid);
	if (!$checker->email){
		if ($checker->addEmail($email)){
			echo "<p><strong>Thank you! You'll soon get a song from us in your email.</strong></p>";
			echo $link_home;
		} else {
			echo "<p>Error. Could not add email.</p>";
			echo $link_home;
		}
	} else {
		echo "<p>Error. This bag checker has already entered an email.</p>";
		echo $link_home;
	}
	
} else {
	echo "<p>Invalid data submitted.</p>";
	//echo $link_home;
}
?>
		</div><!-- /container -->	
	</div> <!-- /wrapper -->

</body>
</html>