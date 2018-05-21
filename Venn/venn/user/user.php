<?php

namespace Venn\user;

/**
 * @deprecated since version 1 use database extension or create app specific classes in app folder
 */
class User implements UserInterface {

    private $details;

    public function __construct($details) {
        if ($details instanceof UserDetails) {
            $this->details = $details;
        } else {
            $this->details = new UserDetails($details);
        }
    }

    /**
     *  This takes in the user id and password then returns a @see User instance
     * @param string $identifier This may be the user's phone number or email address
     * @param string $pass This is the user's password
     * @return A \user\User object on success 
     */
    public static function login($identifier, $pass) {
        $db = new \database\Userbase();
        if (is_numeric($identifier)) {
            $identifier = \guard\Verify::phone($identifier);
        } else {
            $identifier = \guard\Verify::email($identifier);
        }
        try {
            $result = $db->login($identifier, $pass);
            if ($result !== false) {
                return new User($result);
            }
        } catch (\Exception\UserException $e) {
            return $e;
            //TODO :  set http status code based on the returned code
        }
    }

    public function register() {
        $joined = time();
        $verify_token = uniqid();
        $verify_end = time() + (20 * 60);
        $db = new \database\Userbase();
        try {
            if ($db->create_user($this->details->username, $this->details->email, $this->details->phone_number, $this->details->first_name, $this->details->last_name, $this->details->other_name, $this->details->password, $joined, $verify_token, $verify_end)) {
                return 'I am Passport 1.0';
            } else {
                echo 'Oops!';
            }
        } catch (\Exception\UserException $e) {
            return $e;
        }
    }

    public function describe() {
        
    }

    public function getFullName() {
        return ucwords(str_ireplace("  ", " ", implode(' ', [$this->details->first_name, $this->details->middle_name, $this->details->last_name, !empty($this->details->other_name) ? "(" . $this->details->other_name . ")" : ''])));
    }

    public function __get($name) {
        return $this->details->$$name; //    TODO :    Change this to filter the kind of data to expose!
    }

}
