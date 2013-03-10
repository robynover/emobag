<?php
require_once ('class.DB.php');
class BagQueue{
	private $db;
	public function clearHolds(){
		if (!$this->db){
			$this->db = DB::getInstance();
		}
		$query = "UPDATE bag_checker SET temp_hold = 0 ";
		$query .= "WHERE hold_date < (UNIX_TIMESTAMP() -(60*60)) AND hold_date <> 0 ";
		$query .= "AND taken_flag = 0 AND temp_hold =1";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		//var_dump($stmt->errorInfo());
	 	return $stmt->rowCount();
	}

}