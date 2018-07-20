<?php

#TODO: There is quite a lot of copypasta in this doc, see if you cant clean up

class DatabaseBuilder {

    private $uname;
    private $upass;
    private $host;
    private $conn;

    private $db;
    private $table;
    private $error;
    public function __construct(string $host, string $uname, string $upass){
        $this->uname = $uname;
        $this->upass = $upass;
        $this->host  = $host;
        self::initConnection();
    }

    /**
     * Tries to initialize a new connection
     * @return      Initializes a connection or sets $error
     */
    private function initConnection(){
        $conn = @new mysqli($this->host, $this->uname, $this->upass);
        if($conn->connect_error)
            $this->error = $conn->connect_error;
        else
            $this->conn = $conn;
    }

    /**
     * Checks if the given credentials where accepted
     * @return bool True/False
     */
    public function checkCredentials(){
        if($this->conn == null)
            return false;
        else
            return true;
    }

    /**
     * TODO: Find a better solution for this
     * Will return any recorded error
     * @return string The error message
     */
    public function throwException(){
        if($this->error != null)
            throw new Exception($this->error);
    }

    /**
     * Provide a DATABASE that we'll be working in
     * @param  string $db The name of the database
     * @return            Sets $db
     */
    public function usingDb(string $db){
        # Make sure we're connected
        if(!self::checkCredentials()){
            $this->error = "Error: Check credentials";
            return false;
        }

        # Check if database exists
        if(!self::issetDb($db)){
            $this->error = "Error: Database doesn't exists";
            return false;
        }

        # Define the value (database)
        $db = mysqli_real_escape_string($this->conn, $db);
        $this->db = $db;
        $this->conn = new mysqli($this->host,$this->uname,$this->upass,$db);
    }

    /**
     * Provide a TABLE that we'll be working in
     * @param  string $table The name of the table
     * @return               Sets $table
     */
    public function usingTable(string $table){
        # Check if workingDb() is set -
        # This will have performed the credential and db check
        if($this->db == null){
            $this->error = "Error: Working database not set";
            return false;
        }

        # Check if table exists
        if(!self::issetTable($this->db, $table)){
            $this->error = "Error: Table doesn't exists";
            return false;
        }

        # Define the value (table)
        $table = mysqli_real_escape_string($this->conn,$table);
        $this->table = $table;
    }

    /**
     * Check if a DATABASE exists
     * @param  string $db The database to check
     * @return [type]     True/False
     */
    public function issetDb(string $db){
        # Make sure we're connected
        if(!self::checkCredentials()){
            $this->error = "Error: Check credentials";
            return false;
        }

        # Secure values and run query
        $db = mysqli_real_escape_string($this->conn, $db);
        $sql = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='".$db."'";
        $query = $this->conn->query($sql);

        # Check if there's any database available
        if($query->num_rows)
            return true;
        else
            return false;
    }

    /**
     * Check if TABLE exists
     * @param  string $db    The name of the database
     * @param  string $table The name of the table
     * @return bool          True/False
     */
    public function issetTable(string $db, string $table){
        # Make sure we're connected
        if(!self::checkCredentials()){
            $this->error = "Error: Check credentials";
            return false;
        }

        # Check if database exists
        if(!self::issetDb($db)){
            $this->error = "Error: Database doesn't exists";
            return false;
        }

        # Secure the values
        $table = mysqli_real_escape_string($this->conn,$table);
        $db = mysqli_real_escape_string($this->conn,$db);

        # Run the query
        $sql = "SELECT * FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = '".$db."'
                AND TABLE_NAME='".$table."'";
        $query = $this->conn->query($sql);

        # Check if there is any tables available
        if($query->num_rows)
            return true;
        else
            return false;
    }

    /**
     * Check if COLUMN exists
     * @param  string $db     The name of the database
     * @param  string $table  The name of the table
     * @param  string $column The name of the column
     * @return Bool           True/False
     */
    public function issetColumn(string $db, string $table, string $column){
        # Make sure we're connected
        if(!self::checkCredentials()){
            $this->error = "Error: Check credentials";
            return false;
        }

        # Check if database exists
        if(!self::issetDb($db)){
            $this->error = "Error: Database doesn't exists";
            return false;
        }

        # Check if table exists
        if(!self::issetTable($db, $table)){
            $this->error = "Error: Table doesn't exists";
            return false;
        }

        # Secure values
        $table = mysqli_real_escape_string($this->conn,$table);
        $db = mysqli_real_escape_string($this->conn,$db);
        $column = mysqli_real_escape_string($this->conn,$column);

        # Perform query
        $sql = "SELECT * FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = '".$db."'
                AND TABLE_NAME='".$table."'
                AND COLUMN_NAME='".$column."'";
        $query = $this->conn->query($sql);

        # Check if there's any column available
        if($query->num_rows)
            return true;
        else
            return false;
    }

