<?php 

session_start();

$bartender_user;

if (isset($_SESSION["bartender_user"])) {
    $bartender_user = $_SESSION["bartender_user"];
}
else{
    // header('Location: bartender_login.php');
}
?>






<html>
  <head>
    <meta charset="UTF-8" />
    <title>Ingredients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/c63cecf2bb.js" crossorigin="anonymous"></script>

    
        <!-- 
        <style>
        @media only screen and (max-width: 600px) {
            table, tr, th{
                width: 100%;
            } 
        </style>
        -->

  </head>
  <body>
    <h1>Ingredients</h1>
    <input type="text" id="ingredient_input"></input>
    <button type="submit" id="ingredient_submit">Submit</button>
    <div id="ingredients_list">
        <?php
            $conn = require __DIR__ . '/db.php';
        
            $sql = "SELECT `ingredient_name` FROM `Ingredients`";
            $result = mysqli_query($conn, $sql);
            // var_dump($result);
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='ingredient'>{$row['ingredient_name']}<i class='fa-solid fa-xmark delete_ingredient' onclick='delete_ingredient(event)'></i></div>";
                }
            }
            else{
                echo "Add ingredients!";
            }
        ?>
    </div>
    <script>

        // add ingredient on keyup
        $("#ingredient_input").on("keyup", function(e) {
            if (e.keyCode === 13) {
                process_ingredient();
            }
        });

        // add ingredient on submit
        $("#ingredient_submit").on("click", function() {
            process_ingredient();
        });

        
        // Format ingredient, add to list, add to database
        function process_ingredient(){
            var ingredient = $("#ingredient_input").val();
            ingredient = ingredient.trim();
            ingredient = ingredient.charAt(0).toUpperCase() + ingredient.slice(1).toLowerCase();
            console.log(ingredient);
            if(ingredient.length > 0 && $(".ingredient").filter(function() { return $(this).text() === ingredient;}).length == 0){
                console.log("im in");

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log("Maybe?");
                    var ingredient_div = `<div class='ingredient'>${ingredient}<i class='fa-solid fa-xmark delete_ingredient' onclick='delete_ingredient(event)'></i></div>`;
                    $("#ingredients_list").append(ingredient_div);
                }
                };
                xhttp.open("POST", "add_ingredients.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("ingredient=" + ingredient);
            }
            $("#ingredient_input").val("");
        }
    </script>

  </body>
</html>