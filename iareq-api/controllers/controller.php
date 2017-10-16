<?php
class Controller
{
    public $_model;  //define an instance of the model
    public $_view;   // define an instance of the view

    public function __construct($prefix, $method)

    {
        // define the initial model to get data, action to process data, and the default template file to display data
        $this->_baseName = $prefix;
        // DS is the directory separator, BASE_DIR is the base directory
     if( isset($this->_baseName) && ($this->_baseName != "") )
              $this->_view = new View(BASE_DIR . DS . 'views' . DS . strtolower($this->_baseName) . DS . strtolower($method) . '.tpl.php');
      else
               $this->_view = new View(BASE_DIR . DS . 'views' . DS.'defaultview.php');
    }

    public function _setModel($modelName)
    {
     // define a method to set model
        $this->_model = new $modelName();
    }

    public function _setView($view_file)
    {
        // define a method to set template
        $this->_view = new View( $view_file);
    }

    public function default_view()
    {
        // define a method to set output
        try {
               $this->_view->output();
        } catch (Exception $e) {
                echo "Application error: ixx".$e->getMessage();
        }
    }
}
