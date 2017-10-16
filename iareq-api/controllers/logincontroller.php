<?php
class LoginController extends Controller
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


    public function getlogin()
    {
	$input_data = json_decode(file_get_contents('php://input'));
        $username = isset($input_data->username)?  $input_data->username : -1;
        $password = isset($input_data->password)?  $input_data->password : -1;

        try {
		$data_set = array();
		if ($username == -1 || $password == -1)
			$data_set = -2;
		else $data_set = $this->_model->checkLogin($username, $password) ;
		$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
		$this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

    public function getAdminLogin()
    {
	$input_data = json_decode(file_get_contents('php://input'));
        $username = isset($input_data->username)?  $input_data->username : -1;
        $password = isset($input_data->password)?  $input_data->password : -1;

        try {
		$data_set = array();
		if ($username == -1 || $password == -1)
			$data_set = -2;
		else $data_set = $this->_model->checkAdminLogin($username, $password) ;
		$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
		$this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

    public function checkuser()
    {
        $input_data = json_decode(file_get_contents('php://input'));
        $username = isset($input_data->username)?  $input_data->username : -1;

        try {
                $data_set = array();
                if ($username == -1 )
                        $data_set = -2;
                else $data_set = $this->_model->checkUser($username, $password) ;
				$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

    public function getStudentList()
    {
        $input_data = json_decode(file_get_contents('php://input'));
        $name = isset($input_data->name)?  $input_data->name : -1;

        try {
                $data_set = array();
                if ($username == -1 )
                        $data_set = -2;
                else $data_set = $this->_model->getStudentList() ;
				$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }


    public function adduser()
    {
        $input_data = json_decode(file_get_contents('php://input'));

        try {
                $data_set = array();
                $data_set = $this->_model->addNewUser($input_data) ;
				$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }


    public function checkduplicate()
    {
        $input_data = json_decode(file_get_contents('php://input'));

        try {
                $data_set = $this->_model->checkUserId($input_data) ;
				$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }



    public function resetpassword()
    {
        $input_data = json_decode(file_get_contents('php://input'));

        try {
                $data_set = $this->_model->resetpassword($input_data) ;
				$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }

    public function updatepassword()
    {
        $input_data = json_decode(file_get_contents('php://input'));

        try {
                $data_set = $this->_model->updatepassword($input_data) ;
				$this->_setView(BASE_DIR.DS.'views'.DS.'generic_ajax_response.php');
                $this->_view->data_set = $data_set;
                $this->_view->output(); // display data
        } catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }
    }


   public function logout(){
	try {
		$this->_view->output();
	} catch (Exception $e) {
                echo "Application error:".$e->getMessage();
        }



   }

}
