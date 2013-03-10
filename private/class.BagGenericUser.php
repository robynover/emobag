<?php
require_once('class.DB.php');
class BagGenericUser{
	protected $table_name;
	protected $id;
	public $email;
	public $msg;
	protected $db;
	
	public function __construct($id = null){
		if ($id == null){
			//new
		} else {
			$this->id = (int)$id;
			//load existing
			if (!$this->db){
				$this->db = DB::getInstance();
			}
			$query = sprintf("SELECT * FROM %s WHERE id = ? LIMIT 1",$this->table_name);
			$stmt = $this->db->prepare($query);
			$stmt->execute(array($this->id));
			$result = $stmt->fetch();
			//var_dump($result);
			$this->email = $result['email'];
			$this->msg = $result['msg'];
		}
	}
	public function addNewData($array){
		if (!$array){return false;}
		$this->email = $array['email'];
		$this->msg = $array['msg'];
		//validate email
		$this->email = filter_var($this->email, FILTER_VALIDATE_EMAIL);
		//filter msg
		$this->msg = trim(strip_tags($this->msg));
		//msg is required. is email required?
		if (!$this->msg){ return false;}
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		$query = sprintf("INSERT INTO %s (id,email,msg,checked_date) VALUES ('',?,?,%d)",$this->table_name,time());
		$stmt = $this->db->prepare($query);
		if ($stmt->execute(array($this->email, $this->msg))){
			//success
			//echo "added!";
			return $this->db->lastInsertId(); //return new id
		} else {
			return false;
		}	
	}
}