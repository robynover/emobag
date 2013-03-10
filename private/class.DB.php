<?php
class DB{
    
    private static $_instance;
    private $_pdo;

    public static function getInstance(){
        if(self::$_instance === NULL) {
			require_once('/your_full_path/private/inc.db.php');

            // call constructor and assign instance
            self::$_instance = new self($dsn, $user, $password);
        }

        return self::$_instance;
    }

    /**
     * Creates new MyPdo wrapping a PDO instance
     * 
     */
    private function __construct($dsn, $user, $password)
    {
        try {
            $this->_pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * Singletons may not be cloned
     */
    private function __clone() {}

    
    /**
     * Delegate every method call to PDO instance
     *  
     * @param  String $method
     * @param  Array  $args
     * @return Mixed
     */    
    public function __call($method, $args) {
        return call_user_func_array(array($this->_pdo, $method), $args);
    }
}