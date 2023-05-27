<?php 
    $ingredient = $_POST["ingredient"];

    $conn = require __DIR__ . '/db.php';

    $sql = "INSERT INTO `Ingredients`(`ingredient_name`) VALUES ('$ingredient')";

    if (mysqli_query($conn, $sql)) {
        echo "Ingredient added successfully.";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }

    mysqli_close($conn);
?>