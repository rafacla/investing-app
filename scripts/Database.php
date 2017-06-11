<?php
class Database
{
	protected static $servername;
	protected static $username;
	protected static $password;
	protected static $database;
	
	protected static $connection;
	
	public function connect() {
		self::$servername = "localhost";
		self::$username = "root";
		self::$password = "";
		self::$database = "app-inv_r1";
		
		if(!isset($connection)) {
			self::$connection = new mysqli(self::$servername,self::$username,self::$password,self::$database);
		}
		
		// If connection was not successful, handle the error
		if(self::$connection === false) {
			// Handle error - notify administrator, log to a file, show an error screen, etc.
			return false; 
		}
		return self::$connection;
	}
	
	/**
	 * Query the database
	 *
	 * @param $query The query string
	 * @return mixed The result of the mysqli::query() function
	 */
	public function query($query) {
		// Connect to the database
		$connection = $this -> connect();

		$result = false;
		
		if ($connection!=false) {
			// Query the database
			$sql = '';
			if (is_string($query)) {
				$result = $connection->query($query);
				
			} else {
				foreach ($query as $linha) {
					$sql .= $linha;
				}
				$connection->multi_query($sql);
			}
		}		
		return $result;
	}
		
	/**
	 * Fetch rows from the database (SELECT query)
	 *
	 * @param $query The query string
	 * @return bool False on failure / array Database rows on success
	 */
	public function select($query) {
		$rows = array();
		
		$result = $this -> query($query);
		
		if($result === false) {
			return false;
		}
		while ($row = $result -> fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}
		
	/**
     * Fetch the last error from the database
     * 
     * @return string Database error message
     */
    public function error() {
        $connection = $this -> connect();
        return $connection -> error;
    }

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function quote($value) {
        $connection = $this -> connect();
        return "'" . $connection -> real_escape_string($value) . "'";
    }
}