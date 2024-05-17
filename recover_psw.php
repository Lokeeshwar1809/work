<?php 
session_start();

?>

<?php

#usage of sanitize input function for input validation purpose
function sanitize_input($data) {
    $data = trim($data); // Remove whitespace from the beginning and end of the string
    $data = stripslashes($data); // Remove backslashes (\)
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $data;
}

    $email = sanitize_input($_POST['email']);

    if(isset($_POST["recover"])){
        include('config.php');
        
        #using mysqli parameterized statement
        $sql = "SELECT * FROM Student WHERE email=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if($row<=0){
            ?>
            <script>
                alert("<?php  echo "Sorry, no emails exists "?>");
            </script>
            <?php
        }   else{
            // generate token by binaryhexa 
            $token = bin2hex(random_bytes(50));

            //session_start ();
            $_SESSION['token'] = $token;
            $_SESSION['email'] = $email;

            require "Mail/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->Port=587;
            $mail->SMTPAuth=true;
            $mail->SMTPSecure='tls';

            // h-hotel account
            $mail->Username='lokeesh.dinesh@gmail.com';
            $mail->Password='lbmk emta cewm xlxm';

            // send by h-hotel email
            $mail->setFrom('lokeesh.dinesh@gmail.com', 'Password Reset');
            // get email from input
            $mail->addAddress($_POST["email"]);
            //$mail->addReplyTo('lamkaizhe16@gmail.com');

            // HTML body
            $mail->isHTML(true);
            $mail->Subject="Recover your password";
            $mail->Body="<b>Dear User</b>
            <h3>We received a request to reset your password.</h3>
            <p>Kindly click the below link to reset your password</p>
            http://localhost/user%20auth/reset_psw.html
            <br><br>
            <p>With regrads,</p>
            <b>Programming with Lam</b>";

            if(!$mail->send()){
                ?>
                    <script>
                        alert("<?php echo " Invalid Email "?>");
                    </script>
                <?php
            }else{
                ?>
                    <script>
                        window.location.replace("notification.html");
                    </script>
                <?php
            }
        }
    }


?>
