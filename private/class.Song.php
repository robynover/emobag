<?php
require_once('class.DB.php');
class Song{
	public $url;
	public $title;
	public $artist;
	public $checker_id;
	private $db;
	
	public function __construct(){
		//echo "song obj constructed";
	}
	
	public function saveSong($checker_id,$url,$ti,$artist,$ip=0){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		
		//validate URL
		if (substr($url,0,20) == 'http://tinysong.com/' && ctype_alnum(substr($url,20))){
			$this->url = $url;
			$this->title = strip_tags($ti);
			$this->artist = strip_tags($artist);
			$this->checker_id = (int)$checker_id;
			$query = "INSERT INTO bag_queue (cid,take_date,tiny_song_url,song_title,song_artist,ip) ";
			$query .=" VALUES(?,?,?,?,?,?)";
			$stmt = $this->db->prepare($query);
			if ($stmt->execute(array($this->checker_id,time(),$this->url,$this->title,$this->artist,$ip))){
				//success
				return $this->db->lastInsertId(); //return new id
			} else {
				//db problem
				//var_dump($this->db->errorInfo());
				return false; 
			}
		} else {
			return false; //invalid URL
		}	
	}
	
	public function addMessage($qid,$msg){
		if (is_numeric($qid) && $qid > 0){
			if (!$this->db){
				$this->db = DB::getInstance();
			}
			$msg = strip_tags($msg);
			//check off 'send_ready' in bag_queue table
			$query = "UPDATE bag_queue SET msg = ?,send_ready=1 WHERE qid = ? LIMIT 1";
			$stmt = $this->db->prepare($query);
			if ($stmt->execute(array($msg,$qid))){
				return true;
			} else {
				//echo "db problem.";
				return false;
			}
		}
	}
	
	public function markReady($qid){
		if (is_numeric($qid) && $qid > 0){
			if (!$this->db){
				$this->db = DB::getInstance();
			}
			$query = "UPDATE bag_queue SET send_ready=1 WHERE qid = ? LIMIT 1";
			//var_dump($this->db);
			$stmt = $this->db->prepare($query);
			if ($stmt->execute(array($qid))){
				return true;
			} else {
				//echo "db problem.";
				return false;
			}
		}
	}
	
	public function getNextCheckerId($random_5 = false){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		//var_dump($this->db->errorInfo());
	
		if ($random_5){
			$num = rand(0,10);
			$query = sprintf("SELECT id FROM bag_checker WHERE taken_flag = 0 AND temp_hold = 0 AND email <>'' ORDER BY checked_date ASC LIMIT %d,1",$num);
		} else{
			$query = "SELECT id FROM bag_checker WHERE taken_flag = 0 AND temp_hold = 0 AND email <>'' ORDER BY checked_date ASC LIMIT 1";
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$result = $stmt->fetch();
		//var_dump($stmt->errorInfo());
		return $result['id'];
		
	}
	
	public function getCheckerId($qid){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		if (!is_numeric($qid)){return false;}
		$query = "SELECT cid FROM bag_queue where qid = ? LIMIT 1";
		$stmt = $this->db->prepare($query);
		$stmt->execute(array($qid));
		$result = $stmt->fetch();
		if ($result){
			return $result['cid'];
		}
		return false;
		
			
	}
	
}

?>