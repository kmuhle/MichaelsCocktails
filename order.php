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
        table,tr,td,th{
            border: solid;
        }
    </style>
</head>
<body>
    <h1>Order A Drink:</h1>
    <div id="main_content">
        <div id="drink_list"></div>
    </div>

    <script>
        ///////////////////////////////////////////////////////////////////////

        const MAX_REQUESTS = 40;
        const TIME_WINDOW = 10000;

        let requestCount = 0;
        let lastRequestTimestamp = 0;

        async function makeRequest(url) {
            // Calculate the time elapsed since the last request
            const currentTime = Date.now();
            const timeElapsed = currentTime - lastRequestTimestamp;

            // Check if the time elapsed exceeds the time window
            if (timeElapsed >= TIME_WINDOW) {
                // Reset the request count and update the last request timestamp
                requestCount = 0;
                lastRequestTimestamp = currentTime;
            }

            // Check if the maximum number of requests has been reached
            if (requestCount >= MAX_REQUESTS) {
                // Calculate the time to wait before making the request
                const timeToWait = TIME_WINDOW - timeElapsed;

                // Wait for the specified time before making the request
                await new Promise(resolve => setTimeout(resolve, timeToWait));

                // Reset the request count and update the last request timestamp
                requestCount = 0;
                lastRequestTimestamp = Date.now();
            }

            // Increment the request count
            requestCount++;

            // Make the API request
            return fetch(url)
                .then(response => response.json())
                .catch(error => {
                    console.log("An error occurred during the API request:", error);
                    throw error;
                });
        }

        /////////////////////////////////////////////////////////////////////////

        var drink_ids = new Set();

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
        console.log("ingredient_list =", ingredient_list);

        var ingredient_search_promises = [];
        // For each ingredient, search for cocktails
        $.each(ingredient_list, function (i, ingredient) {
            console.log(ingredient + i);
            ingredient_search_promises.push(get_ingredient_drinks(ingredient));
        });

        get_drink_info();

        function get_ingredient_drinks(ingredient) {
            const apiUrl = "https://www.thecocktaildb.com/api/json/v1/1/filter.php?i=" + ingredient;
            return makeRequest(apiUrl)
                .then(data => {
                    if (data.drinks && data.drinks.length !== 0) {
                        console.log(1);
                        var drinks = data.drinks;
                        console.log(drinks);
                        // For each drink result, add to the drink_id set
                        $.each(drinks, function (i, drink) {
                            console.log(2);
                            drink_ids.add(drink.idDrink);
                        });
                    }
                })
                .catch(error => {
                    console.log("An error occurred when searching with the '" + ingredient + "'.");
                });
        }

        // async function get_drink_info() {
        //     await Promise.all(ingredient_search_promises);
        //     console.log("maybe?");
        //     console.log(drink_ids);

        //     drink_ids.forEach(function (id, i) {
        //         console.log(3);
        //         var drink_ingredients = [];
        //         var drink_row = "";

        //         const apiUrl = "proxy.php?url=https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=" + id;
        //         makeRequest(apiUrl)
        //             .then(data => {
        //                 var drink = data.drinks[0];
        //                 drink_row = "<th colspan='2'><h2>" + drink.strDrink + "</h2></th><tr><td><img src='" + drink.strDrinkThumb + "' alt='" + drink.strDrink + "'></td><td><h4>Ingredients:</h4>";
        //                 for (var i = 1; i < 16; i++) {
        //                     var strMeasure = "strMeasure" + i;
        //                     var strIngredient = "strIngredient" + i;
        //                     if (drink[strIngredient] !== null && drink[strMeasure] !== null) {
        //                         drink_row += drink[strIngredient] + ":&ensp;" + drink[strMeasure] + "<br>";
        //                         drink_ingredients.push(drink[strIngredient]);
        //                     }
        //                 }

        //                 drink_row += "<br> <h4>Instructions:</h4>" + drink.strInstructions + "</td></tr>";
        //                 console.log(drink_row);
        //                 console.log(drink_ingredients);
        //                 console.log(ingredient_list);
        //                 var correct_ingredients = drink_ingredients.every(function (element) {
        //                     return ingredient_list.includes(element);
        //                 });
        //                 console.log("correct ingredients: " + correct_ingredients);

        //                 if (correct_ingredients) {
        //                     console.log(5);
        //                     $("#drink_list").append("<table class='drink_table'>" + drink_row + "</table>");
        //                 }
        //             })
        //             .catch(error => {
        //                 console.log("Error getting drink with id " + id);
        //             });
        //     });
        // }

        async function get_drink_info() {
  await Promise.all(ingredient_search_promises);
  console.log("maybe?");
  console.log(drink_ids);

  const drinkIdsArray = Array.from(drink_ids);

  for (const id of drinkIdsArray) {
    console.log("Processing drink", id);

    var drink_ingredients = [];
    var drink_row = "";

    const apiUrl =
      "proxy.php?url=https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=" +
      id;

    try {
      const data = await makeRequest(apiUrl);
      var drink = data.drinks[0];
      drink_row =
        "<th colspan='2'><h2>" +
        drink.strDrink +
        "</h2></th><tr><td><img src='" +
        drink.strDrinkThumb +
        "' alt='" +
        drink.strDrink +
        "'></td><td><h4>Ingredients:</h4>";

      for (var i = 1; i < 16; i++) {
        var strMeasure = "strMeasure" + i;
        var strIngredient = "strIngredient" + i;
        if (drink[strIngredient] !== null && drink[strMeasure] !== null) {
          drink_row +=
            drink[strIngredient] + ":&ensp;" + drink[strMeasure] + "<br>";
          drink_ingredients.push(drink[strIngredient]);
        }
      }

      drink_row +=
        "<br> <h4>Instructions:</h4>" + drink.strInstructions;
      drink_row += `<br><form class='order_form' action='submit_order.php' method='post' enctype='multipart/form-data' target='_blank'><input type="hidden" name="drink_id" value='${drink.idDrink}'><label for="special_request">Special Instructions: </label><input type="text" name="special_request"><button type="submit" class='submit_order_button'>Order Drink</button></form></td></tr>`;
    //   console.log(drink_row);
    //   console.log(drink_ingredients);
    //   console.log(ingredient_list);
      var correct_ingredients = drink_ingredients.every(function (element) {
        return ingredient_list.includes(element);
      });
    //   console.log("correct ingredients: " + correct_ingredients);

      if (correct_ingredients) {
        // console.log(5);
        console.log(drink_row);
        $("#drink_list").append(
          "<table class='drink_table'>" + drink_row + "</table>"
        );
      }
    } catch (error) {
      console.log("Error getting drink with id " + id);
    }

    // Wait for a delay before processing the next drink
    await new Promise((resolve) => setTimeout(resolve, .0001)); // Adjust the delay as needed
  }
}
    </script>
</body>
</html>
