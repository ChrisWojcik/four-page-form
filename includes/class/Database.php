<?php
/**
 * Wrapper class provides a simplified interface for the MySQLi object
 * 
 * @category Database
 * @author Christopher Wojcik <hello@chriswojcik.net>
 */
class Database
{
    /**
     * 
     * @var mysqli
     */
    protected $_db;
    
    /**
     *
     * @var mysqli_stmt
     */
    protected $_stmt;
    
    /**
     *
     * @var mysqli_result
     */
    protected $_result;
    
    /**
     * Creates a connection to the database and stores it in a class property
     * 
     * @return void
     * @throws Exception 
     */    
    public function __construct()
    {
        try {
            $this->_db = new mysqli(DB_HOST,
                                    DB_USER,
                                    DB_PASSWORD,
                                    DB_NAME);
            if (mysqli_connect_error()) {
                throw new Exception(mysqli_connect_error());
            }
        } 
        catch (Exception $e) {
            $error_message = $e->getMessage();
            echo "<p>Error connecting to the database $error_message</p>";
            exit();
        }
    }
    
    /**
     * Simplified interface for performing select queries with prepared statements
     * 
     * @param string $sql an SQL select statement 
     * @param array $params an optional array of parameters to bind to a prepared statement
     * @return array rows returned as associated arrays
     */
    public function get($sql, $params = null)
    {
        /**
         * Prepare a statement and hold it in a class property
         * 
         * @see _prepare()
         */
        $this->_prepare($sql);
        
        /**
         * Dynamically find the datatype of each parameter and bind them
         * 
         * @see _getDataTypes()
         * @see _bindParams()
         */
        if (!empty($params)) {
           $types = $this->_getDataTypes($params);                
           $this->_bindParams($types, $params);
        }        
        $this->_stmt->execute();
        
        /**
         * Obtain meta_data about the resultset to dynamically 
         * find the column names so they can be bound to the result
         */
        $meta_data = $this->_stmt->result_metadata();  
        
        /**
         * The parameters will need to be passed to bind_result by reference 
         */
        while ($field = $meta_data->fetch_field()) {    
            $params_by_ref[] = &$row[$field->name];  
        }
        
        /**
         * bind_result does not natively take an array, so use php's built-in
         * function to pass the array as parameters to the function 
         */
        call_user_func_array(array($this->_stmt, 'bind_result'), $params_by_ref);
        
        /**
         * Loop through each row and each field to build each row as an
         * associative array
         */
        $rows = array();
        
        while ($this->_stmt->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            $rows[] = $r;
        }
        
        $this->_stmt->close();
        
        return $rows;
    }
    
    /**
     * Provides a simplified interface for performing queries which modify
     * data, utilizing prepared statements
     * 
     * @param string $sql an INSERT, UPDATE, or DELETE query 
     * @param array $params an optional array of parameters to bind to the statement
     * @return void
     */
    public function execute($sql, $params = null)
    {
        /**
         * Prepare a statement and hold it in a class property
         * 
         * @see _prepare()
         */
        $this->_prepare($sql);
        
        /**
         * Dynamically find the datatype of each parameter and bind them
         * 
         * @see _getDataTypes()
         * @see _bindParams()
         */
        if (!empty($params)) {
           $types = $this->_getDataTypes($params);                
           $this->_bindParams($types, $params);
        }
        
        /**
         * Execute the query 
         */
        $this->_stmt->execute();
        $this->_stmt->close();
    }
    
    /**
     * If it exists, get the last row id inserted into the database
     * 
     * @return integer the insert id 
     */
    public function getLastInsertId()
    {
        if (isset($this->_db->insert_id)) {
            return $this->_db->insert_id;
        }
    }
    
    /**
     * Make sure the database connection is always explicitly closed
     * 
     * @return void 
     */
    public function __destruct()
    {
        if (isset($this->_db)) {
            $this->_db->close();
        }
    }
    
    /**
     * Creates a prepared statement using the supplied SQL and stores it
     * in a class property
     * 
     * @param string $sql the SQL statement to prepare
     * @return void
     */
    private function _prepare($sql)
    {
        $this->_stmt = $this->_db->prepare($sql);
    }
    
    /**
     * Loops through an array of parameters to determine the best fit for
     * their datatype and outputs a string of types, e.g. 'ssi' for 
     * use with bind_param
     * 
     * @param array $params the parameters to be checked
     * @return string the datatypes string
     */
    private function _getDataTypes($params)
    {
        $types = '';
                
        foreach ($params as $param) {
            $type = gettype($param);
            if ($type == 'integer') {
                $types .= 'i';
            } elseif ($type == 'double') {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        
        return $types;
    }
    
    /**
     * Dynamically binds any number of parameters to a prepared statement
     * 
     * @param string $types a string representing the datatypes
     * @param array $params an array of parameters
     */
    private function _bindParams($types, $params) 
    {   
        /**
         * Add the datatype string onto the front of the array 
         */
        array_unshift($params, $types);
        
        /**
         * Parameters will need to be passed to bind_param by reference 
         */
        foreach ($params as $key => &$value) {
            $params_by_ref[] = &$value;
        }
        
        /**
         * bind_param does not natively take an array, so use php's built-in
         * function to pass the array as parameters to the function 
         */
        call_user_func_array(array($this->_stmt, 'bind_param'), $params_by_ref);
    }
}