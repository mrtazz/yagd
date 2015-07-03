<?php
include_once("../config.php");
function echoActiveClassIfRequestMatches($requestUri)
{
    if ($_SERVER['REQUEST_URI'] == $requestUri) {
        return "class='active'";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboards</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

    <link rel="stylesheet" href="/css/yagd.css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><?php echo $CONFIG["title"] ?> </a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li <?=echoActiveClassIfRequestMatches("/")?>><a href="/">Home</a></li>
            <?php foreach ($CONFIG['navitems'] as $name => $url) {
                echo "<li " . echoActiveClassIfRequestMatches($url) ."><a href='{$url}'>{$name}</a></li>";
            }
            ?>
          </ul>
            <div class='nav navbar-nav navbar-left'>
              <?php include_once('time_select.php'); ?>
            </div>
            <?php
                if (!empty($selectbox)) {
                    echo "<div class='nav navbar-nav navbar-right'>";
                    echo $selectbox;
                    echo "</div>";
                }
            ?>
        </div><!--/.nav-collapse -->
      </div>
    </div>
  <div class="container">
