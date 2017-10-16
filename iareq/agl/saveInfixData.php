<?php
 include('../.config.php');
 try {
	 $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
                // establish a connection
        $db = new PDO($dsn, DB_USER, DB_PASS);
 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // set default fetch mode
               $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
 } catch (PDOException $e) {
     die();
 }

 $result = array('mode'=>1);
$data = json_decode(file_get_contents('php://input'));
$id = $data->id;
$infix = $data->infixPrereq;

 $sql1 = 'update `course` set infixPrereq = :infix WHERE id =:id';
 $stm1 = $db->prepare($sql1);
 $stm1->execute(array(':id'=>$id, ':infix'=>$infix));
echo json_encode($result);
 
