<?php

include('./.config.php');

class Db {

    private static $db;

    public static function getDb() {
        if (!self::$db) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
                // establish a connection

                self::$db = new PDO($dsn, DB_USER, DB_PWD);

                // after each error, throw exceptions

                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // set default fetch mode

                self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {

                die('Connection error: ' . $e->getMessage());
            }
        }

        return self::$db;
    }

}

class Model {

    public $_db; // property to store database connection
    public $_sql; // property to store SQL statement

    public function __construct() {
        $this->_db = Db::getDb(); // establish database connection 
    }

    public function _setSql($sql) {
        $this->_sql = $sql;
    }

    public function getAll($data = null) {
        /* Define a method to set SQL statement. This method accepts an associative array, $data, as an argument
          $data should contain named parameters and their corresponding values as key/value pairs.
         */
        if (!$this->_sql) {
            throw new Exception("No SQL query defined!");
        }
        $stm = $this->_db->prepare($this->_sql);
        $stm->execute($data);
        return $stm->fetchAll();
    }

    public function getOne($data = null) {

        /* Define a method to set SQL statement. This method accepts an associative array, $data, as an argument
          $data should contain named parameters and their corresponding values as key/value pairs.
         */
        if (!$this->_sql) {
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
    public function deleteRecords($table, $condition, $condition_values) {
        $delete = "DELETE FROM {$table} WHERE {$condition} ";

        // bind values to named parameters
        $parameter_values = array();
        foreach ($condition_values as $field => $value) {
            $parameter_values[":{$field}"] = $value;
        }

        $stm = $this->_db->prepare($delete);
        $stm->execute($parameter_values);
        return json_encode($condition_values) . ', ' . $delete; //true;
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
    public function updateRecord($table, $changes, $condition, $condition_values) {
        $update = "UPDATE " . $table . " SET ";
        $parameter_values = array();
        foreach ($changes as $field => $value) {
            // define named parameters
            $update .= "`" . $field . "`=:{$field},";
            // bind values to named parameters
            $parameter_values[":{$field}"] = $value;
        }
        // remove our trailing ,
        $update = substr($update, 0, -1);
        if ($condition != '') {
            $update .= " WHERE " . $condition;
        }

        // bind values to named parameters
        foreach ($condition_values as $field => $value) {
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
    public function insertRecord($table, $data) {
        // setup some variables for fields and values
        $fields = "";
        $named_parameters = "";
        $parameter_values = array();

        // populate them
        foreach ($data as $f => $v) {
            $fields .= "`$f`,";
            $named_parameters .= ":{$f},";
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
        return $this->_db->lastInsertId();
    }

}

class DegreeModel extends Model {

    public function saveRequirements() {
        //$data = json_decode(file_get_contents('php://input'));
        $data = new stdClass();
        $data->category = 'major';
        $data->state = 1;
        $data->type = 1;
        $data->id = -1;
        $data->label = 'Test';
        //$data->courses = 'COMPSCI 220, COMPSCI 222, compsci 223';
         $data->courses = 'COMPSCI 220';
        $data->max_credits = 9;
        $data->min_credits = 9;
        $data->model = '';
        $data->notes = '';
        $data->num_courses = 3;
        $data->pk = 1;
        $data->template = '';
        $data->units = 0;
        $data->sub_state = '';
        $data->choices = '';
        $data->major_id = 1;
        $category = $data->category;
        $category_id = $category . '_id';

        $tmp_data = new stdClass();
        // Assign a unique req_id
        $req_id = $this->getRequirementID($category, $category_id);

        // prepare course list
        $c = $data->courses;
        if (stripos($c, ',') === false) {
            // No change in the value or incorrect format. Do not update course list
            //$mode = 1;
            $courseList = trim($c);
            $info = array($courseList);
        } else {
            $info = explode(',', $c);
        }
            $courseList = $this->getCourseList($info);
            if (!$courseList){
                $tmp_data->mode = -1;
                return $tmp_data;
            }
            //$mode = 2;
        
        // Define Modal information
        $modal_info = $this->getModalInfo($data->state, $data->sub_state);
        return $this->saveData($category, $req_id, $data, $modal_info, $category_id, $courseList);
    }

    public function saveData($category, $req_id, $data, $modal_info, $category_id, $courseList) {

        $values = array('req_id' => $req_id, 'category_id' => $data->{$category_id}, 'courses' => $courseList, 'label' => $data->label,
            'notes' => $data->notes, 'min_credits' => $data->min_credits, 'choices' => $data->choices,
            'max_credits' => $data->max_credits, 'num_courses' => $data->num_courses, 'type' => $data->type, 'state' => $data->state);

        /* if (trim($code->id) == -1) {
          $result = $this->insertRecord($category, $values);
          } else
          $result = $this->updateRecord($category, $values, ' id=:id ', array('id' => $code->id));
          $tmp_data = new stdClass();
          $tmp_data->mode = ($result) ? 1 : -1; */
        
        $tmp_data = new stdClass();
          $tmp_data->mode = $courseList;
        //$tmp_data = $courseList;
        return $tmp_data;
    }

    public function getCourseList($info) {
        $str = '';
        print_r($info);

        for ($i = 0; $i < count($info); $i++) {
            if (isset($info[$i]) && ($info[$i] != '')) {
                $line = explode(' ', trim($info[$i]));
                $subject = trim($line[0]);
                $id = trim($line[1]);


                if (isset($id) && ($id != '')) {
                    $course_info = $this->getCourseid($subject, $id);
                    if (is_object($course_info))
                        $str .= $course_info->id . ", ";
                    else
                        return false;
                }
            }
        }
        $str = substr($str, 0, -2);
        return $str;
    }

    public function getCourseid($subject, $number) {

        echo "Number: " . $number . '<br/>';
        $course_list = array();
        $sql = "SELECT c.id
FROM course AS c
WHERE 
c.subject = :subject AND number = :number
ORDER BY c.id";
        $stm = $this->_db->prepare($sql);
        $sub = $subject;
        $items = array(':subject' => $sub, ':number' => $number);
        //$stm->bindParam(1, $subject);
        $stm->execute($items);

        return $stm->fetch();
    }

    public function getModalInfo($state, $substate) {
        $controller = '';
        $template = '';
        $method = '';
        if ($state == 3) {
            switch ((int) $substate) {
                case 0:
                    $controller = "RadioModalController";
                    $template = "selectRadioGroup.html";
                    $method = "selectOneElective";
                    break;
                case 1:
                    $controller = "SelectManyModalController";
                    $template = "selectCheckboxes.html";
                    $method = "selectManyElectives";
                    break;
            }
        }
        return array('controller' => $controller, 'template' => $template, 'method' => $method);
    }

    public function getRequirementID($category, $category_id) {
        $sql1 = 'SELECT req_id FROM `' . $category . '_requirements` WHERE ' . $category_id . ' = :id order by req_id DESC limit 1';
        $stm1 = $this->_db->prepare($sql1);
        $stm1->execute(array(':id' => $category_id));
        $req_id = 0;
        $last_index = $stm1->rowCount();
        if ($last_index > 0) {
            $result = $stm1->fetch();
            $req_id = (int) $result->req_id + 1;
        }
        return $req_id;
    }

}

$req = new DegreeModel();
print_r($req->saveRequirements());
