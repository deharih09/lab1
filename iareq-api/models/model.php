<?php
class Model
{
    public $_db; // property to store database connection
    public $_sql; // property to store SQL statement
 
    public function __construct()
    {
        $this->_db = Db::getDb(); // establish database connection 
    }
 

    public function _setSql($sql)
    {
        $this->_sql = $sql; 
    }

    public  function getAll($data = null)
    {
/* Define a method to set SQL statement. This method accepts an associative array, $data, as an argument
     $data should contain named parameters and their corresponding values as key/value pairs.
*/
        if (!$this->_sql)
        {
            throw new Exception("No SQL query defined!");
        }
        $stm = $this->_db->prepare($this->_sql);
        $stm->execute($data);
        return $stm->fetchAll();
    }

    public function getOne($data = null)
    {

/* Define a method to set SQL statement. This method accepts an associative array, $data, as an argument
     $data should contain named parameters and their corresponding values as key/value pairs.
*/
        if (!$this->_sql)
        {
            throw new Exception("No SQL query defined!");
        }

        $stm = $this->_db->prepare($this->_sql);
        $stm->execute($data);
         return $stm->fetch();

    }



  /**
     * Delete records from the database
     * Parameters:  table_name,
     *              condition (string)  Example: ' customer_id = :customer_id '
     *              condition_values - associative array (field => value ) of parameter values  for named parameters in the condition
     *              Example: condition_values = array('customer_id'=>100)  Note: do not include ':' when defining field
     * return bool
     */
    public function deleteRecords( $table, $condition, $condition_values )
    {
        $delete = "DELETE FROM {$table} WHERE {$condition} ";

        // bind values to named parameters
        $parameter_values = array();
        foreach( $condition_values as $field => $value )
        {
            $parameter_values[":{$field}"] = $value;
        }

        $stm = $this->_db->prepare($delete);
        $stm->execute($parameter_values);
        return json_encode($condition_values).', '.$delete; //true;
    }


    /**
     * Update records in the database
     * Parameters:  table_name,
     *              changes - associative array of changes field => value,
     *              condition (string)
     *              condition_values - array of parameter values  for named parameters in the condition
     *
     *   Example:   $changes = array('cost'=>99, 'title'=>'HDTV',..)
     *              $condition = 'product_id = :product_id '
     *              $condition_values = array('product_id'=>100);
     * return bool
     */
    public function updateRecord( $table, $changes, $condition, $condition_values )
    {
        $update = "UPDATE " . $table . " SET ";
        $parameter_values = array();
        foreach( $changes as $field => $value )
        {
            // define named parameters
            $update .= "`" . $field . "`=:{$field},";
            // bind values to named parameters
            $parameter_values[":{$field}"] = $value;
        }
        // remove our trailing ,
        $update = substr($update, 0, -1);
        if( $condition != '' )
        {
            $update .= " WHERE " . $condition;
        }

        // bind values to named parameters
       foreach( $condition_values as $field => $value )
        {
            $parameter_values[":{$field}"] = $value;
        }
        $stm = $this->_db->prepare($update);
        $stm->execute($parameter_values);
        return true;

    }

    /**
     * Insert record into the database
     * Parameters:  database table,  associative array of data to insert (field => value)
     * return bool
     */
    public function insertRecord( $table, $data )
    {
        // setup some variables for fields and values
        $fields  = "";
        $named_parameters = "";
        $parameter_values = array();

        // populate them
        foreach ($data as $f => $v)
        {
            $fields  .= "`$f`,";
            $named_parameters  .= ":{$f},";
            // bind values to named parameters
            $parameter_values[":{$f}"] = $v;
        }

        // remove our trailing ,
        $fields = substr($fields, 0, -1);
        // remove our trailing ,
        $named_parameters = substr($named_parameters, 0, -1);

        $insert = "INSERT INTO $table ({$fields}) VALUES({$named_parameters})";
        $stm = $this->_db->prepare($insert);
        $stm->execute($parameter_values);
        return  $this->_db->lastInsertId();
    }



}

 

 

?>
