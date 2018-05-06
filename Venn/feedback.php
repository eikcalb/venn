<?php

class Feedback {
    private $db;
    public function __construct() {
        $this->db = DB::init()->link;
    }
    
    public function add_question($title,$question){
        $stmt = mysqli_stmt_init($this->db);$time = time();
        if(!mysqli_stmt_prepare($stmt, "INSERT INTO Question(title,question,asked_time) VALUES (?,?,'".$time."')")){
            echo mysqli_stmt_error($stmt);
            return false;
        }
        mysqli_stmt_bind_param($stmt, "ss", $title,$question);
        if(mysqli_stmt_execute($stmt)){
            return true;
        }
        return false;
    }
    
    public function give_feedback($userid, $questionid, $answer = "No Idea What You Mean"){
        $stmt = mysqli_stmt_init($this->db);
        mysqli_stmt_prepare($stmt, "INSERT INTO feedback(userid,questionid,answer,answered_time) VALUES (?,?,?,'".time().";')");
        mysqli_stmt_bind_param($stmt, "iis", $userid,$questionid,$answer);
        if(mysqli_stmt_execute($stmt)){
            return true;
        }
        return false;
    }
    
    public function list_user_answers($userid){
        $stmt = mysqli_stmt_init($this->db);
        mysqli_stmt_prepare($stmt,"SELECT Question.title,Question.question,Question.asked_time,Feedback.answer,Feedback.answered_time FROM Question,Feedback WHERE Feedback.questionid = Question.id AND Feedback.userid = ?");
        mysqli_stmt_bind_param($stmt, "i", $userid);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        return false;
    }
    
    public function question($id){
        if($this->check_answered($id)){
            header("location:./feed.php?all");
            exit;
        }
        $stmt = mysqli_stmt_init($this->db);
        mysqli_stmt_prepare($stmt,"SELECT id,title,question,asked_time FROM Question WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }else{
            return false;
        }
    }
    
    private function check_answered($id){
        $query = "SELECT `answer` FROM feedback WHERE `userid`=? AND `questionid`=?";
        $stmt = mysqli_stmt_init($this->db);
        mysqli_stmt_prepare($stmt,$query);
        mysqli_stmt_bind_param($stmt, "ii",$_SESSION['id'],$id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_num_rows($result)>0?true:false;
        }else{
            return false;
        }
    }

    public function list_questions(){
        if(($result = mysqli_query($this->db,"SELECT id,title,question,asked_time FROM Question WHERE 1"))){
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }else{
            return false;
        }
    }
}

class User{
    private $db;
    public $details = null;
    const NUMBER = 1; const EMAIL = 2;
    
    public function __construct(){
        $this->db = DB::init()->link;
    }
    
    public function add_user($email,$first_name,$last_name,$pass,$phone_number){
        $stmt = mysqli_stmt_init($this->db);
        $password = password_hash($pass,PASSWORD_BCRYPT);
        mysqli_stmt_prepare($stmt, "INSERT INTO User(email,first_name,last_name,password,phone_number) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssss", $email, $first_name, $last_name, $password, $phone_number);
        if(!mysqli_stmt_execute($stmt)){
            return false;
        }
        return true;
    }
    
    public function login_user($token,$pass){
        $stmt = mysqli_stmt_init($this->db);
        if(filter_var($token,FILTER_VALIDATE_EMAIL)){
            $type = self::EMAIL;
        }  elseif(filter_var($token, FILTER_VALIDATE_INT)){
            $type = self::NUMBER;
        }else{
            return false;
        }
        if(!$this->verify_pass($token,$pass,$type)){
            return false;                       
        }
        session_name("cuser");
        if($this->details!==null){
            if(session_status()!==PHP_SESSION_ACTIVE){session_start();}
            $_SESSION = $this->details;
            $_SESSION['login'] = true;
            return true;
        }  else {
            return false;
        }
    }
    
    private function verify_pass($user_token,$pass,$type){
        $stmt = mysqli_stmt_init($this->db);
        switch ($type){
            case self::EMAIL:$query =  "SELECT id,email,phone_number,first_name,last_name,password FROM User WHERE email = ?";
                break;
            case self::NUMBER:$query =  "SELECT id,email,phone_number,first_name,last_name,password FROM User WHERE phone_number = ?";
                break;
            default : throw new Exception("Login format not supported");
        }
        mysqli_stmt_prepare($stmt,$query);
        mysqli_stmt_bind_param($stmt, "s", $user_token);
        mysqli_stmt_execute($stmt);
        $result= mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));//,$result["id"], $result["email"],$result["phone_number"],$result["first_name"],$result["last_name"],$result["password"]);
        if(password_verify($pass,$result["password"])){
            unset($result["password"]);
            $this->details = $result;
            return true;
        }else{
            return false;
        }
    }
    
}

class DB{
    const DBNAME = "Zercom";
    const USERNAME = "root";
    const PASSWORD = "";
    const ADDRESS = "localhost";
    public $link;
    
    private function __construct($link){
        $this->link = $link;
    }
    
    public static function init(){
        $link = mysqli_connect(self::ADDRESS, self::USERNAME, self::PASSWORD);
        if(is_null($link)){
            throw new Exception("Error connecting to database.<p>Error description: ".  mysqli_connect_error());
        }
        if(mysqli_query($link, "CREATE DATABASE IF NOT EXISTS ".self::DBNAME)&&mysqli_select_db($link, self::DBNAME)){
            mysqli_begin_transaction($link);
            if(!mysqli_query($link,"CREATE TABLE IF NOT EXISTS User("
                    . "id int(10) AUTO_INCREMENT PRIMARY KEY,"
                    . "email varchar(82) UNIQUE,"
                    . "first_name varchar(80) NOT NULL,"
                    . "last_name varchar(85) NOT NULL,"
                    . "password varchar(100) NOT NULL,"
                    . "phone_number varchar(20) UNIQUE NOT NULL)")||!mysqli_query($link,"CREATE TABLE IF NOT EXISTS Feedback("
                    . "id int(10) AUTO_INCREMENT PRIMARY KEY,"
                    . "userid int(10) NOT NULL ,"
                    . "questionid int(10) NOT NULL ,"
                    . "answer TEXT NOT NULL,"
                    . "answered_time int(20) DEFAULT 0,"
                    . "UNIQUE(userid,questionid))")||!mysqli_query($link,"CREATE TABLE IF NOT EXISTS Question("
                    . "id int(10) AUTO_INCREMENT PRIMARY KEY,"
                    . "title varchar(80) UNIQUE,"
                    . "question TEXT NOT NULL,"
                    . "asked_time int(10) DEFAULT 0)")){
                mysqli_rollback($link);
            }
            mysqli_commit($link);
            return new DB($link);
        }else{
            echo mysqli_error($this->link);
            return false;
        }
    }
}
