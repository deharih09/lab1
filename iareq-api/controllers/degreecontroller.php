<?php
class DegreeController extends Controller
{
    public $_model;
    public $_view;
    public $_baseName;
    public $_action;
    public function __construct($base_name, $action)
    {
	parent::__construct($base_name, $action);

	// Define the model to access database
	$this->_setModel($this->_baseName."Model");
    }

    public function saveRequirement()
    {
        try {
                $data_set = array();
                $data_set = $this->_model->saveRequirement();
                /* if it is a content usert then store data using session variables */
                $this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

}
