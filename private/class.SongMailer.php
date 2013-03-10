<?php
require_once('class.DB.php');

class SongMailer{
	private $db;
	private $rows;
	public $success = 0;
	public $fail = 0;
	public $total = 0;
	
	public function getSendReadySongs(){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		$query = "SELECT qid,tiny_song_url,song_title,song_artist,email,bag_queue.msg AS bqmsg ,bag_checker.msg AS bcmsg FROM bag_queue ";
		$query .= "LEFT JOIN bag_checker on bag_queue.cid = bag_checker.id ";
		$query .= "WHERE send_ready = 1 AND email IS NOT NULL AND email <> '' AND sent=0 ";
		$query .= "ORDER BY take_date DESC LIMIT 100";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
		if ($results){
			$this->rows = $results;
			return true;
		} else {
			var_dump($stmt->errorInfo());
			return false;
		}
	}
	
	public function sendEmailList(){
		if ($this->rows){	
			foreach ($this->rows as $row){
				$to = filter_var($row->email, FILTER_VALIDATE_EMAIL); //validate email
				$title = strip_tags($row->song_title);
				$artist = strip_tags($row->song_artist);
				$taker_msg = strip_tags($row->bqmsg);
				$orig_msg = strip_tags($row->bcmsg);
				$url = $row->tiny_song_url; 
				//
				if ($to && $this->validateTinySong($url)){
					//include PEAR Mail class
					require_once 'Mail.php';
					$mail_obj =& Mail::factory('sendmail');
					
					$headers['From']  = "Emotional Bag Check <email@youremail>";
					$headers['To'] = $to;
					$headers['Subject'] = 'Someone picked up your baggage and sent you a song!';
					$headers['Return-Path'] = 'email@youremail';
					$headers["Content-Type"] = 'text/plain; ISO-8859-1';
					$headers['Reply-To'] = 'email@youremail';
					$headers['X-Mailer'] = 'PHP v'.phpversion();
					$headers['MIME-Version'] = '1.0';
					//$headers['X-From-Web'] = 'ipaddress';
					
					//----
					$id_crypt = $this->makeMsgId($row->qid); // [#{$id_crypt}]
					$body = $this->makeBodyMsg($title,$artist,$url,$taker_msg,$orig_msg);
					$body .= "[ref #{$id_crypt}]";
					$body .= "\n\rIf you feel the message you received was abusive or otherwise inappropriate, ";
					$body .= "please send the reference number above (or just forward this message) to abuse@emotionalbagcheck.com\n";
					$this->total++;
					//---mail it -------//
					$send = $mail_obj->send($to, $headers, $body);
					if (PEAR::isError($send)) { 
						$this->fail++;
						print($send->getMessage());
					} else {
						$this->success++;
						$this->markSent($row->qid);
						print "sent to $to!\n";
					}
					
				}else {
					//bad email address
					echo "bad email address OR bad URL";	
				}
				
			}
		}
	}
	public function getReport(){
		return $this->success . ' successful mails and '.$this->fail.' failures out of a total '.$this->total."\n";
	}
	private function makeBodyMsg($ti,$ar,$url,$xtra_msg,$orig_msg = null){
		$msg_body = "Hi there! Someone read the message you left on emotionalbagcheck.com and suggested you listen to the song \"%s\", by %s, which you can hear at this URL: %s."; 
		$msg = sprintf($msg_body,$ti,$ar,$url);
		if ($xtra_msg){
			$msg .= "\n\rThey also sent you this message: \n\r";
			$msg .= strip_tags($xtra_msg);
		}
		$msg .= "\n\r-------------\n";
		$msg .= "\n\r---In case you've forgotten, here's the baggage you checked:---\n\r";
		$msg .= $orig_msg;
		$msg .= "\n\r\n\r=======\n\rhttp://emotionalbagcheck.com\n\r=======\n\r\n\r";
		return $msg;
	}
	
	private function validateTinySong($url){
		/*url format : http://tinysong.com/ijRR*/ 
		if (substr($url,0,20) == 'http://tinysong.com/' && ctype_alnum(substr($url,20))){
			return true;
		}
		return false;
	}
	private function markSent($qid){
		//echo "mark sent called";
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		$now = time();
		$query = "UPDATE bag_queue SET sent=1,sent_time=? WHERE qid = ? LIMIT 1";
		$stmt = $this->db->prepare($query);
		if ($stmt->execute(array($now,$qid))){
			return true;
		} else {
			echo "db problem.";
			return false;
		}
	}
	
	private function makeMsgId($qid){
		//if (!is_numeric($qid)){return false;}
		$qid = (int) $qid;
		$alpha_array = range('a','z');
		$rand_index = array_rand($alpha_array);
		$letter = $alpha_array[$rand_index];
		$hex_id = dechex($qid);
		$rand_digit = rand(0,9);
		return $letter.$hex_id.$rand_digit;
		
	}
	
	/*find songs that are ready to send but don't have a sent flag
	
	*/
	public function findOrphans(){ 
		
	}
	
}