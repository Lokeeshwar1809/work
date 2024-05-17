<?php
session_start();
include('config.php');
require_once 'session.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize if necessary
    $pizza = isset($_POST["pizza"]) ? $_POST["pizza"] : [];
    $size = isset($_POST["size"]) ? $_POST["size"] : "";
    $toppings = isset($_POST["toppings"]) ? $_POST["toppings"] : [];
    $total = isset($_POST["total"]) ? $_POST["total"] : 0;

    // Store data in session
    $_SESSION["order"] = [
        "pizza" => (array)$pizza, // Ensure pizza is stored as an array
        "size" => $size,
        "toppings" => (array)$toppings, // Ensure toppings is stored as an array
        "total" => $total
    ];

    echo "success"; // Response for JavaScript
} else {
    echo "failed"; // Response for JavaScript
}

mysqli_close($con);
?>
