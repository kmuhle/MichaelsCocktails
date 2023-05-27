<?php
session_start();

$user_name;

if (isset($_SESSION["user_name"])) {
    $user_name = $_SESSION["user_name"];
} else {
    header('Location: order_name.php');
    exit; // Added an exit statement after the redirect
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>API Fun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <style>
        .drink_div{
            border: solid;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .order_form{
            text-align:center;
        }
        .drink_image {
            width: 90%;
        }
    </style>
</head>
<body>
    <h1>Order A Drink:</h1>
    <div id="main_content">
        <div id="drink_list"></div>
    </div>

    <script>
        // Get ingredient list from PHP
        var ingredient_list = <?php
            // Connect to your MySQL database
            $conn = require __DIR__ . '/db.php';

            $sql = "SELECT `ingredient_name` FROM `Ingredients`";
            $result = mysqli_query($conn, $sql);

            $ingredients = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $ingredients[] = $row['ingredient_name'];
            }

            $json_ingredients = json_encode($ingredients);

            echo $json_ingredients . ";";

            
            ?>
        console.log("ingredient_list = ", ingredient_list);

      
      fetch("https://www.thecocktaildb.com/api/json/v2/9973533/search.php?s=")
          .then(response => response.json())
          .then(data => {
            if (data.drinks && data.drinks.length !== 0) {
                console.log(1);
                var drinks = data.drinks;
                var drink_row = "";
                console.log(drinks);
                // For each drink result, add to the drink_id set
                $.each(drinks, function (i, drink) {
                    var drink_ingredients = [];
                    drink_row = `<div class='drink_name'>${drink.strDrink}</div><img class='drink_image' src='${drink.strDrinkThumb}' alt='${drink.strDrink}'><div class='drink_ingredients_title'>Ingredients:</div><div class='drink_ingredients'>`;

                    for (var i = 1; i < 16; i++) {
                        // var strMeasure = "strMeasure" + i;
                        var strIngredient = "strIngredient" + i;
                        if (drink[strIngredient] !== null) {
                        //   if(drink[strMeasure] !== null){
                        //     drink_row += drink[strMeasure] + "&ensp;";
                        //   }
                        drink_row += drink[strIngredient] + "<br>";
                        drink_ingredients.push(drink[strIngredient]);
                        }
                    }

                    drink_row += `</div><form class='order_form' action='submit_order.php' method='post' enctype='multipart/form-data'><input type="hidden" name="drink_id" value='${drink.idDrink}'><label for="special_requests">Special Instructions:</label><input type="text" name="special_requests"><br><button type="submit" class='submit_order_button'>Order Drink</button></form>`;
                    var correct_ingredients = drink_ingredients.every(function (element) {
                        return ingredient_list.includes(element);
                    });

                    if (correct_ingredients) {
                        console.log(drink_row);
                        $("#drink_list").append("<div class='drink_div'>" + drink_row + "</div>");
                    }
                });
            }
        });
    </script>
</body>
</html>
