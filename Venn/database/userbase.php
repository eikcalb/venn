<?php
namespace database;

class Userbase extends Database{
    
    public function add_user($form){ 
        $user=[];
        foreach($form as $a=>$b){
            if(empty($b)){throw new \Exception\FormError("Sorry, but you forgot to give me your $a :(");}
            switch ($a){
                    case "username": $user[$a] = \guard\Verify::username($b); break;
                    case "password": $user[$a] = \guard\Verify::password($b); break;
                    case "phone number": // Possible bug here... there shouldnt be any space between words in array names
                        $user[$a] = \guard\Verify::phone($b); break;
                    case "email": $user[$a] = \guard\Verify::email($b);   
                }                        
            }
            return $this->create_user('user',$user);
    }
    
    public function login($id,$pass){
        $result=$this->login_User($id);    
        if(password_verify($pass,$result['password'])){
            unset($result['password']);
            return $result;            
        }
        unset($result['password']);
        
        return false;              
    }
    
    private function login_User($id) {
        $result=null;
        if(is_numeric($id)){
            $query="SELECT `username`,`email`,`phone_number`,`first_name`,`last_name`,`password`,`userid` FROM `user` WHERE phone_number = ? ";            
        }else{
            $query="SELECT `username`,`email`,`phone_number`,`first_name`,`last_name`,`password`,`userid` FROM `user` WHERE email = ? ";
        }
        $stmt= mysqli_stmt_init($this->instance);
        if(!mysqli_stmt_prepare($stmt,$query)){
            throw new \Exception\Database("LOGIN :  init".mysqli_stmt_error($stmt),222);
        }
        if($this->stmt($stmt,'s',$id) && mysqli_stmt_execute($stmt)){
            $sql_result=mysqli_stmt_get_result($stmt);
            $numrows = mysqli_num_rows($sql_result);
        }  else {
            throw new \Exception\Database("LOGIN :  ".mysqli_stmt_error($stmt),223);
        }
        switch ($numrows){
            case 1: $result = mysqli_fetch_assoc($sql_result);break;
            case 0: throw new \Exception\Database("LOGIN :  Error Logging In (\"$id\")",\Exception\UserException::NO_MATCH);
                break;
            default : throw new \Exception\Database("LOGIN :  Error Logging In (\"$id\")",\Exception\UserException::MULTIPLE_USERS);
                break;                
        }
        mysqli_stmt_close($stmt);
        return isset($result)&&!empty($result)?$result:false;        
    }


    /**
     * 
     * @param type $username
     * @param type $email
     * @param type $phone_number
     * @param type $first_name
     * @param type $last_name
     * @param type $other_name
     * @param type $password
     * @param type $joined
     * @param type $verify_token This token is issued to the user upon registration and must be used for verification before $verify_end
     * @param type $verify_end The expiry time for issued verification token. the token should be changed after this period
     * @return boolean true on success but false on error
     * @throws \Exception\Database
     */
    public function create_user($username,$email,$phone_number,$first_name,$last_name,$other_name,$password,$joined,$verify_token,$verify_end){
        if($this->check_user("email", $email)!==false){ return false; }
        
        $query = "INSERT INTO user(`username`,`email`,`phone_number`,`first_name`,`last_name`,`other_name`,`password`,`joined`,`verify_token`,`verify_end`) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmt= mysqli_stmt_init($this->instance);
        if(!mysqli_stmt_prepare($stmt,$query)){
            throw new \Exception\Database("REGISTER :  init".mysqli_stmt_error($stmt),122);
        }
        if($this->stmt($stmt,'sssssssisi',$username,$email,$phone_number,$first_name,$last_name,$other_name,$password,$joined,$verify_token,$verify_end) && mysqli_stmt_execute($stmt)){
            $numrows =  mysqli_stmt_affected_rows($stmt);
                        echo $numrows;
        }  else {
            throw new \Exception\Database("REGISTER :  ".mysqli_stmt_error($stmt),123);
        }
        switch ($numrows){
            case 1: mysqli_stmt_close($stmt);break;
            case 0: return false;
                break;
            default : throw new \Exception\Database("REGISTER :  Error Signing Up (".  mysqli_stmt_error($stmt).")",\Exception\UserException::REGISTRATION_FAILED);
                break;                
        }
        return true;
    }
    
    public function update_user($user){
        
    }
    
    private function check_user($what,$expected){
        $query = "SELECT `status` FROM user WHERE `$what` = ?";        
        if(!$stmt= mysqli_prepare($this->instance,$query)){
            throw new \Exception\Database("CHECK :  init".mysqli_error($this->instance),252);
        }
        $sql_result='';
        if($this->stmt($stmt,'s',$expected)&&mysqli_stmt_execute($stmt)&&mysqli_stmt_bind_result($stmt,$sql_result)){
            $numrows = mysqli_stmt_num_rows($stmt);
        }  else {
            throw new \Exception\Database("CHECK :  ".mysqli_stmt_error($stmt),253);
        }
        switch ($numrows){
            case 0: return false;
            default : return $sql_result;               
        }        
    }
    
    public function read_user($user){
        
    }
    
    public function delete_data(){
        
    }
        
}