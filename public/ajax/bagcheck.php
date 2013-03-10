<?php
if (isset($_COOKIE['ams'])){
	echo "<p>Looks like you've checked some baggage very recently. You'll need to wait a minute or two before you can submit again.</p>";
	echo '<p><a href="/">Back to home</a></p>';
	exit;
}
require_once('/your_full_path/private/class.BagChecker.php');
$checker = new BagChecker();
//echo $checker->email;
//echo "<p>";
//echo $checker->msg;
if ($_POST['tell']){
	$message = strip_tags($_POST['tell']);
	//email happens at the next step.
	$data = array(
		'msg'=>$message
	);
	if ($id = $checker->addNewData($data)){
		echo '<h3 class="emailbox-title">Thanks for checking your baggage.</h3>';
		echo '<p>Enter your email and someone will read your message and return a small musical gift.</p><br/>';
		echo makeCheckerEmailForm($id);
		echo '<p>ps: They won\'t see your email. And we don\'t spam. </p>';
	}
	
}

function makeCheckerEmailForm($hidden_id){
	$form = <<<EOT
	<form id="taker_form" method="post" action="add_checker_email.php">
	<input type="hidden" name="ckid" value="$hidden_id"/>
	<input type="text" name="checker_email" id="checker_email"/>
	<input type="submit" name="submit" value="Go" id="send-submit"/>
	</form>
EOT;
	return $form;
}
?>