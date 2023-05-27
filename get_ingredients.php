<?php
// Connect to your MySQL database
$conn = require __DIR__ . '/db.php';

$sql = "SELECT `ingredient_name` FROM `Ingredients`";
$result = mysqli_query($conn, $sql);

// Store the ingredient names in an array
$ingredients = array();
while ($row = mysqli_fetch_assoc($result)) {
  $ingredients[] = $row['ingredient_name'];
}

echo $ingredients;

// Convert the array to JSON format
$json_ingredients = json_encode($ingredients);

// Return the JSON response
header('Content-Type: application/json');
echo $json_ingredients;
?>
