<?php
class Cache{
	private $handle;
	public $timeout;
	private $cachefile;
	
	public function __construct($timeout = 3600,$songs = true){
		if (!$timeout){ //if you set this param to 0 or false, it will set timeout to 1hr
			echo "NO TIMEOUT SET";
			$timeout = 3600;
		}
		$this->timeout = $timeout; //default to 1hr
		if ($songs){
			$file = 'topsongs_cache.html';
		} else {
			$file = 'topartists_cache.html';
		}
		$this->cachefile = '/your_full_path/private/cache/'.$file;
	}
	/*
	* returns the contents of the cache file if it hasn't expired.
	* or returns false if the file is out of date
	*
	*/
	public function getCache(){
		$is_empty = 0;
		$is_old = 0;
		//attempt to open or create file
		$this->handle = fopen($this->cachefile,'a+');
		//if we got a file
		if ($this->handle){
			//is it new?
			if (filesize($this->cachefile) == 0){
				$is_empty = 1;
				//echo "empty";
			} 
			$last_write_time = filemtime($this->cachefile);
			/*
			say it's now 10:00
			last write time was 8:00
			8:00 + :60 = 9:00
			9:00 < 10:00 so it's time to update the file
			*/
			if ( ($last_write_time + $this->timeout < time() ) && !$is_empty){
				//time for an update, do the query
				//close the file
				fclose($this->handle);
				return false; //false = the current cache file is no longer valid
			} else {
				$is_old = 1;
			}
			//output the results of the file - if it's not empty or outdated
			$contents = fread($this->handle, filesize($this->cachefile));
			fclose($this->handle);
			return $contents;
			
		} else {
			//echo "-----------could not find or create file";
		}
	}
	
	public function writeCache($contents){
		//check for XSS for good measure
		if (strpos($contents,'<script>') !== false || strpos($contents,'<style>') !== false){
			return false;
		}
		//write
		if (file_put_contents($this->cachefile,$contents)){
			return true;
		}
		return false;
	}
	
}