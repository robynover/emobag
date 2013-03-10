<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once("GroovesharkAPI/gsAPI.php");
require_once("GroovesharkAPI/gsSearch.php");
$datetime = date('m-d-Y H:i');

//check token from showCheckerMsg.php
$salt = file_get_contents('/your_full_path/private/salt.txt');
if ($_SESSION['token'] != md5($_SERVER['REMOTE_ADDR'].$salt)){
    echo "<p>Sorry. We've had problems with hackers, so we've shut down temporarily.</p>";
exit;
}
//set token to prevent XSRF in sendmessage.php
$send_salt = file_get_contents('/your_full_path/private/send_salt.txt');
$_SESSION['sendtoken'] = md5($_SERVER['REMOTE_ADDR'].$send_salt);

$success = 0;
if ($_POST['song_search']){
    if (strlen(trim($_POST['song_search'])) == 0){
	    exit;
	}
	$cid = (int)$_POST['checker_id'];
	$search = urlencode($_POST['song_search']);
	
	//Connect to Grooveshark
	$key = 'your_gs_key';
	$secret = 'your_gs_secret';
	//new GS
	$gsapi = new gsapi($key, $secret);
	//set header (?)
	gsAPI::$headers = array("X-Client-IP: " . $_SERVER['REMOTE_ADDR']);
	//store/set session
	if (isset($_SESSION['gs_session']) && ctype_alnum($_SESSION['gs_session'])){
		$gsapi->setSession($_SESSION['gs_session']);
	} else {
		$gsapi->startSession();
		$_SESSION['gs_session'] = $gsapi->getSession();
	}
	//required by GS
	$gsapi->getCountry($_SERVER['REMOTE_ADDR']);
	//search
	$gsSearch = new gsSearch();
	$gsSearch->setTitle($search);
	$results = $gsSearch->songSearchResults(20);
	//array to prevent duplicates
	$titleartist=array();
	
	if ($results > 0){
		foreach ($results as $song){
			$ti = $song['SongName'];
			$artist = $song['ArtistName'];
			$ta = $ti.$artist;
			if (in_array($ta,$titleartist)){
				continue;
			}
			$titleartist[] = $ta;

			echo '<li>';
			$url = gsAPI::getTinysongURLFromSongID($song['SongID']);;
			echo $ti . ", by ".$artist;
			$song_id = $song['SongID'];
			echo " | ";

			$qstring = "ck=".$cid;
			$qstring.='&s='.$song_id.'&u='.urlencode($url).'&ti='.urlencode(trim($ti)).'&ar='.urlencode(trim($artist));

			echo "<a href='javascript:sendSong(\"$qstring\")'>Yeah, send this one!</a>";
			echo " | <a href='$url' target='_blank'>Listen first</a>";
			echo '</li>';
		}
	} else {
		echo "<p>No song found. Try a different search?</p>";
	}
	
	//for song not found, error code = 11
	
} else {
	echo "no search terms detected";
}

?>
<!-- gsAPI -->