<?php
require_once 'stripe-php/init.php';
//require_once 'session.php';

// Include the session start and config file if necessary
require_once 'config.php';
session_start();
$order = isset($_SESSION["order"]) ? $_SESSION["order"] : null;

// Stripe API secret key
$stripe_secret_key = "sk_test_51PECXpCyYr5RSYWyMNUYLyrKDXvVgRxPK3fnq9U3DdhYwHZHxh34tKyuCAURVl3KApEi9aki59Sl6gTXXJ0pVoe400jOryQLUR";


// Check if the user is logged in and retrieve the username from the session
if (isset($_SESSION['username'])) {
    $loggedInUsername = $_SESSION['username'];
} else {
    // Handle the case where the user is not logged in
    // You might redirect them to a login page or display an error message
    exit("User not logged in.");
}

// Function to establish a connection to the MySQL database
function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password ="";
    $dbname = "user";

    // Create connection
    $con = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $con;
}

// Function to insert order data into the database
function insertOrderIntoDatabase($con, $order) {
    // Prepare SQL statement
    $sql = "INSERT INTO orders (pizza, size, topping, total) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "sssi", $pizza, $size, $toppings, $total);

    // Set parameters and execute
    $pizza = implode(", ", $order["pizza"]);
    $size = $order["size"];
    $toppings = !empty($order["toppings"]) ? implode(", ", $order["toppings"]) : "No toppings selected";
    $total = $order["total"];
    mysqli_stmt_execute($stmt);

    // Check for errors
    if (mysqli_stmt_error($stmt)) {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Function to fetch the address of the logged-in user
function fetchUserAddress($conn, $username) {
    $sql = "SELECT address FROM student WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $address);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $address;
}

// Function to fetch data from both tables using an inner join and save it into user_order table for the logged-in user
function saveDataToUserOrder($con, $username, $order) {
    // Fetch user's address
    $address = fetchUserAddress($con, $username);

    // Prepare SQL statement
    $sql = "INSERT INTO user_order (name, address, pizza, size, topping, total) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssds", $username, $address, $pizza, $order['size'], $order['toppings'], $order['total']);

    foreach ($order['pizza'] as $pizzaName) {
        $pizza = $pizzaName;
        // Execute query
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error: " . mysqli_error($con);
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);
}




// Check if the form is submitted for Stripe payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["place_order"])) {
    // Establish connection to the database
    $con = connectToDatabase();

    // Insert order into the database
    if ($order) {
        insertOrderIntoDatabase($con, $order);
        saveDataToUserOrder($con, $loggedInUsername, $order);
    }

    // Close connection
    mysqli_close($con);
}

// Check if the form is submitted for Stripe payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["place_order"])) {
    \Stripe\Stripe::setApiKey($stripe_secret_key);
$total = $order["total"];
$pizzas = $order["pizza"];

// Create an array to store line items
$line_items = [];

// Loop through each pizza to create line items
foreach ($pizzas as $pizza) {
    $line_items[] = [
        "price_data" => [
            "currency" => "MYR",
            "unit_amount" => $total * 100, // Amount should be in cents
            "product_data" => [
                "name" => $pizza  // Use the pizza name directly
            ]
        ],
        "quantity" => 1,
    ];
}

// Create a checkout session
$checkout_session = \Stripe\Checkout\Session::create([
    "payment_method_types" => ["card"],
    "line_items" => $line_items, // Pass the array of line items
    "mode" => "payment",
    "success_url" => "http://localhost/User_Auth/success.php",
    "cancel_url" => "http://localhost/User_Auth/cart.php",
    "locale" => "auto"
]);


    // Redirect to the checkout session URL
    header("Location: " . $checkout_session->url);
    exit();
}

// Function to remove an item from the cart
function removeFromCart($itemIndex) {
    if ($itemIndex !== null && isset($_SESSION["order"]["pizza"][$itemIndex])) {
        unset($_SESSION["order"]["pizza"][$itemIndex]);
        // Reindex the array
        $_SESSION["order"]["pizza"] = array_values($_SESSION["order"]["pizza"]);
    }
}

// Check if a delete request is made
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $itemIndex = $_POST["delete"];
    removeFromCart($itemIndex);
    // Redirect back to the cart page to refresh the view
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <style>
         table {
        border-collapse: collapse;
        width: 100%;
        }
        th, td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }
        th {
            
            background-color: #04AA6D;
            color: white;
        }
        td {
            
            background-color:rgb(173, 229, 175, 0.5);
            color: black;
        }
        body {
        background-image: url('images/wallpaper2.jpg');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: 100% 100%;
        }
        #button{
        background-color:rgb(173, 229, 175, 0.5);
        width: 300px;
        height: 30px;
        font-weight: bold;
        border-radius: 6px;
      }
      .design{
        margin-top: 30px;
        margin-left: 450px;
      }
    </style>
</head>
<body>
    <div style="display: inline-block; margin-bottom: 30px;">
        <h1 style="color: white; display: inline;">My Cart</h1>
        <button onclick="window.location.href='logout.php';" style="display: inline; background-color: rgb(173, 229, 175, 0.5); width: 100px; height: 30px; margin-left: 1280px;">Log Out</button>
    </div>
    
    <?php if ($order): ?>
        <table>
            <thead>
                <tr>
                    <th>Pizza</th>
                    <th>Size</th>
                    <th>Toppings</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($order["pizza"]); $i++): ?>
                    <tr>
                        <td><?php echo $order["pizza"][$i]; ?></td>
                        <td><?php echo $order["size"]; ?></td>
                        <td><?php echo !empty($order["toppings"]) ? implode(", ", $order["toppings"]) : "No toppings selected"; ?></td>
                        <td>$<?php echo $order["total"]; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="delete" value="<?php echo $i; ?>">
                                <button type="submit"  id = "button" style="width:80px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <!-- Form for placing the order and redirecting to Stripe payment -->
        <form method="post" action="">
            <div class = "design">
            <input type="submit" name="place_order" value="Place Order" id = "button">
            <input type="button" value="Continue Shopping" onclick="window.location.href='order.html';"  id = "button">
            </div>
        </form>
    <?php else: ?>
        <p>No items in cart</p>
    <?php endif; ?>
</body>
</html>
