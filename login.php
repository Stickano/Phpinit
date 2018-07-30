<?php

class Login {

	private $conn;
	private $table;
	private $unameRow;
	private $upassRow;

	private $failed;
	private $empty;
	private $noProfile;
	private $passwordFailed;

	private $sessionName;
	private $sessionValue;

	/**
	 * Constructor
	 * @param connection $conn MySQLi connection object
	 */
	public function __construct(Connection $conn){
		$this->conn           = $conn;
		$this->failed         = 'Login failed';
		$this->empty          = $this->failed;
		$this->noProfile      = $this->failed;
		$this->passwordFailed = $this->failed;
	}

	/**
	 * Define the values for the User table in the DB
	 * @param  string $tableName   The name of the table
	 * @param  string $userRowName The name of the username row
	 * @param  string $passRowName The name of the password row
	 * @return Exception 	Throws exception if invalid
	 */
	public function defineDatabase(string $tableName, string $userRowName, string $passRowName) {
		$sql = "SELECT * FROM information_schema.COLUMNS
				WHERE TABLE_SCHEMA='".$tableName."'
				AND COLUMN_NAME='".$userRowName."'
				AND COLUMN_NAME='".$passRowName."'";

		if(!mysqli_query($this->conn, $sql))
			throw new Exception('The provided values does not match a valid table/rows');

		$this->table    = $tableName;
		$this->unameRow = $userRowName;
		$this->upassRow = $passRowName;
	}

	/**
	 * This will set what the SESSION name and value should be,
	 * once a user is successfully logged in.
	 * @param  string $sessionName  The name of the session.
	 * @param  string $sessionValue The value of the session.
	 *                              This needs to correspond with a database row
	 *                              in the user table.
	 * @return                      Sets sessionName and sessionValue.
	 */
	public function defineSession(string $sessionName, string $sessionValue) {
		if (empty($sessionName) || empty($sessionValue))
			throw new Exception('Missing Session Name and/or Value.');

		$this->sessionName  = $sessionName;
		$this->sessionValue = $sessionValue;
	}

	/*
	 * Methods to define the various failed return messages.
	 * Be aware, if you define more than the Failed value,
	 * 	you are leaving up a security vulnerability by giving
	 * 	a clue to weather a email address or password is valid.
	 */
	public function setFailed($string) {
		$this->failed = $string;
	}

	public function setEmpty($string) {
		$this->empty = $string;
	}

	public function setNoProfile($string) {
		$this->noProfile = $string;
	}

	public function setPasswordFailed($string) {
		$this->passwordFailed = $string;
	}

	/**
	 * Logout function
	 * @return  unsets 'loggedIn' session
	 */
	public function logout() {
		if(isset($_SESSION[$this->sessionName]))
			unset($_SESSION[$this->sessionName]);
	}

	/**
	 * Login function
	 * @param  string $username Username to match
	 * @param  string $password Password (will be hashed)
	 * @return SESSION 		$_SESSION['loggedIn'] = id;
	 */
	public function login(string $username, string $password){

		# Checks that the db connection has been defined
		if(empty($this->table))
			throw new Exception('Please define the database connection before using this method (setting).');

		# Check that a session name/value has been set
		if (empty($this->sessionName) || empty($this->sessionValue))
			throw new Exception('Please define the SESSION Name and Value (setting).');

		# Check that the SESSION value row exists
		$sql = "SELECT * FROM information_schema.COLUMNS
				WHERE TABLE_SCHEMA='".$this->tableName."'
				AND COLUMN_NAME='".$this->sessionValue."'";

		if(!mysqli_query($this->conn, $sql))
			throw new Exception('The provided value for SESSION value does not match a table row. (setting)');

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
			throw new Exception($this->noProfile);

		# Match the password
		$result = mysqli_fetch_array($query);
		if(!password_verify($upass, $result[$this->upassRow]))
			throw new Exception($this->passwordFailed);

		$_SESSION[$this->sessionName] = $result[$this->sessionValue];
	}
}

?>