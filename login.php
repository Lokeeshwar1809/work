<?php
// Start up your PHP Session
session_start();

include('config.php');
require_once 'session.php';
?>


<?php

#usage of sanitize input function for input validation purpose
function sanitize_input($data) {
    $data = trim($data); // Remove whitespace from the beginning and end of the string
    $data = stripslashes($data); // Remove backslashes (\)
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $data;
}


$username = sanitize_input($_POST['username']);
$password = sanitize_input($_POST['password']);

$sql = "SELECT * FROM Student WHERE username= ?";

    #using mysqli parameterized statement
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if($row){

    #password hash verification
    if(password_verify($password, $row['password'])){

        $_SESSION["Login"] = "YES";

        $_SESSION['username'] = $username;
        header("Location: order.html");
    }
    else{
        $_SESSION["Login"] = "NO";

       echo "<script>alert('Incorect username or password')</script>";
       echo "<script>location.href='login.html'</script>";
    }
}
else{
    $_SESSION["Login"] = "NO";

    echo "<script>alert('You are not correctly log in')</script>";
    echo "<script>location.href='login.html'</script>";
}
mysqli_stmt_close($stmt);


?>




















<?php
// Start up your PHP Session
session_start();
/*
include('config.php');
?>


<?php

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM Student WHERE username='$username' and password='$password'";

$result = mysqli_query($con, $sql);
$rows = mysqli_fetch_array($result);
#$user_name = $rows['username'];
#$user_pass = $rows['password'];



$count = mysqli_num_rows($result);


if ($count == 1) {

    $_SESSION["Login"] = "YES";

    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
  

    header("Location: hello.php");

} else {

    $_SESSION["Login"] = "NO";

    echo "<script>alert('You are not correctly log in')</script>";
    echo "<script>location.href='login.html'</script>";
}
*/
?>
