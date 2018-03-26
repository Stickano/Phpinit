<?php

class Login {

	private $conn;
	private $table;
	private $unameRow;
	private $upassRow;

	private $failed;
	private $empty;
	private $no_profile;
	private $password_failed;

	/**
	 * Constructor
	 * @param connection $conn MySQLi connection object
	 */
	public function __construct(Connection $conn){
		$this->conn = $conn;

		$this->failed = 'Login failed';
		$this->empty = $this->failed;
		$this->no_profile = $this->failed;
		$this->password_failed = $this->failed;
	}

	/**
	 * Define the values for the User table in the DB
	 * @param  strin $tableName   The name of the table
	 * @param  string $userRowName The name of the username row
	 * @param  string $passRowName The name of the password row
	 * @return Exception 	Throws exception if invalid  
	 */
	public function defineDb($tableName, $userRowName, $passRowName) {
		$sql = "SELECT * FROM information_schema.COLUMNS 
				WHERE TABLE_SCHEMA='".$tableName."' 
				AND COLUMN_NAME='".$userRowName."'
				AND COLUMN_NAME='".$passRowName."'";
		if(!mysqli_query($this->conn, $sql))
			throw new Exception('The provided values does not match a valid table/rows');
		$this->table = $tableName;
		$this->unameRow = $userRowName;
		$this->upassRow = $passRowName;
	}

	/*
	 * Functions to define the various failed return messages.
	 * Be aware, if you define more that the Failed value, 
	 * 	you are leaving up a security vulnerbility by leaving
	 * 	a clue to weather a email address or password is valid.
	 */
	public function setFailed($string) {
		$this->failed = $string;
	}

	public function setEmpty($string) {
		$this->empty = $string;
	}

	public function setNoProfile($string) {
		$this->no_profile = $string;
	}

	public function setPasswordFailed($string) {
		$this->password_failed = $string;
	}

	/**
	 * Logout function
	 * @return  unsets 'loggedIn' session
	 */
	public function logout() {
		if(isset($_SESSION['loggedIn']))
			unset($_SESSION['loggedIn']);
	}

	/**
	 * Login function
	 * @param  string $username Username to match
	 * @param  string $password Password (will be hased) 
	 * @return SESSION 		$_SESSION['loggedIn'] = id;
	 */
	public function login($username, $password){

		# Checks that the db connection has been defined
		if(empty($this->table))
			throw new Exception('Please define the database connection before using this function.');

		# Secure the inputs
		$uname = mysqli_real_escape_string($this->conn, strtolower($username));
		$upass = mysqli_real_escape_string($this->conn, $password);

		# Confirm both username and password are filled out
		if(empty($uname) || empty($upass))
			throw new Exception($this->empty);

		# Search the profile in the db
		$sql = "SELECT * FROM ".$this->table." 
			WHERE ".$this->unameRow."='".$uname."'";
		$query = mysqli_query($this->conn,$sql)or die(mysqli_error($this->conn));
		$num = mysqli_num_rows($query);

		# If the username was NOT found
		if($num != true)
			throw new Exception($this->no_profile);

		# Match the password
		$result = mysqli_fetch_array($query);
		if(!password_verify($upass, $result[$this->upassRow]))
			throw new Exception($this->password_failed);

		$_SESSION['loggedIn'] = $result['id'];
	}
}

?>