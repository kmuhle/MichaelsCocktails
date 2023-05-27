<?php
    session_start();
    
     echo "hello\n";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "maybe\n";
        if(isset($_SESSION['user_name']))
        {
            echo "possibly\n";
            $_SESSION['drink_id'] = $_POST['drink_id'];
            $_SESSION['special_requests'] = $_POST['special_requests'];

            if($_SESSION['special_requests'] == ''){
                $_SESSION['special_requests'] = "None";
            }
        
            // header('Location: ' . $_SERVER['PHP_SELF']);

            $conn = require __DIR__ . '/db.php';

            $sql = "INSERT INTO `orders`(`user_name`, `drink_id`, `special_requests`, `time_ordered`) VALUES ('" . $_SESSION['user_name'] . "', '" . $_SESSION['drink_id'] . "', '" . $_SESSION['special_requests'] . "', NOW())";
            echo $sql;

            if (mysqli_query($conn, $sql)) {
                echo "Order Submitted Successfully.";
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }



        }
        else{
            echo "perhaps\n";
            echo "<scrip>alert('Please provide your name for the order!');</script>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>Submit Order</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    </head>
    <body>
    </body>
</html>