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

    <link rel = 'stylesheet' href = './css/event_search.css'/>

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
      <div class='jumbotron'>
        <h2><center>Event Search</center></h2>
          <form action = "" method="POST" role = "form">
            <div class = "form-group">
            <?php
              $con = mysqli_connect($db_host, $db_user, $db_password, $db_database) or die("Connection Failed");

              $query_event = "SELECT DISTINCT EName FROM Event";
              $result_event = mysqli_query($con, $query_event);
              echo "<label for=\"countrysel\">Event Name: </label>";
              echo "<select class=\"form-control\" id=\"countrysel\" name=\"event\">";
              echo "<option value = 'empty'></option>";
              while($row = mysqli_fetch_array($result_event)) {
                echo "<option value = '" . $row['EName'] . "'>" . $row['EName'] . "</option>";
              }
              echo "</select> <br />";

              $query_city = "SELECT DISTINCT CityName FROM City";
              $result_city = mysqli_query($con, $query_city);
              echo "<label for=\"citysel\">City: </label>";
              echo "<select class=\"form-control\" id=\"citysel\" name=\"city\">";
              echo "<option value = 'empty'></option>";
              while($row = mysqli_fetch_array($result_city)) {
                echo "<option value = '" . $row['CityName'] . "'>" . $row['CityName'] . "</option>";
              }
              echo "</select> <br />";
              echo "<label for=\"edate\">Date: </label>";
              echo "<input type=\"date\" id = \"edate\" name=\"edate\"><br />";
             ?>
             </div>
             <div class="form-group">
              <div class = "col-md-2">
              <label for="cost">Cost: </label>
              </div>
              <input type="text" class="form-horizontal" id="cost" name="minimum" placeholder="Minimum"/> to
              <input type="text" class="form-horizontal" id="cost" name="maximum" placeholder="Maximum"/><br />
              </div>
             <b class = "text-center">Student Discount: </b>
              <select name="discount">
                <option value = "empty"></option>
                <option value = "1 OR ">Free or Discounted</option>
                <option value = "1 AND ">Discounted</option>
                <option value = "0 AND ">Not Discounted</option>
              </select><br />
              <br/>
             <b class="text-center">Category</b>
             <?php
                $con = mysqli_connect($db_host, $db_user, $db_password, $db_database) or die("Connection Failed");
                $query = "SELECT DISTINCT EventType FROM Event";
                $result = mysqli_query($con, $query);
                echo "<fieldset id=\"group1\">";
                while($row = mysqli_fetch_array($result)) {
                    echo "<input type = \"radio\" name=\"etype\" value = '" . $row['EventType'] . "'>" .  $row['EventType'] . "</input><br />";
                }
              ?>
              <br/>
              <b>Sort Review Scores</b>
              <select name="scoresort">
                <option value = 'ASC'>Ascending</option>
                <option value = 'DESC'>Descending</option>
              </select><br />
              <br/>
              <input type="submit" name="submit" value="Search"><br />
              <br/>
          </form>
          <?php
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
            $con = mysqli_connect($db_host, $db_user, $db_password, $db_database) or die("Connection Failed");
            if (isset($_POST['minimum']) && $_POST['minimum'] != ""
              && isset($_POST['maximum']) && $_POST['maximum'] != "") {
                $cost = "Event.Cost BETWEEN ". $_POST['minimum'] ." AND ". $_POST['maximum'] . " AND ";
            } else {
                $cost = "";
            }
            if (isset($_POST['etype']) && $_POST['etype'] != "") {
                $type = "Event.EventType = \"". $_POST['etype'] ."\" AND ";
            } else {
                $type = "";
            }
            if (isset($_POST['edate']) && $_POST['edate'] != "") {
                $date = "Event.Date = \"". $_POST['edate'] ."\" AND ";
            } else {
                $date = "";
            }

            if(isset($_POST['event'])
              && isset($_POST['city'])
              && isset($_POST['discount'])
              && isset($_POST['scoresort'])) {
                  if($_POST['event'] == "empty") {
                    $eve = "";
                  } else {
                    $eve = "Event.EName = \"". $_POST['event'] ."\" AND ";
                  }
                  if($_POST['city'] == "empty") {
                    $city = "";
                  } else {
                    $city = "Event.CityName = \"". $_POST['city'] ."\" AND ";
                  }
                  $dis = $_POST['discount'];
                  if($_POST['discount'] == "empty") {
                    $sdiscount = "";
                  } else if ($_POST['discount'] == "1 OR "){
                    $sdiscount = "(Event.StudentDiscount = $dis";
                    $cost = "Event.Cost BETWEEN 0 AND 0) AND ";
                  } else {
                    $sdiscount = "Event.StudentDiscount = $dis";
                  }
                  $sort = $_POST['scoresort'];
                  $sql = "SELECT DISTINCT Event.EName, Event.CityName, Event.EDate, Event.StartTime, Event.Cost, Event.EventType,
                          AVG(Score) AS AvgScore, Event.Address, Event.CountryName
                          FROM Event, Review RIGHT OUTER JOIN Reviewable ON Review.ReviewableID=Reviewable.ReviewableID
                          WHERE $eve $city $sdiscount $cost $type
                          Event.ReviewableID = Reviewable.ReviewableID
                          GROUP BY Event.EName, Event.CityName, Event.EDate, Event.StartTime, Event.Cost, Event.EventType
                          ORDER BY AvgScore $sort;";
                  $result = mysqli_query($con, $sql);
                  if(mysqli_num_rows($result) > 0) {
                      $_SESSION['location_search'] = $result;
                      //echo "<script>window.location.href='country_search_results.php'</script>";
                      echo "<table class= \"table table-striped\" border=\"1\">";
                      echo "<tr>";
                          echo "<th> Event Name </th><th> City </th><th> Date </th><th>Start Time</th><th> Cost </th><th>Category</th><th>Average Score</th>";
                      echo "</tr>";
                      while($val = mysqli_fetch_array($result)) {
                          echo "<tr>";
                          echo "<td><a href = \"event_listing.php?a=$val[0]&b=$val[7]&c=$val[1]&d=$val[8]&e=$val[2]&f=$val[3]\">" . $val[0] . "</td>";
                          echo "<td>" . $val[1] . "</td>";
                          echo "<td>" . $val[2] . "</td>";
                          echo "<td>" . $val[3] . "</td>";
                          echo "<td>" . $val[4] . "</td>";
                          echo "<td>" . $val[5] . "</td>";
                          echo "<td>" . $val[6] . "</td>";
                          echo "</tr>";
                      }
                      echo "</table>";
                  } else {
                      echo "No results found!";
                  }
              }
             ?>
      </div>
    </div>

  </body>
</html>
