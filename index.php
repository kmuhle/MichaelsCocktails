<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>API Fun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <style>
      body {
        background-color: #252525;
        color: #f8f8f8;
        font-family: Arial, sans-serif;
        text-align: center;
        margin: 0;
      }
      h1, h2, h3 {
        color: #f8f8f8;
      }
      a {
        color: #4ddbff;
        text-decoration: none;
      }
      #filter_form{
        margin: 30px auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 60%;
      }
      label {
        font-size: 1.5rem;
        margin-bottom: 1rem;
      }
      input[type="text"] {
        padding: 0.5rem 1rem;
        margin: 1rem 0;
        font-size: 1.1rem;
        border-radius: 5px;
        border: none;
        width: 100%;
        background-color: #333333;
        color: #f8f8f8;
      }
      input[type="text"]:focus {
        outline: none;
        border-color: #4ddbff;
        box-shadow: 0 0 0 0.2rem rgba(77, 219, 255, 0.25);
      }
      button[type="submit"] {
        padding: 0.5rem 1rem;
        margin: 1rem 0;
        font-size: 1.1rem;
        border-radius: 5px;
        background-color: #4ddbff;
        color: #1a0577;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
      }
      button[type="submit"]:hover {
        background-color: #32c8ff;
      }
      #drink_list {
        width: 90%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        padding-top: 2rem;
        padding-bottom: 2rem;
      }
      table {
        border: 1px solid #ddd;
        width: 60%;
        margin: 1rem auto;
        box-shadow: 0px 0px 15px #ccc;
        border-radius: 10px;
        padding: 1rem;
      }
      th, td {
        border-radius: 5px;
        border-collapse: collapse;
        text-align: left;
        padding: 1rem;
      }
      th {
        text-align: center;
        background-color: #8727bf;
        color: #f8f8f8;
      }
      td {
        font-size: 1.1rem;
        width: 50%;
      }
      img{
        width:100%;
        margin: 2rem auto;
        display: block;
        border-radius: 5px;
        box-shadow: 0px 0px 15px #ccc;
      }
      #hw_answers{
        margin: auto;
        width: 75%;
      }
      @media only screen and (max-width: 600px) {
        table, tr, th{
            width: 100%;
        }

        td {
            display: block;
            width: 100%;
            text-align: left;
        }
        img{
            width: 80%;
        }

      }
    </style>
  </head>
  <body>
    <h1>API Fun</h1>
    <div id="hw_answers">The API I am using is <a href="https://www.thecocktaildb.com/api.php" target="_blank">The Cocktail DataBase</a>, which I found through the <a href="https://mixedanalytics.com/blog/list-actually-free-open-no-auth-needed-apis/" target="_blank">Mixed Analytics blog on free APIs</a>. This API can be used to search for specific cocktail recipes, get random recipes, and even filter recipes based on various parameters like ingredients, alcoholic strength, glassware, and more. Two applications where the Cocktail Database API would be helpful is for a website to search for a cocktail based on the ingredients in your home (similar to what I'm doing on this page) and maybe even an application for a bar so that bartenders can look up a drink recipe by name if a customer asks for a drink they haven't heard of. <br> <br> I used the Cocktail database in two different ways on this page. Firstly, I requested data using AJAX to get the drinks that included the user's specified ingredient. I used "filter.php?i=" plus the ingredient at the end of the request URL to filter the data with the ingredient. The second way I used the database was to get the cocktail recipes and images for the previous steps resulting cocktails. For this request I used Fetch to get the data, and used "lookup.php?i=" plus the drink id (that was gotten in the previous request). From there I had the data for the drinks and was able to access all of the necessary information to be able to display the recipes. </div>
    <form id="filter_form">
      <label for="ingredient_search">
        <h2>Find drinks by ingredient:</h2>
      </label>
      <input type="text" id="ingredient_search" value="Gin"></input>
      <button type="submit" id="search_button">search</button>
    </form>
    <div id="drink_list"></div>
    <script>
      $(document).ready(function() {
          $("#filter_form").submit(function(event) {
              event.preventDefault();
      
              var ingredient = $("#ingredient_search").val();
              var drink_ids = [];
      
              var request = new XMLHttpRequest();
      
              request.open("GET", "https://www.thecocktaildb.com/api/json/v1/1/filter.php?i=" + ingredient);
      
              request.onreadystatechange = function() {
                  if(request.readyState == 4 && request.status == 200){
                      var theDrinks = request.responseText;
                      var returnHTML = "<br>";
                      if(theDrinks == ""){
                          $("#drink_list").html("<h2>No drinks were found that use '" + ingredient + "'</h2>");
                      }
                      else{
                          var drinks = JSON.parse(theDrinks).drinks;
                          $.each(drinks, function(i, drink){
                              drink_ids.push(drink.idDrink);
                          });
                          get_recipe(drink_ids);
                      }
                      
      
                  }
                  else if (request.readyState == 4 && request.status != 200) {
                      $("#drink_list").append("Unable to find any drinks :/");
                  }
                  else if (request.readyState == 3) {
                      $("#drink_list").append("Looking for drinks...");
                  }
              };
      
              request.send();
          });
      });
      
      function get_recipe(drink_ids){
          $("#drink_list").empty();
          var drink_row = "";
              
          $.each(drink_ids, function(i, id){
      
              fetch("https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i=" + id)
                  .then(response => response.json())
                  .then(data => {
                  var drink = data.drinks[0];
                  drink_row = "<th colspan='2'><h2>" + drink.strDrink + "</h2></th><tr><td><img src='" + drink.strDrinkThumb + "' alt='" + drink.strDrink + "'></td><td><h4>Ingredients:</h4>";
                      
                  for(i = 1; i < 16; i++){
                      var strMeasure = "strMeasure" + i;
                      var strIngredient = "strIngredient" + i;
                      if(drink[strIngredient] !== null && drink[strMeasure] !== null){
                          drink_row += drink[strIngredient] + ":&ensp;" + drink[strMeasure] + "<br>";
                      }
                  }
                  drink_row += "<br> <h4>Instructions:</h4>" + drink.strInstructions + "</td></tr>";
                  $("#drink_list").append("<table>" + drink_row + "</table>");
              });
          });
      }
    </script>
  </body>
</html>