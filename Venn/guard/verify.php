<?php
namespace guard;

class Verify{
    
    public static function username($name){
        return trim($name);
    }
    
    /**
     *  This is used to verify email addresses
     * @param string $email
     * @return string
     * @throws \Exception\FormError
     */
    public static function email($email){
        $mail=trim($email);
        if(filter_var($mail,FILTER_VALIDATE_EMAIL)!=false){
            //  VERIFY EMAIL              
            return $mail;
        }  else {
            throw new \Exception\FormError("Email address you entered doesn't seem to be valid");
            }
    }
    
    public static function password($pass, $verify, $tell = false, $args = []) {
        if (stripos($pass, " ") !== false) {
            throw new \Exception\FormError("Your password should not contain spaces");            
        }
        if(strlen($pass)<6){
            throw new \Exception\FormError("Minimum password length is 6(six)");            
        }
        
        if ($tell) {
            return password_verify($pass,$verify);
        }elseif($pass!==$verify){
            throw new \Exception\FormError("Password does not match");
        } else {
            throw new \Exception\FormError("Password verify error");
        }
        return $pass;
    }
    
    public static function phone($number){
        $newnumber=trim($number);
        if(is_numeric($newnumber)&&strlen($newnumber)>8&&strlen($newnumber)<13){
            //  VERIFY MOBILE NUMBER              
            return $newnumber;
        }
        throw new \Exception\FormError("Phone Number is Not Valid!");
    }
    
    public static function name($name){
        if($i=filter_var($name, FILTER_SANITIZE_STRING)){
            return strtoupper(trim($i));
        }
        throw new \Exception\FormError("Invalid Name");
    }


    public static function cert($cert,$ca){
        
    }
}
