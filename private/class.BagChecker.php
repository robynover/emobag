<?php
require('class.BagGenericUser.php');
class BagChecker extends BagGenericUser{
	protected $table_name = 'bag_checker';
	
	public function markTaken(){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		$query = sprintf("UPDATE bag_checker SET taken_flag=1 WHERE id = %d LIMIT 1",$this->id);
		$stmt = $this->db->prepare($query);
		if ($stmt->execute()){
			return true;
		} else {
			echo "BC db problem.";
			return false;
		}
	}
	public function markHeld(){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		$now = time();
		$query = sprintf("UPDATE bag_checker SET temp_hold=1,hold_date=%d WHERE id = %d LIMIT 1",$now,$this->id);
		$stmt = $this->db->prepare($query);
		if ($stmt->execute()){
			return true;
		} else {
			echo "BC db problem.";
			return false;
		}
	}
	public function addEmail($email){
		//validate email
		$this->email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if ($this->email){
			if (!$this->db){
				$this->db = DB::getInstance();
			}
			$ip = $_SERVER['REMOTE_ADDR'];
			$query = sprintf("UPDATE %s SET email = ?,ip_address=? WHERE id = %d LIMIT 1",$this->table_name,$this->id);
			$stmt = $this->db->prepare($query);
			if ($stmt->execute(array($this->email,$ip))){
				return true;
			} else {
				echo var_dump($this->db->errorInfo());
				echo "DB problem";
				echo $this->email;
				echo "<p>$query";
				return false;
			}
		} else {
			echo $email;
			return false;
		}
	}
	
}