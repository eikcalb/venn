<?php

namespace user;

class UserDetails {

    public $first_name, $last_name, $middle_name, $other_name, $email, $birthday, $username, $account_number, $bank_name, $phone_number, $address;
    public $status, $profile_photo;
    private $password, $verify_password;

    public function __construct($data) {
        if (is_array($data)) {
            $this->parseArray($data);
        }
    }

    private function setPassword($name) {
        if (!isset($this->password) && !empty($name)) {
            $this->password = password_hash($name, PASSWORD_BCRYPT);
        } else {
            throw new \Exception\UserException("Oops, password must be provided and verified :D", \Exception\UserException::blank);
        }
    }

    public function getUser() {
        return new user\User($this);
    }

    public function __get($name) {
        if ($name === "password") {
            if (isset($this->password)) {
                return $this->password;
            } else {
                return FALSE;
            }
        }
    }

    private function parseArray($data) {
        foreach ($data as $a => $b) {
            if (empty($b)) {
                throw new \Exception\UserException("Sorry, but you forgot to give me your $a :(", \Exception\UserException::blank);
            }
            switch ($a) {
                case "username": $this->username = \guard\Verify::username($b);
                    break;
                case "password":
                    $this->setPassword(array_key_exists("verify_password", $data) ? \guard\Verify::password($b, $data['verify_password']) : null);
                    break;
                case "phone_number": // Possible bug here... there shouldnt be any space between words in array names
                    $this->phone_number = \guard\Verify::phone($b);
                    break;
                case "email": $this->email = \guard\Verify::email($b);
                    break;
                case "account_number": break;
                case "first_name": $this->first_name = \guard\Verify::name($b);
                    break;
                case "last_name": $this->last_name = \guard\Verify::name($b);
                    break;
                case "other_name": $this->other_name = \guard\Verify::name($b);
                    break;
                case "profile_photo": $this->profile_photo = $b;
            }
        }
    }

}
