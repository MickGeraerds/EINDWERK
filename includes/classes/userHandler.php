<?php
require_once('includes/classes/dbHandler.php');
class userHandler extends dbHandler {
    
    public function makeUser($userInfo) {
        
        $username = $this->quote($userInfo['username']);
        $password = $this->quote(md5($userInfo['password']));
        $email = $this->quote($userInfo['email']);
        $lastActive = $this->quote($_SERVER['REQUEST_TIME']);
        $lastIP = $this->quote($_SERVER['REMOTE_ADDR']);
        
        
        $query = "INSERT INTO `accounts` (`username`, `password`, `email`, `lastActive`, `lastIP`) VALUES (" . $username . "," . $password . "," . $email . "," . $lastActive . "," . $lastIP . ")";
        
        $result = $this -> query($query);
        
        if(isset($this->dbConnect()->error_list[0]['errno']) && $this->dbConnect()->error_list[0]['errno'] == 1062) {
            return showError("username already in use");
        }
        elseif(!isset($this->dbConnect()->error_list[0]['errno'])) {
            return showError("Account created succesfull");
        }
        else {
            return showError("Oops something went wrong.");
        }
    }
    public function loginUser($userInfo) {
        
        $username = $this->quote($userInfo['username']);
        $password = $this->quote(md5($userInfo['password']));
        $remember = $userInfo['remember'];
        $lastActive = $this->quote($_SERVER['REQUEST_TIME']);
        $lastIP = $this->quote($_SERVER['REMOTE_ADDR']);
        
        $query = "SELECT * FROM `accounts` where username = " . $username . "and password = " . $password;
        
        $result = $this -> select($query);
        if(isset($result[0]["account_id"])) {
            $accountId = $result[0]["account_id"];
        }
        
        if($result != false) {
            $query = "UPDATE `accounts` SET `lastActive` ="  . $lastActive . ", `lastIP` =" . $lastIP . " WHERE `username` = " . $username . "and `password` = " . $password;
            
            $result = $this -> query($query);
            
            if($remember == 'on'){
                setcookie("login[username]", $username, time()+604800);
                setcookie("login[password]", $password, time()+604800);
                setcookie("login[ip]", $lastIP, time()+604800);
            }
            else {
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $password;
            }
            $_SESSION["login_id"] = $accountId;
            setcookie("display[username]", $username);
            return Header('Location: '.$_SERVER['PHP_SELF']);
                
        }
        else {
            return showError("Login does not match");
        }
    }
    public function checkLoggedIn() {
        
        if (isset($_COOKIE["login"]["username"]) && isset($_COOKIE["login"]["password"]) && isset($_COOKIE["login"]["ip"])) {
        
            $query = "SELECT * FROM `accounts` where username = " . $_COOKIE["login"]["username"] . "and password = " . $_COOKIE["login"]["password"];
        
            $result = $this -> select($query);
            $accountId = $result[0]["account_id"];
            
            $_SESSION["login_id"] = $accountId;
            setcookie("display[username]", $_COOKIE["login"]["username"], time()+604800);            
        }
        
        
        if (isset($_COOKIE["login"]["username"]) && isset($_COOKIE["login"]["password"]) && isset($_COOKIE["login"]["ip"]) || isset($_SESSION["username"]) && isset($_SESSION["password"])) {
            if (isset($_COOKIE["login"]["username"])) {
                $username = $_COOKIE["login"]["username"];
            }
            else {
                $username = $_SESSION["username"];
            }
            setcookie("display[username]", $username, time()+604800);
            return 1;
        }
        else {
            return 0;
        }
        
    }
	public function forgotPassword($email){
		$query = "SELECT * FROM `accounts` where email = " . $email;
		
		$result = $this -> select($query);
		if($result != false) {
			$token = openssl_random_pseudo_bytes(16);
			$token = bin2hex($token);
			echo $token;
		}
	}
	public function makeNewPassword() {
		
	}
    public function logout() {
        session_destroy ();
        setcookie("login[username]", '');
        setcookie("login[password]", '');
        setcookie("login[ip]", '');
        setcookie("display[username]", '');
        Header('Location: '.$_SERVER['PHP_SELF']);
        
    }
}

/*
    ********* Optional adds ************
    - Forgot Password
    - secure cookies
*/
?>