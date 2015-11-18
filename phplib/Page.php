<?php

namespace Yagd;

class Page {

    protected $config = [];
    protected $default_times = [
        '1hour', '4hours', '12hours',
        '1day', '2days',
        '1week',
        '1month', '3months', '6months',
        '1year'
    ];
    protected $default_time = "4hours";
    protected $select_box = null;

    function __construct($config) {
        $this->config = $config;
        if (!isset($this->config["graphite"]["hidelegend"])) {
            $this->config["graphite"]["hidelegend"] = true;
        }
        if (isset($_GET["from"])) {
            $this->from = $_GET["from"];
        } else {
            $this->from = "-" . $this->default_time;
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
    function build_page_for_metrics($metrics) {
        $ret = '<div class="row">';
            foreach ( $metrics as $metric) {

                $graph = new GraphiteGraph($this->config['graphite']['host'],
                    $this->from, null,
                    $this->config['graphite']['hidelegend']);

                $ret .= '<div class="col-md-4">';
                $ret .= $graph->build_graph_img_tag($metric);
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
    function render_full_page_with_metrics($metrics) {
        print $this->get_header();
        print $this->build_page_for_metrics($metrics);
        print $this->get_footer();
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
    function get_active_class_if_request_matches($requestUri)
    {
        $ret = "";
        if ($_SERVER['REQUEST_URI'] == $requestUri) {
            $ret = "class='active'";
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
    function get_time_select_box($times = null) {

        if (is_null($times)) {
            $times = $this->default_times;
        }

        $ret = "";
        $ret .= "<form method='get' action='{$_SERVER['REQUEST_URI']}' ";
        $ret .= "style='margin-top: 15px'class='pull-right'>";
        $ret .= "<select name='from' onchange='this.form.submit()'>" . PHP_EOL;
        foreach ($times as $timefrom) {
            $current = $this->from ?: "-{$this->default_time}";
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
    function set_select_box($select_box) {
        $this->select_box = $select_box;
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
    function get_header($title = null, $nav_items = [],
                        $selectbox = null, $times = null) {

        if (is_null($title)) {
            $title = $this->config["title"];
        }
        $nav_bar = '<li' . $this->get_active_class_if_request_matches("/") .'>';
        $nav_bar .= '<a href="/">Home</a></li>';
        foreach ($nav_items as $name => $url) {
            $nav_bar .= PHP_EOL;
            $nav_bar .= "<li" . $this->get_active_class_if_request_matches($url) .">";
            $nav_bar .= "<a href='{$url}'>{$name}</a></li>";
        }
        if (!is_null($selectbox)) {
            $selectbox_html = "<div class='nav navbar-nav navbar-right'>";
            $selectbox_html .= $selectbox . "</div>";
        } elseif (!is_null($this->select_box)) {
            $selectbox_html = "<div class='nav navbar-nav navbar-right'>";
            $selectbox_html .= $this->select_box . "</div>";
        } else {
            $selectbox_html = "<!-- select box would appear here -->";
        }

        $timeselect = $this->get_time_select_box($times);

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
            {$nav_bar}
          </ul>
            <div class='nav navbar-nav navbar-left'>
            {$timeselect}
            </div>
            {$selectbox_html}
        </div><!--/.nav-collapse -->
      </div>
    </div>
  <div class="container">
EOD;
        return $header;
    }

    /**
     * get footer for page. This should always be called if get_header() has
     * been called before as they complement each other.
     *
     * Returns the footer as a string
     */
    function get_footer() {
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
