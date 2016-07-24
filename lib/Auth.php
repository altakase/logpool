<?php
namespace lib;
require_once( dirname( __FILE__ ) . '/Database.php' );
class Auth
{
    public static function getPasswordHash($username, $password)
    {
        return hash('sha512', $username.$password.Constants::PASSWORD_HASH_SUFFIX);
    }

    public static function makeRandom($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function validate($username, $password) {
        $user = \ORM::for_table('user')
            ->select('user.password')
            ->where_equal('username', $username)
            ->find_one();
        if($user) {
            return self::getPasswordHash($username, $password) === $user->password;
        }
        return false;
    }

    /**
     * @return int user_id or null
     */
    public static function getUserId(){
        if(!empty($_SESSION['username'])) {
            $user = \ORM::for_table('user')
                ->where_equal('username', $_SESSION['username'])
                ->find_one();
            if($user){
                return $user->user_id;
            }
        }
        return null;
    }
}