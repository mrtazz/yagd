<?php
require_once("phplib/Page.php");

use Yagd\Page;

class PageTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {

        $this->page = new Page(["title" => "foo",
                                "graphite" => ["host" => "http://grpht.exmpl.com"]]);
        $this->page->setRequestURI("/foo");
    }
    public function testInstantiation()
    {
        $this->assertInstanceOf('Yagd\Page', $this->page);
    }

    public function testInstantiationWithMockGET()
    {
        $_GET["from"] = "-1hour";
        $this->page = new Page([]);
        $this->assertInstanceOf('Yagd\Page', $this->page);
    }

    public function testGetFooter() {
        $expected = <<<"EOF"
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  </body>
</html>
EOF;
        $this->assertEquals($expected, $this->page->getFooter());

    }

    public function testGetActiveClassIfRequestMatches() {

        $this->assertEquals(" class='active'", $this->page->getActiveClassIfRequestMatches("/foo"));
    }

    public function testGetTimeSelectBox() {
        $times = [
            '1hour', '4hours', '12hours',
            '1day', '2days',
            '1week',
            '1month', '3months', '6months',
            '1year'
        ];
        $expected = <<<EOE
<form method='get' action='/foo' style='margin-top: 15px'class='pull-right'><select name='from' onchange='this.form.submit()'>
<option value='-1hour'>1hour</option>
<option value='-4hours' selected>4hours</option>
<option value='-12hours'>12hours</option>
<option value='-1day'>1day</option>
<option value='-2days'>2days</option>
<option value='-1week'>1week</option>
<option value='-1month'>1month</option>
<option value='-3months'>3months</option>
<option value='-6months'>6months</option>
<option value='-1year'>1year</option>
</select> </form>
EOE;
        $ret = $this->page->getTimeSelectBox($times);

        $this->assertEquals($expected, $ret);
    }

    public function testGetTimeSelectBoxWithDefaultTimes() {
        $expected = <<<EOE
<form method='get' action='/foo' style='margin-top: 15px'class='pull-right'><select name='from' onchange='this.form.submit()'>
<option value='-1hour'>1hour</option>
<option value='-4hours' selected>4hours</option>
<option value='-12hours'>12hours</option>
<option value='-1day'>1day</option>
<option value='-2days'>2days</option>
<option value='-1week'>1week</option>
<option value='-1month'>1month</option>
<option value='-3months'>3months</option>
<option value='-6months'>6months</option>
<option value='-1year'>1year</option>
</select> </form>
EOE;
        $ret = $this->page->getTimeSelectBox();

        $this->assertEquals($expected, $ret);
    }

    public function testGetHeader() {
        $expected = <<<"EOD"
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
          <a class="navbar-brand" href="#">foo </a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/">Home</a></li>
<li><a href='/bla'>bla</a></li>
          </ul>
            <div class='nav navbar-nav navbar-left'>
            <form method='get' action='/foo' style='margin-top: 15px'class='pull-right'><select name='from' onchange='this.form.submit()'>
<option value='-1hour'>1hour</option>
<option value='-4hours' selected>4hours</option>
<option value='-12hours'>12hours</option>
<option value='-1day'>1day</option>
<option value='-2days'>2days</option>
<option value='-1week'>1week</option>
<option value='-1month'>1month</option>
<option value='-3months'>3months</option>
<option value='-6months'>6months</option>
<option value='-1year'>1year</option>
</select> </form>
            </div>
            <div class='nav navbar-nav navbar-right'>selectbox here</div>
        </div><!--/.nav-collapse -->
      </div>
    </div>
  <div class="container">
EOD;
        $nav_items = ["bla" => "/bla"];
        $selectbox = "selectbox here";
        $ret = $this->page->getHeader("foo", $nav_items, $selectbox);
        $this->assertEquals($expected, $ret);
        $this->page->setSelectBox($selectbox);
        $ret = $this->page->getHeader("foo", $nav_items);
        $this->assertEquals($expected, $ret);
    }

    public function testGetHeaderWithoutSelectBox() {
        $expected = <<<"EOD"
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
          <a class="navbar-brand" href="#">foo </a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/">Home</a></li>
<li><a href='/bla'>bla</a></li>
          </ul>
            <div class='nav navbar-nav navbar-left'>
            <form method='get' action='/foo' style='margin-top: 15px'class='pull-right'><select name='from' onchange='this.form.submit()'>
<option value='-1hour'>1hour</option>
<option value='-4hours' selected>4hours</option>
<option value='-12hours'>12hours</option>
<option value='-1day'>1day</option>
<option value='-2days'>2days</option>
<option value='-1week'>1week</option>
<option value='-1month'>1month</option>
<option value='-3months'>3months</option>
<option value='-6months'>6months</option>
<option value='-1year'>1year</option>
</select> </form>
            </div>
            <!-- select box would appear here -->
        </div><!--/.nav-collapse -->
      </div>
    </div>
  <div class="container">
EOD;
        $nav_items = ["bla" => "/bla"];
        $ret = $this->page->getHeader("foo", $nav_items);
        $this->assertEquals($expected, $ret);
    }

    public function testBuildPageForMetrics() {
        $expct = '<div class="row"><div class="col-md-4"><img src="http://grpht.exmpl.com/render?width=400&from=-4hours&target=foo&hideLegend=true"></img></div><div class="col-md-4"><img src="http://grpht.exmpl.com/render?width=400&from=-4hours&target=bla&hideLegend=true"></img></div></div>';
        $ret = $this->page->buildPageForMetrics(["foo", "bla"]);
        $this->assertEquals($expct, $ret);
    }

    public function testRenderFullPageWithMetrics() {
        // we don't care about the output here as it's tested in individual
        // tests already, thus we don't assert anything and clean the output
        // buffer. However we still want to call the method as part of the
        // test suite.
        ob_start();
        $this->page->renderFullPageWithMetrics(["foo", "bla"]);
        ob_end_clean();
    }

}
