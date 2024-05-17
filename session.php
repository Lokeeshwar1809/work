<?php

session_start();


if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $_SESSION['expire_time'])) {
    
    session_unset();
    session_destroy();
    
    header("Location: logout.php");
    exit;
}


$_SESSION['last_activity'] = time();


$_SESSION['expire_time'] = 300; 

ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

session_set_cookie_params([
    'lifetime' => 300,
    'domain' => 'localhost',
    'path' => '/',
    'secure' => true,
    'httponly' => true
]);

session_start();

$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (!isset($_SESSION['last_regeneration']) || $_SESSION['ip_address'] !== $ip_address || $_SESSION['user_agent'] !== $user_agent) {
    
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
    $_SESSION['ip_address'] = $ip_address;
    $_SESSION['user_agent'] = $user_agent;
} else {
    
    $interval = 60 * 5;
    if (time() - $_SESSION['last_regeneration'] >= $interval) {
        
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}
?>
