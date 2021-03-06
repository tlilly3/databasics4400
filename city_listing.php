<?php
include 'database_con.php';
session_start();
?>
<html>
  <head>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
          crossorigin="anonymous"/>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css"
          rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1"
          crossorigin="anonymous">

    <link rel = 'stylesheet' href = './css/city_listing.css'/>

    <meta charset ='utf-8'/>
    <title>GTtravel</title>
  </head>
  <body>
    <nav class = 'navbar navbar-light navbar-fixed-top'>
        <div id = "spy-scroll-id" class = 'container'>
          <ul class="nav navbar-nav navbar-right">
            <li class = 'active'><a href="home.php"><i class="fa fa-home"></i>Home</a></li>
            <li><a href = "login.php"><i class ="fa fa-user"></i>Logout</a></li>
          </ul>
          <a href = '#' class = "pull-left navbar-left"><img id = "logo" src = "./images/LogoMakr.png"></a>
          <ul class="nav navbar-nav navbar-left">
            <li><a href = "country_search.php"><i class="fa fa-globe"></i> Country</a></li>
            <li><a href = "city_search.php"><i class="fa fa-building-o"></i> City</a></li>
            <li><a href = "location_search.php"><i class="fa fa-map-marker"></i> Location</a></li>
            <li><a href = "event_search.php"><i class="fa fa-calendar"></i> Event</a></li>
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class = "fa fa-pencil"></i> Reviews <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="review_city.php">Review a City</a></li>
                <li><a href="review_event.php">Review an Event</a></li>
                <li><a href="review_location.php">Review a Location</a></li>
                <li><a href="see_reviews.php">See All Reviews</a></li>
              </ul>
            </li>
          </ul>
        </div>
    </nav>
    <div class="container text-center">
      <div class = "jumbotron">
        <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $con = mysqli_connect($db_host, $db_user, $db_password, $db_database) or die("Connection Failed");
        $city = $_GET['a'];
        $country = $_GET['b'];
        echo "<h2> $city </h2>";
        $query = "SELECT City.Population, City.Latitude, City.Longitude, AVG(Score) as AvgScore, City.ReviewableID
                  FROM City, Review RIGHT OUTER JOIN Reviewable ON Review.ReviewableID=Reviewable.ReviewableID
                  WHERE City.CityName = \"$city\" AND City.CountryName = \"$country\"
                  AND City.ReviewableID = Reviewable.ReviewableID;";
        $result = mysqli_query($con, $query);
        $result_array = mysqli_fetch_array($result);
        $avg = $result_array['AvgScore'];
        $revid = $result_array['ReviewableID'];
        if($avg == NULL) {
          $avgs = "No Reviews Yet";
        } else {
          $avgs = $avg;
        }
        $population = $result_array['Population'];

        echo "<h4>Country: $country</h4>";
        echo "<h4>Population: $population</h4>";
        $query_languages = "SELECT LanguageName
                            FROM CityLanguage
                            WHERE CityLanguage.CityName = \"$city\" AND CityLanguage.CountryName = \"$country\";";
        $result_languages = mysqli_query($con, $query_languages);
        echo "<h4>Languages: ";
        while($row = mysqli_fetch_array($result_languages)) {
            $lang = $row['LanguageName'];
            echo "$lang ";
        }
        echo "</h4>";
        echo "<h4> Average Review Score: $avgs</h4>";
        $lat = $result_array["Latitude"];
        $long = $result_array["Longitude"];
        echo "<h4>GPS: $lat, $long</h4>";

        $query_locations = "SELECT DISTINCT Location.LName, Location.LocationType, Location.Cost, AVG(Score) AS AvgScore
                            FROM Location, Review RIGHT OUTER JOIN Reviewable ON Review.ReviewableID=Reviewable.ReviewableID
                            WHERE Location.CityName = \"$city\"
                            AND Location.ReviewableID = Reviewable.ReviewableID
                            GROUP BY Location.LName, Location.LocationType, Location.Cost
                            ORDER BY AvgScore DESC;";

        $result_locations = mysqli_query($con, $query_locations);
        if(mysqli_num_rows($result_locations) > -1) {
          echo "<div class = \"container text-center\" id = \"locationsWithinTable\">";
          echo "<h3>Locations Within: </h3>";
          echo "<table class= \"table table-striped\" border=\"1\">";
          echo "<tr>";
          echo "<th>Name</th><th>Category</th><th>Cost</th><th>Score</th>";
          echo "</tr>";
          while($val = mysqli_fetch_array($result_locations)) {
              echo "<tr>";
              echo "<td>" . $val[0] . "</td>";
              echo "<td>" . $val[1] . "</td>";
              echo "<td>" . $val[2] . "</td>";
              echo "<td>" . $val[3] . "</td>";
              echo "</tr>";
          }
          echo "</div>";
          echo "<br />";
        } else {
            echo "<h3>Locations Within: </h3>";
            echo "No Locations Found!";
        }
        $query_reviews = "SELECT DISTINCT Review.Username, Review.RDate, Review.Score, Review.Description
                          FROM City, Review, Reviewable
                          WHERE City.CityName = \"$city\" AND City.CountryName = \"$country\"
                          AND City.ReviewableID = Reviewable.ReviewableID AND Review.ReviewableID = Reviewable.ReviewableID
                          ORDER BY Review.RDate DESC;";
        $result_reviews = mysqli_query($con, $query_reviews);
        if(mysqli_num_rows($result_reviews) > -1) {
          echo "<table class= \"table table-striped\" border=\"1\">";
          echo "<tr>";
          echo "<th>Username</th><th>Date</th><th>Score</th><th>Description</th>";
          echo "</tr>";
          while($val = mysqli_fetch_array($result_reviews)) {
              echo "<tr>";
              echo "<td>" . $val[0] . "</td>";
              echo "<td>" . $val[1] . "</td>";
              echo "<td>" . $val[2] . "</td>";
              echo "<td>" . $val[3] . "</td>";
              echo "</tr>";
          }
          echo "<br/>";
          echo "<h3>Reviews:</h3>";
          echo "<br />";
        }
        ?>
      </div>
    </div>
    <div class = "btn-group">
      <?php
        echo "<a class=\"btn btn-default\" href=\"write_review.php?a=$revid\">Review City</a>";
        echo "<a class=\"btn btn-default\" href=\"city_search.php\">Go Back</a>";
        ?>
    </div>
  </body>
</html>
