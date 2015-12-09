<?php

namespace Yagd;

class Page
{

    protected $config = [];
    protected $defaultTimes = [
        '1hour', '4hours', '12hours',
        '1day', '2days',
        '1week',
        '1month', '3months', '6months',
        '1year'
    ];
    protected $defaultTime = "4hours";
    protected $selectBox = null;

    public function __construct($config)
    {
        $this->config = $config;
        if (!isset($this->config["graphite"]["hidelegend"])) {
            $this->config["graphite"]["hidelegend"] = true;
        }
        $this->requestURI = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $this->from = filter_input(INPUT_GET, 'from');
        if (!isset($this->from)) {
            $this->from = "-" . $this->defaultTime;
        }
    }

    /**
     * helper function to build a generic row of Graphite img tag metrics
     *
     * Parameters
     *  $metrics - array of Graphite metrics
     *
     * Returns the HTML for the graphs
     */
    public function buildPageForMetrics($metrics)
    {
        $ret = '<div class="row">';
        foreach ($metrics as $metric) {
            $graph = new GraphiteGraph(
                $this->config['graphite']['host'],
                $this->from,
                null,
                $this->config['graphite']['hidelegend']
            );

            $ret .= '<div class="col-md-4">';
            $ret .= $graph->buildGraphImgTag($metric);
            $ret .= '</div>';
        }
        $ret .= '</div>';
        return $ret;
    }

    /**
     * helper function to generically render a full page
     *
     * Parameters:
     *  $metrics - array of graphite metrics to render
     */
    public function renderFullPageWithMetrics($metrics)
    {
        print $this->getHeader();
        print $this->buildPageForMetrics($metrics);
        print $this->getFooter();
    }

    /**
     * helper function to return bootstrap compatible attribute data if the
     * current URI matches the passed in one
     *
     * Parameters
     *  $requestUri - URI to match
     *
     * Returns class='active' or the empty string
     */
    public function getActiveClassIfRequestMatches($requestUri)
    {
        $ret = "";
        if ($this->requestURI === $requestUri) {
            $ret = " class='active'";
        }

        return $ret;
    }

    /**
     * get a select box for an array of times
     *
     * Parameters
     *  $times - array of times
     *
     * Returns the HTML for the full select box as a string
     */
    public function getTimeSelectBox($times = null)
    {

        if (is_null($times)) {
            $times = $this->defaultTimes;
        }

        $ret = "";
        $ret .= "<form method='get' action='{$this->requestURI}' ";
        $ret .= "style='margin-top: 15px'class='pull-right'>";
        $ret .= "<select name='from' onchange='this.form.submit()'>" . PHP_EOL;
        foreach ($times as $timefrom) {
            $current = $this->from ?: "-{$this->defaultTime}";
            $selected = ($current == "-${timefrom}") ? " selected" : "";
            $ret .= "<option ";
            $ret .= "value='-{$timefrom}'{$selected}>";
            $ret .= "{$timefrom}</option>";
            $ret .= PHP_EOL;
        }

        $ret .= "</select> </form>";

        return $ret;

    }

    /**
     * Set the select box property
     *
     * Parameters
     *  $select_box - HTML for the select box
     */
    public function setSelectBox($selectBox)
    {
        $this->selectBox = $selectBox;
    }

    /**
     * Set the requestURI property. This is probably only useful for unit
     * testing.
     *
     * Parameters
     *  $requestUri - URI to set the requestURI to
     */
    public function setRequestURI($requestURI)
    {
        $this->requestURI = $requestURI;
    }


    /**
     * Get the Yagd HTML header for a page. This can be passed in a couple of
     * parameters to get a customized page. However by default all parameters
     * are taken from the Page object configuration.
     *
     * Parameters
     *  $title     - title of the current page
     *  $nav_items - items to show in the nav bar
     *  $selectbox - select box HTML to show
     *  $times     - array of times for the drop down
     *
     *  Returns the header data as a string
     */
    public function getHeader(
        $title = null,
        $navItems = [],
        $selectbox = null,
        $times = null
    ) {

        if (is_null($title)) {
            $title = $this->config["title"];
        }
        $navBar = '<li' . $this->getActiveClassIfRequestMatches("/") .'>';
        $navBar .= '<a href="/">Home</a></li>';
        foreach ($navItems as $name => $url) {
            $navBar .= PHP_EOL;
            $navBar .= "<li" . $this->getActiveClassIfRequestMatches($url) .">";
            $navBar .= "<a href='{$url}'>{$name}</a></li>";
        }
        $selectboxHtml = "<!-- select box would appear here -->";
        if (!is_null($selectbox)) {
            $selectboxHtml = "<div class='nav navbar-nav navbar-right'>";
            $selectboxHtml .= $selectbox . "</div>";
        } elseif (!is_null($this->selectBox)) {
            $selectboxHtml = "<div class='nav navbar-nav navbar-right'>";
            $selectboxHtml .= $this->selectBox . "</div>";
        }

        $timeselect = $this->getTimeSelectBox($times);

        $header = <<<"EOD"
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

    <style>
        body {
            padding-top: 65px;
        }
    </style>

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
          <a class="navbar-brand" href="#">{$title} </a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            {$navBar}
          </ul>
            <div class='nav navbar-nav navbar-left'>
            {$timeselect}
            </div>
            {$selectboxHtml}
        </div><!--/.nav-collapse -->
      </div>
    </div>
  <div class="container">
EOD;
        return $header;
    }

    /**
     * get footer for page. This should always be called if getHeader() has
     * been called before as they complement each other.
     *
     * Returns the footer as a string
     */
    public function getFooter()
    {
        $footer = <<<"EOF"
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  </body>
</html>
EOF;
        return $footer;
    }
}
