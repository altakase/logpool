<?php
require_once( dirname( __FILE__ ) . '/../lib/Auth.php' );
$def_user = 'servermaster';

lib\Database::Initialize();
function addUser($username){
    $password = lib\Auth::makeRandom(8);

    $user = \ORM::for_table('user')->create();
    $user->set('username',$username);
    $user->set('display_name', '管理者');
    $user->set('email', '');
    $user->set('notify_mail', 1);
    $user->set('password', lib\Auth::getPasswordHash($username, $password));
    $user->set_expr('created_at', 'NOW()');
    $user->set_expr('updated_at', 'NOW()');
    $user->save();
    return $password;
}

$password = "";
try{
    $user_number = \ORM::for_table('user')->count();
    if($user_number > 0) {
        print "[WARN] User already exists.\n";
    } else {
        $password = addUser($def_user);
        print "User account created.\n";
        print "Username: $def_user\n";
        print "Password: $password\n\n";
    }
} catch(Exception $e){
    print "Database Connection error.\n";
    print "Please import sql by following command.\n";
    print "-------------\n";
    print "mysql {dbname} < conf/initialize.sql\n";
    print "-------------\n";
    print $e->getTraceAsString()."\n";
}
