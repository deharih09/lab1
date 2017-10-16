<?php
class LoginModel extends Model
{

    public  function checkLogin($username, $password)
    {
	$sql = "SELECT id, first, last, status, num_tries FROM user_stat_new WHERE user = :uname and pwd = :pwd";
	$pwd = md5($password);
	$data = array(':uname'=>strtolower($username), ':pwd'=>$pwd);
	$data2 = array(':username'=>strtolower($username));
	$this->_sql = $sql;
        $result = $this->getOne($data);
	if (count($result)>0 && isset($result->id)){
		$sql_2 = "UPDATE `user_stat_new` SET num_tries = 0 WHERE user = :username ";
		$this->_sql = $sql_2;
		$result_2 = $this->updateSingleRecord($data2);
		$result->total = 1;
		return $result;
	} else {
		$result = array();
		$sql_3 = "UPDATE `user_stat_new` SET num_tries = num_tries + 1 WHERE user = :username ";
                $this->_sql = $sql_3;
                $result_3 = $this->updateSingleRecord($data2);
		if ($result_3 === true ){
			$sql_4 = 'SELECT num_tries FROM `user_stat_new` WHERE user = :username';
			$this->_sql = $sql_4;
			$result = $this->getOne($data2);
                	$result->total = 0;
			return $result;
		}
		$result = array('success'=>false, 'total'=>0);
		return $result;
	}
    }

       public  function checkAdminLogin($username, $password)
    {
	$sql = "SELECT id, first, last FROM user_admin WHERE user_name = :uname and pwd = :pwd";
	$pwd = md5($password);
	$data = array(':uname'=>strtolower($username), ':pwd'=>$pwd);
	$data2 = array(':username'=>strtolower($username));
	$this->_sql = $sql;
        $result = $this->getOne($data);
	if (count($result)>0 && isset($result->id)){
		$result->total = 1;
		return $result;
	} else {
		
		
		$result = array('success'=>false, 'total'=>0);
		return $result;
	}
    }
    
  public  function checkUser($username, $password)
    {
        $sql = "SELECT id, first, last, status, num_tries FROM user_stat_new WHERE user_name = :uname";
        $data = array(':uname'=>strtolower($username));
        $this->_sql = $sql;
        $result = $this->getOne($data);

        if (count($result)>0 && isset($result->user_name) ){
		$info = array('success'=>true);
		return $info;
	}
		$info = array('success'=>false);
		return $info;

  }


  public  function getStudentList()
    {
        $sql = "SELECT concat(last, ', ',first) as name, id FROM user_stat_new WHERE type='s'"; // and first like ? or last like ?";
        $this->_sql = $sql;
	//$data = array("%$name%", "%$name%");
        $result = $this->getAll();

        return $result;

  }



  public  function updatePwd($username, $password)
    {
        $sql = "SELECT * FROM user_stat_new WHERE user_name = :uname";
        $pwd = md5($password);
        $data = array(':uname'=>strtolower($username));
        $this->_sql = $sql;
        $result = $this->getOne($data);

        $current = false;
        if (count($result)>0 && isset($result->pwd)){
                if ($result->pwd === $pwd)
                        $current = true;
                $info = array('success'=>true, 'current'=>$current);
                return $info;
        }
                $info = array('success'=>false);
                return $info;

  }

 public function resetpassword($user){
	$passwd = $this->generateRandomString(12);
	$table = 'user_stat_new';
        $condition = ' user_name = :user_name ';
        $condition_values = array('user_name'=>$user->username);
        $sql = 'update `user_stat_new` set pwd = :pwd, status = 0 where user_name= :username';
        $data = array(':pwd'=>md5($passwd), ':username'=>strtolower($user->username));
        $this->_sql = $sql;
        $result = $this->updateSingleRecord($data);
        //$result = $this->updateRecord($table, $values, $condition, $condition_values);
        if ($result>0){
		$check = $this->sendTempPassword($user, $passwd);
		if ($check>0){
                	return array('success'=>true, 'result'=>$result);
        	}else
                	return array('success'=>true, 'result'=>$result);
        }
	return array('success'=>false);

 }


 public function addNewUser($user_info){
        $uname = strtolower($user_info->username);
	$passwd = $this->generateRandomString(12);
        $sql = "insert into user_stat_new (user_name, pwd, first, last, type) values (:user_name, :pwd, :first, :last, 's')";
        $values = array(':user_name'=>$uname, ':pwd'=>md5($passwd), ':first'=>$user_info->first, ':last'=>$user_info->last);
	$this->_sql = $sql;
        $result = $this->getRowCount($values);
	if ($result == 1){
		$check = $this->sendTempPassword($user_info, $passwd);
        	if ($check>0)
                	return array('success'=>true, 'send'=>true);
        	else {
                	return array('success'=>true, 'send'=>false);
		}
	}
        return array('success'=>true);
 }

 public function checkUserId($user_info){
        $uname = strtolower($user_info->username);
        //$passwd = $this->mega_escape_string($user_info->password);
        $sql = "SELECT * from user_stat_new where user_name = :username";
        $this->_sql = $sql;
        $data = array(':username'=>$uname);
        $result = $this->getRowCount($data);
        if ($result>0)
                return array('success'=>false);
        else {
                return array('success'=>true);
        }
 }


 public function updatepassword($user_info){
        $table = 'user_stat_new';
        $values = array(':pwd'=>$user_info->password);
        $condition = ' user_name = :user_name ';
	$condition_values = array('user_name'=>$user_info->username);
	$sql = 'update `user_stat_new` set pwd = :pwd, status = 1 where user_name= :username';
	$data = array(':pwd'=>md5($user_info->password), ':username'=>strtolower($user_info->username));
	$this->_sql = $sql;
	$result = $this->updateSingleRecord($data);
	//$result = $this->updateRecord($table, $values, $condition, $condition_values);
        if ($result>0)
                return array('success'=>true, 'result'=>$result);
        else {
                return array('success'=>false, 'result'=>$result);
        }
 }

  public function updateSingleRecord($data){
	 if (!$this->_sql)
        {
            throw new Exception("No SQL query defined!");
        }
        $stm = $this->_db->prepare($this->_sql);
        $stm->execute($data);
	return true;

	
 }

 public function sendTempPassword($user, $passwd){
	$header = "<html><head><style type='text/css'> .left {margin-left: 30px}</style></head><body>";
	$mail_to = $user->username;
	$mail_subject = "IDP Temporary password";
	$mail_body = "Your temporary password is ".$passwd;
	$mail_From = "no_reply@optsolv.com";
	$mail_headers = 'Content-type: text/html; charset=utf-8'."\r\n";
	$mail_headers .= "From: $mail_From \r\n";

//if ( mail($mail_to, $mail_subject, $mail_body, $mail_headers) )
	if ( mail($mail_to,$mail_subject,$mail_body,"From: $mail_From\nMIME-Version: 1.0\nContent-type: text/html; charset=utf-8\n") )
	{
		return 1;
	}
	return -1;
 }

public function generateRandomString($length = 10) {
    $characters = '*0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


}

?>
