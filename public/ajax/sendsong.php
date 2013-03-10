<?php
/*sendsong.php*/
session_start();
if (isset($_COOKIE['xjd'])){
   echo '<p>Ooh. Looks like you just sent a song very recently. Wait a minute and try again.</p>' ;
} 
$datetime = date('m-d-Y H:i');
//check token from showCheckerMsg.php
$salt = file_get_contents('/your_full_path/private/salt.txt');
if ($_SESSION['token'] != md5($_SERVER['REMOTE_ADDR'].$salt)){
    echo "<p>Sorry. Looks like you tried to access this page through the backdoor. Please try again.</p>";
   	file_put_contents('/your_full_path/private/iptrack.txt',"--BACKDOOR: ". $_SERVER['REMOTE_ADDR']." $datetime\n",FILE_APPEND);
    exit;
}

require_once('/your_full_path/private/class.Song.php');
require_once('/your_full_path/private/class.BagChecker.php');

$sid = (int)$_POST['s'];
$url = strip_tags($_POST['u']); 
/*url format : http://tinysong.com/ijRR*/ 
$checker_id = (int)$_POST['ck'];
$title = strip_tags(trim($_POST['ti']));

if ($sid == 0 || $checker_id == 0 || strlen($title) < 1){
	echo "<p>Oops. Missing some information. Please try again.</p>";
	exit;
}

if (substr($url,0,20) == 'http://tinysong.com/' && ctype_alnum(substr($url,20))){	
	//$title = trim($_POST['ti']);
	$artist = strip_tags(trim($_POST['ar']));
	
	//use grooveshark to check instead against song id? 1.apishark.com/f:{text or json}/getSongInfo/{songID}/
	//$json = file_get_contents('http://1.apishark.com/p:a6q2z7/f:json/getSongInfo/'.$sid);
	//$gs_data = json_decode($json); //->songID, ->ArtistName
	//$gs_title = $gs_data->Result->SongName;
	
	//if ($gs_title == $title){
		//matched grooveshark. ok.
		//save the song part to the db here to avoid having to re-validate
		$song = new Song();
		$ip = 0;
		$pattern = '#[/d/.]+#';
        if (preg_match($pattern,$_SERVER['REMOTE_ADDR'])){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
		if ($song_id = $song->saveSong($checker_id,$url,$title,$artist,$ip)){
			//mark checker as taken:
			$ckr = new BagChecker($checker_id);
			//$ckr->markTaken();
			$ckr->markHeld();
			echo "<p>Great. We'll send <strong>$title, by $artist</strong>. ";
			echo "Would you like to include a personal message too?</p>";
			echo makeTakerMsgForm($song_id);
		} else {
			echo "db error";
			//var_dump(DB::getInstance()->errorInfo());
		}
	
	/*} else {
		//give it one more chance?
		echo "Sorry. Something was wrong with the name of that song. You're not trying to use this site to spam are you?";
		echo "<br/>GS: ".$gs_title;
		echo "<br/>orig: ".$title;
		//var_dump($json);
		//echo $sid;
	}*/
	
} else {
	echo 'bad url';
	//exit;
}


function makeTakerMsgForm($hidden_id){
	$form = <<<EOT
	<form id="taker_form" method="post" action="sendmessage.php">
	<input type="hidden" name="sid" value="$hidden_id"/>
	<input type="submit" name="submit" id="no-thanks" value="No thanks, just the song" />
	<br/>
	<textarea name="taker_msg" id="taker_msg"></textarea><br/>
	<input type="submit" name="submit" value="Send" id="send-submit"/>
	
	</form>
EOT;
	return $form;
}

?>