    /**
     * Creates a DATABASE
     * @param  string $name Name of the database
     * @return bool         True/False
     */
    public function createDb(string $name){
        # Make sure we're connected
        if(!self::checkCredentials()){
            $this->error = "Error: Check credentials";
            return false;
        }

        # Secure values and perform query
        $name = mysqli_real_escape_string($this->conn, $name);
        $sql = "CREATE DATABASE ".$name;
        if($this->conn->query($sql)){
            return true;
        }else{
            $this->error = $this->conn->error;
            return false;
        }
    }

    /**
     * Create a TABLE in a DATABASE
     * @param  string $db    The database to create a table in
     * @param  string $table The table to create
     * @return bool          True/False
     */
    public function createTable(string $table){
        # Check that the workingDb() is used
        if($this->db == null){
            $this->error = "Error: Use workingDb() to define a database";
            return false;
        }

        # Secure value and perform query
        $table = mysqli_real_escape_string($this->conn, $table);
        $sql = "CREATE TABLE ".$table." (id INT(12) UNSIGNED AUTO_INCREMENT PRIMARY KEY)";
        if($this->conn->query($sql)){
            return true;
        }else{
            $this->error = $this->conn->error;
            return false;
        }
    }

    /**
     * Creates a COLUMN in a TABLE
     * @param  string       $column  The name of the column
     * @param  string       $type    The type of the column
     * @param  bool|boolean $null    If the column is nullable
     * @param               $default Default value for the column
     * @return bool                  True/False
     */
    public function createColumn(string $column, string $type, bool $null=false, $default=null){
        # Check that the workingTable() is used
        if($this->table == null){
            $this->error = "Error: Use workingTable() to define a table";
            return false;
        }

        # Check if ćolumn already exists
        if(self::issetColumn($this->db, $this->table, $this->table)){
            $this->error = "Error: Column already exists";
            return false;
        }

        # Build the NULL clause
        if($null == true)
            $null = null;
        else
            $null = " NOT NULL";

        if($default != null){
            $default = mysqli_real_escape_string($this->conn,$default);
            $default = " DEFAULT '".$default."'";
        }

        # Secure inputs
        $column = mysqli_real_escape_string($this->conn,$column);
        $type = mysqli_real_escape_string($this->conn,$type);

        # Run query
        $sql = "ALTER TABLE ".$this->table." ADD (".$column." ".$type.$null.$default.")";
        if($this->conn->query($sql)){
            return true;
        }else{
            $this->error = $this->conn->error;
            return false;
        }
    }

    /**
     * Deletes (drop) a DATABASE
     * @param  string $db The name of the database
     * @return bool     True/False
     */
    public function dropDb(string $db){
        # Make sure we're connected
        if(!self::checkCredentials()){
            $this->error = "Error: Check credentials";
            return false;
        }

        # Check if database exists
        if(!self::issetDb($db)){
            $this->error = "Error: Database doesn't exists";
            return false;
        }

        # Secure value and run query
        $db = mysqli_real_escape_string($this->conn, $db);
        $sql = "DROP DATABASE ".$db;
        if($this->conn->query($sql)){
            return true;
        }else{
            $this->error = $this->conn->error;
            return false;
        }
    }

    /**
     * Delete (drop) a TABLE
     * @param  string $table The table to drop
     * @return [type]        True/False
     */
    public function dropTable(string $table){
        # Check that the workingDb() is used
        if($this->db == null){
            $this->error = "Error: Use workingDb() to define a database";
            return false;
        }

        # Check if table exists
        if(!self::issetTable($this->db, $table)){
            $this->error = "Error: Table doesn't exists";
            return false;
        }

        # Secure value and run query
        $table = mysqli_real_escape_string($this->conn, $table);
        $sql = "DROP TABLE ".$table;
        if($this->conn->query($sql)){
            return true;
        }else{
            $this->error = $this->conn->error;
            return false;
        }
    }

    /**
     * Delete (drop) a COLUMN
     * @param  string $column The name of the column
     * @return bool           True/False
     */
    public function dropColumn(string $column){
        # Check that the workingTable() is used
        if($this->table == null){
            $this->error = "Error: Use workingTable() to define a table";
            return false;
        }

        # Check if ćolumn already exists
        if(!self::issetColumn($this->db, $this->table, $column)){
            $this->error = "Error: Column doesn't exists";
            return false;
        }

        #Secure value and run query
        $column = mysqli_real_escape_string($this->conn,$column);
        $sql = "ALTER TABLE ".$this->table." DROP COLUMN ".$column;
        if($this->conn->query($sql)){
            return true;
        }else{
            $this->error = $this->conn->error;
            return false;
        }
    }
}

?>
