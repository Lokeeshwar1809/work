<?php
session_start();

// Check if the username session variable is set
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    echo "Welcome, $username!";
    echo "How are you";
} else {
    // If the username session variable is not set, redirect the user to the login page
    header("Location: login.html");
    exit();
}
?>