<?php
class RequirementController extends Controller
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

  public function getCategoryList($area)
    {
        try {
                $data_set = array();
                if ($area == '-1')
                         $data_set = -1;
                else
                        $data_set = $this->_model->getCategoryList($area);
                /* if it is a content usert then store data using session variables */
                $this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

    public function getRequirements($category=null, $id=null)
    {
        try {
                
                
                $data_set = array();
                if (isset($category) && isset($id)){
                    $data_set = $this->_model->getRequirements( $category, $id);
                }
                $this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }
    
    

    public function saveRequirement()
    {
        try {
                $data_set = array();
                $data_set['content'] = $this->_model->saveRequirement();
                /* if it is a content usert then store data using session variables */
                $this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

}