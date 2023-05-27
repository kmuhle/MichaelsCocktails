<?php
session_start(); 


function contains_profanity($text) {
    $url = "https://www.purgomalum.com/service/containsprofanity?text=" . urlencode($text);
  
    $result = file_get_contents($url);
  
    return ($result == 'true');
  }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(contains_profanity($_POST['name'])){
        echo "<script>alert('Please do not include any profanity in your name.');</script>";
    }
    else{
        $_SESSION['user_name'] = $_POST['name'];

        header('Location: order.php');
        exit();
        
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Your Name</title>
</head>
<body>
    <form id="name_form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" maxlength="15" required>
        <button type="submit">Submit</button>
    </form>

</body>
</html>