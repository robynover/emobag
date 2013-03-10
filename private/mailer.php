<?php
require_once('class.SongMailer.php');
$mailer = new SongMailer();
if ($mailer->getSendReadySongs()){
	$mailer->sendEmailList();
	echo $mailer->getReport();
} else {
	echo "DB problem fetching records to send, or just no records to send right now.";
}
