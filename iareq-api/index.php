<?php
/********************************************************************
   Author: Sobitha Samaranayake 
   Version: Basic API 1.0

   URL pattern: HOME/prefix/action/parameter

   IMPORTANT: URL rewriting rules are included in the .htaccess file

               Database access is defined in .config.php file

   URL rewriting rule changes the way server handles requests.
   The include() method requires the correct path of each file.
   Use the absolute path of the base folder as the reference point.
   

/**************  Define Global Constants ****************************/
    define('HOSTNAME', 'http://localhost');
    define('INSTITUTION', 'uww');
    define('API', 'iareq-api');  // name of the folder/api
    define('EXE', '/home/athula/dp/ar5/degree'); // C++ executable for degree plan
    define('EXEAR', '/home/athula/dp/ar5/degreeAR'); // C++ executable for AAR
    // The magic constant __FILE__ returns the current file.
    // The dirname(file) method returns the path of the file.
    define('BASE_DIR', dirname(__FILE__));

    /* dirname() method does not return the trailing '/' for the path.
       This may create problems when copying files to a different operating system.
       Use the PHP built-in DIRECTORY_SEPARATOR constant for the directory separator.
    */

    define('DS', DIRECTORY_SEPARATOR);

    /*  
	Use the relative URL of the application folder as the base URL for templates and scripts
    */
    define("HOME", HOSTNAME.DS.API);
/**********************************************************************************************/

    // Define database connection
    include('.config.php');

    /*******************************************************************************************
	Class files need to be loaded first. Classes are defined using an appropriate 'prefix'
	and  placed in controllers, models, and views folders.
	Define a method (autoloder) to include class files if those files are not already included

       IMPORTANT: All class files are assumed to be in the models, controllers, and utilities folders.
		  Base Classes:  
			Model (models/model.php)
			Controller (controllers/controller.php)
			View (utilities/view.php)
			Db (utilities/db.php) (singleton object)

		  Derived Classes are named using the 'prefix'. 
		  For example, if the prefix is 'course', then CourseController and CourseModel are 
		  classes defined by extending Controller and Model classes, respectively.

		  IMPORTANT: File names are case-sensitive: all lower case. 
    */

    function __autoload($class){
	if (file_exists(BASE_DIR.DS.'models'.DS.strtolower($class).'.php')){
	    require_once(BASE_DIR.DS.'models'.DS.strtolower($class).'.php');
	} else if (file_exists(BASE_DIR.DS.'controllers'.DS.strtolower($class).'.php')){
	     require_once(BASE_DIR.DS.'controllers'.DS.strtolower($class).'.php');
	} else if (file_exists(BASE_DIR.DS.'utilities'.DS.strtolower($class).'.php')){
	     require_once(BASE_DIR.DS.'utilities'.DS.strtolower($class).'.php');
	}

    }

    /***************************************************************************************
       if the request URL  is not an existing file or a folder then the URL rewriting rule assumes
       that the request URL is in the following form:
            HOME/prefix/method/parameter

       The URL rewriting rule  converts the "prefix/method/parameter" string into the following
       'key=value' format:
       	   'action=prefix/method/parameter'

    ******************************************************************************************/

    // Define a variable to store request action
    $action = "";
    // Read request action
    if (isset($_REQUEST['action']))
    	$action = $_REQUEST['action'];

    // Read the request data into an array
    $request_data = explode('/', $action);

    /*********************************************************************************************
	The 'prefix' defines the controller, model, and view files to be used.
        For example, if the 'prefix is 'faculty' then the script uses the following :
            Controller class: FacultyController    file: controllers/facultycontroller.php
            Model class :     FacultyModel         file: models/facultymodel.php
            Default View folder : views/faculty

	    Method to invoke (action) : method
	    Parameter for the GET type request : parameter

    **********************************************************************************************/

     // Read the prefix
     $prefix = (isset($request_data[0]))? $request_data[0] : '';

    // Use the prefix to define the controller
     $thiscontroller = ucfirst($prefix).'Controller';

    // Use the prefix to define the model
     $this_model = ucfirst($prefix).'Model';

    // Define a method to obtain data and display result
    $method = (isset($request_data[1])) ? $request_data[1] : 'index';

    /**********************************************************************************************
	Parameter values may be sent using GET or POST methods or both.
	If they are sent via POST method then appropriate keys can be used to extract additional data.
	Use JSON type data for POST values
    */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS");
header("Access-Control-Allow-Headers", "Content-Type, authorization");
    $parameter = null;
	$parameter2 = null;
    if (isset($request_data[2]))
	$parameter = $request_data[2];
	if (isset($request_data[3]))
	$parameter2 = $request_data[3];
    elseif (isset($_POST['parameter']))
	$parameter = $_POST['parameter'];  // use JSON data instead
    /**********************************************************************************************/

    /**********************************************************************************************
	USE appropriate controller, mode, and view to fetch data and display result
    
        If the prefix is non-empty then display requested data. Otherwise, display the default view

        Create an instance of an appropriate controller.
	The controller uses the 'prefix' to set an appropriate model.
	The controller uses $method to obtain data end display result.
    */
	$controller = new $thiscontroller($prefix, $method);

	// Invoke a method to obtain and display data

   if (method_exists($controller, $method))
	$controller->$method($parameter, $parameter2);
   else
	$controller->default_view();
/*** end main ***/

?>
