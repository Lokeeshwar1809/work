<?php

include('config.php')

?>

<?php

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$address = $_POST['address'];

#password hashing
$pass = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO student (username, password, email, address) VALUES 
('$username','$pass', '$email', '$address')";

$result = mysqli_query($con, $sql);

if($result){
    echo "<script>alert('Your data saved successfully')</script>";
    echo "<script>location.href='login.html'</script>";
    exit;
}


