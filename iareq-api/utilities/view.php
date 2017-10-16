<?php
class View
{
    public $_file;
    public function __construct($file)
    {
        /* The output is generated using a template file. Initialize the template file */
        $this->_file = $file;
    }
    public function setData($data)
    {
        /*  define a method to set the data */
        $this->data_set = $data;
    }
    public function __set($key, $value){
        /* The _ _set( ) method is automatically called when any variable is set that did not directly exist */
        $this->$key = $value;
    }
    public function __get($key)
    {
        /* The _ _get( ) method is automatically called and passed the name of the variable desired whenever variable access is attempted */
        return $this->$key;
    }

    public function output()
    {
        if (!file_exists($this->_file))
        {
            throw new Exception( $this->_file . " doesn't exist.");
        }
 	if (isset($this->data_set))
                $data_set = $this->data_set;
        else {
                $data_set = array();
                $data_set['page-title'] = "Degree Planner API";
                $data_set['content-title'] = "Interactive Degree Planner";
                $data_set['top-image'] = HOME.'/assets/images/computer.jpg';

	}
        include($this->_file); // display view
    }

}
