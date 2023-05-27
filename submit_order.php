<?php
    session_start();
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_SESSION['user_name']))
        {
            $_SESSION['drink_id'] = $_POST['drink_id'];
            $_SESSION['special_requests'] = $_POST['special_requests'];
        
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
        else{
            echo "<scrip>alert('Please provide your name for the order!');</script>";
            header('Location: order_name.php');
            exit();
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Submit Order</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    </head>
    <body>
    </body>
</html>