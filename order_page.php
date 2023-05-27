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


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>API Fun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <style>

        #order_list{
            width: 100%;
            display:flex;
            flex-wrap: wrap;
            align-items: stretch;
            justify-content: center;
            
        }
        .order_div{
            border: solid;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 2vh 2vh 2vh;
            margin: 2vh;
            width: 80%;
            /* flex-wrap: wrap; */
            /* height: 90vh; */
            overflow: auto;
        }

        .status_form{
            text-align:center;
        }
        .drink_image {
            width: 90%;
        }
        .order_details{
            display: flex;
            flex-direction: row;
            width: 100%;
            justify-content: space-between;         
            border-bottom: solid;
            padding: 2vh 0 2vh 0;
        }

        .order_drink_instructions{
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .order_detail_title{
            font-weight: bold;
            font-size: medium;
        }

        .drink_ingredients tr{
            border:solid;
        }

        .order_time, .user_name{
            display: flex;
            flex-direction: column;
            /* margin-right: 10vh; */
            margin-top: 1.5vh;
            font-size: 14px;
        }

        .drink_name{
            font-weight: bold;
            font-size: 3vh;
            /* text-align: center; */
            padding: 2vh;
        }

        .instructions_titles{
            padding: 2vh 2vh 1vh 2vh;
            font-size: 2.5vh;
        }

        .drink_instructions{
            width: 75%;
        }

        .status_button_div{
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .accept_button, .deny_button{
            border: none;
            border-radius: 5px;
            width: 10vh;
            height: 4vh;
            padding: 1vh;
            margin: 1vh;
        }

        .accept_button{
            background-color: green;
            color: white;
        }

        .deny_button{
            background-color: FireBrick;
            color: white;
        }

        @media screen and (min-width: 1000px) {
            .order_div {
                width: 29%; /* Set a different width on web */
            }

            .status_button_div{
                flex-direction: column;
                width: 25%;
            }

            /* .order_details{
                flex-direction: row;
            } */

        }
        
        

    </style>
</head>
<body>
    <h1 style='text-align:center;'>Incoming Orders:</h1>
    <div id="main_content">
        <div id="order_list">
        </div>
    </div>

    <script>

            var orders = <?php
                    $conn = require __DIR__ . '/db.php';

                    $sql = "SELECT * FROM `orders`";
                    $result = mysqli_query($conn, $sql);

                    $orders = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $orders[] = $row;
                    }
                    $json_orders = json_encode($orders);

                    echo $json_orders . ";";
                    ?>
            console.log(orders);

            $.each(orders, function(i, order){
                var order_div = `<div class='order_div'><div class='order_details'><div class='user_name'><div class='order_detail_title'>Order Name:</div>${order['user_name']}</div><div class='order_time'><div class='order_detail_title'>Order Time:</div>${format_time(order['time_ordered'])}</div><div class='status_button_div'><button class='accept_button' onclick='accept_status()'>Accept</button><button class='deny_button' onclick='deny_status()'>Deny</button></div></div><div class='order_drink_instructions'>`;


                fetch("https://www.thecocktaildb.com/api/json/v2/9973533/lookup.php?i=" + order['drink_id'])
                .then(response => response.json())
                .then(data => {
                    var drink = data.drinks[0];
                    order_div += `<div class='drink_name'>${drink.strDrink}</div><img class='drink_image' src='${drink.strDrinkThumb}' alt='${drink.strDrink}'><div class='instructions_titles'>Ingredients:<hr></div><table class='drink_ingredients'>`;

                    for (var i = 1; i < 16; i++) {
                        var strMeasure = "strMeasure" + i;
                        var strIngredient = "strIngredient" + i;
                        if (drink[strIngredient] !== null) {
                            order_div += "<tr class='ingredient_row'><td class='measure_cell'>"
                        //   if(drink[strMeasure] !== null){
                            order_div += `${drink[strMeasure]}`;
                        //   }
                        order_div += `</td><td class='ingredient_cell'>${drink[strIngredient]}</td></tr>`;
                        }
                    }
                    order_div += `</table><div class='instructions_titles'>Instructions:<hr></div><div class='drink_instructions'>${drink.strInstructions}</div>`;
                    order_div += `<div class='instructions_titles'>Special Requests: <hr></div><div class='special_requests'>${order['special_requests']}</div></div>`;
                    
                    order_div += `<div class='status_button_div'><button class='accept_button' onclick='accept_status()'>Accept</button><button class='deny_button' onclick='deny_status()'>Deny</button></div></div>`;

                    $("#order_list").append(order_div);
                    
                });


                
            });

        
            function format_time(order_time){
                var date_time = new Date(order_time);

                var formatted_date = date_time.toLocaleDateString(); 
                var formatted_time = date_time.toLocaleString([], {hour: 'numeric', minute: 'numeric', hour12: true});

                // var formatted_time = date_time.toLocaleString([], {month: '2-digit', day: '2-digit', hour: 'numeric', minute: 'numeric', hour12: true})

                
                
                return formatted_date + "<br>" + formatted_time;
                // return formatted_time;
            }
    </script>
</body>
</html>
