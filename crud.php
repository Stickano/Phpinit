<?php

class Crud{

    private $conn;
    public function __construct(Connection $conn){
        $this->conn = $conn;
    }

    /**
     * Receive (read) data from the database
     * @param  array      $select 'data' => 'table'
     * @param  array|null $where  'row' => 'value'
     * @param  array|null $order  'row' => 'ASC/DESC'
     * @param  int|null   $limit  Integer limit value
     *
     * It is possible to build a JOIN query as well,
     * by passing an multidemensional array as the last parameter.
     * See https://sloa.dk/WebHelpers#Crud on how to build the JOIN clause(s).
     * @param  array(multi)|null $join   Array(s) with the JOIN clauses
     *
     * @return array|null          The result(s)
     */
    public function read(array $select,
                         array $where=null,
                         array $order=null,
                         int   $limit=null,
                         array $join=null){

        # Throw an Exception if the SELECT array is not build correctly
        if(empty($select) || count($select) != 1 || !is_array($select))
            throw new Exception("Error in SELECT handling - pass an array with with what data as key and from what table as value!");

        # Secure SELECT inputs
        $data = mysqli_real_escape_string($this->conn, key($select));
        $from = mysqli_real_escape_string($this->conn, $select[key($select)]);

        # Build the WHERE clause
        if($where != null && !empty($where)){

            $whereString = 'WHERE ';

            $br = 0;
            foreach ($where as $key => $value) {
                # Secure the inputs
                $whereSel = mysqli_real_escape_string($this->conn, $key);
                $whereVal = mysqli_real_escape_string($this->conn, $value);

                if ($br != 0)
                    $whereString .= ' AND ';
                $br++;
                $whereString .= $whereSel."='".$whereVal."'";
            }

            $where = $whereString;
        }

        # Build the JOIN clause
        $joinString = "";
        if(!empty($join) && $join != null && is_array($join)){

            # Open each of the JOIN clauses
            foreach ($join as $key => $value) {
                if(!is_array($value)){

                    # Secure inputs
                    $whichJoin  = mysqli_real_escape_string($this->conn, $key);
                    $whichTable = mysqli_real_escape_string($this->conn, $value);

                    # Add to query string
                    $joinString .= strtoupper($whichJoin)." JOIN ";
                    $joinString .= $whichTable;
                    $joinString .= " ON ";

                # The ON clauses
                }else{
                    $br = 0;
                    foreach ($value as $on => $onVal) {

                        # Secure inputs
                        $onVal = mysqli_real_escape_string($this->conn, $onVal);
                        $on    = mysqli_real_escape_string($this->conn, $on);

                        # Add an 'AND' on several conditions
                        if($br != 0)
                            $joinString .= " AND ";

                        # Add to the query string
                        $joinString .= $on."=".$onVal;
                        $br++;
                    }
                }
            }
        }

        # Build the ORDER clause
        if(!empty($order) && $order != null){

            # Check if array length equals 1
            if(count($order) != 1)
                throw new Exception("Error in ORDER handling - pass an array with one key and its value");

            # Check that the value is either ASC or DESC
            $scending = null;
            if ($order[key($order)] != null){
                $scending = strtoupper($order[key($order)]);
                if($scending != 'ASC' && $scending != 'DESC')
                    throw new Exception("Error in ORDER handling - value should be either 'ASC' or 'DESC'");
            }

            # Secure input
            $orderBy = mysqli_real_escape_string($this->conn, key($order));

            # Add to query string
            $order = "ORDER BY '".$orderBy."' ".$scending;
        }

        # Build the LIMIT clause
        if($limit != null){

            # Check that it's a integer value
            if(!is_numeric($limit))
                throw new Exception("Error in LIMIT handling - pass an integer value");

            # Check that it's a positive value
            if($limit < 0)
                throw new Exception("Error in LIMIT handling - pass a positive value");

            # Add to the query string
            $limit = "LIMIT ".$limit;
        }

        # Build and run the query
        $sql = "SELECT ".$data." FROM ".$from." ".$joinString." ".$where." ".$order." ".$limit;
        $query = $this->conn->query($sql);

        # Return SQL error if any
        if(!$query)
            throw new Exception('Error: '.$this->conn->error);

        # Return the result(s)
        $result = false;
        if($query->num_rows > 0){
            $result = array();
            while($row = $query->fetch_assoc()){
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Insert data to the db
     * @param  string $table  table to insert data
     * @param  array  $data   row => value
     * @return bool|string    True on succes/ SQL error on fail
     */
    public function create(string $table, array $data){

        $rows = "";
        $values = "";
        $br = 0;

        $data = array_filter($data);

        # Extract key/value and build em correctly for the query
        foreach ($data as $key => $val) {
            $br++;
            $rows   .= mysqli_real_escape_string($this->conn, $key);
            $values .= "'".mysqli_real_escape_string($this->conn, $val)."'";

            # Add commas after each value (except the last)
            if($br < sizeof($data)){
                $rows .= ",";
                $values .= ",";
            }
        }

        # Secure a little more inputs
        $table = mysqli_real_escape_string($this->conn, $table);

        # Build and run query
        $sql = "INSERT INTO ".$table." (".$rows.") VALUES (".$values.")";
        $query = $this->conn->query($sql);

        # Return the SQL error if any
        if(!$query)
            throw new Exception("Error: ".$this->conn->error);

        return true;
    }

    /**
     * Updates a table in the db
     * @param  string $table Table to update in
     * @param  array  $data  'row' => 'value'
     * @param  array  $where 'row' => 'value'
     * @return bool|Exception   True/Exception
     */
    public function update(string $table, array $data, array $where=null) {

        # Check if SET (data) array is passed correctly
        if(!is_array($data) && count($data) != 1)
            throw new Exception("Error in SET handling - pass an array with one key and its value");

        # Check if WHERE array is passed correctly
        if(!is_array($where) && count($where) != 1 && $where != null)
            throw new Exception("Error in WHERE handling - pass an array with one key and its value");

        # Retreive and secure inputs
        $rows   = "";
        $values = "";
        $br     = 0;
        $data   = array_filter($data);
        $table  = mysqli_real_escape_string($this->conn, $table);

        # Extract key/value and build em correctly for the query
        foreach ($data as $key => $val) {
            $br++;
            $row     = mysqli_real_escape_string($this->conn, $key);
            $value   = mysqli_real_escape_string($this->conn, $val);
            $values .= $row."='".$value."'";

            if ($br < sizeof($data))
                $values .= ", ";
        }

        # Build where clause
        if($where != null){
            $whereVal = mysqli_real_escape_string($this->conn, $where[key($where)]);
            $where    = mysqli_real_escape_string($this->conn, key($where));
            $where    = "WHERE ".$where."='".$whereVal."'";
        }

        # Build and run query
        $sql =  "UPDATE ".$table." SET ".$values." ".$where;
        if(!$this->conn->query($sql))
            throw new Exception("Error: ".$this->conn->error);

        return true;
    }

    /**
     * Delete a row in the db
     * @param  string $table Table to delete from
     * @param  array  $where 'row' => 'value'
     * @return bool|Exception  True/Exception
     */
    public function delete(string $table, array $where) {

        # Check if WHERE array is passed correctly
        if(!is_array($where) && count($where) != 1)
            throw new Exception("Error in WHERE handling - pass an array with one key and its value");

        # Secure the inputs
        $table    = mysqli_real_escape_string($this->conn, $table);
        $whereVal = mysqli_real_escape_string($this->conn, $where[key($where)]);
        $where    = mysqli_real_escape_string($this->conn, key($where));

        # Build and run query
        $sql = "DELETE FROM ".$table." WHERE ".$where."='".$whereVal."'";
        $query = $this->conn->query($sql);

        # Return the SQL error if any
        if(!$query)
            throw new Exception("Error: ".$this->conn->error);

        return true;
    }
}

?>
