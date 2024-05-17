<?php session_start() ;
include('config.php');
?>



<?php
#usage of sanitize input function for input validation purpose
function sanitize_input($data) {
    $data = trim($data); // Remove whitespace from the beginning and end of the string
    $data = stripslashes($data); // Remove backslashes (\)
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $data;
}

    if(isset($_POST["reset"])){
        include('config.php');
        
        $psw = sanitize_input($_POST["password"]);

        $token = $_SESSION['token'];
        $Email = $_SESSION['email'];

       #using mysqli parameterized statement
        $sql = "SELECT * FROM Student WHERE email=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $Email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

  	   
        if($row > 0 ){
            #password converts into hash function
            $hash = password_hash( $psw , PASSWORD_DEFAULT );
            $new_pass = $hash;
            #using mysqli parameterized statement
            $sql1 = "UPDATE Student SET password=? WHERE email=?";
            $stmt1 = mysqli_prepare($con, $sql1);
            mysqli_stmt_bind_param($stmt1, "ss", $new_pass, $Email);
            mysqli_stmt_execute($stmt1);
            
            ?>
            <script>
                window.location.replace("login.html");
                alert("<?php echo "your password has been succesful reset"?>");
            </script>
            <?php
        }else{
            ?>
            <script>
                alert("<?php echo "Please try again"?>");
            </script>
            <?php
        }
    }

?>





<?php
/*

    if(isset($_POST["reset"])){
        include('config.php');
        
        $psw = $_POST["password"];

        $token = $_SESSION['token'];
        $Email = $_SESSION['email'];

        $hash = password_hash( $psw , PASSWORD_DEFAULT );

        $sql = mysqli_query($con, "SELECT * FROM Student WHERE email='$Email'");
        $query = mysqli_num_rows($sql);
  	    $fetch = mysqli_fetch_assoc($sql);

        if($Email){
            $new_pass = $hash;
            mysqli_query($con, "UPDATE Student SET password='$new_pass' WHERE email='$Email'");
            ?>
            <script>
                window.location.replace("login.html");
                alert("<?php echo "your password has been succesful reset"?>");
            </script>
            <?php
        }else{
            ?>
            <script>
                alert("<?php echo "Please try again"?>");
            </script>
            <?php
        }
    }
*/
?>

